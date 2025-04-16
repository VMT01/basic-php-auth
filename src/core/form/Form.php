<?php

namespace app\core\form;

use app\core\Application;
use app\entities\User;
use app\models\Model as Model;

class Form
{
    private ?string $attributes = null;
    private ?Model $model = null;
    /** @var array<string, string> $fields */
    private ?array $errors = null;
    /** @var array<int, Field> $fields */
    private array $fields = [];
    private string $submitButton = '';
    private ?User $user;

    public static function builder(): self
    {
        $self = new self();
        $self->user = Application::$SESSION->user;

        return $self;
    }

    /**
     * @param array{
     *  class?: string,
     *  id?: string,
     *  style?: string,
     *  method: 'post' | 'get',
     *  action?: string
     * } $attributes Form attributes
     */
    public function with_attributes($attributes)
    {
        $this->attributes = implode(
            ' ',
            array_map(
                fn($key, $value) => sprintf('%s="%s"', $key, $value),
                array_keys($attributes),
                $attributes
            )
        );
        return $this;
    }

    /**
     * @param null|Model $model
     */
    public function with_model($model): self
    {
        $this->model = $model;
        return $this;
    }

    /** 
     * @param null|array<string, array<string, string>> $errors 
     */
    public function with_errors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @param array<
     *  int,
     *  array{
     *      label?: string,
     *      required?: bool,
     *      reset?: bool,
     *      hidden?: bool,
     *      attributes: array{
     *          name?: string,
     *          type: string,
     *          value?: string
     *      }
     *  }
     * > $fields
     * @param array<string, string> $sharedAttributes
     */
    public function with_fields($fields, $sharedAttributes = [])
    {
        foreach ($fields as $field) {
            $attributes = $field['attributes'];
            $name = $attributes['name'];
            if ($name === null) continue;

            $modelValue = $this->model?->{$name};
            $rawValue = htmlspecialchars_decode(
                (!is_array($modelValue) ? $modelValue : null)
                    ?? $this->user?->{$name}
                    ?? ''
            );

            if (isset($attributes['type']) && $attributes['type'] === 'date') {
                $attributes['value'] = $rawValue ?
                    date('Y-m-d', strtotime($rawValue)) :
                    date('Y-m-d');
            } else {
                $attributes['value'] = $rawValue;
            }

            $this->fields[] = Field::builder()
                ->with_label($field['label'] ?? '', $this->errors[$name] ?? null, $field['required'] ?? false)
                ->with_shared_attributes($sharedAttributes)
                ->with_reset($field['reset'] ?? false)
                ->with_attributes($attributes)
                ->with_hidden($field['hidden'] ?? false)
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
