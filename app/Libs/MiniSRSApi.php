<?php

namespace App\Libs;

use Illuminate\Support\Facades\Log;

class MiniSRSApi
{
    private $client;
    protected string $baseUrl;
    protected string $appId;
    protected string $clientId;
    protected string $token;

    public function __construct(string $appId, string $clientId, string $clientSecret)
    {
        $this->baseUrl = 'https://apis.mimi.fd.ai/v1/applications/'.$appId.'/clients/'.$clientId.'/srs/';
        $this->client = new \GuzzleHttp\Client();
        $this->appId = $appId;
        $this->clientId = $clientId;
        $this->token = $this->getToken($clientSecret);
    }

    private function getToken(string $clientSecret): string
    {
        $scopes = [
            'https://apis.mimi.fd.ai/auth/srs/http-api-service',
            'https://apis.mimi.fd.ai/auth/srs/speaker_groups.r',
            'https://apis.mimi.fd.ai/auth/srs/speaker_groups.w',
            'https://apis.mimi.fd.ai/auth/srs/speakers.r',
            'https://apis.mimi.fd.ai/auth/srs/speakers.w',
            'https://apis.mimi.fd.ai/auth/srs/speeches.r',
            'https://apis.mimi.fd.ai/auth/srs/speeches.w',
            'https://apis.mimi.fd.ai/auth/srs/trainers.r',
            'https://apis.mimi.fd.ai/auth/srs/trainers.w'
        ];
        $response = $this->client->request('POST', 'https://auth.mimi.fd.ai/v2/token', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'https://auth.mimi.fd.ai/grant_type/client_credentials',
                'scope' => implode(';', $scopes),
                'client_id' => $this->appId . ':' . $this->clientId,
                'client_secret' => $clientSecret
            ],
        ]);

        $token = '';
        if ($response->getStatusCode() === 200) {
            $list = json_decode($response->getBody()->getContents(), true);
            $token = $list['accessToken'];
        }
        return $token;
    }

    public function addSpeaker(string $name, int $sex, int $age): array
    {
        $endPoint = $this->baseUrl . 'speakers';
        $params = [
            'lang' => '0',    // 0:ja_JP
            'name' => $name,
            'sex' => $sex,
            'age' => $age
        ];
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $response = $this->client->request('POST', $endPoint, [
            'headers' => $headers,
            'form_params' => $params,
        ]);

        $list = [];
        if ($response->getStatusCode() === 200) {
            $list = json_decode($response->getBody()->getContents(), true);
        }
        return $list;
    }

    public function getSpeaker(string $speakerId): array
    {
        $endPoint = $this->baseUrl . 'speakers/' . $speakerId;
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $response = $this->client->request('GET', $endPoint, [
            'headers' => $headers,
        ]);

        $list = [];
        if ($response->getStatusCode() === 200) {
            $tmpList = json_decode($response->getBody()->getContents(), true);
            $list = $tmpList[0];
        }
        return $list;
    }

    public function editSpeaker(string $speakerId, array $values): array
    {
        $endPoint = $this->baseUrl . 'speakers/' . $speakerId;
        $params = $values;
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $response = $this->client->request('PUT', $endPoint, [
            'headers' => $headers,
            'form_params' => $params,
        ]);
        $list = [];
        if ($response->getStatusCode() === 200) {
            $list = json_decode($response->getBody()->getContents(), true);
        }
        return $list;
    }

    public function addSpeakerToGroup(string $speakerId, string $groupId): array
    {
        $endPoint = $this->baseUrl . 'speaker_groups/' . $groupId . '/speakers/' . $speakerId;
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $response = $this->client->request('POST', $endPoint, [
            'headers' => $headers,
        ]);

        $list = [];
        if ($response->getStatusCode() === 200) {
            $list = json_decode($response->getBody()->getContents(), true);
        }
        return $list;
    }

    public function addSpeech(string $speakerId, string $rawfile): array
    {
        $endPoint = $this->baseUrl . 'speakers/' . $speakerId . '/speeches';
        $params = [
            'file' => $rawfile,
        ];
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $response = $this->client->request('POST', $endPoint, [
            'headers' => $headers,
            'form_params' => $params,
        ]);

        $list = [];
        if ($response->getStatusCode() === 200) {
            $list = json_decode($response->getBody()->getContents(), true);
            $this->trainer($speakerId);
        }
        return $list;
    }

    private function trainer(string $speakerId)
    {
        $endPoint = $this->baseUrl . 'speakers/' . $speakerId . '/trainer/commit';
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $response = $this->client->request('POST', $endPoint, [
            'headers' => $headers,
        ]);
    }

    public function speakerRcognition(string $groupId, string $rawfile)
    {
        $endPoint = 'https://service.mimi.fd.ai';
        $headers = [
            'Content-Type' => 'audio/x-pcm;bit=16;rate=16000;channels=1',
            'x-mimi-srs-speaker-group-id' => $groupId,
            'x-mimi-process' => 'srs',
            'Authorization' => 'Bearer ' . $this->token
        ];
        Log::info($headers);
        $base64data = explode("base64,", $rawfile)[1];
        $data = base64_decode($base64data);

        // 話者認識APIは同時利用でエラーになる可能性があるためリトライ（0.2秒 Sleep）
        $retryCnt = 3;
        $err = null;
        $response = null;
        for ($count = 1; $count <= $retryCnt; $count++) {
            try {
                $response = $this->client->request('POST', $endPoint, [
                    'headers' => $headers,
                    'body' => $data,
                ]);
                if ($response->getStatusCode() === 200) {
                    break;
                }
            } catch (\Throwable $e) {
                Log::info($e->getMessage());
                $err = $e;
            };
            usleep(200000);
        }
        $list = [];
        if ($err) {
            throw $err;
        }
        if ($response->getStatusCode() === 200) {
            $str = $response->getBody()->getContents();
            Log::info($str);
            $list = json_decode($str, true);
        }
        return $list;
    }
}
