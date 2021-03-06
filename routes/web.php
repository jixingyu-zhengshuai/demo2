<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaticPagesController as StaticPages;
use App\Http\Controllers\UsersController as Users;
use App\Http\Controllers\SessionsController as Sessions;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

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

//渲染首页
Route::get('/',[StaticPages::class,'home']) -> name('home');

//渲染帮助页面
Route::get('/help',[StaticPages::class,'help']) ->name('help');

//渲染关于我们页面
Route::get('/about',[StaticPages::class,'about']) -> name('about');


//与用户相关的路由start

//注册用户路由
Route::get('signup',[Users::class,'create']) -> name('signup');

/*
    创建了7个路由
    请求方式            URL                      动作                         作用
    GET                /users                   UsersController@index        显示所有用户列表的页面
    GET                /users/{user}            UsersController@show         显示用户个人信息的页面
    GET                /users/create            UsersController@create       创建用户的页面
    POST               /users                   UsersController@store        创建用户
    GET                /users/{user}/edit       UsersController@edit         编辑用户个人资料的页面
    PATCH              /users/{user}            UsersController@update       更新用户
    DELETE             /users/{user}            UsersController@destroy      删除用户
*/
Route::resource('users','UsersController');

//渲染登录页面
Route::get('login',[Sessions::class,'create']) -> name('login');
//实现用户登录功能
Route::post('login',[Sessions::class,'store']) -> name('login');

//实现用户退出
Route::delete('logout',[Sessions::class,'destroy']) -> name('logout');

//发送邮件
Route::get('signup/confirm/{token}',[Users::class,'confirmEmail'])->name('confirm_email');
//与用户相关的路由end

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');


Route::resource('statuses','StatusesController',['only' => ['store','destroy']]);

Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');

Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');