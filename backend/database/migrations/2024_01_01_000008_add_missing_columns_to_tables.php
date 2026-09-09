<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('image')->nullable()->after('description');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('logo')->nullable()->after('description');
            $table->string('website')->nullable()->after('logo');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('status')->default('active')->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['description', 'image']);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['description', 'logo', 'website']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
