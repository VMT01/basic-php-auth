<?php

use app\core\Application;
use app\core\form\Form;

$flashSuccess = Application::$SESSION->getFlash('success');
$flashError = Application::$SESSION->getFlash('error');

/** @var app\entities\User $user */
$user = Application::$SESSION->user;
$user->decodeHtmlSpecialChars();

$imgUploadForm = Form::builder()
    ->with_attributes([
        'method' => 'post',
        'action' => '/upload-image',
        'style' => 'display:none;',
        'enctype' => 'multipart/form-data',
    ])
    ->with_fields([
        [
            'attributes' => ['name' => 'avatar', 'type' => 'file']
        ]
    ])
    ->build();

$updateProfile = Application::$SESSION->get('updateProfile');
unset($_SESSION['updateProfile']);
$profileUpdateForm = Form::builder()
    ->with_attributes([
        'method' => 'post',
        'action' => '/update-profile',
        'class' => 'modal-content',
        'enctype' => 'multipart/form-data',
    ])
    ->with_model($updateProfile['model'] ?? null)
    ->with_errors($updateProfile['error'] ?? null)
    ->with_fields(
        [
            [
                'label' => 'Tên của bạn',
                'attributes' => ['name' => 'fullname', 'placeholder' => 'Tên của bạn']
            ],
            [
                'label' => 'Email',
                'attributes' => ['name' => 'email', 'disabled' => 'true']
            ],
            [
                'label' => 'Username',
                'attributes' => ['name' => 'username', 'disabled' => 'true']
            ],
            [
                'label' => 'Vị trí công việc',
                'attributes' => ['name' => 'title', 'placeholder' => 'Vị trí công việc']
            ],
            [
                'label' => 'Ảnh đại diện',
                'attributes' => ['name' => 'avatar', 'type' => 'file']
            ],
            [
                'label' => 'Ngày tháng năm sinh',
                'attributes' => ['name' => 'dob', 'type' => 'date']
            ],
            [
                'label' => 'Số điện thoại',
                'attributes' => ['name' => 'phone', 'placeholder' => 'Số điện thoại']
            ],
            [
                'label' => 'Chỗ ở hiện nay',
                'attributes' => ['name' => 'address', 'placeholder' => 'Chỗ ở hiện nay']
            ],
        ],
        ['class' => 'field']
    )
    ->build();

$updatePassword = Application::$SESSION->get('updatePassword');
unset($_SESSION['updatePassword']);
$passwordUpdateForm = Form::builder()
    ->with_attributes([
        'method' => 'post',
        'action' => '/update-password',
        'class' => 'modal-content'
    ])
    ->with_model($updatePassword['model'] ?? null)
    ->with_errors($updatePassword['error'] ?? null)
    ->with_fields(
        [
            [
                'label' => 'Mật khẩu hiện tại',
                'hidden' => true,
                'attributes' => ['name' => 'current_password', 'placeholder' => 'Mật khẩu hiện tại', 'type' => 'password']
            ],
            [
                'label' => 'Mật khẩu mới',
                'hidden' => true,
                'attributes' => ['name' => 'new_password', 'placeholder' => 'Mật khẩu mới', 'type' => 'password']
            ],
            [
                'label' => 'Nhập lại mật khẩu mới',
                'hidden' => true,
                'attributes' => ['name' => 'confirm_password', 'placeholder' => 'Nhập lại mật khẩu mới', 'type' => 'password']
            ],
        ],
        ['class' => 'field']
    )
    ->build();

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <title><?php echo $user->fullname; ?> - Account - Base Inc</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://static-gcdn.basecdn.net/account/image/fav.png" type="image/x-icon" />
    <link rel="shortcut icon" href="https://static-gcdn.basecdn.net/account/image/fav.png" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:500,400,300,400italic,700,700italic,400italic,300italic&subset=vietnamese,latin">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/css/profile.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="/js/FlashMessage.js"></script>
    <script src="/js/OverlayModal.js"></script>
    <script src="/js/PasswordVisibilityManager.js"></script>
    <script src="/js/profile.js"></script>
