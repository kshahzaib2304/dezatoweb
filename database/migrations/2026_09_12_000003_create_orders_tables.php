<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('number')->unique();
            $table->string('status', 32);
            $table->string('method', 32);
            $table->string('location_id')->nullable();
            $table->string('location_name')->nullable();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone', 40);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region', 64)->nullable();
            $table->string('postal_code', 32)->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_method', 64);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamp('placed_at');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('product_id');
            $table->string('product_name');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedSmallInteger('quantity');
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
