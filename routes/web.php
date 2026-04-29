<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\DokumenJemaahController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\JemaahController;
use App\Http\Controllers\KeberangkatanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MaskapaiController;
use App\Http\Controllers\PaketUmrahController;
use App\Http\Controllers\TourLeaderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// landing page
Route::get('/', function () {return view('landing-page');});

// paket list
Route::get('/paket', function () {return view('paket');});
Route::get('/paket/all', [LandingPageController::class, 'getAllPaket']);
Route::get('/paket/home', [LandingPageController::class,'getHomePaket']);
Route::get('/paket/detail/{id}', [LandingPageController::class,'detail']);

// register
Route::get('/register', function () {
    return view('register');
});
Route::post('/actionregister', [LoginController::class, 'register']);

// login
Route::get('/login', [LoginController::class, 'showlogin'])->name('showlogin')->middleware('guest');
Route::post('/actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin')->middleware('guest');

Route::middleware('auth')->group(function(){

    // dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // logout
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');

    // profil
    Route::get('/profile', fn() => view('home.profil.index'));
    Route::post('/profile/update', [JemaahController::class, 'updateProfile']);

    // user
    Route::get('/user', [UserController::class,'index']);
    Route::post('/user/data', [UserController::class,'data'])->name('user');
    Route::post('/user/store', [UserController::class,'store']);
    Route::post('/user/update/{id}', [UserController::class,'update']);
    Route::delete('/user/delete/{id}', [UserController::class,'destroy']);

    // hotel
    Route::get('/hotel', [HotelController::class,'index']);
    Route::post('/hotel/data', [HotelController::class,'data']);
    Route::post('/hotel/store', [HotelController::class,'store']);
    Route::post('/hotel/update/{id}', [HotelController::class,'update']);
    Route::delete('/hotel/delete/{id}', [HotelController::class,'destroy']);

    // paket umrah
    Route::get('/paket-umrah', [PaketUmrahController::class,'index']);
    Route::post('/paket-umrah/data', [PaketUmrahController::class,'data']);
    Route::post('/paket-umrah/store', [PaketUmrahController::class,'store']);
    Route::post('/paket-umrah/update/{id}', [PaketUmrahController::class,'update']);
    Route::delete('/paket-umrah/delete/{id}', [PaketUmrahController::class,'destroy']);

        // fasilitas & program paket umrah
        Route::get('/paket-umrah/fasilitas/{id}',[PaketUmrahController::class,'getFasilitas']);
        Route::get('/paket-umrah/program/{id}',[PaketUmrahController::class,'getProgram']);
        Route::post('/paket-umrah/fasilitas/store',[PaketUmrahController::class,'storeFasilitas']);
        Route::post('/paket-umrah/program/store',[PaketUmrahController::class,'storeProgram']);

    // maskapai
    Route::get('/maskapai',[MaskapaiController::class,'index']);
    Route::post('/maskapai/data',[MaskapaiController::class,'data']);
    Route::post('/maskapai/store',[MaskapaiController::class,'store']);
    Route::post('/maskapai/update/{id}',[MaskapaiController::class,'update']);
    Route::delete('/maskapai/delete/{id}',[MaskapaiController::class,'destroy']);

    // toor leader
    Route::get('/tour-leader',[TourLeaderController::class,'index']);
    Route::post('/tour-leader/data',[TourLeaderController::class,'data']);
    Route::post('/tour-leader/store',[TourLeaderController::class,'store']);
    Route::post('/tour-leader/update/{id}',[TourLeaderController::class,'update']);
    Route::delete('/tour-leader/delete/{id}',[TourLeaderController::class,'destroy']);

    // keberangkatan
    Route::prefix('keberangkatan')->group(function(){

        Route::get('/',[KeberangkatanController::class,'index']);
        Route::get('/list',[KeberangkatanController::class,'list']);

        Route::get('/form-data',[KeberangkatanController::class,'getFormData']);
        Route::post('/store',[KeberangkatanController::class,'store']);

        Route::get('/detail/{id}',[KeberangkatanController::class,'detail']);
        Route::get('/detail/data/{id}',[KeberangkatanController::class,'detail_data']);

        Route::post('/jemaah/data',[KeberangkatanController::class,'jemaah']);

        Route::post('/update-status',[KeberangkatanController::class,'updateStatus']);

        Route::delete('/delete/{id}',[KeberangkatanController::class,'delete']);

    });

    // Jemaah Management
    Route::prefix('jemaah')->group(function () {

        Route::get('/', [JemaahController::class,'index']);
        Route::post('/data', [JemaahController::class,'data']);

        Route::post('/store', [JemaahController::class,'store']);
        Route::post('/update/{id}', [JemaahController::class,'update']);
        Route::delete('/delete/{id}', [JemaahController::class,'destroy']);

        Route::post('/toggle/{id}', [JemaahController::class,'toggleStatus']);
        Route::get('/detail/{id}', [JemaahController::class,'detail']);

    });

    // upload dokumen
    Route::get('/dokumen', [DokumenJemaahController::class, 'indexDokumen']);
    Route::post('/dokumen/upload', [DokumenJemaahController::class, 'uploadDokumen']);

    // verifikasi dokumen
    Route::prefix('admin')->group(function(){

        Route::get('/dokumen', [DokumenJemaahController::class,'index']);
        Route::post('/dokumen/data', [DokumenJemaahController::class,'data']);

        Route::get('/dokumen/{id}', [DokumenJemaahController::class,'show']);
        Route::post('/dokumen/{id}/approve', [DokumenJemaahController::class,'approve']);
        Route::post('/dokumen/{id}/reject', [DokumenJemaahController::class,'reject']);

    });
});
