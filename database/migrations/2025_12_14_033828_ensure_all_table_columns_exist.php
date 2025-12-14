<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration ensures all expected columns exist in all tables.
     * It's safe to run multiple times as it checks for column existence before adding.
     * 
     * Note: Columns are added without specific positioning to avoid issues if reference columns don't exist.
     */
    public function up(): void
    {
        // Users table
        if (Schema::hasTable('users')) {
            $this->addColumnIfMissing('users', 'username', function (Blueprint $table) {
                $table->string('username')->nullable();
            });
            $this->addColumnIfMissing('users', 'super_admin', function (Blueprint $table) {
                $table->boolean('super_admin')->default(false);
            });
            $this->addColumnIfMissing('users', 'email_verified_at', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable();
            });
            $this->addColumnIfMissing('users', 'remember_token', function (Blueprint $table) {
                $table->rememberToken();
            });
            $this->addTimestampsIfMissing('users');
        }

        // Projects table
        if (Schema::hasTable('projects')) {
            $this->addTimestampsIfMissing('projects');
        }

        // Project_user pivot table
        if (Schema::hasTable('project_user')) {
            $this->addColumnIfMissing('project_user', 'role', function (Blueprint $table) {
                $table->enum('role', ['admin', 'user'])->default('user');
            });
            $this->addTimestampsIfMissing('project_user');
        }

        // Emails table
        if (Schema::hasTable('emails')) {
            $this->addColumnIfMissing('emails', 'opens', function (Blueprint $table) {
                $table->unsignedInteger('opens')->default(0);
            });
            $this->addColumnIfMissing('emails', 'clicks', function (Blueprint $table) {
                $table->unsignedInteger('clicks')->default(0);
            });
            $this->addTimestampsIfMissing('emails');
        }

        // Email_recipients table
        if (Schema::hasTable('email_recipients')) {
            $this->addTimestampsIfMissing('email_recipients');
        }

        // Recipient_events table
        if (Schema::hasTable('recipient_events')) {
            $this->addColumnIfMissing('recipient_events', 'payload', function (Blueprint $table) {
                $table->json('payload')->nullable();
            });
            $this->addTimestampsIfMissing('recipient_events');
        }

        // Project_requests table
        if (Schema::hasTable('project_requests')) {
            // Add project_id column if missing (add as unsigned big integer to avoid foreign key constraint issues)
            // Foreign key constraints should be handled by the original migration
            $this->addColumnIfMissing('project_requests', 'project_id', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable();
            });
            $this->addColumnIfMissing('project_requests', 'rejection_reason', function (Blueprint $table) {
                $table->text('rejection_reason')->nullable();
            });
            $this->addColumnIfMissing('project_requests', 'approved_at', function (Blueprint $table) {
                $table->timestamp('approved_at')->nullable();
            });
            $this->addColumnIfMissing('project_requests', 'rejected_at', function (Blueprint $table) {
                $table->timestamp('rejected_at')->nullable();
            });
            $this->addTimestampsIfMissing('project_requests');
        }
    }

    /**
     * Add a column if it doesn't exist
     */
    private function addColumnIfMissing(string $table, string $column, callable $callback): void
    {
        if (!Schema::hasColumn($table, $column)) {
            Schema::table($table, $callback);
        }
    }

    /**
     * Add timestamps (created_at and updated_at) if they don't exist
     */
    private function addTimestampsIfMissing(string $table): void
    {
        $hasCreatedAt = Schema::hasColumn($table, 'created_at');
        $hasUpdatedAt = Schema::hasColumn($table, 'updated_at');
        
        if (!$hasCreatedAt || !$hasUpdatedAt) {
            Schema::table($table, function (Blueprint $table) use ($hasCreatedAt, $hasUpdatedAt) {
                if (!$hasCreatedAt) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!$hasUpdatedAt) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This doesn't remove columns as we want to preserve data.
     * If you need to rollback, create a specific migration.
     */
    public function down(): void
    {
        // Intentionally empty - we don't want to remove columns that might have data
    }
};
