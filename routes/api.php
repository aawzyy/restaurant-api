    <?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\AuthController;
    use App\Http\Controllers\Api\MenuController;
    use App\Http\Controllers\Api\OrderController;

    // Gunakan koma untuk memisahkan Class dan Nama Function
    // Maksimal 5 kali percobaan login per menit
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/login/verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:5,1');
    Route::post('/auth/google', [AuthController::class, 'googleLogin']);
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('menus', MenuController::class);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/kitchen/orders', [OrderController::class, 'kitchenIndex']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::get('/categories', [MenuController::class, 'categories']);
        Route::get('/tables', [App\Http\Controllers\Api\TableController::class, 'index']);
        
    });
