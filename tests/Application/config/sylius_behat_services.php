<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// Sylius < 2.3 provides its Behat services only as XML, which Symfony 8 cannot load
return static function (ContainerConfigurator $container): void {
    $services = dirname(__DIR__, 3) . '/vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services';
    $container->import(is_file($services . '.php') ? $services . '.php' : $services . '.xml');
};
