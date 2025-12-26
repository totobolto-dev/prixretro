#!/usr/bin/env python3
"""
Auto-Classification Tool for PrixRetro
Automatically detects items in wrong variant categories and suggests corrections
"""

import json
import re
from typing import Dict, Tuple, Optional


class VariantClassifier:
    def __init__(self, config_path='config.json'):
        """Load configuration"""
        with open(config_path, 'r', encoding='utf-8') as f:
            self.config = json.load(f)

        # Build variant keyword patterns from config
        self.variant_patterns = self._build_variant_patterns()

    def _build_variant_patterns(self) -> Dict[str, list]:
        """
        Build keyword patterns for each variant based on config and common variations
        Returns dict: {variant_key: [list of keywords/phrases]}
        """
        patterns = {
            # Atomic Purple - Very specific keywords
            'atomic-purple': [
                'atomic purple', 'atomic-purple', 'atomicpurple',
                'violet transparent', 'purple transparent', 'transparent violet',
                'transparent purple', 'clear purple', 'see-through purple',
                'see through purple', 'translucide violet', 'translucent purple'
            ],

            # Pikachu Edition - Very specific
            'pikachu': [
                'pikachu', 'pokemon pikachu', 'pokémon pikachu',
                'edition pikachu', 'édition pikachu'
            ],

            # Pokemon Gold/Silver Edition
            'pokemon-gold-silver': [
                'pokemon or', 'pokemon argent', 'pokémon or', 'pokémon argent',
                'pokemon gold', 'pokemon silver', 'gold silver edition',
                'edition or argent', 'édition or argent'
            ],

            # Standard colors - Less specific (catch-all)
            'violet': [
                'violet', 'purple', 'mauve'
            ],

            'jaune': [
                'jaune', 'yellow'
            ],

            'rouge': [
                'rouge', 'red', 'berry'
            ],

            'bleu': [
                'bleu', 'teal', 'turquoise', 'cyan'
            ],

            'vert': [
                'vert', 'green', 'kiwi'
            ]
        }

        return patterns

    def classify_item(self, title: str, current_variant: str) -> Tuple[Optional[str], float]:
        """
        Suggest the best variant for an item based on its title

        Args:
            title: Item title
            current_variant: Current variant assignment

        Returns:
            (suggested_variant, confidence_score)
            Returns (None, 0.0) if current variant is correct
        """
        title_lower = title.lower()

        # Priority order: Check special editions first, then standard colors
        priority_variants = [
            'atomic-purple',  # Check this BEFORE violet!
            'pikachu',
            'pokemon-gold-silver',
            'violet',
            'jaune',
            'rouge',
            'bleu',
            'vert'
        ]

        best_match = None
        best_score = 0.0

        for variant_key in priority_variants:
            if variant_key not in self.variant_patterns:
                continue

            patterns = self.variant_patterns[variant_key]

            for pattern in patterns:
                if pattern in title_lower:
                    # Calculate confidence score based on pattern specificity
                    if variant_key in ['atomic-purple', 'pikachu', 'pokemon-gold-silver']:
                        # Special editions get high confidence
                        confidence = 0.95
                    else:
                        # Standard colors get medium confidence
                        confidence = 0.70

                    # Boost confidence if pattern is very specific (multi-word)
                    if len(pattern.split()) > 1:
                        confidence += 0.05

                    if confidence > best_score:
                        best_match = variant_key
                        best_score = confidence
                        break  # Found a match for this variant

            # If we found a high-confidence match, stop searching
            if best_score >= 0.95:
                break

        # If suggested variant is same as current, no change needed
        if best_match == current_variant:
            return (None, 0.0)

        # If we found a better match, return it
        if best_match:
            return (best_match, best_score)

        # No better match found
        return (None, 0.0)

    def analyze_scraped_data(self, data_path='scraped_data.json') -> Dict:
        """
        Analyze scraped data and find misclassified items

        Returns:
            Dictionary with analysis results
        """
        try:
            with open(data_path, 'r', encoding='utf-8') as f:
                data = json.load(f)
        except FileNotFoundError:
            print(f"❌ Error: {data_path} not found!")
            print("   Run the scraper first to generate data.")
            return {}

        print("="*60)
        print("🔍 Auto-Classification Analysis")
        print("="*60)

        results = {
            'misclassified_items': [],
            'total_items': 0,
            'misclassified_count': 0,
            'by_variant': {}
        }

        for variant_key, variant_data in data.items():
            listings = variant_data.get('listings', [])
            misclassified_in_variant = []

            print(f"\n📦 Checking variant: {variant_key} ({len(listings)} items)")

            for listing in listings:
                results['total_items'] += 1
                title = listing.get('title', '')
                item_id = listing.get('item_id', 'unknown')

                suggested_variant, confidence = self.classify_item(title, variant_key)

                if suggested_variant:
                    misclassification = {
                        'item_id': item_id,
                        'title': title,
                        'current_variant': variant_key,
                        'suggested_variant': suggested_variant,
                        'confidence': confidence,
                        'price': listing.get('price'),
                        'url': listing.get('url')
                    }

                    misclassified_in_variant.append(misclassification)
                    results['misclassified_items'].append(misclassification)
                    results['misclassified_count'] += 1

                    print(f"   ⚠️  Misclassified: {title[:60]}")
                    print(f"      → Should be '{suggested_variant}' (confidence: {confidence:.0%})")

            results['by_variant'][variant_key] = {
                'total': len(listings),
                'misclassified': len(misclassified_in_variant),
                'items': misclassified_in_variant
            }

        # Summary
        print(f"\n{'='*60}")
        print(f"📊 Summary")
        print(f"{'='*60}")
        print(f"Total items analyzed: {results['total_items']}")
        print(f"Misclassified items: {results['misclassified_count']} ({results['misclassified_count']/results['total_items']*100:.1f}%)")

        # Show breakdown by variant
        print(f"\n📋 Breakdown by variant:")
        for variant_key, stats in results['by_variant'].items():
            if stats['misclassified'] > 0:
                pct = stats['misclassified'] / stats['total'] * 100 if stats['total'] > 0 else 0
                print(f"   • {variant_key}: {stats['misclassified']}/{stats['total']} ({pct:.1f}%)")

        return results

    def save_report(self, results: Dict, output_path='auto_classify_report.json'):
        """Save classification report to JSON"""
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(results, f, ensure_ascii=False, indent=2)
        print(f"\n💾 Report saved to: {output_path}")


def main():
    """Main execution"""
    classifier = VariantClassifier()
    results = classifier.analyze_scraped_data()

    if results:
        classifier.save_report(results)

        # Print top 10 misclassifications
        if results['misclassified_items']:
            print(f"\n🔝 Top 10 Misclassifications:")
            print("="*60)
            for item in results['misclassified_items'][:10]:
                print(f"\n   Title: {item['title']}")
                print(f"   Current: {item['current_variant']} → Suggested: {item['suggested_variant']} ({item['confidence']:.0%})")
                print(f"   Price: {item['price']}€")
                print(f"   URL: {item['url'][:80]}")


if __name__ == "__main__":
    main()
