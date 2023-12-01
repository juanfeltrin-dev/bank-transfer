<?php

declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

Router::post('/transactions', 'App\Controller\TransactionController@store');
Router::post('/accounts', 'App\Controller\AccountController@store');