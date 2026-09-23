<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('label')->nullable();
            $table->string('type', 20); // percent | fixed
            $table->unsignedInteger('value');
            $table->unsignedInteger('min_subtotal')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('discount', 10, 2)->default(0)->after('fee');
            $table->string('promo_code', 40)->nullable()->after('discount');
            $table->string('payment_status', 32)->default('unpaid')->after('payment_method');
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->json('options')->nullable()->after('line_total');
            $table->string('image')->nullable()->after('options');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn(['options', 'image']);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['discount', 'promo_code', 'payment_status']);
        });

        Schema::dropIfExists('promos');
    }
};
