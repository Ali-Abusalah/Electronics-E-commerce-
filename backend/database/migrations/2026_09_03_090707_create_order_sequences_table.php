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
        Schema::create('order_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('last_invoice_number')->default(0);
        });

        // Seed the counter from the highest invoice number already issued, so
        // the sequence continues without reusing any number.
        $last = DB::table('orders')
            ->whereNotNull('invoice_number')
            ->pluck('invoice_number')
            ->map(fn (string $no) => (int) substr($no, 4))
            ->max() ?? 0;

        DB::table('order_sequences')->insert([
            'id' => 1,
            'last_invoice_number' => $last,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_sequences');
    }
};
