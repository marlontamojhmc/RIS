<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();

            // Form name (e.g., "Leave Form", "Overtime Form")
            $table->string('name');

            // Optional description of what this form is for
            $table->text('description')->nullable();

            // Points to the approver group for this form
            $table->foreignId('approver_group_id')
                  ->constrained('approver_groups')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
