<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class WhatsappVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $phone_number;
    private $user;
    private $otp_code;

    public function __construct($phone_number, $otp_code)
    {
        $this->phone_number = $phone_number;
        $this->user = config('app.name');
        $this->otp_code = $otp_code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->generatePayload($this->phone_number, $this->user, $this->otp_code);
        $headers = $this->generateHeaders(config('qiscus.app.id'), config('qiscus.app.secret_key'));

        $url = config('qiscus.app.url') . "/whatsapp/v1/" . config('qiscus.app.id') . "/" . config('qiscus.app.channel_id') . "/messages";
        $resp = Http::withHeaders($headers)->post($url, $payload);
        echo $resp;
    }

    private function generateHeaders($app_id, $secret_key) {
        $headers = [
            "qiscus-app-id" => $app_id,
            "qiscus-secret-key" => $secret_key
        ];

        return $headers;
    }

    private function generatePayload($phone_number, $user, $otp_code) {
        $namespace = env('QISCUS_WA_NAMESPACE');

        $payload = [
            "to" => $phone_number,
            "type" => "template",
            "template" => [
                "namespace" => $namespace,
                "name" => "otp",
                "language" => [
                    "policy" => "deterministic",
                    "code" => "id"
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $user
                            ],
                            [
                                "type" => "text",
                                "text" => $otp_code
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $payload;
    }
}
