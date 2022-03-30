<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->string('id', 21)->primary();
            $table->string('username', 16)->unique();
            $table->string('password');
            $table->string('nickname', 20)->nullable();
            $table->unsignedBigInteger('qq')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('become_member_at')->nullable();
            $table->timestamp('whitelisted_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
