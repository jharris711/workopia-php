<?php

namespace App\Controllers;

class ErrorController {
    /** 
     * 404 - Not Found
     */
    public static function notFound($message = 'Resource not found') {
        $status = 404;

        http_response_code($status);

        loadView('error', [
            'status' => $status,
            'message' => $message
        ]);
    }

    /** 
     * 403 - Unauthorized Found
     */
    public static function unauthorized($message = 'You are not authorized to access this resource') {
        $status = 403;

        http_response_code($status);

        loadView('error', [
            'status' => $status,
            'message' => $message
        ]);
    }
}
