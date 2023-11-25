<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

use function Laravel\Prompts\alert;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/prod', function () {
//     return view('product');
// });



Route::get('cancel', function () {
    alert('Order canceled');
    return Redirect::to('/')->with('status', 'Order canceled');
});
// Route::get('orders', [ProductController::class, 'orders'])->name('orders');




Route::get('/', [ProductController::class, 'index'])->name('/');
Route::get('/p/{id}', [ProductController::class, 'indexById']);



Route::get('/dashboard', [ProductController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('atc', [ProductController::class, 'addToCart'])->name('atc');
    Route::get('/cart', [ProductController::class, 'getAddToCart'])->name('cart');
    Route::delete('deca/{id}', [ProductController::class, 'deleteCart'])->name('deca');
    Route::get('/order', [ProductController::class, 'getOrder'])->name('order');
    Route::post('buy', [StripeController::class, 'buyProduct'])->name('buy');
    Route::post('buym', [StripeController::class, 'buyManyProduct'])->name('buym');
    Route::get('/count', [ProductController::class, 'cartCount'])->name('/count');
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('create', function () {
        return view('create-product');
    })->name('create');

    Route::post('create', [ProductController::class, 'store']);
    Route::put('update/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('delete/{id}', [ProductController::class, 'delete'])->name('delete');
    Route::get('/update/{id}', [ProductController::class, 'indexById2']);
    Route::patch('/status', [ProductController::class, 'changeStatus'])->name('status');
});

require __DIR__ . '/auth.php';
