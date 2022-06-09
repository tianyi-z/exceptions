<?php

declare(strict_types=1);
namespace YuanxinHealthy\Exceptions\Handler;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use YuanxinHealthy\Exceptions\BaseException;
class JsonRpcServerExceptionHandler extends \Hyperf\JsonRpc\Exception\Handler\TcpExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $content = [
            'ip' => env('POD_ID', ''),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'code' => $throwable->getCode(),
            'exception' => get_class($throwable),
            'data' => \Hyperf\Utils\ApplicationContext::getContainer()->get(ServerRequestInterface::class)->getAttribute('data',[])
        ];
        $debug = false;
        if ($throwable instanceof BaseException) {
            $debug = true;
        } elseif (class_exists('\App\Exception\BusinessException') && ($throwable instanceof \App\Exception\BusinessException)) {
            $debug = true;
        } elseif (class_exists('\Hyperf\Utils\Exception\InvalidArgumentException') && ($throwable instanceof \Hyperf\Utils\Exception\InvalidArgumentException)) {
            $debug = true;
        }
        if ($debug) {
            // 自己定的异常
            $this->logger->debug($this->formatter->format($throwable), $content);
        } else {
            $this->logger->error($this->formatter->format($throwable), $content);
        }
        $this->stopPropagation();
        return $response;
    }
}
