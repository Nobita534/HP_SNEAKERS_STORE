<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::whereIn('status', ['completed', 'delivered'])->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'monthly_revenue' => Order::whereIn('status', ['completed', 'delivered'])
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
        ];

        // Tính lãi: Doanh thu - Tổng nhập hàng
        $totalRevenue = $stats['total_revenue'];
        $totalImported = InventoryTransaction::sum('total_cost');
        $totalProfit = $totalRevenue - $totalImported;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Lãi tháng này
        $monthlyRevenue = $stats['monthly_revenue'];
        $monthlyImported = InventoryTransaction::whereMonth('created_at', now()->month)
            ->sum('total_cost');
        $monthlyProfit = $monthlyRevenue - $monthlyImported;

        $stats['total_imported'] = $totalImported;
        $stats['total_profit'] = $totalProfit;
        $stats['profit_margin'] = $profitMargin;
        $stats['monthly_profit'] = $monthlyProfit;
        $stats['monthly_imported'] = $monthlyImported;

        // Doanh thu 7 ngày gần đây
        $revenueChart = Order::whereIn('status', ['completed', 'delivered'])
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Đơn hàng gần đây
        $recent_orders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'revenueChart', 'recent_orders'));
    }
}
