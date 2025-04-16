<?php

namespace app\core\form;

class Field
{
    private const VALIDATION_MESSAGE = [
        'FORM_REQUIRED' => 'Trường này là bắt buộc',
        'FORM_EMAIL' => 'Email không hợp lệ',
        'FORM_PHONE' => 'Số điện thoại không hợp lệ',
        'FORM_RANGE_MIN' => 'Yêu cầu tối thiểu {min} ký tự',
        'FORM_RANGE_MAX' => 'Yêu cầu tối đa {max} ký tự',
        'FORM_COMPLEX' => 'Yêu cầu phải có chữ hoa, chữ thường và số',
        'FORM_MATCH' => 'Yêu cầu phải trùng khớp với {match}',
        'FORM_UNIQUE' => 'Giá trị này đã được sử dụng',
        'FORM_IMAGE_UPLOAD' => 'Đã có lỗi khi gửi ảnh',
        'FORM_IMAGE_INVALID' => 'Tệp đã gửi không phải là ảnh',
        'FORM_IMAGE_TYPE' => 'Ảnh chỉ chấp nhận kiểu {types}',
        'FORM_IMAGE_SIZE' => 'Ảnh không được vượt quá {maxSize}MB',
        'PASSWORD_INCORRECT' => 'Mật khẩu không chính xác',
        'PASSWORD_DUPLICATED' => 'Mật khẩu mới không thể trùng với mật khẩu cũ'
    ];

    private ?string $label = null;
    private bool $reset = false;
    private ?string $hidden = null;
    /** @var array<int, string> $sharedAttributes */
    private array $sharedAttributes = [];
    /** @var array<int, string> $attributes */
    private array $attributes = [];

    /**
     * @return self
     */
    public static function builder()
    {
        return new self();
    }

    /**
     * @param string $label
     * @param null | array<string, string> $error
     * @param bool $required
     * @return self
     */
    public function with_label($label, $error, $required)
    {
        if ($error) {
            $errorMessage = self::VALIDATION_MESSAGE[$error['code']];
            foreach ($error as $key => $value) {
                $errorMessage = str_replace("{{$key}}", $value, $errorMessage);
            }
        }

        if ($label) {
            $this->label = sprintf(
                '<div class="label">
                    <label>%s %s</label>
                    <div>%s</div>
                </div>',
                $label,
                $required ? '<span style="color:red;">(*)</span>' : null,
                $errorMessage ?? null
            );
        }
        return $this;
    }

    /**
     * @param array<string,string> $sharedAttributes
     * @return self
     */
    public function with_shared_attributes($sharedAttributes)
    {
        foreach ($sharedAttributes as $key => $value) {
            $this->sharedAttributes[] = sprintf('%s="%s"', $key, $value);
        }
        return $this;
    }

    public function with_reset(bool $reset): self
    {
        $this->reset = $reset;
        return $this;
    }

    /** 
     * @param array<string, string> $attributes 
     * @return self
     */
    public function with_attributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->reset && $key === 'value') continue;
            $this->attributes[] = sprintf('%s="%s"', $key, $value);
        }
        return $this;
    }

    public function with_hidden(?bool $hidden): self
    {
        if ($hidden) {
            $this->hidden = '<i class="toggle-hidden fa fa-eye-slash" aria-hidden="true"></i>';
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
