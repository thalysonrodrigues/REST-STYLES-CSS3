<?php

declare(strict_types=1);

namespace App\Core\Factory;

use Psr\Container\ContainerInterface;
use App\Domain\Service\CssServiceInterface;
use App\Domain\Service\UsersServiceInterface;
use App\Domain\Service\LogsServiceInterface;

final class CssAndUsersLogHandlerFactory
{
    public function __invoke(ContainerInterface $container, string $name)
    {
        return new $name(
            $container->get(CssServiceInterface::class),
            $container->get(UsersServiceInterface::class),
            $container->get(LogsServiceInterface::class)
        );
    }
}