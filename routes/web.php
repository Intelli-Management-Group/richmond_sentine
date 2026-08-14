<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertisementController;
// use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticlelistController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponzoneController;
use App\Http\Controllers\EditionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FooterdetailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideolistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
/*
Route::get('/', function () {
return view('home');
});
 */
// Auth::routes();
Route::get('/apple-app-site-association', function () {
    $json = file_get_contents(base_path('.well-known/apple-app-site-association'));
    return response($json, 200)
        ->header('Content-Type', 'application/json');
});
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/logout', [LoginController::class, 'logout'])->name("logout");
Route::get('/', [HomeController::class , 'index'])->name('home');

Route::post('/admin-login', [AdminController::class, 'vefiryLogin'])->name('admin-login');

Route::get('/home-category', [CategoryController::class, 'getHomeCategory']);

//Forntend Pages
Route::get('/list', [HomeController::class , 'searchList']);
Route::get('/contact-us', [HomeController::class , 'contactus'])->name('contact-us');
Route::get('/about-us', [HomeController::class ,  'aboutus'])->name('about-us');
Route::get('/terms-of-use', [HomeController::class ,  'termsofuse'])->name('terms-of-use');
Route::get('/privacy-policy', [HomeController::class ,  'privacypolicy'])->name('privacy-policy');
Route::get('/article-detail/{id}/{heading}', [HomeController::class ,  'articledetail'])->name('article-detail');
Route::get('/articles/{id}/{name}', [ArticlelistController::class, 'articlelist']);
Route::get('/couponzone', [CouponzoneController::class, 'index'])->name('couponzone');
Route::post('/couponzone', [CouponzoneController::class, 'getBody']);
Route::get('/coupondetail/{id}', [CouponzoneController::class, 'coupondetail']);
Route::get('/videos', [VideolistController::class, 'index'])->name('videos');
// Route::post('/videos', [VideolistController::class, 'getVideo']);
Route::get('/videos/{id}/{heading}', [VideolistController::class, 'getVideoclick']);
Route::get('/editions', [HomeController::class ,  'editions'])->name('editions');
Route::get('/place-an-ad', [HomeController::class ,  'placeanad'])->name('place-an-ad');
Route::get('redirect-to-app', [HomeController::class ,  'redirectToApp']);
Route::post('search-business', [HomeController::class ,  'getBusinessData']);
Route::get('/stores', [HomeController::class ,  'getStores'])->name('stores');
Route::get('/events', [HomeController::class ,  'getEvents'])->name('events');
Route::post('/events-by-date', [HomeController::class ,  'getEventList'])->name('events-by-date');
Route::get('/business-names', [HomeController::class ,  'getAllBusiness'])->name('business-names');
Route::get('/organizer-names', [EventController::class, 'getBusinessData'])->name('organizer-names');
Route::get('/stores/{short_code}', [HomeController::class ,  'getBusinessDetail']);
Route::post('/update-business', [BusinessController::class, 'updateBusiness']);
Route::post('/insert-event', [EventController::class, 'insertEvent']);
Route::get('/leaderboard', [HomeController::class ,  'stepUpLeaderBoard']);
Route::group(['middleware' => ['auth', 'web']], function () {
    Route::get('/category-list', [CategoryController::class, 'loadCategory'])->name('category-list');
    Route::get('/dashboard', [AdminController::class, 'loadDashboard'])->name('dashboard');
    Route::post('/insert-category', [CategoryController::class, 'insertCategory'])->name('insert-category');
    Route::get('/delete-category/{id}', [CategoryController::class, 'deleteCategory']);
    Route::get('/author-list', [AuthorController::class, 'loadAuthor'])->name('author-list');
    Route::post('/author-list', [AuthorController::class, 'authorPaginationData']);
    Route::get('/edit-author/{id}', [AuthorController::class, 'editAuthor']);
    Route::post('/edit-author/{id}', [AuthorController::class, 'updateAuthor']);
    Route::get('/delete-author/{id}', [AuthorController::class, 'deleteAuthor']);
    Route::post('/delete-all-authors', [AuthorController::class, 'deleteAuthors']);
    Route::get('/unpublish-author/{id}', [AuthorController::class, 'unpublishAuthor']);
    Route::get('/publish-author/{id}', [AuthorController::class, 'publishAuthor']);
    Route::get('/add-author', [AuthorController::class, 'loadAddauthor']);
    Route::post('/add-author', [AuthorController::class, 'insertAuthor']);
    Route::get('/edit-category/{id}', [CategoryController::class, 'editCategory']);

    Route::get('/advertisement-list', [AdvertisementController::class, 'loadAdvertisement'])->name('advertisement-list');
    Route::post('/advertisement-list', [AdvertisementController::class, 'advertisementPaginationData']);
    Route::get('/add-advertisement', [AdvertisementController::class, 'loadAdverstisementView']);
    Route::post('/insert-advertisement', [AdvertisementController::class, 'insertAdverstisement'])->name('insert-advertisement');
    Route::post('/update-advertisement', [AdvertisementController::class, 'updateAdvertisement'])->name('update-advertisement');
    Route::get('/edit-advertisement/{id}', [AdvertisementController::class, 'editAdvertisement']);
    Route::get('/publish-advertisement/{id}', [AdvertisementController::class, 'publishAdvertisement']);
    Route::get('/unpublish-advertisement/{id}', [AdvertisementController::class, 'unpublishAdvertisement']);
    Route::get('/delete-advertisement/{id}', [AdvertisementController::class, 'deleteAdvertisement']);
    Route::post('/delete-all-advertisements', [AdvertisementController::class, 'deleteAdvertisements']);
    Route::get('/article-list', [ArticleController::class, 'loadArticleList'])->name('article-list');
    Route::post('/article-list', [ArticleController::class, 'publishedArticleListPaginationData']);
    Route::get('/unpublished-article-list', [ArticleController::class, 'loadUnpublishedArticleList'])->name('unpublished-article-list');
    Route::post('/unpublished-article-list', [ArticleController::class, 'unpublishedArticleListPaginationData']);
    Route::get('/add-article/{is_video}', [ArticleController::class, 'loadAddArticle']);
    Route::post('/insert-article', [ArticleController::class, 'insertArticle']);
    Route::post('/insert-video', [ArticleController::class, 'insertVideo']);
    Route::get('/meta-tags', [ArticleController::class, 'getMetaTags']);
    Route::post('/get-youtube-video-data', [ArticleController::class, 'getYoutubeData']);

    Route::get('/edit-about-us', [FooterdetailController::class, 'editAboutus'])->name("edit-about-us");
    Route::post('/update-about-us', [FooterdetailController::class, 'updateDescription'])->name("update-about-us");
    Route::get('/edit-terms-of-use', [FooterdetailController::class, 'editTermsofuse'])->name("edit-terms-of-use");
    Route::post('/update-terms-of-use', [FooterdetailController::class, 'updateDescription'])->name("update-terms-of-use");
    Route::get('/edit-privacy-policy', [FooterdetailController::class, 'editPrivacypolicy'])->name("edit-privacy-policy");
    Route::post('/update-privacy-policy', [FooterdetailController::class, 'updateDescription'])->name("update-privacy-policy");
    Route::get('/admin-contact-us', [FooterdetailController::class, 'loadAddContact']);
    Route::post('/admin-contact-us', [FooterdetailController::class, 'insertContactus'])->name("admin-contact-us");

    Route::get('/edit-article/{id}', [ArticleController::class, 'editArticle']);
    Route::get('/delete-article/{id}', [ArticleController::class, 'deleteArticle']);
    Route::get('/publish-article/{id}', [ArticleController::class, 'publishArticle']);
    Route::get('/unpublish-article/{id}', [ArticleController::class, 'unpublishArticle']);
    Route::post('/delete-all-articles', [ArticleController::class, 'deleteArticles']);

    Route::get('/coupon-list', [CouponzoneController::class, 'loadCouponList'])->name('coupon-list');
    Route::post('/coupon-list', [CouponzoneController::class, 'couponPaginationData']);
    Route::get('/add-coupon', [CouponzoneController::class, 'loadAddCoupon']);
    Route::post('/insert-coupon', [CouponzoneController::class, 'insertCoupon']);
    Route::get('/delete-coupon/{id}', [CouponzoneController::class, 'deleteCoupon']);
    Route::get('/edit-coupon/{id}', [CouponzoneController::class, 'loadEditCoupon']);
    Route::post('/delete-all-coupons', [CouponzoneController::class, 'deleteCoupons']);

    Route::get('/edition-list', [EditionController::class, 'loadEditions'])->name('edition-list');
    Route::post('/edition-list', [EditionController::class, 'editionPaginationData']);
    Route::get('/add-edition', [EditionController::class, 'loadAddEdition']);
    Route::post('/insert-edition', [EditionController::class, 'insertEdition']);
    Route::get('/delete-edition/{id}', [EditionController::class, 'deleteEdition']);
    Route::get('/edit-edition/{id}', [EditionController::class, 'editEdition']);
    Route::post('/delete-all-editions', [EditionController::class, 'deleteEditions']);

    Route::get('/business-category-list', [BusinessController::class, 'loadCategory'])->name('business-category-list');
    Route::post('/insert-business-category', [BusinessController::class, 'insertCategory'])->name('insert-business-category');
    Route::get('/edit-business-category/{id}', [BusinessController::class, 'editCategory']);
    Route::get('/delete-business-category/{id}', [BusinessController::class, 'deleteCategory']);
    Route::get('/business-list', [BusinessController::class, 'loadBusinessList'])->name('business-list');
    Route::post('/business-list', [BusinessController::class, 'loadBusinessListPaginationData']);

    Route::get('/unapproved-business-list', [BusinessController::class, 'loadUnapprovedBusinessList'])->name('unapproved-business-list');
    Route::post('/unapproved-business-list', [BusinessController::class, 'loadUnapprovedBusinessListPaginationData']);
    Route::get('/edit-business/{id}', [BusinessController::class, 'editBusiness']);
    Route::get('/edit-approved-business/{id}', [BusinessController::class, 'editApprovedBusiness']);
    Route::get('/approve-business/{id}', [BusinessController::class, 'approveBusiness']);
    Route::post('/unapprove-business', [BusinessController::class, 'unapproveBusiness']);
    Route::post('/delete-business', [BusinessController::class, 'deleteApprovedBusiness']);
    Route::post('/delete-unapproved-business', [BusinessController::class, 'deleteUnapprovedBusiness']);
    Route::get('/make-business-featured/{id}', [BusinessController::class, 'toggleBusinessFeatured']);
    Route::get('/notification-list', [BusinessController::class, 'loadNotificationList']);
    Route::post('/read-notification', [BusinessController::class, 'readNotification']);

    Route::get('/event-category-list', [EventController::class, 'loadCategory'])->name('event-category-list');
    Route::post('/insert-event-category', [EventController::class, 'insertCategory'])->name('insert-event-category');
    Route::get('/edit-event-category/{id}', [EventController::class, 'editCategory']);
    Route::get('/delete-event-category/{id}', [EventController::class, 'deleteCategory']);
    Route::get('/event-list', [EventController::class, 'loadEventList'])->name('event-list');
    Route::post('/event-list', [EventController::class, 'loadEventPaginationData']);
    Route::get('/add-event', [EventController::class, 'loadAddEvent']);
    Route::get('/edit-event/{id}', [EventController::class, 'editEvent']);
    Route::post('/delete-event', [EventController::class, 'deleteEvent']);

});
Route::get('/richmond-election-2022', [HomeController::class ,  'loadElectionPage']);
Route::get('/election/{type?}', [HomeController::class ,  'loadNewElectionPage']);