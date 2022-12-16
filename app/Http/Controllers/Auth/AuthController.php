<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @return View
     */

    protected $redirectTo = 'home';

    public function __construct()
    {
        $this->middleware('guest:user')->except('logout'); //変更
    }

    public function showLogin()
    {
        return view('login.login_form');
    }

    /**
     * ユーザーをアプリケーションにログインさせる
     * @param App\Http\Requests\LoginFormRequest
     */
    public function login(LoginFormRequest $request)
    {

        $credentials = $request->only('name', 'password');

        //ログイン承認
        if (Auth::guard('user')->attempt($credentials)) {
            $request->session()->regenerate();

            //ログイン成功時画面遷移
            return redirect()->route('home')->with('login_success', 'ログインが成功しました！');
        }

        //ログイン承認失敗時
        return back()->withErrors([
            //ログイン承認エラー時の内容
            'email' => '名前かパスワードが間違っています。',
        ]);
    }

    /**
     * ユーザーをアプリケーションからログアウトさせる
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();  //変更
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('logout', 'ログアウトしました！');  //変更
    }
}
