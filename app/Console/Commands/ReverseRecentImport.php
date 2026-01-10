<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReverseRecentImport extends Command
{
    protected $signature = 'listings:reverse-import
                            {--minutes=10 : Delete listings created in the last X minutes}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Reverse a recent import by deleting recently created listings';

    public function handle()
    {
        $minutes = $this->option('minutes');
        $cutoffTime = Carbon::now()->subMinutes($minutes);

        $this->warn("🔍 Searching for listings created after {$cutoffTime->format('Y-m-d H:i:s')}");
        $this->newLine();

        // Get count
        $count = DB::table('listings')
            ->where('created_at', '>', $cutoffTime)
            ->count();

        if ($count === 0) {
            $this->info('✅ No recent listings found.');
            return Command::SUCCESS;
        }

        // Show breakdown by console
        $breakdown = DB::table('listings')
            ->select('console_slug', DB::raw('count(*) as count'))
            ->where('created_at', '>', $cutoffTime)
            ->groupBy('console_slug')
            ->orderByDesc('count')
            ->get();

        $this->info("Found {$count} listings created in the last {$minutes} minutes:");
        $this->newLine();

        foreach ($breakdown as $item) {
            $consoleSlug = $item->console_slug ?: 'NULL';
            $this->line("  {$consoleSlug}: {$item->count} items");
        }

        $this->newLine();

        // Show status breakdown
        $statusBreakdown = DB::table('listings')
            ->select('status', DB::raw('count(*) as count'))
            ->where('created_at', '>', $cutoffTime)
            ->groupBy('status')
            ->get();

        $this->info("Status breakdown:");
        foreach ($statusBreakdown as $item) {
            $this->line("  {$item->status}: {$item->count} items");
        }

        $this->newLine();

        // Show sample titles
        $samples = DB::table('listings')
            ->select('title')
            ->where('created_at', '>', $cutoffTime)
            ->limit(10)
            ->get();

        $this->info("Sample titles (first 10):");
        foreach ($samples as $sample) {
            $this->line("  - " . substr($sample->title, 0, 80));
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN - No data was deleted');
            return Command::SUCCESS;
        }

        // Confirm deletion
        if (!$this->confirm("Delete all {$count} listings created in the last {$minutes} minutes?", false)) {
            $this->info('❌ Deletion cancelled');
            return Command::SUCCESS;
        }

        // Delete
        $deleted = DB::table('listings')
            ->where('created_at', '>', $cutoffTime)
            ->delete();

        $this->newLine();
        $this->info("✅ Deleted {$deleted} listings");

        return Command::SUCCESS;
    }
}
