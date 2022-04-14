<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wanderers;
use App\Http\Requests\ExeRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VoiceUpdate;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //徘徊者ホーム、情報登録ボタン選択時の画面遷移用メソッド
    public function showWanderer()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = DB::table('wanderers')
            ->where('user_id', $user_id)
            ->first();
        // dd([$wanderer_list]);

        return view('register_walk', ['wanderer_list' => $wanderer_list]);
    }
    public function voiceWalk()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = DB::table('wanderers')
            ->where('user_id', $user_id)
            ->first();
        // dd([$wanderer_list]);

        return view('voice_walk', ['wanderer_list' => $wanderer_list]);
    }

    // 音声認識をするページへ遷移
    public function voiceDiscover()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = DB::table('wanderers')
            ->where('user_id', $user_id)
            ->first();
        // dd([$wanderer_list]);

        return view('voice_discover', ['wanderer_list' => $wanderer_list]);
    }

    // 徘徊者声掛けページにて性別を選択したときユーザを選定する処理。
    public function voiceSelect($sex)
    {
        $wanderer_list = Wanderers::whereSex($sex)->where('wandering_flg', '=', 1)->get();

        // dd($wanderer_list->profile_id);

        if (empty($wanderer_list)) {
            return view('voice_discover');
        }

        return view('voice_discover', ['wanderer_list' => $wanderer_list]);
    }

    //徘徊者ホーム、情報未登録の場合ボタン非表示
    public function homeWalk()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = DB::table('wanderers')
            ->where('user_id', $user_id)
            ->first();
        if (empty($wanderer_list)) {
            $status = "hidden";
            // dd("ssss");
        } else {
            $status = "";
        }

        // dd([$status]);
        return view('home_walk')->with('status', $status);
    }
    //徘徊者ホーム、情報未登録の場合ボタン非表示
    public function wandererFlg()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = Wanderers::whereUser_id($user_id)->first();
        $id = Auth::user()->id;
        // dd($wanderer_list['voiceprint_flg']);
        if ($wanderer_list['voiceprint_flg'] >= 0) {
            if ($wanderer_list['wandering_flg'] == 0) {
                try {
                    //ユーザ情報更新処理
                    $userupdate = Wanderers::find($wanderer_list['id']);
                    $userupdate->fill([
                        'wandering_flg' => 1,
                    ]);
                    $status = "";
                    $userupdate->save();
                    \DB::commit();
                } catch (\Throwable $e) {
                    \DB::rollback();
                    abort(500);
                };
            } else {
                try {
                    //ユーザ情報更新処理
                    $userupdate = Wanderers::find($wanderer_list['id']);
                    $userupdate->fill([
                        'wandering_flg' => 0,
                    ]);
                    $status = "";
                    $userupdate->save();
                    \DB::commit();
                } catch (\Throwable $e) {
                    dd($e);
                    \DB::rollback();
                    abort(500);
                };
            }
        } else {
            $status = "";
            return view('home_walk')->with('status', $status);
        }

        // dd([$status]);
        return view('home_walk')->with('status', $status);
    }

    //ユーザ情報更新処理
    public function userUpdate(ExeRequest $request)
    {
        //データの受け取り。
        $inputs = $request->all();
        \DB::beginTransaction();
        try {
            //ユーザ情報更新処理
            $user_id = Auth::user()->id;
            $userupdate = User::find($user_id);
            $userupdate->fill([
                'sex' => $inputs['sex'],
                'age' => $inputs['age'],
                'name' => $inputs['name'],
            ]);
            $userupdate->save();
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollback();
            abort(500);
        };
        // dd([$status]);
        return redirect()->route('register_discover')->with('exe_msg', '登録情報を更新しました！');
    }

    //ユーザ情報更新処理
    public function registerUpdate(RegisterRequest $request)
    {
        //データの受け取り。
        $inputs = $request->all();
        $id = Auth::user()->id;
        $user_id = Wanderers::whereUser_id($id)->first();
        \DB::beginTransaction();
        try {
            //ユーザ情報更新処理
            if (empty($user_id)) {
                //新規登録
                Wanderers::create([
                    'user_id' => $id,
                    'sex' => $inputs['sex'],
                    'age' => $inputs['age'],
                    'wanderer_name' => $inputs['wanderer_name'],
                    'emergency_tel' => $inputs['emergency_tel'],
                    'profile_id' => $inputs['profile_id'],
                    'voiceprint_flg' => $inputs['voiceprint_flg'],
                ]);
                \DB::commit();
            } else {
                $userupdate = Wanderers::find($user_id['id']);
                $userupdate->fill([
                    'sex' => $inputs['sex'],
                    'age' => $inputs['age'],
                    'wanderer_name' => $inputs['wanderer_name'],
                    'emergency_tel' => $inputs['emergency_tel'],
                    'profile_id' => $inputs['profile_id'],
                    'voiceprint_flg' => $inputs['voiceprint_flg'],
                ]);
                $userupdate->save();
                \DB::commit();
            }
        } catch (\Throwable $e) {
            \DB::rollback();
            abort(500);
        };
        return redirect()->route('register_walk')->with('exe_msg', '登録情報を更新しました！');
    }

    // 追加音声データ登録時の処理
    public function voiceUpdate(VoiceUpdate $request)
    {
        //データの受け取り。
        $inputs = $request->all();
        $id = Auth::user()->id;
        $user_id = Wanderers::whereUser_id($id)->first();
        \DB::beginTransaction();
        // 音声登録をしてカウントが上がった時のみ更新したと表示させる。
        if ($user_id['voiceprint_flg'] == intval($inputs['voiceprint_flg'])) {
            $status = "";
        } else {
            $status = "登録情報を更新しました！";
        }
        try {
            //ユーザ情報更新処理
            $userupdate = Wanderers::find($user_id['id']);
            $userupdate->fill([
                'profile_id' => $inputs['profile_id'],
                'voiceprint_flg' => $inputs['voiceprint_flg'],
            ]);
            $userupdate->save();
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollback();
            abort(500);
        };
        return redirect()->route('voice_walk')->with('exe_msg', $status);
    }
}
