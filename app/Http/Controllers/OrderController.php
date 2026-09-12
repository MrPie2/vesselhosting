<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController
{
    public function index() {
        $orders = Auth::user()->orders()->latest()->paginate(15);
        return view('dashboard.orders.index', compact('orders'));
    }

    public function show(Order $order) {
        abort_unless($order->user_id === Auth::id(),403);
        $order->load('items');
        return view('dashboard.orders.show', compact('order'));
    }
}