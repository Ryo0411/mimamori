<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function getMiniSRSApi(): MiniSRSApi
    {
        return new MiniSRSApi($this->getApplicationId(), $this->getClientId(), $this->getClientSecret());
    }

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
        if ($wanderer_list == null) {
            $wanderer_list["profile_id"] = "";
            $wanderer_list["wanderer_name"] = "";
        }

        return view('voice_discover', ['wanderer_list' => $wanderer_list]);
    }

    // 徘徊者声掛けページにて性別を選択したときユーザを選定する処理。
    public function voiceSelect($sex)
    {
        $wanderer_list = Wanderers::whereSex($sex)->where('wandering_flg', '!=', 0)->get();

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
            $exe = "";
            // dd("ssss");
        } else {
            $status = "";
            $user_id = Auth::user()->id;
            $wanderer_list = Wanderers::whereUser_id($user_id)->first();
            // 音声登録があるかを判定。ない場合はそのままreturn
            if ($wanderer_list['voiceprint_flg'] >= 0) {
                // 徘徊フラグが立っていない場合、徘徊フラグを立てる
                if ($wanderer_list['wandering_flg'] == 0) {
                    $exe = "捜索対象外です。";
                } elseif ($wanderer_list['wandering_flg'] == 1) {
                    $exe = "捜索対象に選択中です。";
                } else {
                    $exe = "発見されました！";
                }
            } else {
                $exe = "";
            }
        }

        // dd([$status]);
        return view('home_walk')->with(['status' => $status, 'exe' => $exe]);
    }
    //徘徊者ホーム、情報未登録の場合ボタン非表示
    public function wandererFlg()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = Wanderers::whereUser_id($user_id)->first();
        $id = Auth::user()->id;
        // 音声登録があるかを判定。ない場合はそのままreturn
        if ($wanderer_list['voiceprint_flg'] >= 0) {
            // 徘徊フラグが立っていない場合、徘徊フラグを立てる
            if ($wanderer_list['wandering_flg'] == 0) {
                try {
                    //ユーザ情報更新処理
                    $userupdate = Wanderers::find($wanderer_list['id']);
                    $userupdate->fill([
                        'wandering_flg' => 1,
                    ]);
                    $status = "";
                    $exe = "捜索対象に選択中です。";
                    $userupdate->save();
                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollback();
                    Log::info($e->getMessage());
                    abort(500);
                };
                // 徘徊フラグが立っている場合、徘徊フラグを下げる
            } else {
                try {
                    //ユーザ情報更新処理
                    $userupdate = Wanderers::find($wanderer_list['id']);
                    $userupdate->fill([
                        'wandering_flg' => 0,
                    ]);
                    $status = "";
                    $exe = "捜索対象外です。";
                    $userupdate->save();
                    DB::commit();
                } catch (\Throwable $e) {
                    // dd($e);
                    DB::rollback();
                    Log::info($e->getMessage());
                    abort(500);
                };
            }
        } else {
            $status = "";
            $exe = "";
            return view('home_walk')->with(['status' => $status, 'exe' => $exe]);
        }

        // dd([$status]);
        return view('home_walk')->with(['status' => $status, 'exe' => $exe]);
    }

    //ユーザ情報更新処理
    public function userUpdate(ExeRequest $request)
    {
        //データの受け取り。
        $inputs = $request->all();
        DB::beginTransaction();
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
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
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
        DB::beginTransaction();
        try {
            $wname = $inputs['wanderer_name'];
            $sex = $inputs['sex'];
            $age = $inputs['age'];
            $etel = $inputs['emergency_tel'];
            $vflg = $inputs['voiceprint_flg'];
            $rawfile = $inputs['audio_file'];
            $mini_sex = intval($sex) - 1;
            if ($mini_sex < 0) {
                $mini_sex = 0;
            }

            //ユーザ情報更新処理
            if (empty($user_id)) {
                $speakerId = $this->addSpeaker($wname, $mini_sex, $age, $rawfile);
                $vflg = intval($vflg) + 1;
                //新規登録
                Wanderers::create([
                    'user_id' => $id,
                    'sex' => $sex,
                    'age' => $age,
                    'wanderer_name' => $wname,
                    'emergency_tel' => $etel,
                    'profile_id' => $speakerId,
                    'voiceprint_flg' => $vflg,
                ]);
                DB::commit();
            } else {
                $userupdate = Wanderers::find($user_id['id']);
                if ($userupdate['wanderer_name'] != $wname
                    || $userupdate['sex'] != $sex || $userupdate['age'] != $age) {
                    $this->editSpeaker($userupdate['profile_id'], $wname, $mini_sex, $age);
                }
                $userupdate->fill([
                    'sex' => $sex,
                    'age' => $age,
                    'wanderer_name' => $wname,
                    'emergency_tel' => $etel,
                    'voiceprint_flg' => $vflg,
                ]);
                $userupdate->save();
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            abort(500);
        };
        return redirect()->route('register_walk')->with('exe_msg', '登録情報を更新しました！');
    }

    private function addSpeaker(string $name, int $sex, int $age, string $rawfile): string
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $result = $miniSRSApi->addSpeaker($name, $sex, $age);
        $speakerId = '';
        if ($result) {
            $speakerId = $result['id'];
        } else {
            throw new \Exception('Speaker registration failed!');
        }
        $result = $miniSRSApi->addSpeakerToGroup($speakerId, $this->getGroupId());
        if (!$result) {
            throw new \Exception('Speaker group registration failed!');
        }
        $result = $miniSRSApi->addSpeech($speakerId, $rawfile);
        if (!$result) {
            throw new \Exception('Speech registration failed!');
        }
        return $speakerId;
    }

    private function addSpeech($speakerId, $rawfile)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $result = $miniSRSApi->addSpeech($speakerId, $rawfile);
        if (!$result) {
            throw new \Exception('Speech registration failed!');
        }
    }

    private function editSpeaker(string $speakerId, string $name, int $sex, int $age)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $values = [
            'name' => $name,
            'sex' => $sex,
            'age' => $age,
        ];
        $result = $miniSRSApi->editSpeaker($speakerId, $values);
        if (!$result) {
            throw new \Exception('Speaker editing failed!');
        }
    }

    // 追加音声データ登録時の処理
    public function voiceUpdate(VoiceUpdate $request)
    {
        //データの受け取り。
        $inputs = $request->all();
        $id = Auth::user()->id;
        $user_id = Wanderers::whereUser_id($id)->first();
        DB::beginTransaction();
        try {
            $rawfile = $inputs['audio_file'];
            $vflg = intval($inputs['voiceprint_flg']);
            $speakerId = $inputs['profile_id'];
            $this->addSpeech($speakerId, $rawfile);
            $vflg++;

            //ユーザ情報更新処理
            $userupdate = Wanderers::find($user_id['id']);
            $userupdate->fill([
                // 'profile_id' => $inputs['profile_id'],
                'voiceprint_flg' => $vflg,
            ]);
            $userupdate->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            abort(500);
        };
        return redirect()->route('voice_walk')->with('exe_msg', '音声の追加学習を行いました！');
    }
}
