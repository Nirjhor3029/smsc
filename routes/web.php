<?php

use App\SmsDetails;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

// Temp Start

Route::get('/update-length-single', 'BulkSmsController@msgLengthUpdate');
Route::get('/update-length-bulk', 'PromotionController@msgLengthUpdate');

// Temp End

Route::get('/ajax-check-msg-length', 'PromotionController@ajaxMsgLengthCount');

Route::get('/', 'LandingPageController@index')->name('landing.page');

//Route::get('/admin-dashboard',function(){
//    return 'Maintenance mode! Try again later';
//});


Route::get('/admin-dashboard', 'DashboardController@adminIndex')->name('admin-dashboard');
Route::get('/promotion', 'PromotionController@promotionList')->name('promotion.list');
Route::view('/promotion/send/form', 'dashboard.send_promotion')->name('promotion.send.form');

Route::view('/promotion/bulk', 'dashboard.send_bulk_promotion')->name('promotion.bulk.promotion');
Route::post('/promotion/bulk/send', 'PromotionController@bulkSend')->name('promotion.bulk.send');
Route::get('/promotion/bulk/list', 'PromotionController@bulkPromotionList')->name('bulk.promotion.list');


// Route::view('/promotion/anondomela','anondomela')->name('promotion.anondomela');
Route::get('/promotion/anondomela', 'PromotionController@form')->name('promotion.anondomela');
Route::post('/promotion/send/create', 'PromotionController@send')->name('promotion.send');
Route::post('promotion/anondomela/send', 'PromotionController@anondomelaSend')->name('promotion.anondomela.send');


//API's'

Route::get('/ip', function () {
    echo $_SERVER['SERVER_ADDR'] . '<br>';
    echo $_SERVER['REMOTE_ADDR'];
});

Route::get('/bulk', function () {
    return 'Please use new url given';

});


// API given to external parties
// Start Here
Route::get('/externalApi', 'BulkSmsController@externalApi');
Route::get('/externalApi/count', 'SmsReportController@smsCount');

Route::get('/number-validation', 'Controller@mobileNoFormatValidation');
Route::get('/number-validation/logs', 'Controller@showLogs');

// End here



Route::get('/test', 'PromotionController@test');

Route::get('/bulk/dlr', 'BulkSmsController@dlrReport');
Route::get('/bulk/dlr/report/{number?}', 'BulkSmsController@dlrReportAll');

Route::get('/bulk/client', 'BulkSmsController@dlrReportFromClient');

//Temporary Down the ekshop sms service

// Route::get('/ekshop', function(){
//     return "temporarily down";
// });

Route::get('/ekshop', 'BulkSmsController@ekShopSms');

Route::get('/ekshop-promotion', 'BulkSmsController@ekShopPromotionSms');
Route::get('/ekshop-delivery', 'BulkSmsController@ekShopSmsSmsc');

Route::get('/beelink-report-email', 'MailController@sendBeelinkMail');

Route::get('/request-dlr', 'BulkSmsController@requestDlr');
Route::get('/request-teletalk-dlr', 'BulkSmsController@requestCustomDlrForTeletalk');
Route::get('/request-robi-dlr', 'BulkSmsController@requestCustomDlrRobi');
Route::get('/request-gp-dlr', 'BulkSmsController@requestCustomDlrGp');
Route::get('/request-vf-dlr', 'BulkSmsController@requestCustomDlrForVf');

Route::get('/send-dlr-to-beelink', 'BulkSmsController@sendDlrToBeelink');


Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Auth::routes();
Route::get('admin/dashboard', 'HomeController@adminHome')->name('admin.home')->middleware('is_admin');
Route::get('/home', 'HomeController@index')->name('home');
