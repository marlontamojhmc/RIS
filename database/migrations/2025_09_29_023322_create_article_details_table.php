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
        Schema::create('article_details', function (Blueprint $table) {
            $table->id();

            // 🔗 Relationship to application_forms
            $table->unsignedBigInteger('application_form_id'); 
            
            // 🔗 Relationship to users
            $table->unsignedBigInteger('user_id');

            // 📦 Article detail fields
            $table->string('marks_and_number')->nullable();
            $table->integer('qty')->default(0);
            $table->text('detailed_description_of_article')->nullable();
            $table->decimal('gross_weight', 10, 2)->nullable();

            $table->timestamps();

            // 🛠 Foreign key constraints
            $table->foreign('application_form_id')
                  ->references('id')->on('application_forms')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_details');
    }
};
