<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar')->nullable();

            $table->timestamp('last_login')->nullable();
            $table->string('last_ip')->nullable();
            $table->integer('login_count')->default(0);

            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('locale')->default('en');
            $table->string('currency')->default('USD');

            $table->string('ip_address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('registration_ip')->nullable();
            $table->string('browser')->nullable(); // e.g. Chrome or Firefox
            $table->string('platform')->nullable(); // e.g. Windows or Linux
            $table->string('device')->nullable(); // e.g. Desktop or Smartphone

            $table->string('mobile')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->text('bio')->nullable();



            $table->tinyInteger('status')->default(1)->unsigned();
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_at')->nullable();
            $table->text('ban_reason')->nullable();

            $table->string('password');

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users table
            $table->string('provider'); // e.g., 'google', 'github'
            $table->string('provider_id'); // Provider's unique user ID
            $table->string('provider_token')->nullable(); // Optional: Store access token
            $table->string('avatar')->nullable(); // Store provider avatar if available
            $table->string('name')->nullable(); // Store user name if available
            $table->string('nickname')->nullable(); // Store user nickname if available
            $table->string('token')->nullable(); // Store user token if available
            $table->timestamps();

            $table->unique(['provider', 'provider_id']); // Ensure unique provider-user combination
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('providers');
    }
};
