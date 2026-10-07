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
use App\Http\Controllers\ResellerClubController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NewsletterController;

use Illuminate\Support\Facades\Route;

Route::get('/', fn()=>view('landing'))->name('home');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/domain-search',[DomainSearchController::class,'search'])->name('domain.search');
Route::get('/domain-av',[ResellerClubController::class,'check'])->name('domain.av');
Route::get('/domain-suggestions',[ResellerClubController::class,'suggestions'])->name('domain.suggestions');

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
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/domain', [CartController::class, 'addDomain'])->name('cart.domain');
Route::delete('/cart/{key}', [CartController::class, 'remove'])->name('cart.remove');


Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function(){
    Route::get('/',[CheckoutController::class,'show'])->name('show');
    Route::post('/',[CheckoutController::class,'placeOrder'])->name('place');
});

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function(){
    Route::get('/',[ClientController::class,'index'])->name('home');

    Route::get('/domains',[DomainController::class,'index'])->name('domains');
    Route::get('/domains/{domain}',[DomainController::class,'show'])->name('domains.show');
    Route::post('/domains/{domain}/renew',[DomainController::class,'renew'])->name('domains.renew');
    Route::post('/domains/{domain}/nameservers',[DomainController::class,'updateNameservers'])->name('domains.nameservers');
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
Route::get('/admin',[AdminController::class,'index'])->name('home');

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/admin',[AdminController::class,'index'])->name('home');
    Route::get('/customers',[AdminController::class,'customers'])->name('customers');
    Route::get('/orders',[AdminController::class,'orders'])->name('orders');

    Route::get('/domains',[AdminController::class,'domains'])->name('domains');
    Route::get('/hosting',[AdminController::class,'hosting'])->name('hosting');
    Route::get('/plans',[AdminController::class,'plans'])->name('plans');
    Route::patch('/plans/{plan}',[AdminController::class,'updatePlan'])->name('plans.update');
    Route::get('/domain-prices',[AdminController::class,'domainPrices'])->name('domain-prices');
    Route::post('/domain-prices',[AdminController::class,'storeDomainPrice'])->name('domain-prices.store');
    Route::patch('/domain-prices/{domainPrice}',[AdminController::class,'updateDomainPrice'])->name('domain-prices.update');
});