<?php

namespace App\Http\Controllers;

use App\VerifyPhone;

class VerifyController extends Controller
{
    public function code()
    {
        if (auth()->check()){
            return back()->with('error', 'Phone already verified');
        }
        return view('code');
    }

    public function verify()
    {

        request()->validate([
            'verify_number' => ['required', 'regex:/[0-9]+/', 'max:5']
        ]);


        $verify = VerifyPhone::where('verify_number', request()->verify_number)->firstOrFail();

        $verify->user->verified = true;

        $verify->user->save();

        return redirect('login')->with('success', 'Your phone verified successfully');

    }
}
