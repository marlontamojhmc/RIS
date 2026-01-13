<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('location', function (Blueprint $table) {
            $table->unsignedBigInteger('street_id')->nullable()->after('id');

            // Add foreign key constraint
            $table->foreign('street_id')
                  ->references('id')->on('streets')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('location', function (Blueprint $table) {
            $table->dropForeign(['street_id']);
            $table->dropColumn('street_id');
        });
    }
};
