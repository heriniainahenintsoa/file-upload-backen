<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->string('image_name')->nullable();
            $table->string('image_url')->default(asset('images/users/user-default.png'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image_path');
            $table->dropColumn('image_name');
            $table->dropColumn('image_url');
        });
    }
};
