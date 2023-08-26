<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImageController;
use App\Models\Image;


Route::get('/', function () {
    // ユーザーがログインしているか確認
    if (Auth::check()) {
        return redirect()->route('home'); // あなたが定義したユーザートップページへの名前付きルート
    }
    $images = Image::orderBy('download_count', 'desc')->take(7)->get();
        
    return view('auth.login', compact('images'));
});

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.top');
    Route::get('/admin/get-content/{content}', [App\Http\Controllers\AdminController::class, 'getContent']);



    Route::post('/category/store', [App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/category/destroy/{categoryId}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::put('/category/update/{categoryId}', [App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');

    Route::put('/admin/images/update-category', [App\Http\Controllers\CategoryController::class, 'updateAllCategories'])->name('images.updateAllCategories');


    Route::get('/style', [App\Http\Controllers\StyleController::class, 'index'])->name('style');
    Route::post('/style/store', [App\Http\Controllers\StyleController::class, 'store'])->name('style.store');
    Route::put('/style/update/{styleId}', [App\Http\Controllers\StyleController::class, 'update'])->name('style.update');
    Route::delete('/style/destroy/{styleId}', [App\Http\Controllers\StyleController::class, 'destroy'])->name('style.destroy');


    Route::post('/upload', [App\Http\Controllers\ImageController::class, 'upload'])->name('upload.image');
    Route::delete('/image/delete/{image}', [App\Http\Controllers\ImageController::class, 'destroyAPI'])->name('api.images.destroy');

    Route::post('/user/store', [App\Http\Controllers\UserController::class, 'store'])->name('user.store');
    Route::put('/user/edit/{userId}', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');

    Route::delete('/user/delete/{userId}', [App\Http\Controllers\UserController::class, 'destroy'])->name('user.destroy');

    Route::put('/users/{userId}/update-ban-info', [App\Http\Controllers\UserController::class, 'updateBanInfo'])->name('user.updateBanInfo');

    Route::get('/user/index', [App\Http\Controllers\UserController::class, 'index'])->name('user.index');
    
});


Auth::routes();

Route::get('/home/{categoryName?}', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home/loadImage', [App\Http\Controllers\HomeController::class, 'getImagesByCategory']);

Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact'])->name('contact');
Route::post('/submitContact', [App\Http\Controllers\HomeController::class, 'submitContact'])->name('submitContact');

Route::get('/terms', [App\Http\Controllers\HomeController::class, 'terms'])->name('terms');


Route::get('/fetch-images', [App\Http\Controllers\HomeController::class, 'fetchImages']);



Route::get('/image/{filename}', [App\Http\Controllers\ImageController::class, 'downloadImage'])->name('image.download');

Route::get('/load-more-images', [App\Http\Controllers\ImageController::class, 'loadMoreImages']);


Route::post('/increase-download-count/{filename}', [App\Http\Controllers\ImageController::class, 'incrementDownloadCount'])->name('increase.downloadCount');
