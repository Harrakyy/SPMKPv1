<?php

namespace App\Observers;

use App\Models\Permintaan;
use App\Models\User;
use App\Notifications\RequestNotification;

class RequestObserver
{
    public function created(Permintaan $request)
    {
        $request->load('user');

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new RequestNotification($request));
        }
    }
}