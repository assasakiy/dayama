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
        Schema::table('post_views', function (Blueprint $table) {
            $table->string('session_id')->nullable()->after('user_id');
            $table->string('identity_key')->after('session_id')->index();
            $table->string('country')->nullable()->after('source');
            $table->string('city')->nullable()->after('country');
            $table->string('device')->nullable()->after('city');
            $table->string('browser')->nullable()->after('device');
            $table->string('os')->nullable()->after('browser');
            $table->boolean('is_unique')->default(false)->after('os')->index();
            
            $table->date('view_date')->after('is_unique')->index();
            
            $table->unique(['post_id', 'identity_key', 'view_date'], 'view_unique_identity_window');
        });

        Schema::table('reactions', function (Blueprint $table) {
            $table->string('session_id')->nullable()->after('user_id');
            $table->string('identity_key')->after('session_id')->index();
            
            $table->unique(['post_id', 'identity_key', 'type'], 'reaction_unique_identity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_views', function (Blueprint $table) {
            $table->dropUnique('view_unique_identity_window');
            $table->dropColumn([
                'session_id', 'identity_key', 'country', 'city', 
                'device', 'browser', 'os', 'is_unique', 'view_date'
            ]);
        });

        Schema::table('reactions', function (Blueprint $table) {
            $table->dropUnique('reaction_unique_identity');
            $table->dropColumn(['session_id', 'identity_key']);
        });
    }
};
