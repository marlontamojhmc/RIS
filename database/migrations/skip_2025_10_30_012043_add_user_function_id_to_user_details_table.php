<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Add user_function_id with foreign key
            $table->unsignedBigInteger('user_function_id')->nullable()->after('department_id');
            $table->foreign('user_function_id')->references('id')->on('user_functions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['user_function_id']);
            $table->dropColumn('user_function_id');
        });
    }
};
