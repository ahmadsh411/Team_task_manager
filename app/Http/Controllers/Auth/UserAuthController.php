<?php

namespace App\Http\Controllers\Auth;


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Controllers\Controller;
use App\Mail\makenewPassword;
use App\Mail\SendNewPassword;
use App\Models\User;
use App\Traits\MessagesStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    use MessagesStatus;

    public function register(Request $request)
    {

        try {

            $validation = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            if ($validation->fails()) {
                return response()->json(['errors' => $validation->errors()], 422);
            }
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
            ]);

            return response()->json([
                "message" => $this->sendMessageStatus(201),
                'user' => $user,
                "status" => 201,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => $this->sendMessageStatus(500)], 500);
        }
    }


    public function login(Request $request)
    {
        // مفتاح تعريف المحاولات (Email + IP)
        $key = 'login:' . Str::lower($request->input('email')) . '|' . $request->ip();

        // التحقق إذا تجاوز المستخدم الحد المسموح به من المحاولات
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key); // عدد الثواني المتبقية
            return response()->json([
                'message' => 'لقد تجاوزت عدد محاولات تسجيل الدخول المسموح بها.',
                'retry_after' => $seconds . ' ثانية متبقية قبل المحاولة التالية.',
            ], 429);
        }

        // بيانات تسجيل الدخول
        $credentials = $request->only('email', 'password');

        // التحقق من صحة البيانات
        if (!$token = auth('api')->attempt($credentials)) {
            // تسجيل محاولة فاشلة في الـ Rate Limiter
            RateLimiter::hit($key, 60); // مدة الحظر 60 ثانية
            $attemptsRemaining = 5 - RateLimiter::attempts($key); // المحاولات المتبقية

            return response()->json([
                'message' => $this->sendMessageStatus(401),
                'attempts_remaining' => $attemptsRemaining,
            ], 401);
        }

        // إذا كانت المحاولات ناجحة، قم بإعادة تعيين العدادات
        RateLimiter::clear($key);

        // الرد بنجاح مع التوكن
        return response()->json([
            'message' => $this->sendMessageStatus(200),
            'token' => $token,
            'status' => 200,
        ], 200);
    }


    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => $this->sendMessageStatus(200)], 200);
    }

    //    ########################################Password Forget#####################################

    public function forgotPassword(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'exists:users,email'],
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }

            // توليد كود التحقق
            $verification_code = random_int(100000, 999999); // كود رقمي من 6 أرقام

            // إنشاء JWT Token يحتوي على البريد الإلكتروني وكود التحقق
            $payload = [
                'email' => $request->email,
                'verification_code' => $verification_code,
                'exp' => time() + 600, // التوكن صالح لمدة 10 دقائق
            ];
            $jwt_token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

            // إرسال البريد الإلكتروني
            Mail::to($request->email)->send(new SendNewPassword($request->email, $verification_code));

            return response()->json([
                'message' => 'Verification code sent successfully',
                'token' => $jwt_token, // إرسال التوكن للمستخدم لاستخدامه لاحقًا
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'An error occurred',
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validation = Validator::make($request->all(), [
                'token' => ['required'], // التوكن الذي تم إرساله
                'verification_code' => ['required', 'numeric'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors(),
                    'message' => 'Validation failed',
                ], 422);
            }

            // فك التوكن واستخراج البيانات
            try {
                $decoded = JWT::decode($request->token, new Key(env('JWT_SECRET'), 'HS256'));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Invalid or expired token',
                ], 422);
            }

            // التحقق من كود التحقق
            if ($decoded->verification_code != $request->verification_code) {
                return response()->json([
                    'message' => 'Invalid verification code',
                ], 422);
            }

            // جلب المستخدم بناءً على البريد الإلكتروني
            $user = User::where('email', $decoded->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            // تحديث كلمة المرور
            $user->password = Hash::make($request->password);
            $user->save();

            // إرسال إشعار بالبريد الإلكتروني
            Mail::to($decoded->email)->send(new makenewPassword($request->password, $decoded->email));

            return response()->json([
                'message' => 'Password reset successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'An error occurred',
            ], 500);
        }
    }


}
