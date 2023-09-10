<?php

use Wof\AltozRouter;

$router = new AltozRouter();


$router->map(
    'GET',
    '/wof/home/',
    function() {
        echo 'hello world';
    },
    'woz-home'
);

$router->run();
