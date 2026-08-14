<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CronController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'APIToken'], function () {
    Route::post('category-list', [ArticleController::class, 'getCategoryData']);
    Route::post('get-featured-article', [ArticleController::class, 'getFeaturedArticles']);
    Route::post('categorywise-article', [ArticleController::class, 'getCategoryWiseArticleData']);
    Route::get('article-by-id/{article_id}', [ArticleController::class, 'getArticleByID']);
    Route::post('register-stepup', [ArticleController::class, 'registerStepUp']);
    Route::post('update-avatar', [ArticleController::class, 'updateAvatar']);
    Route::post('verify-code', [ArticleController::class, 'verifyVerificationCode']);
    Route::post('resend-verification-code', [ArticleController::class, 'ResendVerificationCode']);
    Route::post('update-user-location', [ArticleController::class, 'updateUserLocation']);
    Route::post('update-device-token', [ArticleController::class, 'updateDeviceToken']);
    Route::post('update-location-detection', [ArticleController::class, 'updateLocationDetection']);
    Route::post('update-notification-alert', [ArticleController::class, 'updateNotificationSoundSetting']);
    Route::post('update-category-notification-setting', [ArticleController::class, 'updateCategoryNotificationSetting']);
    Route::post('update-bookmark-status', [ArticleController::class, 'updateUserArticleBookmark']);
    Route::post('update-article-read-status', [ArticleController::class, 'markArticleAsRead']);
    Route::post('get-article-bookmark-status', [ArticleController::class, 'getUserArticleBookmark']);
    Route::post('search-data', [ArticleController::class, 'searchData']);
    Route::post('search-all-data', [ArticleController::class, 'searchAllData']);
    Route::post('get-videos', [ArticleController::class, 'getVideoData']);
    Route::post('video-by-id', [ArticleController::class, 'getVideoByID']);
    Route::post('latest-videos', [ArticleController::class, 'getLatestVideos']);
    Route::post('business-list', [BusinessController::class, 'getBusinessCategoryData']);
    Route::post('search-list', [BusinessController::class, 'getBusinessList']);
    Route::post('business-by-id', [ArticleController::class, 'getBusinessDetail']);
    Route::post('event-list', [BusinessController::class, 'getEventList']);
    Route::post('event-list-by-date', [BusinessController::class, 'getEventListFromDate']);
    Route::post('event-by-id', [ArticleController::class, 'getEventDetail']);
    Route::post('update-steps', [CommonController::class, 'updateUserSteps']);
    Route::post('steps-leaderboard', [CommonController::class, 'stepsLeaderboards']);
    Route::post('steps-statistics', [CommonController::class, 'getStatistics']);
});

Route::post('update-auth-token', [ArticleController::class, 'updateAuthorisedToken']);
Route::post('update-author-data', [CronController::class, 'getAuthorData']);
Route::post('update-footer-content', [CronController::class, 'updateFooterContent']);
//Route::post('update-article-data', [CronController::class, 'getArticleData']);
//Route::post('update-canada-press-article-data', [CronController::class, 'updateCPArticleData']);

Route::post('set-user-data', [ArticleController::class, 'setUserData']);
Route::get('get-category-image/{category_id}', [ArticleController::class, 'getImageOfCategory']);
Route::post('get-full-image-name', [ArticleController::class, 'getArticleImage']);
Route::post('get-footer-content', [CommonController::class, 'getFooterContent']);
Route::post('update-coupon-data', [CronController::class, 'updateCouponData']);
Route::post('get-coupon-list-by-category', [CouponController::class, 'getCouponListByCategory']);
Route::get('get-coupon-by-id/{coupon_id}', [CouponController::class, 'getCouponById']);
Route::post('send-notification', [ArticleController::class, 'sendNotification']);
Route::post('send-notification-android', [ArticleController::class, 'sendNotificationAndroid']);
//Route::post('update-video-data', [CronController::class, 'updateVideoData']);
Route::post('update-advertisement-data', [CronController::class, 'updateAdvertisementData']);
Route::post('update-admin-login-data', [CronController::class, 'updateAdminLoginData']);
Route::get('update-article-caption', [CronController::class, 'updateArticleCaptionCreditData']);