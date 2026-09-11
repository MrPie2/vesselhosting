<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController
{
 public function index(){
    $hosting_plan = Plan::get();
    return view('/dashboard/products/index', compact('hosting_plan'));
 }
 public function single_prod($id){
    $plan = Plan::where('id',$id)->first();
    return view('/dashboard/products/single-product', compact('plan'));
 }   
}
