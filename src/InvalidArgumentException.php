<?php

declare(strict_types=1);

namespace YuanxinHealthy\Exceptions;

class InvalidArgumentException extends BaseException
{
    const DEFAULT_ERROR_CODE = ErrorCode::CODE_ERROR_ARGUMENT;
}
