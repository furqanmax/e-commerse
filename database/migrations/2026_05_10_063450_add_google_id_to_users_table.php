<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('google_id')->nullable()->after('email');
            $table->string('google_token')->nullable()->after('google_id');
            $table->timestamp('google_token_expires_at')->nullable()->after('google_token');
            
            // Add indexes for better performance
            $table->index('google_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['google_id']);
            $table->dropColumn(['google_id', 'google_token', 'google_token_expires_at']);
        });
    }
};