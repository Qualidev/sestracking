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
        if (Schema::hasTable('project_user')) {
            $hasCreatedAt = Schema::hasColumn('project_user', 'created_at');
            $hasUpdatedAt = Schema::hasColumn('project_user', 'updated_at');
            
            if (!$hasCreatedAt || !$hasUpdatedAt) {
                Schema::table('project_user', function (Blueprint $table) use ($hasCreatedAt, $hasUpdatedAt) {
                    if (!$hasCreatedAt) {
                        $table->timestamp('created_at')->nullable();
                    }
                    if (!$hasUpdatedAt) {
                        $table->timestamp('updated_at')->nullable();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('project_user')) {
            if (Schema::hasColumn('project_user', 'updated_at')) {
                Schema::table('project_user', function (Blueprint $table) {
                    $table->dropColumn('updated_at');
                });
            }
            if (Schema::hasColumn('project_user', 'created_at')) {
                Schema::table('project_user', function (Blueprint $table) {
                    $table->dropColumn('created_at');
                });
            }
        }
    }
};
