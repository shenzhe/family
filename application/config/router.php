<?php
return [
    'router' => function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/users', ['controller\Index', 'list']);
        $r->addRoute('GET', '/user/{uid:\d+}', 'controller\Index@user');
        $r->get('/add', ['controller\Index', 'add']);
        $r->get('/test', function () {
            return "i am test";
        });
        $r->post('/post', function () {
            return "must post method";
        });
    }
];