</head>

<body>
    <div class="container">
        <div class="wrapper">
            <div class="header">
                <div>
                    <p class="label">Tài khoản</p>
                    <p class="title"><?php echo $user->fullname; ?> · <?php echo $user->title ??  'Chưa nhập chức danh'; ?></p>
                </div>
                <button class="btn" data-action="openProfile">Chỉnh sửa tài khoản</button>
            </div>
            <div class="detail">
                <div class="uploadable">
                    <img src="<?php echo $user->avatar ?? '/media/anonymous.jpeg'; ?>">
                    <?php echo $imgUploadForm; ?>
                </div>
                <div>
                    <div class="fullname"><?php echo $user->fullname; ?></div>
                    <div class="title"><?php echo $user->title ?? 'Chưa nhập chức danh'; ?></div>

                    <!-- TODO: This can be a Grid Builder -->
                    <div class="grid-container">
                        <div class="info-item">
                            <div class="label">Ngày sinh</div>
                            <div class="value"><?php echo date("d/m/Y", strtotime($user->dob)); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Địa chỉ email</div>
                            <div class="value"><?php echo $user->email; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Số điện thoại</div>
                            <div class="value"><?php echo $user->phone; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Công ty</div>
                            <div class="value"><?php echo $user->company; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Quản lý trực tiếp</div>
                            <div class="value" style="display: flex; flex-direction: column;">
                                <a href="#" class="link">Bùi Thanh Tùng</a>
                                <a href="#" class="link">Vũ Thanh Hải</a>
                                <a href="#" class="link">Minh Nguyen Thuong Tuong</a>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">Địa chỉ</div>
                            <div class="value"><?php echo $user->address ?? 'Chưa nhập địa chỉ'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="side-navbar">
            <div class="top">
                <div class="fullname"><?php echo $user->fullname; ?></div>
                <div class="subtitle">
                    <?php echo $user->username; ?> · <?php echo $user->email; ?>
                </div>
            </div>
            <div class="title">Thông tin tài khoản</div>
            <div class="box">
                <ul>
                    <li><i class="fa fa-cog"></i> Tài khoản</li>
                    <li data-action="openProfile"><i class="fa fa-pencil"></i> Chỉnh sửa</li>
                    <li data-action="openPassword"><i class="fa fa-info-circle "></i> Đổi mật khẩu</li>
                </ul>
            </div>
            <button class="btn logout">Đăng xuất</button>
        </div>
    </div>
    <div
        class="modal-overlay"
        style="<?php if (($updateProfile && $updateProfile['error']) || ($updatePassword && $updatePassword['error'])): echo 'display: flex;';
                endif; ?>
            ">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Chỉnh sửa thông tin cá nhân</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="profile-update-form" style="<?php if ($updateProfile && $updateProfile['error']): echo 'display: inline;';
                                                        endif; ?>">
                    <div><?php echo $profileUpdateForm; ?></div>
                    <div class="modal-footer">
                        <button class="btn btn-cancel">Hủy</button>
                        <button class="btn btn-confirm">Xác nhận</button>
                    </div>
                </div>
                <div class="password-update-form" style="<?php if ($updatePassword && $updatePassword['error']): echo 'display: inline;';
                                                            endif; ?>">
                    <div><?php echo $passwordUpdateForm; ?></div>
                    <div class=" modal-footer">
                        <button class="btn btn-cancel">Bỏ qua</button>
                        <button class="btn btn-confirm">Đổi mật khẩu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const flash = new FlashMessage();
        <?php if ($flashSuccess): ?>
            flash.show('<?php echo $flashSuccess ?>');
        <?php elseif ($flashError): ?>
            flash.show('<?php echo $flashError ?>', 'error');
        <?php endif; ?>
    </script>
</body>

</html>
