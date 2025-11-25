<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\VendorApprovalController;
use App\Http\Controllers\Api\CustomStudyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MeController;
/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
| Aquí definimos todas las rutas de la API.
*/

/* =========================================================
|  AUTENTICACIÓN (público)
|  - Login / Logout / Registro
========================================================= */

Route::post('/login',    [AuthController::class, 'login']);
Route::post('/logout',   [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [RegisterController::class, 'store']);

// Contacto
// Contacto (público)
Route::post('/contact', [ContactController::class, 'store']);


/* =========================================================
|  PRODUCTOS (público)
|  - Listado y detalle visibles sin autenticación
========================================================= */
Route::get('/productos',      [ProductoController::class, 'index']); // listar (público)
Route::get('/productos/{id}', [ProductoController::class, 'show'])->whereNumber('id'); // detalle (público)

/* === Reseñas de producto (público) === */
Route::get('/productos/{id}/reviews', [ReviewController::class, 'index'])->whereNumber('id');

/* =========================================================
|  ZONA AUTENTICADA (cliente logueado)
|  - Perfil propio
|  - Carrito
|  - Checkout (Stripe)
|  - Pedidos del cliente
========================================================= */
Route::middleware('auth:sanctum')->group(function () {
    // ---- Perfil propio ----
    Route::get('/me', function (Request $r) {
        return $r->user()->load(['perfilCliente', 'perfilVendedor']);
    });

    // Aliases de compatibilidad
    Route::get('/users/me', function (Request $r) {
        return $r->user()->load(['perfilCliente', 'perfilVendedor']);
    });

    // ---- Perfil propio ----
    Route::get('/me', [MeController::class, 'profile']);
    Route::put('/me', [MeController::class, 'updateProfile']);
    Route::put('/me/password', [MeController::class, 'updatePassword']);

    // (Opcionales) aliases de compatibilidad, apuntando al mismo controlador
    Route::get('/users/me', [MeController::class, 'profile']);
    Route::put('/users/me', [MeController::class, 'updateProfile']);
    Route::put('/users/me/profile', [MeController::class, 'updateProfile']);
    Route::put('/users/me/password', [MeController::class, 'updatePassword']);

    // Route::put('/users/me',            [UserController::class, 'updateSelf']);
    // Route::put('/users/me/profile',    [UserController::class, 'updateSelf']);
    // Route::put('/users/me/password',   [UserController::class, 'changePassword']);

    // // Endpoints /me directos
    // Route::put('/me',                  [UserController::class, 'updateSelf']);
    // Route::put('/me/password',         [UserController::class, 'changePassword']);

    // ---- Carrito ----
    Route::get('/cart',          [CartController::class, 'show']);
    Route::post('/cart/add',     [CartController::class, 'add']);
    Route::post('/cart/update',  [CartController::class, 'updateQty']);
    Route::post('/cart/remove',  [CartController::class, 'remove']);
    Route::post('/cart/clear',   [CartController::class, 'clear']);
    Route::post('/cart/sync',    [CartController::class, 'sync']);

    // ----- notificaciones -----
    Route::get('/notifications', function (Request $r) {
        return $r->user()->notifications()->latest()->limit(50)->get();
    });
    Route::post('/notifications/{id}/read', function (Request $r, string $id) {
        $n = $r->user()->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();
        return ['ok' => true];
    });

    // ---- Reseñas (auth) ----
    Route::post('/productos/{id}/reviews', [ReviewController::class, 'storeOrUpdate'])->whereNumber('id'); // crear/editar reseña propia
    Route::post('/reviews/{id}/react',     [ReviewController::class, 'react'])->whereNumber('id');  // like/dislike
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->whereNumber('review');
    // ---- Checkout (Stripe) ----
    Route::post('/checkout/sessions', [StripeController::class, 'createCheckoutSession']);
    //_ --- estudio personalizado ----
    Route::post('/study/request', [CustomStudyController::class, 'requestStudy']);
    // ---- Pedidos del cliente ----
    Route::get('/orders/by-session/{session}', [OrderController::class, 'showBySession']); // usado en pantalla de éxito
    Route::get('/orders/mine',                  [OrderController::class, 'index']);
    Route::get('/orders/{order}/invoice',       [OrderController::class, 'invoiceHtml']);
    Route::get('/orders/{order}/invoice.pdf',   [OrderController::class, 'invoicePdf']);
});

/* =========================================================
|  ZONA ADMIN (requiere auth + rol admin)
|  - Gestión de usuarios
|  - CRUD de productos (crear/editar/eliminar)
|  * OJO: no re-declaramos GET /productos para no pisar las públicas
========================================================= */
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureRole::class])->group(function () {
    // Usuarios
    Route::get('/users',         [UserController::class, 'index']);
    Route::post('/users',        [UserController::class, 'store']);   // crear
    Route::get('/vendedores',    [UserController::class, 'vendedores']);
    Route::put('/users/{id}',    [UserController::class, 'update'])->whereNumber('id');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->whereNumber('id');

    // Productos (solo admin)
    Route::post('/productos',              [ProductoController::class, 'store']);
    Route::put('/productos/{producto}',    [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);
    // ==== Imágenes múltiples de producto (admin) ====
    Route::get('/productos/{producto}/images', [ProductoController::class, 'listImages'])->whereNumber('producto');
    Route::post('/productos/{producto}/images', [ProductoController::class, 'uploadImages'])->whereNumber('producto');
    Route::post('/productos/{producto}/images/reorder', [ProductoController::class, 'reorderImages'])->whereNumber('producto');
    Route::delete('/productos/images/{image}', [ProductoController::class, 'deleteImage'])->whereNumber('image');

    // ==== aprobar o rechazar solicitudes de vendedor (admin) ====
    Route::get('/vendors/requests', [UserController::class, 'vendorRequests']);
    Route::post('/vendors/{user}/approve', [VendorApprovalController::class, 'approve']);
    Route::post('/vendors/{user}/reject',  [VendorApprovalController::class, 'reject']);
});

/* =========================================================
|  WEBHOOKS STRIPE (público)
|  - Endpoint para eventos de Stripe (checkout.session.completed, etc.)
|  - Test simple para comprobar disponibilidad
========================================================= */
Route::post('/stripe/webhook',      [StripeController::class, 'webhook']); // sin auth
Route::post('/stripe/webhook/test', fn() => response('OK', 200));
