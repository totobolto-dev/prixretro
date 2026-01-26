<?php

namespace App\Console\Commands;

use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MatchVariantImages extends Command
{
    protected $signature = 'variants:match-images {--dry-run : Preview matches without saving} {--auto : Auto-match high-confidence matches}';
    protected $description = 'Match scraped images to variant records using fuzzy matching';

    public function handle()
    {
        $this->info('🔍 Scanning for variant images...');

        // Get all images in storage/variants/
        $imageFiles = collect(glob(public_path('storage/variants/*')))
            ->map(fn($path) => basename($path))
            ->filter(fn($name) => preg_match('/\.(png|jpg|jpeg|webp|avif)$/i', $name));

        $this->info("📊 Found {$imageFiles->count()} images");
        $this->newLine();

        // Get all variants
        $variants = Variant::with('console')->get();
        $this->info("📊 Found {$variants->count()} variants in database");
        $this->newLine();

        $matches = [];
        $unmatched = [];

        foreach ($imageFiles as $imageFile) {
            $match = $this->findBestMatch($imageFile, $variants);

            if ($match) {
                $matches[] = [
                    'image' => $imageFile,
                    'variant' => $match['variant'],
                    'score' => $match['score'],
                    'reason' => $match['reason'],
                ];
            } else {
                $unmatched[] = $imageFile;
            }
        }

        // Display results
        $this->info("✅ Matched: " . count($matches));
        $this->info("❌ Unmatched: " . count($unmatched));
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->showPreview($matches, $unmatched);
            return 0;
        }

        // Show preview and confirm
        $this->showPreview($matches, $unmatched, 20);

        if (!$this->option('auto') && !$this->confirm('Apply these matches to database?')) {
            $this->warn('Cancelled.');
            return 0;
        }

        // Apply matches
        $updated = 0;
        foreach ($matches as $match) {
            $variant = $match['variant'];

            if (!$variant->image_filename) {
                $variant->image_filename = $match['image'];
                $variant->save();
                $updated++;
            }
        }

        $this->info("✅ Updated $updated variants with images");

        return 0;
    }

    private function findBestMatch(string $imageFile, $variants)
    {
        $imageName = pathinfo($imageFile, PATHINFO_FILENAME);
        $imageName = strtolower($imageName);

        // Extract console prefix (e.g., "game-boy-color_" from filename)
        preg_match('/^([a-z0-9-]+)_/', $imageName, $consoleMatch);
        $consolePrefix = $consoleMatch[1] ?? null;

        $bestMatch = null;
        $bestScore = 0;

        foreach ($variants as $variant) {
            $score = $this->calculateMatchScore($imageName, $variant, $consolePrefix);

            if ($score > $bestScore && $score >= 50) { // Minimum 50% match
                $bestScore = $score;
                $bestMatch = $variant;
            }
        }

        if ($bestMatch) {
            return [
                'variant' => $bestMatch,
                'score' => $bestScore,
                'reason' => $this->getMatchReason($bestScore),
            ];
        }

        return null;
    }

    private function calculateMatchScore(string $imageName, Variant $variant, ?string $consolePrefix): int
    {
        $score = 0;

        $variantName = strtolower($variant->name);
        $consoleName = strtolower($variant->console->slug);

        // CRITICAL: Console MUST match, otherwise return 0
        if (!$consolePrefix || $consolePrefix !== $consoleName) {
            return 0; // No match if console doesn't match
        }

        // Console matched - start with strong base score
        $score += 60;

        // Variant name appears in image name
        $cleanVariantName = Str::slug($variantName);
        if (str_contains($imageName, $cleanVariantName)) {
            $score += 50;
        } else {
            // Fuzzy match - check individual words
            $variantWords = explode('-', $cleanVariantName);
            $matchedWords = 0;

            foreach ($variantWords as $word) {
                if (strlen($word) > 2 && str_contains($imageName, $word)) {
                    $matchedWords++;
                }
            }

            if (count($variantWords) > 0) {
                $wordMatchPercent = ($matchedWords / count($variantWords)) * 30;
                $score += $wordMatchPercent;
            }
        }

        // Bonus for exact slug match
        if (str_contains($imageName, $variant->slug)) {
            $score += 10;
        }

        return (int) $score;
    }

    private function getMatchReason(int $score): string
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 70) return 'Good';
        if ($score >= 50) return 'Fair';
        return 'Weak';
    }

    private function showPreview(array $matches, array $unmatched, ?int $limit = null)
    {
        $this->info('=== MATCHED ===');
        $this->newLine();

        $shown = 0;
        foreach ($matches as $match) {
            if ($limit && $shown >= $limit) {
                $this->info("... and " . (count($matches) - $limit) . " more");
                break;
            }

            $variant = $match['variant'];
            $score = $match['score'];
            $reason = $match['reason'];

            $this->line(sprintf(
                '<fg=green>✓</> <fg=cyan>%s</> → %s / %s <fg=yellow>(%d%% - %s)</>',
                $match['image'],
                $variant->console->name,
                $variant->name,
                $score,
                $reason
            ));

            $shown++;
        }

        $this->newLine();

        if (count($unmatched) > 0) {
            $this->warn('=== UNMATCHED ===');
            $this->newLine();

            $shown = 0;
            foreach ($unmatched as $image) {
                if ($limit && $shown >= 10) {
                    $this->warn("... and " . (count($unmatched) - 10) . " more");
                    break;
                }

                $this->line("<fg=red>✗</> $image");
                $shown++;
            }

            $this->newLine();
        }
    }
}
