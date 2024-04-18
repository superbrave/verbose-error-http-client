<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClient\DependencyInjection;

use Superbrave\VerboseErrorHttpClient\VerboseErrorHttpClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HttpClientPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('http_client.client') as $id => $tags) {
            $container->register('.verbose_error_http_client.'.$id, VerboseErrorHttpClient::class)
                ->setArguments([new Reference('.verbose_error_http_client.'.$id.'.inner')])
                ->setDecoratedService($id, null, 4); // higher priority than TraceableHttpClient (5)
        }
    }
}
