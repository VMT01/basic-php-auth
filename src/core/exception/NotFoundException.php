<?php

namespace app\core\exception;

class NotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = "Trang không tồn tại";
}
