<?php

namespace MirandaLeyva\ContaoCourseManagementBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class MirandaLeyvaContaoCourseManagementBundle extends AbstractBundle
{
  public function loadExtension(
    array $config,
    ContainerConfigurator $containerConfigurator,
    ContainerBuilder $containerBuilder
  ): void {
    $containerConfigurator->import(__DIR__ . '/Resources/config/services.yaml');
  }
}
