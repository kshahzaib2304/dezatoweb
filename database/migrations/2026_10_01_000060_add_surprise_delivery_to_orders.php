<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->boolean('surprise_delivery')->default(false)->after('notes');
            $table->string('surprise_note', 280)->nullable()->after('surprise_delivery');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['surprise_delivery', 'surprise_note']);
        });
    }
};
