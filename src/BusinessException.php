<?php

declare(strict_types=1);

namespace YuanxinHealthy\Exceptions;
class BusinessException extends BaseException
{
    const DEFAULT_ERROR_CODE = ErrorCode::CODE_ERROR_BUSINESS;
}
