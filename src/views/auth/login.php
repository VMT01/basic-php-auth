<?php

use app\core\form\Form;

$loginForm = Form::builder()
    ->with_attributes([
        'class' => 'auth-form',
        'method' => 'post',
    ])
    ->with_model($model ?? null)
    ->with_errors($errors['form_error'] ?? null)
    ->with_fields(
        [
            [
                'label' => 'Email',
                'attributes' => [
                    'name' => 'email',
                    'type' => 'email',
                ]
            ],
            [
                'label' => 'Mật khẩu',
                'reset' => true,
                'hidden' => true,
                'attributes' => [
                    'name' => 'password',
                    'type' => 'password',
                ]
            ],
        ],
        [
            'class' => 'field'
        ]
    )
    ->with_submit_button([
        'label' => 'Đăng nhập',
        'attributes' => [
            'type' => 'submit',
            'class' => 'auth-submit-button'
        ]
    ])
    ->build();
?>

<h1>Đăng nhập</h1>
<p class="auth-subtitle">Chào mừng trở lại. Đăng nhập để bắt đầu làm việc.</p>

<?php echo $loginForm; ?>

<div class="auth-choice">Chưa có tài khoản? <a href="register">Đăng ký</a> để tiếp tục</div>
