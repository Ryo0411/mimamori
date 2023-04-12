<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getApplicationId(): string
    {
        return config('fd_mini.fd_application_id');
    }

    protected function getClientId(): string
    {
        return config('fd_mini.fd_client_id');
    }

    protected function getClientSecret(): string
    {
        return config('fd_mini.fd_client_secret');
    }

    protected function getGroupId(): string
    {
        return config('fd_mini.fd_speaker_group_id');
    }

    protected function getRecGroupId(): string
    {
        return config('fd_mini.fd_speaker_recgroup_id');
    }
}
