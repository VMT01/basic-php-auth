<?php

namespace app\core\form;

class Field
{
    private ?string $label = null;
    private bool $reset = false;
    private ?string $hidden = null;
    /** @var array<int, string> $sharedAttributes */
    private array $sharedAttributes = [];
    /** @var array<int, string> $attributes */
    private array $attributes = [];

    public static function builder(): self
    {
        return new self();
    }

    public function with_label(?string $label, ?string $error): self
    {
        if ($label) {
            $this->label = sprintf(
                '<div class="label">
                    <label>%s</label>
                    <div>%s</div>
                </div>',
                $label,
                $error,
            );
        }
        return $this;
    }

    public function with_reset(bool $reset): self
    {
        $this->reset = $reset;
        return $this;
    }

    public function with_hidden(?bool $hidden): self
    {
        if ($hidden) {
            $this->hidden = '<i class="toggle-hidden fa fa-eye-slash" aria-hidden="true"></i>';
        }
        return $this;
    }

    public function with_shared_attributes(array $sharedAttributes): self
    {
        foreach ($sharedAttributes as $key => $value) {
            $this->sharedAttributes[] = sprintf('%s="%s"', $key, $value);
        }
        return $this;
    }

    /** @param array<string, string> $attributes */
    public function with_attributes(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->reset) continue;
            $this->attributes[] = sprintf('%s="%s"', $key, $value);
        }
        return $this;
    }

    public function build(): string
    {
        $field = sprintf(
            '<div %s>
                %s
                <div class="input">
                    <input %s>
                    %s
                </div>
            </div>',
            implode(' ', $this->sharedAttributes),
            $this->label,
            implode(' ', $this->attributes),
            $this->hidden
        );

        return $field;
    }
}
