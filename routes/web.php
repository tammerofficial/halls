<?php

use Illuminate\Support\Facades\Route;

// Vue.js SPA - جميع المسارات تعيد إلى الصفحة الرئيسية
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');