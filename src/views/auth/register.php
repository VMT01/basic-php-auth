<?php

use app\core\form\Form;

$registerForm = Form::builder()
    ->with_attributes([
        'class' => 'auth-form',
        'method' => 'post',
    ])
    ->with_model($model ?? null)
    ->with_errors($errors['form_error'] ?? null)
    ->with_fields(
        [
            [
                'label' => 'Họ và tên',
                'required' => true,
                'attributes' => [
                    'name' => 'fullname'
                ]
            ],
            [
                'label' => 'Ngày sinh',
                'required' => true,
                'attributes' => [
                    'name' => 'dob',
                    'type' => 'date'
                ]
            ],
            [
                'label' => 'Email',
                'required' => true,
                'attributes' => [
                    'name' => 'email',
                    'type' => 'email'
                ]
            ],
            [
                'label' => 'Số điện thoại',
                'attributes' => [
                    'name' => 'phone'
                ]
            ],
            [
                'label' => 'Mật khẩu',
                'required' => true,
                'reset' => true,
                'hidden' => true,
                'attributes' => [
                    'name' => 'password',
                    'type' => 'password',
                ],
            ],
            [
                'label' => 'Xác nhận mật khẩu',
                'required' => true,
                'reset' => true,
                'hidden' => true,
                'attributes' => [
                    'name' => 'password_confirm',
                    'type' => 'password',
                ]
            ],
        ],
        ['class' => 'field']
    )
    ->with_submit_button([
        'label' => 'Đăng ký',
        'attributes' => [
            'type' => 'submit',
            'class' => 'auth-submit-button'
        ]
    ])
    ->build();
?>

<h1>Đăng ký</h1>
<p class="auth-subtitle">Đăng ký để bắt đầu làm việc.</p>

<?php echo $registerForm; ?>

<div class="auth-choice">Đã có tài khoản? <a href="login">Đăng nhập</a> để tiếp tục</div>
