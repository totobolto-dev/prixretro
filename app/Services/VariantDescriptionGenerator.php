<?php

namespace App\Services;

use App\Models\Variant;

class VariantDescriptionGenerator
{
    public static function generate(Variant $variant, array $statistics, array $statsByCompleteness = []): string
    {
        $console = $variant->console;
        $count = $statistics['count'] ?? 0;
        $avgPrice = $statistics['avg_price'] ?? 0;
        $minPrice = $statistics['min_price'] ?? 0;
        $maxPrice = $statistics['max_price'] ?? 0;

        // Calculate rarity based on listings count
        $avgListingsForConsole = $console->variants()
            ->withCount(['listings' => function($q) {
                $q->where('status', 'approved');
            }])
            ->get()
            ->avg('listings_count');

        $rarity = self::calculateRarity($count, $avgListingsForConsole);
        $priceRange = self::calculatePriceRange($avgPrice, $minPrice, $maxPrice);

        // Generate description
        $desc = self::buildDescription($variant, $count, $avgPrice, $minPrice, $maxPrice, $rarity, $priceRange, $statsByCompleteness);

        return $desc;
    }

    private static function calculateRarity(int $count, float $avgCount): string
    {
        if ($count == 0) return 'inconnue';

        $ratio = $count / max($avgCount, 1);

        if ($ratio < 0.3) return 'très rare';
        if ($ratio < 0.6) return 'rare';
        if ($ratio < 1.2) return 'courante';
        return 'très courante';
    }

    private static function calculatePriceRange(float $avg, float $min, float $max): string
    {
        if ($avg == 0) return 'varie selon l\'état';

        $spread = (($max - $min) / $avg) * 100;

        if ($spread < 30) return 'relativement stable';
        if ($spread < 60) return 'modérée selon l\'état';
        return 'très variable selon l\'état';
    }

    private static function buildDescription(
        Variant $variant,
        int $count,
        float $avgPrice,
        float $minPrice,
        float $maxPrice,
        string $rarity,
        string $priceRange,
        array $statsByCompleteness = []
    ): string {
        // Use display_name to avoid "Console Console" for default variants
        $fullName = $variant->display_name;

        if ($count == 0) {
            return "La {$fullName} est actuellement suivie sur PrixRetro. " .
                   "Nous collectons les données du marché de l'occasion pour vous offrir " .
                   "les meilleures informations sur les prix.";
        }

        // Build per-condition price text
        $priceTexts = [];
        $conditionLabels = [
            'loose' => '<span class="underline decoration-dotted cursor-help" title="Console seule, sans boîte ni accessoires">loose</span>',
            'cib' => '<span class="underline decoration-dotted cursor-help" title="Console complète en boîte avec accessoires et manuels (Complete In Box)">CIB</span>',
            'sealed' => '<span class="underline decoration-dotted cursor-help" title="Console neuve, encore sous cellophane">scellée</span>',
        ];

        foreach (['loose', 'cib', 'sealed'] as $condition) {
            if (isset($statsByCompleteness[$condition])) {
                $price = number_format($statsByCompleteness[$condition]['avg_price'], 0);
                $priceTexts[] = "{$price}€ en " . $conditionLabels[$condition];
            }
        }

        if (count($priceTexts) > 0) {
            $desc = "La {$fullName} a été vendue en moyenne " . implode(', ', $priceTexts) . " sur eBay. ";
        } else {
            // Fallback to overall average if no condition stats
            $desc = "La {$fullName} a été vendue en moyenne à " .
                    number_format($avgPrice, 0) . "€ sur eBay, " .
                    "avec des prix variant de " . number_format($minPrice, 0) . "€ " .
                    "à " . number_format($maxPrice, 0) . "€ selon l'état. ";
        }

        // Rarity context
        $rarityText = match($rarity) {
            'très rare' => "Cette variante est très rare sur le marché de l'occasion français. " .
                          "Avec seulement {$count} ventes enregistrées, c'est l'une des plus difficiles à trouver.",
            'rare' => "Cette variante est relativement rare sur le marché. " .
                     "Basé sur {$count} ventes analysées, elle nécessite de la patience pour être trouvée.",
            'courante' => "Cette variante est disponible régulièrement sur le marché de l'occasion. " .
                         "Avec {$count} ventes enregistrées, vous devriez pouvoir la trouver sans trop de difficulté.",
            'très courante' => "Cette variante est très courante et facile à trouver. " .
                              "Avec {$count} ventes enregistrées, de nombreuses offres sont disponibles.",
            default => "Basé sur {$count} ventes analysées sur eBay France."
        };

        $desc .= $rarityText;

        // Price advice
        if ($priceRange === 'très variable selon l\'état') {
            $desc .= " L'écart de prix important reflète la grande variabilité de l'état des consoles. " .
                    "Privilégiez les photos détaillées et les descriptions précises.";
        }

        return $desc;
    }
}
