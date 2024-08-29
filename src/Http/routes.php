<?php

use Yxs\SsoCompany\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Yxs\SsoCompany\SsoCompanyServiceProvider;

Route::get('sso-company', Controllers\SsoCompanyController::class.'@index');
if(config('admin.auth.enable')){
    $check_url=SsoCompanyServiceProvider::setting('checkUrl');
//    $check_url=null;
    if(!empty($check_url)){
        Route::get('auth/login', Controllers\SsoCompanyController::class.'@ssoCompanyLogin')->name('sso_company_login');
        Route::post('auth/login', Controllers\SsoCompanyController::class.'@postLogin');
    }

}