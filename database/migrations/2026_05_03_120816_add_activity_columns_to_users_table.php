<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('total_searches')->default(0)->after('password');
            $table->unsignedInteger('time_spent')->default(0)->after('total_searches')->comment('Seconds spent on site');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_searches', 'time_spent']);
        });
    }
};
