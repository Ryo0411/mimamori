<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wanderers;
use App\Models\Voicelist;
use App\Http\Requests\ExeRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VoiceUpdate;
use App\Libs\MiniSRSApi;
use App\Libs\DbVoicelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Storage;
use App\Mail\Confirmmail;
use Illuminate\Support\Facades\Mail;

class AppController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function getMiniSRSApi(): MiniSRSApi
    {
        return new MiniSRSApi($this->getApplicationId(), $this->getClientId(), $this->getClientSecret());
    }

    private function getDbVoicelist(): DbVoicelist
    {
        return new DbVoicelist($this->getApplicationId(), $this->getClientId(), $this->getClientSecret());
    }

    //徘徊者ホーム、情報未登録の場合ボタン非表示
    public function homeBack()
    {
        try {
            $user_id = Auth::user()->id;
            $wanderer_list = DB::table('wanderers')
                ->where('user_id', $user_id)
                ->first();
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
                $exe = "情報登録をして下さい。";
            }
            return view('home', ['exe' => $exe]);
        } catch (\Throwable $e) {
            return view('home', ['exe' => "ご家族情報を登録して下さい。"]);
        };
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
        $speakerId = $wanderer_list->profile_id;
        $voiceLength = $this->getVoiceLength($speakerId);

        return view('voice_walk', ['wanderer_list' => $wanderer_list], ['voice_length' => $voiceLength]);
    }

    protected function getVoiceLength(string $speakerId)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $speeches = $miniSRSApi->getSpeeches($speakerId);
        $length = 0;
        if (empty($speeches)) {
            $length = 0;
        } else {
            foreach ($speeches as $speech) {
                $length += $speech['length'];
            }
        }
        return $length;
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
            $exe = "情報登録をして下さい。";
            $discoverflg = 0;
            // dd("ssss");
        } else {
            $status = "";
            $user_id = Auth::user()->id;
            $wanderer_list = Wanderers::whereUser_id($user_id)->first();
            // 音声登録があるかを判定。ない場合はそのままreturn
            if ($wanderer_list['voiceprint_flg'] >= 0) {
                //ユーザ情報更新処理
                $userupdate = Wanderers::find($wanderer_list['id']);
                // 徘徊フラグが立っていない場合、徘徊フラグを立てる
                if ($wanderer_list['wandering_flg'] == 0) {
                    $exe = "捜索対象外です。";
                    $discoverflg = $userupdate["discover_flg"];
                } elseif ($wanderer_list['wandering_flg'] == 1) {
                    $exe = "捜索対象に選択中です。";
                    $discoverflg = $userupdate["discover_flg"];
                } else {
                    $exe = "発見されました！";
                    $discoverflg = $userupdate["discover_flg"];
                }
            } else {
                $exe = "情報登録をして下さい。";
                $discoverflg = 0;
            }
        }

        // dd([$status]);
        return view('home_walk')->with(['status' => $status, 'exe' => $exe, 'discoverflg' => $discoverflg]);
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
                    // 認識用グループに対象者を入れる処理
                    $this->addSpeechGroupId($wanderer_list['profile_id']);

                    //ユーザ情報更新処理
                    $userupdate = Wanderers::find($wanderer_list['id']);
                    Log::info("捜索対象に選択処理、対象者");
                    Log::info($userupdate);
                    $userupdate->fill([
                        'wandering_flg' => 1,
                    ]);
                    $status = "";
                    $exe = "捜索対象に選択中です。";
                    $discoverflg = $userupdate["discover_flg"];
                    $userupdate->save();
                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollback();
                    Log::info($e->getMessage());
                    abort(500);
                };
                // 徘徊フラグが立っている場合、徘徊フラグを下げる
            } else {
                // 認識用グループから話者を削除
                try {
                    $result = $this->deleteSpeechGroupId($wanderer_list['profile_id']);
                    Log::info("対象者グループから削除");
                    Log::info($result);
                } catch (\Throwable $e) {
                    Log::info($e->getMessage());
                };
                try {
                    //ユーザ情報更新処理
                    $userupdate = Wanderers::find($wanderer_list['id']);
                    Log::info("捜索対象から外す処理、対象者");
                    Log::info($userupdate);
                    $userupdate->fill([
                        'wandering_flg' => 0,
                        'discover_flg' => 0,
                        'latitude' => NULL,
                        'longitude' => NULL,
                    ]);
                    $status = "";
                    $exe = "捜索対象外です。";
                    $discoverflg = $userupdate["discover_flg"];
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
            $exe = "情報登録をして下さい。";
            $discoverflg = 0;
            return view('home_walk')->with(['status' => $status, 'exe' => $exe, 'discoverflg' => $discoverflg]);
        }

        // dd([$status]);
        return view('home_walk')->with(['status' => $status, 'exe' => $exe, 'discoverflg' => $discoverflg]);
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
            $family_name = $inputs['family_name'];
            $email = $inputs['email'];
            $sex = $inputs['sex'];
            $age = $inputs['age'];
            $address = $inputs['address'];
            $etel = $inputs['emergency_tel'];
            $vflg = $inputs['voiceprint_flg'];
            $rawfile = $inputs['audio_file'];
            $mini_sex = intval($sex) - 1;
            if ($mini_sex < 0) {
                $mini_sex = 0;
            }

            //ユーザ情報更新処理
            if (empty($user_id)) {
                list($speakerId, $result) = $this->addSpeaker($wname, $mini_sex, $age, $rawfile);
                $vflg = intval($vflg) + 1;
                //新規登録
                Wanderers::create([
                    'user_id' => $id,
                    'sex' => $sex,
                    'age' => $age,
                    'address' => $address,
                    'wanderer_name' => $wname,
                    'family_name' => $family_name,
                    'email' => $email,
                    'emergency_tel' => $etel,
                    'profile_id' => $speakerId,
                    'voiceprint_flg' => $vflg,
                ]);
                DB::commit();
                // 音声ファイルの情報をDbに登録する変数
                $voicename = $this->addVoicelist($result);
                // 音声ファイル復元し保存
                $this->voiceDownload($inputs['audio_base64'], $voicename);

                $messegedata = "※このメールはシステムからの自動返信です" . "\n\n" .
                    $family_name . "　様" . "\n\n" .
                    "このメールはご家族情報の更新に伴い、確認のためにメールを送信させていただいております。" . "\n" .
                    "今後ご家族の発見通知などは、このメールアドレス宛に送信させて頂きます。" . "\n" .
                    "ご家族情報などを再度更新したい場合は情報登録画面より更新をお願いいたします。";
                // 新規登録時の確認メール
                Mail::to($email)->send(new Confirmmail(
                    $messegedata,
                ));

                // ユーザー情報新規登録
            } else {
                $userupdate = Wanderers::find($user_id['id']);
                if (
                    $userupdate['wanderer_name'] != $wname
                    || $userupdate['sex'] != $sex || $userupdate['age'] != $age
                ) {
                    $this->editSpeaker($userupdate['profile_id'], $wname, $mini_sex, $age);
                }
                $userupdate->fill([
                    'sex' => $sex,
                    'age' => $age,
                    'address' => $address,
                    'wanderer_name' => $wname,
                    'family_name' => $family_name,
                    'email' => $email,
                    'emergency_tel' => $etel,
                    'voiceprint_flg' => $vflg,
                ]);
                $userupdate->save();
                DB::commit();

                $messegedata = "※このメールはシステムからの自動返信です" . "\n\n" .
                    $family_name . "　様" . "\n\n" .
                    "このメールは、ご登録時に確認のため送信させていただいております。" . "\n" .
                    "今後ご家族の発見通知などは、このメールアドレス宛に送信させて頂きます。" . "\n" .
                    "ご家族情報などを更新したい場合は情報登録画面より更新をお願いいたします。";
                // 新規登録時の確認メール
                Mail::to($email)->send(new Confirmmail(
                    $messegedata,
                ));
            }
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            abort(500);
        };
        return redirect()->route('register_walk')->with('exe_msg', '登録情報を更新しました！');
    }

    private function addSpeaker(string $name, int $sex, int $age, string $rawfile)
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
        return array($speakerId, $result);
    }

    // 音声ファイルの追記変数
    private function addSpeech($speakerId, $rawfile)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $result = $miniSRSApi->addSpeech($speakerId, $rawfile);
        if (!$result) {
            throw new \Exception('Speech registration failed!');
        }
        return $result;
    }

    // 音声ファイルの取得変数
    private function getSpeaker($speechId)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $result = $miniSRSApi->getSpeaker($speechId);
        if (!$result) {
            throw new \Exception('Failed to get audio!');
        }
    }

    // 音声ファイルの取得変数
    private function speechId($voice_table)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $result = $miniSRSApi->getSpeeches($voice_table);
        if (!$result) {
            throw new \Exception('Speech registration failed!');
        }
        return $result;
    }

    // 認識用グループに話者を追加API
    private function addSpeechGroupId($speakerId)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $miniSRSApi->addSpeakegroup($this->getRecGroupId(), $speakerId);
    }
    // 認識用グループに話者を追加API
    private function deleteSpeechGroupId($speakerId)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $result = $miniSRSApi->deleteSpeakegroup($this->getRecGroupId(), $speakerId);
        if (!$result) {
            throw new \Exception('Speech registration failed!');
        }
        return $result;
    }

    // 音声ファイルの削除
    private function speechDelete($speakerId)
    {
        $miniSRSApi = $this->getMiniSRSApi();
        $result = $miniSRSApi->deleteSpeeches($speakerId);
        if (!$result) {
            throw new \Exception('Speech registration failed!');
        }
        return $result;
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

    // 音声ファイルの取得変数
    private function addVoicelist($result)
    {
        $getDBvoicelist = $this->getDbVoicelist();
        $result = $getDBvoicelist->updateVoicelist($result);
        if (!$result) {
            throw new \Exception('Voicelist to not update!');
        }
        return $result;
    }

    // 音声ファイルの一覧を取得
    private function selectVoicelist()
    {
        $id = Auth::user()->id;
        // $voice_table = Voicelist::whereUser_id($id)->get();
        $voice_table = DB::table('voicelist')->where('user_id', $id)->get();
        if (!$voice_table) {
            throw new \Exception('Voicelist to not!');
        }

        return $voice_table;
    }

    // 音声ファイルのダウンロード
    private function voiceDownload($request, $voicename)
    {
        //base64形式データをデコードして音声ファイルに変換
        $decoded = base64_decode($request);

        //ファイル名を作成
        $filename = $voicename . ".wav";
        // 画像ファイルの保存
        Storage::put("audio/{$filename}", $decoded);
    }

    // 登録音声のspeakerIDを取得
    private function selectspeechId($profile_id)
    {
        $getDBvoicelist = $this->getDbVoicelist();
        $result = $getDBvoicelist->selectVoicelist($profile_id);
        if (!$result) {
            throw new \Exception('Voicelist to not update!');
        }
        return $result;
    }

    // 登録音声のspeakerIDを取得
    private function deletespeechId($profile_id)
    {
        $getDBvoicelist = $this->getDbVoicelist();
        $result = $getDBvoicelist->deleteVoicelist($profile_id);
        if ($result) {
            throw new \Exception('Voicelist to not update!');
        }
    }

    // 音声ファイルを削除
    private function voiceDelete($voicename)
    {
        //ファイル名を作成
        $filename = $voicename . ".wav";
        // ファイルの削除
        Storage::delete("audio/{$filename}");
    }

    // 音声ファイルを取得
    private function voiceGet($voicename)
    {
        //ファイル名を作成
        $filename = $voicename . ".wav";
        // ファイルの取得
        $file = Storage::get("audio/{$filename}");
        //wav形式データをエンコードして文字列に戻す
        $decoded = base64_encode($file);
        return $decoded;
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
            $result = $this->addSpeech($speakerId, $rawfile);
            $vflg++;

            //ユーザ情報更新処理
            $userupdate = Wanderers::find($user_id['id']);
            $userupdate->fill([
                'voiceprint_flg' => $vflg,
            ]);
            $userupdate->save();
            DB::commit();

            // 音声ファイルの情報をDbに登録する変数
            $voicename = $this->addVoicelist($result);
            // 音声ファイル復元し保存
            $this->voiceDownload($inputs['audio_base64'], $voicename);

            // 音声認識をし、レスポンスがERRORになったらcatchで初期化ポップアップの表示
            $miniSRSApi = $this->getMiniSRSApi();
            $miniSRSApi->speakerRcognition($this->getRecGroupId(), $rawfile);
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            // abort(500);
            return redirect()->route('voice_walk')->with('err_msg', '音声学習時に何らかのエラーが発生しました。学習データを復元するか削除をして下さい。');
        };
        return redirect()->route('voice_walk')->with('exe_msg', '音声の追加学習を行いました！');
    }

    // 追加音声データ登録時の処理
    public function userReset()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = Wanderers::whereUser_id($user_id)->first();
        DB::beginTransaction();
        try {
            //userをまず削除
            $speakerId = $wanderer_list['profile_id'];
            $miniSRSApi = $this->getMiniSRSApi();
            $miniSRSApi->deleteUser($speakerId);

            // 新しくユーザを追加
            $wname = $wanderer_list['wanderer_name'];
            $sex = $wanderer_list['sex'];
            $mini_sex = intval($sex) - 1;
            if ($mini_sex < 0) {
                $mini_sex = 0;
            }
            $age = $wanderer_list['age'];

            // 保存してある本姓データをbase64形式で取得
            $voicelist = Voicelist::whereUser_id($user_id)->where('delete_flg', 0)->get();
            $base64_data = $this->voiceGet($voicelist[0]["speech_id"]);
            $rawfile = 'data:audio/wav;name=' . $voicelist[0]["speech_id"] . '.wav;base64,' . $base64_data;
            Log::info($rawfile);

            // 新規にユーザを登録
            list($speakerId, $result) = $this->addSpeaker($wname, $mini_sex, $age, $rawfile);
            Log::info("新しいspeakerId↓");
            Log::info($speakerId);
            Log::info($result);

            Voicelist::where('speech_id', $voicelist[0]["speech_id"])
                ->update(['speech_id' => $result["id"]]);
            Storage::move("audio/" . $voicelist[0]["speech_id"] . '.wav', "audio/" . $result["id"] . '.wav');

            // 登録してある音声データを全て再学習
            for ($i = 1; $i < count($voicelist); $i++) {
                $base64_data = $this->voiceGet($voicelist[$i]["speech_id"]);
                $voice = 'data:audio/wav;name=' . $voicelist[$i]["speech_id"] . '.wav;base64,' . $base64_data;
                $result = $this->addSpeech($speakerId, $voice);
                Log::info("新しい音声ID↓");
                Log::info($result);

                Voicelist::where('speech_id', $voicelist[$i]["speech_id"])
                    ->update(['speech_id' => $result["id"]]);
                Storage::move("audio/" . $voicelist[$i]["speech_id"] . '.wav', "audio/" . $result["id"] . '.wav');
            }

            // profile_idを新規の物に更新
            Voicelist::where('user_id', $user_id)
                ->update(['speaker_id' => $speakerId]);
            Wanderers::where('user_id', $user_id)
                ->update(['profile_id' => $speakerId]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            // abort(500);
            // return redirect()->route('')->with('err_msg', '学習データを復元出来ませんでした。管理者にお問い合わせください。');
            return redirect('/voice_error');
        };
        return redirect()->route('voice_walk')->with('exe_msg', '復元処理が完了しました!');
    }

    // 発見フラグの更新
    public function discoverflg()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = Wanderers::whereUser_id($user_id)->first();

        // 認識用グループにから話者を削除
        try {
            $result = $this->deleteSpeechGroupId($wanderer_list['profile_id']);
            Log::info("対象者グループから削除");
            Log::info($result);
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        };

        // 発見フラグが立っている場合下げる
        if ($wanderer_list['discover_flg'] == 1) {
            //ユーザ情報更新処理
            $userupdate = Wanderers::find($wanderer_list['id']);
            Log::info("捜索対象から外す処理、対象者");
            Log::info($userupdate);
            $userupdate->fill([
                'wandering_flg' => 0,
                'discover_flg' => 0,
                'latitude' => NULL,
                'longitude' => NULL,
            ]);
            // dd([$userupdate]);
            $userupdate->save();
            DB::commit();
            // それ以外の場合も発見フラグを下げる
        } else {
            //ユーザ情報更新処理
            $userupdate = Wanderers::find($wanderer_list['id']);
            Log::info("捜索対象から外す処理、対象者");
            Log::info($userupdate);
            $userupdate->fill([
                'wandering_flg' => 0,
                'discover_flg' => 0,
                'latitude' => NULL,
                'longitude' => NULL,
            ]);
            $userupdate->save();
            DB::commit();
        }
        // dd([$wanderer_list]);
        return redirect('/home_walk');
    }

    //voiceListを返す変数
    public function voiceList()
    {
        try {
            $user_id = Auth::user()->id;
            $voice_table = Wanderers::whereUser_id($user_id)->first();

            // APIで登録してある音声ファイル一覧を取得
            $voicelist = $this->selectspeechId($voice_table["profile_id"]);
            // $voicelist応答例
            // [
            //     "id" => "91d6b8a459cf456daf839643a0efa709"
            //     "speakerId" => "c5d9d26662cc45a3b888b56ec99669b9"
            //     "creationTimestamp" => 1669884577
            //     "samplingrate" => 16000
            //     "encoding" => "raw/pcm"
            //     "length" => 3904
            //     "status" => 1
            // ]
            // dd($voicelist);
            for ($i = 0; $i < count($voicelist); $i++) {
                $voice = $this->voiceGet($voicelist[$i]["speech_id"]);
                $voicelists[] = [
                    'id' => $voicelist[$i]["id"],
                    'speaker_id' => $voicelist[$i]["speech_id"],
                    'speaker_audio' => $voice
                ];
            }
        } catch (\Throwable $e) {
            return redirect('/home_walk');
        };

        if (empty($voicelists)) {
            $status = "";
            $exe = "音声ファイルがありませんでした。";
            $user_id = Auth::user()->id;
            $wanderer_list = Wanderers::whereUser_id($user_id)->first();
            $userupdate = Wanderers::find($wanderer_list['id']);
            // 徘徊フラグが立っていない場合、徘徊フラグを立てる
            $discoverflg = $userupdate["discover_flg"];
            //ユーザ情報更新処理
            return view('home_walk')->with(['status' => $status, 'exe' => $exe, 'discoverflg' => $discoverflg]);
        } else {
            return view('voice_list', compact('voicelists'));
        }
    }

    // 追加音声データ登録時の処理
    public function wandererReset()
    {
        $user_id = Auth::user()->id;
        $wanderer_list = Wanderers::whereUser_id($user_id)->first();
        DB::beginTransaction();
        try {
            //userをまず削除
            $speakerId = $wanderer_list['profile_id'];
            $miniSRSApi = $this->getMiniSRSApi();
            $miniSRSApi->deleteUser($speakerId);

            // profile_idを新規の物に更新
            Voicelist::where('user_id', $user_id)
                ->delete();
            Wanderers::where('user_id', $user_id)
                ->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            // abort(500);
            // return redirect()->route('')->with('err_msg', '学習データを復元出来ませんでした。管理者にお問い合わせください。');
            return redirect('/voice_error');
        };
        return redirect('/home_walk');
    }

    //voiceListを返す変数
    public function audioDelete($request)
    {
        // DB、音声データ削除フラグを立てる
        $this->deletespeechId($request);
        // 音声ファイルの削除
        $this->voiceDelete($request);
        // API音声データの削除
        $this->speechDelete($request);
        return redirect('/voice_list');
    }
}
