<?php
return [
    'router' => function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/users', ['controller\Index', 'list']);
        $r->get('/add', ['controller\Index', 'add']);
    }
];