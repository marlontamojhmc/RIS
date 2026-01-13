<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('application_for_approval', function (Blueprint $table) {
            $table->id();

            // Link to the application/transaction
            $table->foreignId('application_id')
                  ->constrained('application_forms') // your application table
                  ->cascadeOnDelete();

            // Link to the group/stage in the approval flow
            $table->foreignId('approver_group_id')
                  ->constrained('approver_groups')
                  ->cascadeOnDelete();

            // The actual approver (user)
            // $table->foreignId('approver_id')
            //       ->constrained('users')
            //       ->cascadeOnDelete();

            // Current status of this approver's action
            $table->enum('status', ['Pending', 'Approved', 'Returned'])
                  ->default('Pending');

            // Optional remarks when returning
            $table->text('remark')->nullable();

            // Timestamp when action was taken
            $table->timestamp('acted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_for_approval');
    }
};
