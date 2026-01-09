<?php

namespace App\Services;

use App\Models\Console;

class ConsoleDescriptionGenerator
{
    public static function generate(Console $console): string
    {
        $variantsCount = $console->variants->count();
        $totalListings = $console->variants->sum('listings_count');

        if ($totalListings === 0) {
            return "La {$console->name} est actuellement suivie sur PrixRetro. " .
                   "Nous collectons les données du marché de l'occasion pour vous offrir " .
                   "les meilleures informations sur les prix de toutes les variantes.";
        }

        // Calculate price range across all variants
        $prices = [];
        foreach ($console->variants as $variant) {
            if ($variant->listings_count > 0) {
                $avgPrice = \App\Models\Listing::where('variant_id', $variant->id)
                    ->where('status', 'approved')
                    ->avg('price');
                if ($avgPrice) {
                    $prices[] = $avgPrice;
                }
            }
        }

        if (empty($prices)) {
            return "La {$console->name} est actuellement suivie sur PrixRetro. " .
                   "Nous collectons les données du marché de l'occasion pour vous offrir " .
                   "les meilleures informations sur les prix.";
        }

        $minPrice = min($prices);
        $maxPrice = max($prices);
        $avgPrice = array_sum($prices) / count($prices);

        $desc = "La {$console->name} est disponible en {$variantsCount} variantes sur le marché de l'occasion français. ";
        $desc .= "Basé sur {$totalListings} ventes analysées sur eBay, les prix moyens varient de " .
                 number_format($minPrice, 0) . "€ à " . number_format($maxPrice, 0) . "€ selon la couleur et l'état. ";

        // Add market context
        if ($totalListings > 100) {
            $desc .= "Avec un grand volume de ventes enregistrées, il est facile de trouver des offres pour cette console. ";
        } elseif ($totalListings > 30) {
            $desc .= "Cette console est régulièrement disponible sur le marché de l'occasion. ";
        } else {
            $desc .= "Cette console apparaît occasionnellement sur le marché de l'occasion. ";
        }

        $desc .= "Utilisez notre tracker de prix pour éviter de payer trop cher et trouver les meilleures opportunités.";

        return $desc;
    }
}
