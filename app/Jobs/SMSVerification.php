<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SMSVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $secret;
    private $client;
    private $phone_number;
    private $otp_code;

    public function __construct($phone_number, $otp_code)
    {
        $this->phone_number = $phone_number;
        $this->otp_code = $otp_code;
        $basic  = new \Vonage\Client\Credentials\Basic(config('vonage.app.key'), config('vonage.app.secret'));
        $this->client = new \Vonage\Client($basic);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = $this->client->sms()->send(
            new \Vonage\SMS\Message\SMS($this->phone_number, config('app.name'), $this->generateStatement($this->otp_code))
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
            echo "The message was sent successfully\n";
        } else {
            echo "The message failed with status: " . $message->getStatus() . "\n";
        }
    }

    private function generateStatement($otp_code) {
        $text = "JANGAN BERI kode ini ke siapa pun, TERMASUK Qiscus. masukkan kode verifikasi (OTP) " . $otp_code . ".";
        return $text;
    }
}
