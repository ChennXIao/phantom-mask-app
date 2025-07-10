<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('weekday');
            $table->time('open_time');
            $table->time('close_time');
            $table->timestamps();

            $table->index(['pharmacy_id', 'weekday', 'open_time','close_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_hours');
    }
};
