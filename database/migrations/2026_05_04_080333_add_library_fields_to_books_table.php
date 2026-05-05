<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('cover_image')->nullable();
            $table->string('pdf_file')->nullable();
            $table->integer('total_copies')->default(0);
            $table->integer('available_copies')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'pdf_file', 'total_copies', 'available_copies']);
        });
    }
};
