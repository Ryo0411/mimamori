<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\SRSController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//ログイン前にアクセス可能
Route::group(['middleware' => ['guest']], function () {
    //トップページの表示
    // Route::get('/sinin', function() {
    //     return view('/login/login_form');
    // })->name('index');

    //ゲスト発見者の表示
    // Route::get('/guest_discover', function() {
    //     return view('/guest/guest_discover');
    // })->name('guest_discover');

    //ログインページの表示
    Route::get('/', [AuthController::class, 'showLogin'])->name('login.show');

    //ログイン処理
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

//ログイン後のみアクセス可能
Route::group(['middleware' => ['auth']], function () {
    //ログイン成功時の画面遷移(home画面遷移用)
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    //ログアウト処理
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //徘徊者ボタン選択時の画面遷移
    // Route::get('/home_walk', function() {
    //     return view('home_walk');
    // })->name('home_walk');
    Route::get('/home_walk', [AppController::class, 'homeWalk'])->name('home_walk');

    Route::get('/home_walk/wanderer', [AppController::class, 'wandererFlg'])->name('wandererflg');

    //発見者ボタン選択時の画面遷移
    Route::get('/home_discover', function () {
        return view('/host/home_discover');
    })->name('home_discover');

    //徘徊者ホーム、情報登録ボタン選択時の画面遷移
    Route::get('/register_walk', [AppController::class, 'showWanderer'])->name('register_walk');

    //徘徊者ホーム、声登録ボタン選択時の画面遷移
    Route::get('/voice_walk', [AppController::class, 'voiceWalk'])->name('voice_walk');

    //発見者ホーム、情報登録ボタン選択時の画面遷移
    Route::get('/register_discover', function () {
        return view('/host/register_discover');
    })->name('register_discover');

    //発見者ホーム、徘徊者声掛けボタン選択時の画面遷移(ログインの有無に関係なく遷移可能)
    Route::get('/voice_discover', [AppController::class, 'voiceDiscover'])->name('voice_discover');
    Route::get('/voice_discover/select/{sex}', [AppController::class, 'voiceSelect'])->name('voice_select');

    //ユーザの登録情報更新
    Route::post('/register_discover/update', [AppController::class, 'userUpdate'])->name('userupdate');

    //徘徊者の登録情報更新＆登録
    Route::post('/register_walk/update', [AppController::class, 'registerUpdate'])->name('registerupdate');

    Route::post('/voice_walk/update', [AppController::class, 'voiceUpdate'])->name('voiceupdate');

    //
    Route::post('/speaker_rcognition', [SRSController::class, 'speakerRcognition'])->name('speaker_rcognition');
});
