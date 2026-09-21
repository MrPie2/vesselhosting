<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DnsRecordController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\DomainSearchController;
use App\Http\Controllers\HostingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PlanController;
<<<<<<< HEAD
use App\Http\Controllers\ResellerClubController;
=======
use App\Http\Controllers\CreateAccountController;
>>>>>>> origin/main

use Illuminate\Support\Facades\Route;

Route::get('/', fn()=>view('landing'))->name('home');
Route::post('/domain-search',[DomainSearchController::class,'search'])->name('domain.search');
<<<<<<< HEAD
Route::get('/domain-av',[ResellerClubController::class,'check'])->name('domain.av');
=======
>>>>>>> origin/main

Route::middleware('guest')->group(function(){
    Route::get('/login',[AuthController::class,'showLogin'])->name('login');
    Route::post('/login',[AuthController::class,'login'])->name('login.store');
    Route::get('/register',[AuthController::class,'showRegister'])->name('register');
    Route::post('/register',[AuthController::class,'register'])->name('register.store');
});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');

Route::get('/regiter-domain', function(){
    return view('domain-register');
});

Route::post('/domain-s', [SearchController::class, 'search_domain'])->name('dashboard.domain-s');
Route::get('/dashboard/products/index', [PlanController::class, 'index'])->name('dashboard.products');
Route::get('/dashboard/products/single-product/{id}', [PlanController::class, 'single_prod'])->name('dashboard.single-product');
<<<<<<< HEAD
=======
Route::post('/create-account', [CreateAccountController::class, 'createhosting'])->name('dashboard.create');
>>>>>>> origin/main

Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function(){
    Route::get('/domain',[CheckoutController::class,'domain'])->name('domain');
    Route::post('/domain',[CheckoutController::class,'purchaseDomain'])->name('domain.purchase');
    Route::post('/hosting/{plan}',[CheckoutController::class,'hosting'])->name('hosting');
    Route::post('/bundle/{plan}',[CheckoutController::class,'bundle'])->name('bundle');
});

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function(){
    Route::get('/',[ClientController::class,'index'])->name('home');

    Route::get('/domains',[DomainController::class,'index'])->name('domains');
    Route::get('/domains/{domain}',[DomainController::class,'show'])->name('domains.show');
    Route::post('/domains/{domain}/renew',[DomainController::class,'renew'])->name('domains.renew');
    Route::post('/domains/{domain}/dns',[DnsRecordController::class,'store'])->name('dns.store');
    Route::delete('/domains/{domain}/dns/{record}',[DnsRecordController::class,'destroy'])->name('dns.destroy');

    Route::get('/hosting',[HostingController::class,'index'])->name('hosting');
    Route::get('/hosting/{hosting}',[HostingController::class,'show'])->name('hosting.show');

    Route::get('/orders',[OrderController::class,'index'])->name('orders');
    Route::get('/orders/{order}',[OrderController::class,'show'])->name('orders.show');

    Route::post('/payments/paystack/initialize/{order}',[PaymentController::class,'initialize'])->name('payments.initialize');
    Route::get('/payments/paystack/callback',[PaymentController::class,'callback'])->name('payments.callback');
});
Route::post('/payments/paystack/webhook',[PaymentController::class,'webhook'])->name('payments.paystack.webhook');

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/',[AdminController::class,'index'])->name('home');
    Route::get('/customers',[AdminController::class,'customers'])->name('customers');
    Route::get('/domains',[AdminController::class,'domains'])->name('domains');
    Route::get('/hosting',[AdminController::class,'hosting'])->name('hosting');
    Route::get('/plans',[AdminController::class,'plans'])->name('plans');
    Route::patch('/plans/{plan}',[AdminController::class,'updatePlan'])->name('plans.update');
});