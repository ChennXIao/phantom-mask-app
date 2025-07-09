<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['pharmacy_id', 'name']);
            $table->index(['pharmacy_id', 'price']);
            $table->index(['pharmacy_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masks');
    }
};
