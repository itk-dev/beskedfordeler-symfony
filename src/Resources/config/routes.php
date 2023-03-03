<?php

use Itkdev\BeskedfordelerBundle\Controller\BeskedfordelerController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('beskedfordeler_postStatusBeskedModtag', '/beskedfordeler/PostStatusBeskedModtag')
        ->controller([BeskedfordelerController::class, 'postStatusBeskedModtag'])
    ;
};
