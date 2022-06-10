<?php
namespace YuanxinHealthy\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;
use Hyperf\HttpMessage\Stream\SwooleStream;
/**
 * 基础异常，里面得是public,不然json是空对象,导致客户端拿不到
 */
class BaseException extends \Exception
{
    public $message;
    public $code;
    public $file;
    public $line;
    public $subCode;
    const DEFAULT_ERROR_CODE = ErrorCode::CODE_ERROR_SYSTEM;

    public function __construct(string $message = "", int $code = 0, int $subCode = 0, \Throwable $previous = null)
    {
        if (!$code) {
            $code = static::DEFAULT_ERROR_CODE;
        }
        $this->subCode = $subCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 子业务code.
     * 
     * @return
     */
    public function getSubCode()
    {
        return $this->subCode;
    }

    /**
     * http响应.
     * 
     * @param Throwable $throwable
     * @param ResponseInterface $response
     * @return type
     */
    public function httpHandle(Throwable $throwable, ResponseInterface $response, $data = null)
    {
        return $response->withHeader(
            'Content-Type',
            'application/json; charset=utf-8'
        )->withBody(
            new SwooleStream(
                json_encode([
                    'code' => $throwable->getCode(),
                    'msg' => $throwable->getMessage(),
                    'sub_code' => $this->getSubCode(),
                    'data' => $data,
                ])
            )
        );
    }
}