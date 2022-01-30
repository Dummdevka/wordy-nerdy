<?php

declare(strict_types=1);
use Psr\Container\ContainerInterface as ContainerInterface;
use DI\Container;

return function ( Container $container ) {
    $container->set('settings', function() {
        return [
            // Oh. I thought it was Wordy Nerdy =P
            'name' => 'Example Slim Application',
            'displayErrorDetails' => true,
            'logErrorDetails' => true,
            'logErrors' => true
        ];
    });
    $container->set( 'config', require_once BASEDIR . '/config.php');
};
