<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with orders list and aggregate statistics.
     * Uses Nested Eager Loading to stay well under 10 queries per page load.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $cityId = $request->integer('city_id') ?: null;

        $orders = Order::with([
            'customer.city',
            'orderItems.menu.category',
            'paymentMethod',
            'courier',
        ])
        ->when($search, function ($query, string $search): void {
            $query->whereHas('customer', function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('city', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            })->orWhere('id', 'like', "%{$search}%");
        })
        ->when($status, fn ($q) => $q->where('status', $status))
        ->when($cityId, fn ($q) => $q->whereHas('customer', fn ($cq) => $cq->where('city_id', $cityId)))
        ->latest()
        ->paginate(15)
        ->withQueryString();

        // Aggregate statistics — computed independently so pagination doesn't affect them.
        $totalOrders = Order::count();
        $totalRevenue = OrderItem::sum('subtotal');
        $pendingCount = Order::where('status', 'pending')->count();
        $averageOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $stats = compact('totalOrders', 'totalRevenue', 'pendingCount', 'averageOrder');

        $cities = City::orderBy('name')->get(['id', 'name']);

        return view('dashboard', compact('orders', 'stats', 'cities', 'search', 'status', 'cityId'));
    }
}
