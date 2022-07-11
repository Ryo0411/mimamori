<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wanderers;
use App\Http\Requests\ExeRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VoiceUpdate;
use App\Libs\MiniSRSApi;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    //徘徊者ホーム、情報登録ボタン選択時の画面遷移用メソッド
    public function adminview()
    {
        // マッチングしたユーザーの一覧を取得
        $wanderer_lists = DB::table('wanderers')
            ->where('wandering_flg', 2)
            ->get();

        // dd($wanderer_lists[0]->wanderer_id);
        // dd([$wanderer_list]);
        foreach ($wanderer_lists as $wanderer_list) {

            $wanderer_name = DB::table('users')
                ->where('id', $wanderer_list->wanderer_id)
                ->first();

            $wanderer_list->discover_name = $wanderer_name->name;
        }

        return view('admin.adminview', ['wanderer_lists' => $wanderer_lists]);
    }

    //徘徊者ホーム、情報未登録の場合ボタン非表示
    // 発見フラグの更新
    public function discoverflg($id)
    {
        $user_id = $id;
        $wanderer_list = Wanderers::whereId($user_id)->first();

        // 発見フラグが立っている場合下げる
        if ($wanderer_list['discover_flg'] == 1) {
            //ユーザ情報更新処理
            $userupdate = Wanderers::find($wanderer_list['id']);
            $userupdate->fill([
                'wandering_flg' => 0,
                'discover_flg' => 0,
            ]);
            // dd([$userupdate]);
            $userupdate->save();
            DB::commit();
            // それ以外の場合は発見フラグを立てたままにする
        } else {
            //ユーザ情報更新処理
            $userupdate = Wanderers::find($wanderer_list['id']);
            $userupdate->fill([
                'wandering_flg' => 0,
                'discover_flg' => 0,
            ]);
            $userupdate->save();
            DB::commit();
        }
        // dd([$wanderer_list]);
        return redirect('/admin/view');
    }
}
