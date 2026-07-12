<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class CmsDoctor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:doctor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform health checks on the CMS database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running CMS Health Checks...');

        $this->checkOrphanReactions();
        $this->checkOrphanPostViews();
        $this->checkOrphanActivityLogs();
        $this->checkPostsWithoutPrimaryCategory();
        $this->checkCounterMismatches();

        $this->info('Health checks completed.');
    }

    protected function checkOrphanReactions()
    {
        $count = DB::table('reactions')
            ->leftJoin('posts', 'reactions.post_id', '=', 'posts.id')
            ->whereNull('posts.id')
            ->count();

        if ($count > 0) {
            $this->warn("⚠ Found {$count} orphan reactions (posts deleted).");
        } else {
            $this->info('✔ Reactions OK');
        }
    }

    protected function checkOrphanPostViews()
    {
        $count = DB::table('post_views')
            ->leftJoin('posts', 'post_views.post_id', '=', 'posts.id')
            ->whereNull('posts.id')
            ->count();

        if ($count > 0) {
            $this->warn("⚠ Found {$count} orphan post_views.");
        } else {
            $this->info('✔ Post Views OK');
        }
    }

    protected function checkOrphanActivityLogs()
    {
        $count = DB::table('activity_log')
            ->where('subject_type', 'App\Models\Post')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('posts')
                    ->whereColumn('posts.id', 'activity_log.subject_id');
            })
            ->count();

        if ($count > 0) {
            $this->warn("⚠ Found {$count} orphan activity logs for Posts.");
        } else {
            $this->info('✔ Activity Logs OK');
        }
    }

    protected function checkPostsWithoutPrimaryCategory()
    {
        $count = Post::whereNull('primary_category_id')->count();

        if ($count > 0) {
            $this->warn("⚠ Found {$count} posts without a primary category.");
        } else {
            $this->info('✔ Posts OK');
        }
    }

    protected function checkCounterMismatches()
    {
        $mismatches = 0;
        
        $posts = Post::all();
        foreach ($posts as $post) {
            $totals = DB::table('reactions')
                ->select('type', DB::raw('count(*) as count'))
                ->where('post_id', $post->id)
                ->groupBy('type')
                ->get();

            $breakdown = [];
            $totalReactions = 0;

            foreach ($totals as $row) {
                $breakdown[$row->type] = (int) $row->count;
                $totalReactions += (int) $row->count;
            }

            $emptyBreakdown = empty($breakdown) ? null : $breakdown;

            // Sort arrays for strict equality check
            $currentBreakdown = $post->reactions_breakdown;
            if (is_array($currentBreakdown)) ksort($currentBreakdown);
            if (is_array($emptyBreakdown)) ksort($emptyBreakdown);

            if ($post->reactions_count !== $totalReactions || $currentBreakdown !== $emptyBreakdown) {
                $mismatches++;
            }
        }

        if ($mismatches > 0) {
            $this->warn("⚠ Reactions mismatch ({$mismatches}). Run `php artisan cms:repair --type=reactions` to fix.");
        } else {
            $this->info('✔ Reactions Counts OK');
        }
    }
}
