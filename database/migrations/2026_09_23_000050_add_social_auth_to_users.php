<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('provider', 40)->nullable()->after('phone');
            $table->string('provider_id', 191)->nullable()->after('provider');
            $table->unique(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['provider', 'provider_id']);
            $table->dropColumn(['provider', 'provider_id']);
        });
    }
};
