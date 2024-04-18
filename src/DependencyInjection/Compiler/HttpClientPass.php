<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClientBundle\DependencyInjection\Compiler;

use Superbrave\VerboseErrorHttpClientBundle\HttpClient\VerboseErrorHttpClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HttpClientPass implements CompilerPassInterface
{
    /**
     * @var int The priority of the decoration. Should be one lower than {@see TraceAbleHttpClient} which is set in the
     * default HttpClient compiler pass: {@see \Symfony\Component\HttpClient\DependencyInjection\HttpClientPass::process()}.
     */
    public const VERBOSE_ERROR_HTTP_CLIENT_DECORATION_PRIORITY = -1;

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('http_client.client') as $id => $tags) {
            $container->register('.verbose_error_http_client.'.$id, VerboseErrorHttpClient::class)
                ->setArguments([new Reference('.verbose_error_http_client.'.$id.'.inner')])
                ->setDecoratedService($id, null, self::VERBOSE_ERROR_HTTP_CLIENT_DECORATION_PRIORITY);
        }
    }
}
