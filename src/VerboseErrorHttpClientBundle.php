<?php

declare(strict_types=1);

namespace Superbrave\VerboseErrorHttpClientBundle;

use Superbrave\VerboseErrorHttpClientBundle\DependencyInjection\Compiler\HttpClientPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class VerboseErrorHttpClientBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new HttpClientPass());
    }
}
