<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ 1. Table for Approver Groups
        Schema::create('approver_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // e.g., "Finance Approvers", "Leave Approvers"
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ✅ 2. Pivot table linking approver groups to users (approvers)
        
    }

    public function down(): void
    {
        Schema::dropIfExists('approver_groups');
    }
};
