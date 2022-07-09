<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /**
     * @return View
     */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'admin/home'; // 変更

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin')->except('adminlogout'); //変更
    }

    public function showAdmin()
    {
        return view('login.admin_form');  //変更
    }

    // protected function guard()
    // {
    //     return Auth::guard('admin');  //変更
    // }

    public function adminlogout(Request $request)
    {
        Auth::guard('admin')->logout();  //変更
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin')->with('logout', 'ログアウトしました！');  //変更
    }
    // public function adminlogout(Request $request)
    // {
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return redirect()->route('showAdmin')->with('logout', 'ログアウトしました！');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    // public function adminlogin()
    // {
    //     return view('admin.home');  //変更
    // }
    public function adminlogin(LoginFormRequest $request)
    {
        $credentials = $request->only('name', 'password');

        //ログイン承認
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            //ログイン成功時画面遷移
            // return view('admin.home');  //変更
            return redirect()->route('adminhome')->with('login_success', 'ログインが成功しました！');
        }

        //ログイン承認失敗時
        return back()->withErrors([
            //ログイン承認エラー時の内容
            'email' => '名前かパスワードが間違っています。',
        ]);
    }
    // public function adminlogin(LoginFormRequest $request)
    // {
    //     $credentials = $request->only('name', 'password');

    //     //ログイン承認
    //     if (Auth::attempt($credentials)) {
    //         $request->session()->regenerate();
    //         //ログイン成功時画面遷移
    //         return view('admin.home');  //変更
    //     }

    //     //ログイン承認失敗時
    //     return back()->withErrors([
    //         //ログイン承認エラー時の内容
    //         'email' => '名前かパスワードが間違っています。',
    //     ]);
    // }
    // public function showAdmin()
    // {
    //     return view('login.admin_form');
    // }

    // public function adminhome()
    // {
    //     return view('admin.home');
    // }
    // /**
    //  * 管理者ユーザーをアプリケーションにログインさせる
    //  * @param App\Http\Requests\LoginFormRequest
    //  */
    // public function adminlogin(LoginFormRequest $request)
    // {
    //     $credentials = $request->only('name', 'password');

    //     //ログイン承認
    //     if (Auth::attempt($credentials)) {
    //         $request->session()->regenerate();
    //         //ログイン成功時画面遷移
    //         return redirect()->route('adminhome')->with('login_success', 'ログインが成功しました！');
    //     }

    //     //ログイン承認失敗時
    //     return back()->withErrors([
    //         //ログイン承認エラー時の内容
    //         'email' => '名前かパスワードが間違っています。',
    //     ]);
    // }

    // /**
    //  * ユーザーをアプリケーションからログアウトさせる
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function adminlogout(Request $request)
    // {
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return redirect()->route('showAdmin')->with('logout', 'ログアウトしました！');
    // }
}
