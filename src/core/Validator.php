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
        'MIN' => 'min',
        'MAX' => 'max',
        'COMPLEX' => 'complex',
        'MATCH' => 'match',
        'UNIQUE' => 'unique',
        'IMAGE' => 'image'
    ];
    private const RULE_MESSAGES = [
        self::RULES['REQUIRED'] => 'This field is required',
        self::RULES['EMAIL'] => 'This field must be valid email address',
        self::RULES['PHONE'] => 'This field must be valid phone number',
        self::RULES['MIN'] => 'Min length of this field must be {min}',
        self::RULES['MAX'] => 'Max length of this field must be {max}',
        self::RULES['COMPLEX'] => 'This field must include lowercase, uppercase and numbers',
        self::RULES['MATCH'] => 'This field must be the same as {match}',
        self::RULES['UNIQUE'] => 'Record with this {field} already exists',
        self::RULES['IMAGE'] => 'Invalid image'
    ];

    protected array $errors = [];

    public function validate(): array
    {
        foreach ($this->rules ?? [] as $attribute => $rules) {
            if (!property_exists($this, $attribute)) {
                continue;
            }

            $value = $this->{$attribute} ?? null;

            foreach ($rules as $rule) {
                if (is_array($rule)) {
                    $rule_name = $rule[0];
                    unset($rule[0]);
                } else {
                    $rule_name =  $rule;
                }
                $method = 'validate_' . $rule_name;

                if (!method_exists($this, $method)) {
                    continue;
                }

                $this->$method($attribute, $value, $rule);
            }
        }

        return $this->errors;
    }

    private function validate_required(string $attribute, mixed $value)
    {
        if (empty($value) && $value !== '0' && $value !== 0) {
            $this->addError($attribute, self::RULES['REQUIRED']);
        }
    }

    private function validate_email(string $attribute, ?string $value): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($attribute, self::RULES['EMAIL']);
        }
    }

    private function validate_phone(string $attribute, ?string $value): void
    {
        if (
            !empty($value) &&
            !filter_var(
                $value,
                FILTER_VALIDATE_REGEXP,
                ['options' => ['regexp' => '/^(?:\+84|0)[0-9]{9}$/']]
            )
        ) {
            $this->addError($attribute, self::RULES['PHONE']);
        }
    }

    private function validate_min(string $attribute, ?string $value, array $params): void
    {
        $min = $params['min'];
        if (!empty($value) && !empty($min) && strlen($value) < $min) {
            $this->addError($attribute, self::RULES['MIN'], $params);
        }
    }

    private function validate_max(string $attribute, ?string $value, array $params): void
    {
        $max = $params['max'];
        if (!empty($value) && !empty($max) && strlen($value) > $max) {
            $this->addError($attribute, self::RULES['MAX'], $params);
        }
    }

    private function validate_complex(string $attribute, ?string $value): void
    {
        if (
            !empty($value) &&
            !filter_var(
                $value,
                FILTER_VALIDATE_REGEXP,
                ['options' => ['regexp' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/']]
            )
        ) {
            $this->addError($attribute, self::RULES['COMPLEX']);
        }
    }

    private function validate_match(string $attribute, ?string $value, array $params): void
    {
        $match_value = $this->{$params['match']};

        if (!empty($value) && $value !== $match_value) {
            $this->addError($attribute, self::RULES['MATCH'], $params);
        }
    }

    private function validate_unique(string $attribute, ?string $value, array $params): void
    {
        if (empty($value)) return;

        $className = $params['class'];
        $tableName = $className::TABLE_NAME;

        $SQL = SQLBuilder::builder()
            ->select()
            ->table($tableName)
            ->where(["$attribute = :$attribute"])
            ->build();
        $statement = Application::$DATABASE->prepare($SQL);
        $statement->execute([$attribute => $value]);
        $record = $statement->fetchObject();

        // TODO: This must be more generic instead of using SESSION->user
        if (
            $record &&
            (!Application::$SESSION->user || Application::$SESSION->user->id !== $record->id)
        ) {
            $this->addError($attribute, self::RULES['UNIQUE'], ['field' => $attribute]);
        }
    }

    /**
     * @param array{
     *      name: string,
     *      full_path: string,
     *      type: string,
     *      tmp_name: string,
     *      error: bool,
     *      size: int
     *  } $value
     */
    private function validate_image(string $attribute, array $value)
    {
        $fileType = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

        if (
            !is_uploaded_file($value['tmp_name']) ||
            !getimagesize($value['tmp_name']) ||
            ($fileType !== 'jpg' && $fileType !== 'jpeg' && $fileType !== 'png') ||
            $value['size'] > 500_000
        ) {
            $this->addError($attribute, self::RULES['IMAGE'], ['field' => $attribute]);
        }
    }

    private function addError(string $attribute, string $rule, array $params = []): void
    {
        $message = $this->errorMessages($rule);
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute] = $message;
    }

    private function errorMessages(string $rule): string
    {
        return self::RULE_MESSAGES[$rule] ?? 'Validation failed';
    }
}
