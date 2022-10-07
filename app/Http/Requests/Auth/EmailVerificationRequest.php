<?php

namespace App\Http\Requests\Auth;

use App\Models\User;

class EmailVerificationRequest extends \Illuminate\Foundation\Auth\EmailVerificationRequest
{
    public function user($guard = null)
    {
        return User::find($this->route('id'));
    }
}
