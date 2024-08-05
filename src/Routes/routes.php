<?php
use Controllers\ContactController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', [ContactController::class, 'index']);
    $r->addRoute('GET', '/contacts', [ContactController::class, 'index']);
    $r->addRoute('GET', '/contacts/{id:\d+}', [ContactController::class, 'show']);
    $r->addRoute('GET', '/contacts/create', [ContactController::class, 'create']);
    $r->addRoute('POST', '/contacts/create', [ContactController::class, 'store']);
    $r->addRoute('GET', '/contacts/edit/{id:\d+}', [ContactController::class, 'edit']);
    $r->addRoute('POST', '/contacts/edit/{id:\d+}', [ContactController::class, 'update']);
    $r->addRoute('GET', '/contacts/delete/{id:\d+}', [ContactController::class, 'destroy']);
});
