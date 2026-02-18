<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->createUsersTable();
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }

    private function createUsersTable(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('username');
            $table->string('email');
            $table->timestampTz('email_verified_at')
                ->nullable();
            $table->string('password');
            $table->string('password_fingerprint', 64)
                ->nullable();
            $table->rememberToken();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['tenant_id']);
            $table->index(['tenant_id', 'username']);
            $table->index(['tenant_id', 'email']);
            $table->index(['tenant_id', 'password_fingerprint']);
        });
    }
};
