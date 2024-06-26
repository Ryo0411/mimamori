<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\AdminController;
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

Auth::routes();

//ログイン前にアクセス可能
//ログインページの表示
Route::get('/', [AuthController::class, 'showLogin'])->name('login.show');
//ログイン処理
Route::post('login', [AuthController::class, 'login'])->name('login');

//管理者ログイン前にアクセス可能
Route::group(['prefix' => 'admin'], function () {
    // //管理者ログインページの表示
    Route::get('/', [LoginController::class, 'showAdmin'])->name('showAdmin');
    //管理者ログイン処理
    Route::post('/login', [LoginController::class, 'adminlogin'])->name('adminlogin');
});


//管理者ログイン後のみアクセス可能
Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin'], function () {
    //ログイン成功時の画面遷移(home画面遷移用)
    Route::get('/home', function () {
        return view('/admin/home');
    })->name('adminhome');
    //ログアウト処理
    Route::post('/logout', [LoginController::class, 'adminlogout'])->name('adminlogout');

    //迷子者一覧画面表示
    Route::get('/view', [AdminController::class, 'adminview'])->name('adminview');

    //迷子者一覧画面発見
    Route::get('/discover/{id}', [AdminController::class, 'discoverflg'])->name('discoverflg');

    //email送信用クラス
    Route::get('/email/{id}', [AdminController::class, 'emailflg'])->name('emailflg');
});



//ログイン後のみアクセス可能
Route::group(['middleware' => 'auth:user'], function () {
    //ログイン成功時の画面遷移(home画面遷移用)
    // Route::get('/home', function () {
    //     return view('home');
    // })->name('home');
    Route::get('/home', [AppController::class, 'homeBack'])->name('home');

    //ログアウト処理
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //迷子者ボタン選択時の画面遷移
    Route::get('/home_walk', [AppController::class, 'homeWalk'])->name('home_walk');

    Route::get('/home_walk/wanderer', [AppController::class, 'wandererFlg'])->name('wandererflg');

    Route::post('/home_walk/discover', [AppController::class, 'discoverflg'])->name('discoverflg');

    //発見者ボタン選択時の画面遷移
    Route::get('/home_discover', function () {
        return view('/host/home_discover');
    })->name('home_discover');

    //迷子者ホーム、情報登録ボタン選択時の画面遷移
    Route::get('/register_walk', [AppController::class, 'showWanderer'])->name('register_walk');

    //迷子者ホーム、声登録ボタン選択時の画面遷移
    Route::get('/voice_walk', [AppController::class, 'voiceWalk'])->name('voice_walk');

    //発見者ホーム、情報登録ボタン選択時の画面遷移
    Route::get('/register_discover', function () {
        return view('/host/register_discover');
    })->name('register_discover');

    //発見者ホーム、迷子者声掛けボタン選択時の画面遷移(ログインの有無に関係なく遷移可能)
    Route::get('/voice_discover', [AppController::class, 'voiceDiscover'])->name('voice_discover');
    Route::get('/voice_discover/select/{sex}', [AppController::class, 'voiceSelect'])->name('voice_select');

    //ユーザの登録情報更新
    Route::post('/register_discover/update', [AppController::class, 'userUpdate'])->name('userupdate');

    //迷子者の登録情報更新＆登録
    Route::post('/register_walk/update', [AppController::class, 'registerUpdate'])->name('registerupdate');

    Route::post('/voice_walk/update', [AppController::class, 'voiceUpdate'])->name('voiceupdate');

    Route::post('/speaker_rcognition', [SRSController::class, 'speakerRcognition'])->name('speaker_rcognition');
    // Route::post('/api/photo', [AppController::class, 'voiceDownload'])->name('voiceDownload');

    Route::get('/voice_list', [AppController::class, 'voiceList'])->name('voicelist');

    // user情報復元処理
    Route::get('/voice_walk/reset', [AppController::class, 'userReset'])->name('userreset');

    // Audio削除用
    Route::get('/audio/delete/{speaker_id}', [AppController::class, 'audioDelete'])->name('audioDelete');

    // マイクテスト画面
    //発見者ホーム、情報登録ボタン選択時の画面遷移
    Route::get('/voice_test', function () {
        return view('/voice_test');
    })->name('voice_test');

    //ログイン成功時の画面遷移(home画面遷移用)
    Route::get('/voice_error', function () {
        return view('voice_error');
    })->name('voice_error');

    // user情報リセット処理
    Route::get('/wanderer/reset', [AppController::class, 'wandererReset'])->name('wandererReset');
});
