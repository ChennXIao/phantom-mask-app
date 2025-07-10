<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->dateTime('purchased_at');
            $table->timestamps();

            $table->index(['customer_id', 'purchased_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
