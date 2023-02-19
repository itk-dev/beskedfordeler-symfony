<?php

use Itkdev\BeskedfordelerBundle\Controller\BeskedfordelerController;
use Itkdev\BeskedfordelerBundle\Helper\MessageHelper;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(BeskedfordelerController::class)
        ->autowire()
        ->tag('controller.service_arguments')

        ->set(MessageHelper::class)
        ->autowire()
    ;
};
