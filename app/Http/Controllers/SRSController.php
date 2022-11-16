<?php

namespace App\Http\Controllers;

use App\Http\Requests\SRSRequest;
use App\Libs\MiniSRSApi;
use App\Models\Wanderers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\Maildata;
use Illuminate\Support\Facades\Mail;


class SRSController extends Controller
{
    private function getMiniSRSApi(): MiniSRSApi
    {
        return new MiniSRSApi($this->getApplicationId(), $this->getClientId(), $this->getClientSecret());
    }

    public function speakerRcognition(SRSRequest $request)
    {
        $inputs = $request->all();
        $sex = $inputs['sex'];
        $rawfile = $inputs['audio_file'];
        Log::warning($rawfile);
        $latitude = $inputs['latitude'];
        $longitude = $inputs['longitude'];
        try {
            $miniSRSApi = $this->getMiniSRSApi();
            $result = $miniSRSApi->speakerRcognition($this->getGroupId(), $rawfile);
            Log::info($result);
            $json = [
                'status' => -1
            ];
            if ($result) {
                $json = [
                    'status' => 1
                ];
                $speakers = $result['response']['speaker'];
                Log::warning("speakersの中身");
                Log::warning($speakers);

                foreach ($speakers as $speaker) {
                    $speakerId = $speaker['speaker_id'];
                    if (empty($speakerId)) {
                        continue;
                    }
                    $wanderers = new Wanderers();
                    $data = $wanderers
                        ->where('profile_id', $speakerId)
                        ->where('wandering_flg', '!=', 0)
                        ->first();
                    if ($data) {
                        if ($data->sex == $sex) {
                            $json = [
                                'status' => 0,
                                'wanderer_name' => $data->wanderer_name,
                                'sex' => $data->sex,
                                'age' => $data->age,
                                'emergency_tel' => $data->emergency_tel,
                                'confidence' => $speaker['confidence'],
                            ];

                            // 音声認識結果で認識したユーザーのフラグ変更
                            $wanderer_list = Wanderers::whereProfile_id($speakerId)->first();
                            if ($wanderer_list) {
                                //ユーザ情報更新処理
                                $userupdate = Wanderers::find($wanderer_list['id']);
                                $userupdate->fill([
                                    'wandering_flg' => 2,
                                    'discover_flg' => 1,
                                    'wanderer_id' => Auth::user()->id,
                                    'wanderer_time' => now(),
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                ]);
                                Log::info("ユーザー発見処理データベースの処理を下記内容に更新");
                                Log::info($userupdate);
                                // dd([$userupdate]);
                                $userupdate->save();
                                DB::commit();
                            }

                            $messegedata = '';
                            $gps_url = '';
                            $wanderer_time = '';
                            // DB更新後に自動メール送信
                            if ($wanderer_list['email'] != null) {
                                $messegedata =
                                    $wanderer_list['family_name'] . "　様" . "\n\n" .
                                    $wanderer_list['wanderer_name'] . "様が発見されました。" . "\n" .
                                    "アプリを起動して確認してください。" . "\n\n" .
                                    "https://anshinm.onsei.app/";

                                if ($latitude == null || $longitude == null) {
                                    $gps_url = "";
                                } else {
                                    $gps_url = "おおよその発見場所\n
                                            https://www.google.com/maps/search/" . $latitude . "," . $longitude;
                                };
                                $wanderer_time = "発見日時\n" .
                                    now()->format('Y年n月j日H時i分') . "　頃";
                                Mail::to($wanderer_list['email'])->send(new Maildata(
                                    $messegedata,
                                    $gps_url,
                                    $wanderer_time
                                ));
                            }
                            Log::info($messegedata);
                            Log::info($gps_url);
                            Log::info($wanderer_time);
                            break;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            $json = [
                'status' => 2,
                'error' => $e->getMessage()
            ];
        };
        Log::info("jsonの中身、最終的な結果");
        Log::info($json);
        return response()->json($json);
    }
}
