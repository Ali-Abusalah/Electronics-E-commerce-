<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = DB::table('products')->count();
        $totalOrders = DB::table('orders')->count();
        $totalRevenue = DB::table('orders')->where('status', 'completed')->sum('total');
        $totalCategories = DB::table('categories')->count();
        $totalBrands = DB::table('brands')->count();
        $outOfStock = DB::table('products')->where('stock', '<=', 0)->count();

        $latestOrders = DB::table('orders')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'totalCategories',
            'totalBrands',
            'outOfStock',
            'latestOrders'
        ));
    }
}
