<?php

namespace app\core;

use app\core\Application;
use app\core\database\SQLBuilder;

abstract class Validator
{
    protected const RULES = [
        'REQUIRED' => 'required',
        'EMAIL' => 'email',
        'PHONE' => 'phone',
        'RANGE' => 'range',
        'COMPLEX' => 'complex',
        'MATCH' => 'match',
        'UNIQUE' => 'unique',
        'IMAGE' => 'image'
    ];

    /** @var array<string, array<string, string>> $errors */
    protected $errors = [];

    /** @var array<string, array<int, string | array>> $rules */
    protected $rules = [];

    /**
     * Validate all rules against object properties
     */
    public function validate()
    {
        foreach ($this->rules as $attribute => $rules) {
            if (!property_exists($this, $attribute)) continue;

            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = is_array($rule) ? array_shift($rule) : $rule;
                $method = 'validate_' . $ruleName;

                if (!method_exists($this, $method)) continue;
                if (!$this->$method($attribute, $value, $rule)) break;
            }
        }

        return $this->errors;
    }

    /**
     * Validates that a value is not empty
     *
     * @param string $attribute Field name
     * @param null|string $value Field value
     */
    private function validate_required($attribute, $value)
    {
        if (!empty($value) || $value === '0' || $value === 0) return true;
        $this->errors[$attribute] = ['code' => 'FORM_REQUIRED'];
        return false;
    }

    /**
     * Validates email format
     *
     * @param string $attribute Field name
     * @param null|string $value Field value
     */
    private function validate_email($attribute, $value)
    {
        if (empty($value) || filter_var($value, FILTER_VALIDATE_EMAIL)) return true;
        $this->errors[$attribute] = ['code' => 'FORM_EMAIL'];
        return false;
    }

    /**
     * Validates phone number format
     *
     * @param string $attribute Field name
     * @param null|string $value Field value
     */
    private function validate_phone($attribute, $value)
    {
        if (empty($value) || filter_var(
            $value,
            FILTER_VALIDATE_REGEXP,
            [
                'options' => [
                    'regexp' => '/^(?:\+84|0)[0-9]{9}$/'
                ]
            ]
        )) return true;
        $this->errors[$attribute] = ['code' => 'FORM_PHONE'];
        return false;
    }

    /**
     * Validates string length range
     *
     * @param string $attribute Field name
     * @param null|string $value Field value
     * @param array{min?:int,max?:int} $params Min and max length
     */
    private function validate_range($attribute, $value, $params)
    {
        if (empty($value)) return true;

        $length = strlen($value);
        if (isset($params['min']) && $length < $params['min']) {
            $this->errors[$attribute] = ['code' => 'FORM_RANGE_MIN', 'min' => $params['min']];
            return false;
        }
        if (isset($params['max']) && $length > $params['max']) {
            $this->errors[$attribute] = ['code' => 'FORM_RANGE_MAX', 'max' => $params['max']];
            return false;
        }
        return true;
    }

    /**
     * Validates password complexity
     *
     * @param string $attribute Field name
     * @param null|string $value Field value
     */
    private function validate_complex($attribute, $value)
    {
        if (
            empty($value) || filter_var(
                $value,
                FILTER_VALIDATE_REGEXP,
                [
                    'options' => [
                        'regexp' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/'
                    ]
                ]
            )
        ) return true;
        $this->errors[$attribute] = ['code' => 'FORM_COMPLEX'];
        return false;
    }

    /**
     * Validates field matches another field
     *
     * @param string $attribute Field name
     * @param null|string $value Field value
     * @param array{match:string} $params Field to match against
     */
    private function validate_match($attribute, $value, $params)
    {
        if (
            empty($value) ||
            !property_exists($this, $params['match']) ||
            $value === $this->{$params['match']}
        ) return true;
        $this->errors[$attribute] = [
            'code' => 'FORM_MATCH',
            'match' => $params['match']
        ];
        return false;
    }

    /**
     * Validates field is unique in database
     *
     * @param string $attribute Field name
     * @param null|string $value Field value
     * @param array{class:string} $params Class with table info
     */
    private function validate_unique($attribute, $value, $params)
    {
        if (empty($value)) return true;

        // TODO: This must be more generic instead of using SESSION->user
        $except = Application::$SESSION->user->id ?? null;

        $SQL = SQLBuilder::builder()
            ->select()
            ->table($params['class']::TABLE_NAME)
            ->where(["$attribute = :$attribute"])
            ->build();
        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute([$attribute => $value]);
        $record = $statement->fetchObject($params['class']);

        if ($record && (!$except || $except !== $record->id)) {
            $this->errors[$attribute] = ['code' => 'FORM_UNIQUE'];
            return false;
        }
        return true;
    }

    /**
     * Validates file is a proper image
     *
     * @param string $attribute Field name
     * @param null|array{
     *      name: string,
     *      full_path: string,
     *      type: string,
     *      tmp_name: string,
     *      error: bool,
     *      size: int
     *  } $value Field upload info
     *  @param array{
     *      maxSize?: int,
     *      types: array<int, string>
     *  } $params Validation parameters
     */
    private function validate_image($attribute, $value, $params)
    {
        if (empty($value)) return true;

        // Check if file was uploaded properly
        if ($value['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($value['tmp_name'])) {
            $this->errors[$attribute]  = ['code' => 'FORM_IMAGE_UPLOAD'];
            return false;
        }

        // Check if it's an image
        if (!@getimagesize($value['tmp_name'])) {
            $this->errors[$attribute]  = ['code' => 'FORM_IMAGE_INVALID'];
            return false;
        }

        // Check file extension
        $allowedTypes = $params['types'] ?? ['jpg', 'jpeg', 'png'];
        $fileType = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $this->errors[$attribute]  = [
                'code' => 'FORM_IMAGE_TYPE',
                'types' => implode(', ', $allowedTypes)
            ];
            return false;
        }

        // Check file size
        $maxSize = $params['maxSize'] ?? 500_000; // 500KB default
        if ($value['size'] > $maxSize) {
            $this->errors[$attribute]  = [
                'code' => 'FORM_IMAGE_SIZE',
                'maxSize' => round($maxSize / 1048576, 2)
            ];
            return false;
        }
        return true;
    }
}
