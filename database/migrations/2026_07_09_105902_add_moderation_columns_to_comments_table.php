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
        Schema::table('comments', function (Blueprint $table) {
            // Identity & struktur
            $table->string('identity_key')->nullable()->after('author_id')->index();
            $table->boolean('created_as_guest')->default(false)->after('identity_key');
            $table->unsignedTinyInteger('depth')->default(0)->after('parent_id');

            // Moderasi
            $table->unsignedSmallInteger('moderation_score')->nullable()->after('status');
            $table->json('moderation_flags')->nullable()->after('moderation_score');
            $table->timestamp('moderated_at')->nullable()->after('is_pinned');
            $table->foreignUuid('moderated_by')->nullable()->after('moderated_at')->constrained('users')->nullOnDelete();

            // Index optimisasi moderasi
            $table->index(['status', 'created_at']);
        });

        // Migrasi Data
        \Illuminate\Support\Facades\DB::table('comments')
            ->where('status', 'approved')
            ->update(['status' => \App\Enums\CommentStatus::Published->value]);

        \Illuminate\Support\Facades\DB::table('comments')
            ->where('status', 'pending')
            ->update(['status' => \App\Enums\CommentStatus::Review->value]);

        // Salin approved_at & approved_by -> moderated_at & moderated_by
        \Illuminate\Support\Facades\DB::statement('UPDATE comments SET moderated_at = approved_at, moderated_by = approved_by');

        // Drop kolom usang
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'approved_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable();
            $table->uuid('approved_by')->nullable();
        });

        \Illuminate\Support\Facades\DB::statement('UPDATE comments SET approved_at = moderated_at, approved_by = moderated_by');

        \Illuminate\Support\Facades\DB::table('comments')
            ->where('status', 'published')
            ->update(['status' => 'approved']);

        \Illuminate\Support\Facades\DB::table('comments')
            ->where('status', 'review')
            ->update(['status' => 'pending']);

        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropColumn([
                'identity_key',
                'created_as_guest',
                'depth',
                'moderation_score',
                'moderation_flags',
                'moderated_at',
                'moderated_by',
            ]);
        });
    }
};
