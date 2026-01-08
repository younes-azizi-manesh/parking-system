<?php

namespace App\Traits;

use Melipayamak\MelipayamakApi;

trait MeliPayamakTrait
{
    public function sendOtpThroughMeliPayamak( array $text, string $to, int $bodyId): void
    {
        try {
            $api = new MelipayamakApi(config('sms.username'), config('sms.password'));
            $api->sms('soap')->sendByBaseNumber($text, $to, $bodyId);
        } catch (\Throwable $e) {
            logger()->error('SMS send failed', [
                'to' => $to,
                'bodyId' => $bodyId,
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
