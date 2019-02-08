<?php
return [
    'router' => function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/users', ['Index', 'list']);
        $r->addRoute('GET', '/user/{uid:\d+}', 'Index@user');
        $r->get('/add', ['Index', 'add']);
        $r->get('/test', function () {
            return "i am test";
        });
        $r->post('/post', function () {
            return "must post method";
        });
        $r->get('/{m:[a-zA-Z0-9]+}', 'Index@{m}');
    }
];