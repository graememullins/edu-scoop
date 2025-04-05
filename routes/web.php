<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/keep-alive', function () {
    Log::info('Keep-alive ping received at ' . now()->toDateTimeString());

    return response('OK', 200);
});