<?php
declare(strict_types=1);

use Controllers\ContactController;
use Controllers\GroupController;
use Controllers\TagController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    // Contact Routes
    $r->addRoute('GET', '/', [ContactController::class, 'index']);
    $r->addRoute('GET', '/contacts', [ContactController::class, 'index']);
    $r->addRoute('GET', '/contacts/{id:\d+}', [ContactController::class, 'show']);
    $r->addRoute('GET', '/contacts/create', [ContactController::class, 'create']);
    $r->addRoute('POST', '/contacts/create', [ContactController::class, 'store']);
    $r->addRoute('GET', '/contacts/edit/{id:\d+}', [ContactController::class, 'edit']);
    $r->addRoute('POST', '/contacts/edit/{id:\d+}', [ContactController::class, 'update']);
    $r->addRoute('GET', '/contacts/delete/{id:\d+}', [ContactController::class, 'destroy']);
    $r->addRoute('GET', '/contacts/export', [ContactController::class, 'export']);


    // Group Routes
    $r->addRoute('GET', '/groups', [GroupController::class, 'index']);
    $r->addRoute('GET', '/groups/create', [GroupController::class, 'create']);
    $r->addRoute('POST', '/groups/create', [GroupController::class, 'store']);
    $r->addRoute('GET', '/groups/edit/{id:\d+}', [GroupController::class, 'edit']);
    $r->addRoute('POST', '/groups/edit/{id:\d+}', [GroupController::class, 'update']);
    $r->addRoute('GET', '/groups/delete/{id:\d+}', [GroupController::class, 'destroy']);
    $r->addRoute('POST', '/groups/inherit', [GroupController::class, 'inheritGroup']);

    // Tag Routes
    $r->addRoute('GET', '/tags', [TagController::class, 'index']);
    $r->addRoute('GET', '/tags/create', [TagController::class, 'create']);
    $r->addRoute('POST', '/tags/create', [TagController::class, 'store']);
    $r->addRoute('GET', '/tags/edit/{id:\d+}', [TagController::class, 'edit']);
    $r->addRoute('POST', '/tags/edit/{id:\d+}', [TagController::class, 'update']);
    $r->addRoute('GET', '/tags/delete/{id:\d+}', [TagController::class, 'destroy']);
});
