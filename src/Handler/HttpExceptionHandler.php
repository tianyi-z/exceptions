<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace YuanxinHealthy\Exceptions\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Router\Dispatched;

class HttpExceptionHandler extends ExceptionHandler
{
    use \YuanxinHealthy\Exceptions\Traits\VerificationTrait;
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;
    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $request = \Hyperf\Utils\ApplicationContext::getContainer()->get(RequestInterface::class);
        $routeHand = $request->getAttribute(Dispatched::class)->handler;
        $method = strtoupper($request->getMethod() . '');
        $content = [
            'ip' => env('POD_ID', ''),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'code' => $throwable->getCode(),
            'exception' => get_class($throwable),
            'parameter' => json_encode($request->all(), JSON_UNESCAPED_UNICODE), // 参数
            'route' => $routeHand->route . ':' . $method,
        ];
        $this->logger->debug($throwable->getMessage(), $content);
        if ($throwable instanceof \YuanxinHealthy\Exceptions\BaseException) {
            // 自己定义的
            return $throwable->httpHandle($throwable, $response);
        } elseif($this->isWhiteException($throwable)) {
            // 白名单
            $code = $throwable->getCode() ? $throwable->getCode() : 400;
            return $response->withHeader(
                'Content-Type',
                'application/json; charset=utf-8'
            )->withBody(
                new SwooleStream(
                    json_encode([
                        'code' => $code,
                        'msg' => $throwable->getMessage(),
                        'sub_code' => $code,
                        'data' => null,
                    ])
                )
            );
        }elseif (class_exists('\Hyperf\RpcClient\Exception\RequestException') && ($throwable instanceof \Hyperf\RpcClient\Exception\RequestException)) {
            // rpc请求的结果返回的异常,如果是预期的异常需要原样提示出来.
            $className = $throwable->getThrowableClassName();
            if(class_exists($className) && $this->isWhiteExceptionByClass($className)) {
                $code = $throwable->getThrowableCode() ? $throwable->getThrowableCode() : 500;
                return $response->withHeader(
                    'Content-Type',
                    'application/json; charset=utf-8'
                )->withStatus(200)->withBody(
                    new SwooleStream(
                        json_encode([
                            'code' => $code,
                            'msg' => $throwable->getThrowableMessage() ?? "Internal service error",
                            'data' => null,
                            'sub_code' => $code,
                        ])
                    )
                );
            }
        }
        $content['trace'] = $throwable->getTraceAsString();
        $this->logger->error($throwable->getMessage(), $content);
        return $response->withHeader('Server', 'Hyperf')->withStatus(500)->withBody(
            new SwooleStream(
                json_encode([
                    'code' => 500,
                    'msg' => "Internal service error",
                    'data' => null,
                    'sub_code' => 500,
                ])
            )
        );
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
