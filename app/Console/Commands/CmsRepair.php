<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\CMS\Models\Post;

class CmsRepair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:repair {--type=all : The type of repair to run (all, posts, reactions, views)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repair out-of-sync counters and caches across the CMS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        if ($type === 'all' || $type === 'posts') {
            $this->info('Syncing post counts for tags and categories...');
            Post::syncCounts();
            $this->info('Post counts synced.');
        }

        if ($type === 'all' || $type === 'views') {
            $this->info('Recalculating post view counts...');
            DB::statement('UPDATE posts p SET views_count = (SELECT COUNT(*) FROM post_views pv WHERE pv.post_id = p.id AND pv.is_unique = 1)');
            $this->info('Post view counts synced.');
        }

        if ($type === 'all' || $type === 'reactions') {
            $this->info('Recalculating reaction breakdowns...');
            
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

                $post->reactions_breakdown = empty($breakdown) ? null : $breakdown;
                $post->reactions_count = $totalReactions;
                $post->saveQuietly();
            }
            
            $this->info('Reactions recalculated.');
        }

        $this->info('Repair complete.');
    }
}
