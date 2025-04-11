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
        Schema::create('proxies', function(Blueprint $table) {
            $table->id();
            $table->foreignId('check_session_id')->constrained();
            $table->string('ip');
            $table->unsignedInteger('port');
            $table->string('type')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('status');
            $table->unsignedInteger('response_time')->nullable()->comment('Время ответа в миллисекундах');
            $table->string('external_ip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxies');
    }
};
