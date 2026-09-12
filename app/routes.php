<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\BuildingController;
use App\Controllers\DashboardController;
use App\Controllers\DocumentController;
use App\Controllers\InvoiceController;
use App\Controllers\LeaseController;
use App\Controllers\MaintenanceController;
use App\Controllers\NotificationController;
use App\Controllers\TenantController;
use App\Controllers\UnitController;
use App\Core\Router;

/** @var Router $router */

$router->group(['guest'], function (Router $r) {
    $r->get('/login', [AuthController::class, 'showLogin']);
    $r->post('/login', [AuthController::class, 'login']);
});

$router->post('/logout', [AuthController::class, 'logout']);

$router->group(['auth'], function (Router $r) {
    $r->get('/', [DashboardController::class, 'index']);
    $r->get('/dashboard', [DashboardController::class, 'index']);

    $r->get('/buildings', [BuildingController::class, 'index']);
    $r->get('/buildings/create', [BuildingController::class, 'create']);
    $r->post('/buildings', [BuildingController::class, 'store']);
    $r->get('/buildings/{id}', [BuildingController::class, 'show']);
    $r->get('/buildings/{id}/edit', [BuildingController::class, 'edit']);
    $r->put('/buildings/{id}', [BuildingController::class, 'update']);
    $r->delete('/buildings/{id}', [BuildingController::class, 'destroy']);
    $r->post('/buildings/{id}/floors', [BuildingController::class, 'storeFloor']);
    $r->get('/api/buildings/{id}/floors', [UnitController::class, 'floorsForBuilding']);

    $r->get('/units', [UnitController::class, 'index']);
    $r->get('/units/create', [UnitController::class, 'create']);
    $r->post('/units', [UnitController::class, 'store']);
    $r->get('/units/{id}', [UnitController::class, 'show']);
    $r->get('/units/{id}/edit', [UnitController::class, 'edit']);
    $r->put('/units/{id}', [UnitController::class, 'update']);
    $r->delete('/units/{id}', [UnitController::class, 'destroy']);
    $r->post('/units/{id}/photos', [UnitController::class, 'storePhoto']);
    $r->delete('/units/photos/{id}', [UnitController::class, 'destroyPhoto']);

    $r->get('/tenants', [TenantController::class, 'index']);
    $r->get('/tenants/create', [TenantController::class, 'create']);
    $r->post('/tenants', [TenantController::class, 'store']);
    $r->get('/tenants/{id}', [TenantController::class, 'show']);
    $r->get('/tenants/{id}/edit', [TenantController::class, 'edit']);
    $r->put('/tenants/{id}', [TenantController::class, 'update']);
    $r->delete('/tenants/{id}', [TenantController::class, 'destroy']);

    $r->get('/leases', [LeaseController::class, 'index']);
    $r->get('/leases/create', [LeaseController::class, 'create']);
    $r->post('/leases', [LeaseController::class, 'store']);
    $r->get('/leases/{id}', [LeaseController::class, 'show']);
    $r->post('/leases/{id}/terminate', [LeaseController::class, 'terminate']);
    $r->post('/leases/{id}/documents', [LeaseController::class, 'storeDocument']);
    $r->delete('/leases/documents/{id}', [LeaseController::class, 'destroyDocument']);

    $r->get('/invoices', [InvoiceController::class, 'index']);
    $r->get('/invoices/create', [InvoiceController::class, 'create']);
    $r->post('/invoices', [InvoiceController::class, 'store']);
    $r->get('/invoices/{id}', [InvoiceController::class, 'show']);
    $r->post('/invoices/{id}/payments', [InvoiceController::class, 'storePayment']);
    $r->delete('/invoices/{id}', [InvoiceController::class, 'destroy']);

    $r->get('/maintenance', [MaintenanceController::class, 'index']);
    $r->get('/maintenance/create', [MaintenanceController::class, 'create']);
    $r->post('/maintenance', [MaintenanceController::class, 'store']);
    $r->get('/maintenance/{id}', [MaintenanceController::class, 'show']);
    $r->put('/maintenance/{id}', [MaintenanceController::class, 'updateStatus']);
    $r->post('/maintenance/{id}/updates', [MaintenanceController::class, 'storeNote']);

    $r->get('/documents', [DocumentController::class, 'index']);
    $r->post('/documents', [DocumentController::class, 'store']);
    $r->delete('/documents/{id}', [DocumentController::class, 'destroy']);

    $r->get('/notifications', [NotificationController::class, 'index']);
    $r->get('/notifications/create', [NotificationController::class, 'create']);
    $r->post('/notifications', [NotificationController::class, 'store']);
    $r->get('/notifications/{id}', [NotificationController::class, 'show']);
    $r->put('/notifications/{id}', [NotificationController::class, 'updateStatus']);
});
