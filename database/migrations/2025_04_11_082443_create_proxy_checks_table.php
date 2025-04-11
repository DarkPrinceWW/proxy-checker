<?php

declare(strict_types=1);

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
        Schema::create('proxy_checks', function(Blueprint $table) {
            $table->id();
            $table->foreignId('check_session_id')->constrained();
            $table->string('ip');
            $table->integer('port');
            $table->string('type')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->boolean('status');
            $table->integer('response_time')->nullable(); // Время ответа в миллисекундах
            $table->string('external_ip')->nullable();
            $table->integer('error_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_checks');
    }
};
