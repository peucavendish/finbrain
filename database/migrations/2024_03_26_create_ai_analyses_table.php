<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('age');
            $table->json('health_conditions');
            $table->json('lifestyle_factors');
            $table->json('family_history');
            $table->string('occupation');
            $table->decimal('income', 12, 2);
            $table->float('risk_score');
            $table->text('recommendation');
            $table->decimal('suggested_coverage', 12, 2);
            $table->decimal('monthly_premium_estimate', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_analyses');
    }
}; 