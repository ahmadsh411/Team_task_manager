<?php

namespace App\Traits;

trait MessagesStatus{

    public function sendMessageStatus($status) {
        $messages = [
            // نجاح العملية
            200 => "The operation was completed successfully. (OK)",
            201 => "The operation was created successfully. (Created)",
            202 => "The request has been accepted but not yet processed. (Accepted)",
            204 => "The request was successful, but there is no content to return. (No Content)",

            // عمليات إعادة التوجيه
            301 => "The requested resource has been permanently moved to a new location. (Moved Permanently)",
            302 => "The requested resource has been temporarily moved to a new location. (Found)",
            304 => "The resource has not been modified since the last request. (Not Modified)",

            // أخطاء العميل
            400 => "The request could not be understood due to invalid syntax. (Bad Request)",
            401 => "Authentication is required or has failed. (Unauthorized)",
            403 => "The client does not have permission to access the resource. (Forbidden)",
            404 => "The requested page could not be found. (Not Found)",
            405 => "The requested method is not supported. (Method Not Allowed)",
            406 => "The requested resource is not acceptable according to the client’s headers. (Not Acceptable)",
            408 => "The server timed out waiting for the request. (Request Timeout)",
            409 => "The request could not be completed due to a conflict with the current state of the resource. (Conflict)",

            // أخطاء الخادم
            500 => "The server encountered an internal error. (Internal Server Error)",
            501 => "The requested method is not implemented. (Not Implemented)",
            502 => "The server received an invalid response from an upstream server. (Bad Gateway)",
            503 => "The server is currently unavailable due to maintenance or overload. (Service Unavailable)",
            504 => "The server did not receive a timely response from an upstream server. (Gateway Timeout)",
            505 => "The server does not support the HTTP version used in the request. (HTTP Version Not Supported)"
        ];

        // إرجاع الرسالة إذا كانت الحالة موجودة، أو رسالة افتراضية في حال لم تكن موجودة
        return $messages[$status] ?? "Unknown status code. Please consult documentation.";
    }

}
