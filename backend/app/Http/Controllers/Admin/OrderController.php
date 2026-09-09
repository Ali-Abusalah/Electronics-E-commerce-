<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('orders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('orders.order_number', 'like', "%{$search}%")
                    ->orWhere('orders.customer_name', 'like', "%{$search}%")
                    ->orWhere('orders.customer_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('orders.status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('orders.created_at', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->filled('date_to')) {
            $query->where('orders.created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $orders = $query->orderBy('orders.created_at', 'desc')->paginate(15);
        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (! $order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        $items = DB::table('order_items')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->select('order_items.*', 'products.name as product_name', 'products.image as product_image')
            ->where('order_items.order_id', $id)
            ->get();

        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        return view('admin.orders.show', compact('order', 'items', 'statuses'));
    }

    public function edit($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        if (! $order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        return view('admin.orders.edit', compact('order', 'statuses'));
    }

    /**
     * Download the tax invoice (PDF) of an order for printing and stamping.
     * Only issued when the order contains at least one purchased item.
     */
    public function invoice($id)
    {
        $order = Order::with('items')->find($id);

        if (! $order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }        try {
            return app(InvoiceService::class)->pdf($order);
        } catch (RuntimeException $e) {
            return redirect()->route('admin.orders.show', $id)
                ->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        if (! $order) {
            return redirect()->route('admin.orders.index')->with('error', 'Order not found.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);

        $validated['updated_at'] = now();

        DB::table('orders')->where('id', $id)->update($validated);

        return redirect()->route('admin.orders.show', $id)->with('success', 'Order status updated successfully.');
    }
}
