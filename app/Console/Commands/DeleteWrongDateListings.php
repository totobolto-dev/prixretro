<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteWrongDateListings extends Command
{
    protected $signature = 'listings:delete-wrong-dates
                            {date : The incorrect sold_date to delete (format: YYYY-MM-DD)}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Delete listings with incorrect sold_date (from scraper bug)';

    public function handle()
    {
        $date = $this->argument('date');

        $this->warn('🔍 Searching for listings with sold_date = ' . $date);
        $this->newLine();

        // Get count
        $count = DB::table('listings')->where('sold_date', $date)->count();

        if ($count === 0) {
            $this->info('✅ No listings found with that date.');
            return Command::SUCCESS;
        }

        // Show breakdown by console
        $breakdown = DB::table('listings')
            ->select('console_slug', DB::raw('count(*) as count'))
            ->where('sold_date', $date)
            ->groupBy('console_slug')
            ->orderByDesc('count')
            ->get();

        $this->info("Found {$count} listings to delete:");
        $this->newLine();

        foreach ($breakdown as $item) {
            $this->line("  {$item->console_slug}: {$item->count} items");
        }

        $this->newLine();

        // Show status breakdown
        $statusBreakdown = DB::table('listings')
            ->select('status', DB::raw('count(*) as count'))
            ->where('sold_date', $date)
            ->groupBy('status')
            ->get();

        $this->info("Status breakdown:");
        foreach ($statusBreakdown as $item) {
            $this->line("  {$item->status}: {$item->count} items");
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN - No data was deleted');
            return Command::SUCCESS;
        }

        // Confirm deletion
        if (!$this->confirm("Delete all {$count} listings with sold_date = {$date}?", false)) {
            $this->info('❌ Deletion cancelled');
            return Command::SUCCESS;
        }

        // Delete
        $deleted = DB::table('listings')->where('sold_date', $date)->delete();

        $this->newLine();
        $this->info("✅ Deleted {$deleted} listings");

        return Command::SUCCESS;
    }
}
