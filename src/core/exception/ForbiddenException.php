<?php

namespace app\core\exception;

class ForbiddenException extends \Exception
{
    protected $code = 403;
    protected $message = 'Bạn không có quyền truy cập trang này';
}
