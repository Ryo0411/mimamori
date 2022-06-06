<?php

namespace App\Http\Controllers;

use App\Http\Requests\SRSRequest;
use App\Libs\MiniSRSApi;
use App\Models\Wanderers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
