<?php

namespace App\Services\Api\Otp;

use App\Models\User;
use App\Repositories\Api\Otp\OtpRepository;
use App\Repositories\Api\User\UserRepository;
use App\Traits\MeliPayamakTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OtpService
{
    use MeliPayamakTrait;

    public function __construct(private OtpRepository $otpRepository, private UserRepository $userRepository) {}

    public function sendOtp(string $to, int $bodyId = 314700)
    {
        $phone = $this->checkPhoneNumber($to);
        $user = $this->userRepository->findOrCreateByPhone($phone);
        $otp = $this->makeOtp($user);
        $this->sendOtpThroughMeliPayamak([$otp->otp_code], $phone, $bodyId);
        return ['success' => true, 'message' => 'otp code is sent', 'otp_token' => $otp->token, 'status_code' => 200];
    }

    public function reSendOtp(string $token)
    {
        $otp = $this->otpRepository->findWhere(['token' => $token, 'used' => 0, 'created_at' => ['>', Carbon::now()->subMinutes(5)]], ['user'], ['*']);
        if (!$otp) {
            return ['success' => false, 'message' => 'url is invalid', 'status_code' => 404];
        }
        $user = $otp->user;
        return $this->sendOtp($user->phone);
    }

    public function makeOtp(User $user)
    {
        $otpCode = $this->generateNumericOTP();
        $token = Str::random(60);
        $otpInput = [
            'otp_code' => $otpCode,
            'token' => $token,
            'user_id' => $user->id,
            'phone_number' => $user->phone
        ];
        $otp = $this->otpRepository->create($otpInput);
        return $otp;
    }

    public function verifyOtp(string $token, string $inputOtpCode)
    {
        $otp = $this->otpRepository->findWhere(['token' => $token, 'used' => 0], ['user']);
        if (!$otp) {
            return ['success' => false, 'message' => 'url is invalid', 'status_code' => 404];
        }
        if(!is_numeric($inputOtpCode))
        {
            return ['success' => false, 'message' => 'otp code just can be numeric', 'status_code' => 400];
        }
        $inputOtpCode = (int) $inputOtpCode;
        $savedOtpCode = (int) $otp->otp_code;

        if ($inputOtpCode !== $savedOtpCode)
        {
            return ['success' => false, 'message' => 'otp code is incorrect', 'status_code' => 400];
        }
        $token = $otp->user()->createToken('auth');
        return ['success' => true, 'bearerToken' => $token->plainTextToken, 'message' => 'login successfull', 'status_code' => 200];
    }

    public function checkPhoneNumber($phoneNumber)
    {
        if (!preg_match('/^(\+98|98|0)9\d{9}$/', $phoneNumber)) {
            return null;
        }
        $phoneNumber = ltrim($phoneNumber, '+');
        if (str_starts_with($phoneNumber, '98')) {
            $phoneNumber = substr($phoneNumber, 2);
        }
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = substr($phoneNumber, 1);
        }
        return "0{$phoneNumber}";
    }

    function generateNumericOTP($length = 6)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= random_int(0, 9);
        }
        return $otp;
    }

}
