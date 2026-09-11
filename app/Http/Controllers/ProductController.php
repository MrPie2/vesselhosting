<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Services\ProductService;

class ProductController
{
public function showHello(ProductService $productService){
    $data=$productService->getdata();
    return view('/dashboard/products/index', $data);
}

}