<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Drop foreign key first (if exists)
            $table->dropForeign(['location_id']);

            // Drop the columns
            $table->dropColumn(['birth_date', 'phone', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Restore columns if you rollback
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();

            // Restore the foreign key
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }
};
