<?php

namespace App\Libs;

use App\Models\Voicelist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Wanderers;

class DbVoicelist
{
    public function updateVoicelist($result)
    {
        // 音声ファイルの情報をDbに登録する変数
        $result = $this->createVoicelist($result);

        // $response = $this->client->request('POST', $endPoint, [
        //     'headers' => $headers,
        //     'body' => $data,
        // ]);
        // $list = [];
        // if ($response->getStatusCode() === 200) {
        //     $list = json_decode($response->getBody()->getContents(), true);
        //     $this->trainer($speakerId);
        // }
        return $result;
    }

    public function selectVoicelist($result)
    {
        try {
            //データの受け取り。
            $wanderers = Voicelist::whereSpeaker_id($result)->where('delete_flg', '==', 0)->get();
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            abort(500);
            return $e;
        };
        return $wanderers;
    }

    public function deleteVoicelist($result)
    {
        try {
            //データの受け取り。
            $wanderers = Voicelist::whereSpeech_id($result)->first();
            //Voice情報更新
            $wanderers->fill([
                'delete_flg' => 1,
            ]);
            $wanderers->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            abort(500);
            return $e;
        };
    }

    // 音声ファイルの情報を新規Dbに登録する変数
    private function createVoicelist($result)
    {
        //データの受け取り。
        $id = Auth::user()->id;
        $wanderers = Wanderers::whereUser_id($id)->first();
        // $user_id = Wanderers::whereUser_id($id)->first();
        DB::beginTransaction();
        try {
            //Voice情報新規登録
            Voicelist::create([
                'user_id' => $id,
                'speech_id' => $result['id'],
                'speaker_id' => $wanderers['profile_id'],
                // 'delete_flg'はデフォルト値
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            Log::info($e->getMessage());
            abort(500);
            return $e;
        };
        return $result['id'];

        // $result応答例
        // {
        //     "operationId": "<operationId>",
        //     "startTimestamp": 1584938765,
        //     "selfLink": "https://apis.mimi.fd.ai/v1/applications/<applicationId>/clients/<clientId>/operations/<operationId>",
        //     "progress": 100,
        //     "code": 200,
        //     "kind": "srs#operation#speech",
        //     "endTimestamp": 1584938766,
        //     "status": "success",
        //     "error": "",
        //     "targetLink": "https://apis.mimi.fd.ai/v1/applications/<applicationId>/clients/<clientId>/srs/speeches/<speechId>",
        //     "id": "<speechId>"
        //   }
    }
}
