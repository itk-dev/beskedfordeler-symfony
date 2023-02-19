<?php

use Itkdev\BeskedfordelerBundle\Controller\BeskedfordelerController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('beskedfordeler_postStatusBeskedModtag', '/beskedfordeler/postStatusBeskedModtag')
        ->controller([BeskedfordelerController::class, 'postStatusBeskedModtag'])
    ;
};
