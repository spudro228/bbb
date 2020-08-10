<?php

namespace Infra\InfraBot;

use Infra\InfraBot\DependencyInjection\InfraBotExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class InfraBotBundle extends Bundle
{
    protected function getContainerExtensionClass()
    {
        return InfraBotExtension::class;
    }

}
