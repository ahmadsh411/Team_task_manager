<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AdminPasswordMessage;
use App\Mail\AdminResetPassword;
use App\Models\Admin;
use App\Traits\MessagesStatus;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use http\Env;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPUnit\Exception;
use Illuminate\Support\Facades\RateLimiter;

class AdminController extends Controller
{
    use MessagesStatus;

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:admins'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validation->fails()) {
            return response()->json(['message' => $this->sendMessageStatus(401)], 401);
        }
        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => $this->sendMessageStatus(200)], 200);


    }

    public function login(Request $request)
    {
        $key = 'login:' . Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => $this->sendMessageStatus(429),
                'retry_after' => $seconds . ' ثانية متبقية قبل المحاولة التالية.',
            ], 429);
        }


        $card = $request->only('email', 'password');

        if (!$token = auth()->guard('admin')->attempt($card)) {

            RateLimiter::hit($key, 60);
            $reLogin = 5 - RateLimiter::attempts($key);
            return response()->json(
                [
                    "message" => $this->sendMessageStatus(401),
                    'Re-Login' => $reLogin,
                ], 401
            );
        }

        \RateLimiter::clear($key);

        return response()->json(['token' => $token, 'message' => $this->sendMessageStatus(200)], 200);


    }

    public function logout(Request $request)
    {
        auth()->guard('admin')->logout();
        return response()->json(['message' => $this->sendMessageStatus(200)], 200);
    }

//    ###################################################forget_password################################################

    /**
     * @throws RandomException
     */
    public function forgetPassword(Request $request)
    {
        try {
            // التحقق من صحة البريد الإلكتروني
            $validation = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'exists:admins,email'],
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validation->errors()
                ], 422);
            }

            // إنشاء رمز تحقق مؤقت
            $validation_code = random_int(100000, 999999);

            $payload = [
                'email' => $request->email,
                'code' => $validation_code,
                'exp' => time() + 600 // صلاحية التوكن لمدة 10 دقائق
            ];

            $jwt_token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            // إرسال الرمز عبر البريد الإلكتروني
            Mail::to($request->email)->send(new AdminPasswordMessage($validation_code));

            return response()->json([
                'message' => 'A verification code has been sent to your email.',
                'token' => $jwt_token
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in forgetPassword: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

//    ##################################################resetPassword#######################################

    public function resetPassword(Request $request)
    {
        try {
            // التحقق من البيانات المُرسلة
            $validation = Validator::make($request->all(), [
                'code' => ['required'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'token' => ['required'],
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validation->errors()
                ], 422);
            }

            // فك تشفير التوكن والتحقق منه
            $decoded = JWT::decode($request->token, new Key(env('JWT_SECRET'), 'HS256'));

            if ($decoded->code === $request->code) {
                $admin = Admin::where('email', $decoded->email)->first();

                if (!$admin) {
                    return response()->json(['message' => 'Admin not found.'], 404);
                }

                // تحديث كلمة المرور
                $admin->password = Hash::make($request->password);
                $admin->save();

                // إرسال إشعار للبريد الإلكتروني
                Mail::to($decoded->email)->send(new AdminResetPassword($request->password, $decoded->email));

                return response()->json(['message' => 'Password has been reset successfully.'], 200);
            } else {
                return response()->json(['message' => 'Invalid code provided.'], 400);
            }
        } catch (Exception $e) {
            Log::error('Error in resetPassword: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

}

