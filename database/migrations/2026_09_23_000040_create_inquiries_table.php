<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 40);
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->date('event_date')->nullable();
            $table->unsignedInteger('guests')->nullable();
            $table->text('notes')->nullable();
            $table->string('flavour')->nullable();
            $table->string('size', 80)->nullable();
            $table->string('message_on_cake')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
