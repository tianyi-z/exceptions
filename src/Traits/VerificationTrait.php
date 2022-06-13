<?php
namespace YuanxinHealthy\Exceptions\Traits;
use Throwable;
use YuanxinHealthy\Exceptions\BaseException;
trait VerificationTrait
{
    /**
     *  预期的白名单异常，这样不需要后续处理.
     * 
     * @param Throwable $throwable
     * @return boolean
     */
    public function isWhiteException(Throwable $throwable)
    {
        if ($throwable instanceof BaseException) {
            return true;
        }
        return $this->isWhiteExceptionByClass(get_class($throwable));
    }
    /**
     *  预期的白名单异常，这样不需要后续处理.
     * 
     * @param string $throwable
     * @return boolean
     */
    public function isWhiteExceptionByClass(string $throwable)
    {
        if (strpos($throwable, 'App\Exception') === 0) {
            // 自定义的异常内
            return true;
        }
        if (strpos($throwable, 'YuanxinHealthy\Exceptions') === 0) {
            // 自定义的异常内
            return true;
        }
        if (strpos($throwable, 'Hyperf\Utils\Exception\InvalidArgumentException') === 0) {
            // 自定义的异常内
            return true;
        }
        if (strpos($throwable, '\Hyperf\Utils\Exception\InvalidArgumentException') === 0) {
            // 自定义的异常内
            return true;
        }
        if (strpos($throwable, '\App\Exception') === 0) {
            // 自定义的异常内
            return true;
        }
        if (strpos($throwable, '\YuanxinHealthy\Exceptions') === 0) {
            // 自定义的异常内
            return true;
        }
        return false;
    }
}
