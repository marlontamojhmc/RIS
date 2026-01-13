<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_function', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->string('function');
            $table->timestamps();

            // ✅ Add the foreign key constraint
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade'); // or ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // ✅ Drop the foreign key first before dropping the table
        Schema::table('user_function', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        Schema::dropIfExists('user_function');
    }
};
