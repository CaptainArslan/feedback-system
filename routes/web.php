<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Auth\NewPasswordController;


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

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/', 'DashboardController@dashboard')->name('dashboard');

    Route::get('/profile', 'DashboardController@profile')->name('profile');
    Route::post('/profile-save', 'DashboardController@general')->name('profile.save');
    Route::post('/password-save', 'DashboardController@changePassword')->name('password.save');
    Route::post('/email-change', 'DashboardController@changeEmail')->name('email.save');

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/list', 'UserController@list')->name('list');
        Route::get('/fetchlist', 'UserController@list')->name('fetch');
        Route::get('/add', 'UserController@add')->name('add');
        Route::get('/edit/{id?}', 'UserController@edit')->name('edit');
        Route::post('/save/{id?}', 'UserController@save')->name('save');
        Route::get('/delete/{id?}', 'UserController@delete')->name('delete');
        Route::get('/status/{id?}', 'UserController@status')->name('status');
    });
    Route::prefix('categories')->name('category.')->group(function () {
        Route::get('/list', 'CategoryController@list')->name('list');
        Route::get('/add', 'CategoryController@add')->name('add');
        Route::get('/edit/{id?}', 'CategoryController@edit')->name('edit');
        Route::post('/save/{id?}', 'CategoryController@save')->name('save');
        Route::get('/delete/{id?}', 'CategoryController@delete')->name('delete');
        Route::get('/status/{id?}', 'CategoryController@status')->name('status');
    });
    Route::prefix('feedback')->name('feedback.')->group(function () {
        Route::get('/list', 'FeedbackController@list')->name('list');
        Route::get('/add', 'FeedbackController@add')->name('add');
        Route::get('/edit/{id?}', 'FeedbackController@edit')->name('edit');
        Route::post('/save/{id?}', 'FeedbackController@save')->name('save');
        Route::get('/delete/{id?}', 'FeedbackController@delete')->name('delete');
        Route::get('/status/{id?}', 'FeedbackController@status')->name('status');
        Route::get('add_comment', 'FeedbackController@addComment')->name('add_comment');
    });
    Route::post('/save/new/feedback', 'FeedbackController@saveNewFeedback')->name('save.feedback.form');
    Route::get('/show/feedback', 'FeedbackController@getFeedback')->name('get.feedback');

    Route::post('/save/new/comment', 'FeedbackController@saveComment')->name('save.comment');
});

Route::get('/reset_password/{email}/{token}', [NewPasswordController::class, 'create'])->name('reset.password.form');
Route::post('/reset-password/form', [NewPasswordController::class, 'store']);


Route::get('/loginwith/{email?}', function ($id) {
    $user = \App\Models\User::findOrFail($id);
    // clear_setting();
    if ($user) {
        if (in_array(Auth::user()->role, [0, 1])) {
            $key = 'super_admin';
            if ($user->role == 1) {
                session()->put('super_admin', Auth::user());
            }
            Auth::loginUsingId($user->id);
        }
    }

    return redirect()->intended('/');
})->name('user.loginwith');

Route::get('/backtoadmin', function () {
    // clear_setting();
    if (session('super_admin') && !empty(session('super_admin')) && request()->has('admin')) {
        Auth::login(session('super_admin'));
        session()->forget('super_admin', '');
        session()->forget('company_admin', '');
    }

    if (session('company_admin') && !empty(session('company_admin')) && request()->has('company')) {
        Auth::login(session('company_admin'));
        session()->forget('company_admin', '');
    }

    return redirect()->intended('/');
})->name('backtoadmin');

Route::get('/cache', function () {
    Artisan::call('optimize:clear');
});
