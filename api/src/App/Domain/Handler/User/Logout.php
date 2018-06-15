<?php

declare(strict_types=1);

namespace App\Domain\Handler\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Domain\Service\LogsServiceInterface;
use Firebase\JWT\JWT;
use App\Domain\Service\Exception\JtiAlreadyExistsException;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\EmptyResponse;

final class Logout implements MiddlewareInterface
{
    /**
     * @var LogsServiceInterface
     */
    private $log;
    /**
     * @var string
     */
    private $jwtSecret;

    public function __construct(LogsServiceInterface $log, string $jwtSecret)
    {
        $this->log = $log;
        $this->jwtSecret = $jwtSecret;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $query = $request->getQueryParams();

        $payload = JWT::decode($query['token'], $this->jwtSecret, ['HS256']);

        try {
            $process = $this->log->logout((int) $payload->data->id, (string) $payload->jti);
        } catch (JtiAlreadyExistsException $e) {
            return new JsonResponse([
                'code' => '500',
                'message' => $e->getMessage()
            ]);
        }

        if (1 === $process) {
            return new EmptyResponse();
        } else {
            return new JsonResponse([
                'code' => '500',
                'message' => 'Internal server error'
            ]);
        }
    }
}
