<?php

namespace app\core\form;

use app\core\Application;
use app\models\Model as Model;

class Form
{
    private ?string $attributes = null;
    private ?Model $model = null;
    private ?array $errors = null;
    /** @var array<int, Field> $fields */
    private array $fields = [];
    private string $submitButton = '';

    public static function builder(): self
    {
        return new self();
    }

    /** @param array{
     *  class?: string,
     *  id?: string,
     *  style?: string,
     *  method: 'post' | 'get',
     *  action?: string
     * } $attributes */
    public function with_attributes(array $attributes): self
    {
        $this->attributes = implode(
            ' ',
            array_map(
                fn(string $key, string $value) => sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8')),
                array_keys($attributes),
                $attributes
            )
        );
        return $this;
    }

    public function with_model(?Model $model): self
    {
        $this->model = $model;
        return $this;
    }

    /** @param array<string, string> $errors */
    public function with_errors(?array $errors = null): self
    {
        $this->errors = $errors;
        return $this;
    }


    /**
     * @param array<int, array{
     *  label?: string,
     *  reset?: bool,
     *  hidden?: bool,
     *  attribute$s?: array<string, string>,
     * }> $fields
     * @param  array<string, string> $sharedAttributes
     */
    public function with_fields(array $fields, array $sharedAttributes = []): self
    {
        if (!isset($this->model)) {
            $user = Application::$SESSION->user;
        }

        foreach ($fields as $field) {
            $attributes = $field['attributes'] ?? [];
            $fieldName = $attributes['name'] ?? null;

            if (!$fieldName) continue;

            if (isset($attributes['type']) && $attributes['type'] === 'date') {
                if (isset($this->model) && !empty($this->model->{$fieldName})) $attributes['value'] = date('Y-m-d', strtotime($this->model->{$fieldName}));
                else if (isset($user) && !empty($user->{$fieldName})) $attributes['value'] = date('Y-m-d', strtotime($user->{$fieldName}));
                else $attributes['value'] = date('Y-m-d');
            } else {
                $attributes['value'] = $this->model?->{$fieldName} ?? $user->{$fieldName} ?? null;
            }

            $this->fields[] = Field::builder()
                ->with_label(
                    $field['label'] ?? null,
                    $this->errors[$fieldName] ?? null
                )
                ->with_shared_attributes($sharedAttributes)
                ->with_attributes($attributes)
                ->with_reset($field['reset'] ?? false)
                ->with_hidden($field['hidden'] ?? null)
                ->build();
        }
        return $this;
    }

    /** @param array{
     *  label: string,
     *  attributes: array{
     *      type: string,
     *      class?: string,
     *      id?: string,
     *  }
     * } $submitButton */
    public function with_submit_button(array $submitButton): self
    {
        $this->submitButton = sprintf(
            '<button %s>%s</button>',
            implode(
                ' ',
                array_map(
                    fn(string $key, string $value) => sprintf('%s="%s"', $key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8')),
                    array_keys($submitButton['attributes']),
                    $submitButton['attributes']
                ),
            ),
            $submitButton['label'],
        );
        return $this;
    }


    public function build(): string
    {
        $form = sprintf(
            '<form %s>
                %s
                %s
            </form>',
            $this->attributes,
            implode(' ', $this->fields),
            $this->submitButton
        );
        return $form;
    }
}
