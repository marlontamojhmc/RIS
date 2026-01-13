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
        Schema::create('approver_group_approver', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approver_group_id')
                ->constrained('approver_groups')
                ->onDelete('cascade');

            $table->foreignId('approver_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Optional: if you want to associate this approver group to a specific application form
            $table->foreignId('application_form_id')
                ->nullable()
                ->constrained('application_forms')
                ->onDelete('cascade');

            // To determine the order of approval (1 = first approver, 2 = second approver, etc.)
            $table->integer('sequence')->default(1);
            
            // Optional: status or role of the approver in the group
            $table->string('role')->nullable(); // e.g. 'Primary', 'Backup', 'Final'

            $table->timestamps();

            // Prevent duplicate entries of same approver in the same group & form
            $table->unique(['approver_group_id', 'approver_id', 'application_form_id'], 'group_approver_form_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approver_group_approver');
    }
};
