<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('invoice_number', 20)->nullable()->unique()->after('order_number');
        });

        // Backfill already-existing orders with sequential local invoice
        // numbers so invoices stay numbered continuously from the start.
        $counter = 0;
        $orders = DB::table('orders')->orderBy('id')->get(['id']);
        foreach ($orders as $order) {
            $counter++;
            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'invoice_number' => 'INV-'.str_pad((string) $counter, 6, '0', STR_PAD_LEFT),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
            $table->dropColumn('invoice_number');
        });
    }
};
