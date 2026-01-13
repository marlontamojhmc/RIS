<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ceoc', function (Blueprint $table) {
            $table->id();

            // Foreign key to business_types table
            $table->foreignId('business_type_id')
                  ->constrained('business_types')
                  ->cascadeOnDelete();

            $table->string('accreditation_type'); // e.g. Accreditation, Reaccreditation
            $table->decimal('price', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ceoc');
    }
};
