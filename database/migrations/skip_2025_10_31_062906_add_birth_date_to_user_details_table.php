<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('phone'); 
            // or after any column that makes sense in your schema
        });
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('birth_date');
        });
    }
};
