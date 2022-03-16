<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\WhatsappVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use OTPHP\TOTP;

class WhatsappVerificationController extends Controller
{
    private $secret;

    public function __construct() {
        $otp = TOTP::create();
        $this->secret = $otp->getSecret();
    }
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.whatsapp.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string'],
        ]);

        $otp = TOTP::create($this->secret);

        $user = User::where("phone_number", $request->phone_number)->first();

        WhatsappVerification::dispatch($user->phone_number, $otp->now());

        return view('auth.whatsapp.verify-otp');
    }
}
