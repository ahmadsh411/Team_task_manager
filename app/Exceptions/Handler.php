<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Handle exceptions and customize responses.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
            // احصل على عدد الثواني المتبقية من الهيدر
            $retryAfter = $exception->getHeaders()['Retry-After'] ?? 60; // إذا لم يكن موجودًا، استخدم 60 كافتراضي

            return response()->json([
                'message' => 'لقد تجاوزت عدد محاولات تسجيل الدخول المسموح بها.',
                'retry_after' => $retryAfter . ' ثانية متبقية قبل المحاولة التالية.',
            ], 429);
        }

        return parent::render($request, $exception);
    }

}
