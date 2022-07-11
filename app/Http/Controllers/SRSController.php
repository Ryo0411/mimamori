<?php

namespace App\Http\Controllers;

use App\Http\Requests\SRSRequest;
use App\Libs\MiniSRSApi;
use App\Models\Wanderers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


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
                                ]);
                                // dd([$userupdate]);
                                $userupdate->save();
                                DB::commit();
                            }
                            break;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
            $json = [
                'status' => -2,
                'error' => $e->getMessage()
            ];
        };
        return response()->json($json);
    }
}
