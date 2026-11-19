<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('price');
            $table->string('price_note')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('button_label')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
