#!/usr/bin/env python3
"""
Data Validation Tool for PrixRetro
Checks scraped data for quality issues: duplicates, outliers, missing data, etc.
"""

import json
import re
from datetime import datetime
from typing import Dict, List
from collections import defaultdict


class DataValidator:
    def __init__(self, data_path='scraped_data.json'):
        """Load scraped data"""
        self.data_path = data_path
        try:
            with open(data_path, 'r', encoding='utf-8') as f:
                self.data = json.load(f)
        except FileNotFoundError:
            print(f"❌ Error: {data_path} not found!")
            print("   Run the scraper first to generate data.")
            self.data = {}

    def validate_scraped_data(self) -> Dict:
        """
        Run comprehensive quality checks on scraped data

        Returns:
            Dictionary with validation results and issues found
        """
        print("="*60)
        print("🔍 Data Quality Validation")
        print("="*60)

        results = {
            'total_items': 0,
            'total_variants': len(self.data),
            'duplicates': [],
            'missing_dates': [],
            'invalid_dates': [],
            'price_outliers': [],
            'suspicious_titles': [],
            'quality_score': 0.0,
            'warnings': [],
            'errors': []
        }

        if not self.data:
            results['errors'].append("No data to validate!")
            return results

        # Check 1: Find duplicate item_ids across variants
        print("\n📋 Check 1: Checking for cross-variant duplicates...")
        duplicates = self._find_duplicates()
        results['duplicates'] = duplicates
        if duplicates:
            print(f"   ⚠️  Found {len(duplicates)} duplicate items across variants!")
            results['warnings'].append(f"{len(duplicates)} duplicate items found")
        else:
            print(f"   ✅ No duplicates found")

        # Check 2: Validate dates
        print("\n📅 Check 2: Validating sold dates...")
        missing_dates, invalid_dates = self._validate_dates()
        results['missing_dates'] = missing_dates
        results['invalid_dates'] = invalid_dates

        if missing_dates:
            print(f"   ⚠️  {len(missing_dates)} items missing dates")
            results['warnings'].append(f"{len(missing_dates)} items missing dates")
        if invalid_dates:
            print(f"   ⚠️  {len(invalid_dates)} items with invalid dates")
            results['warnings'].append(f"{len(invalid_dates)} items with invalid dates")
        if not missing_dates and not invalid_dates:
            print(f"   ✅ All dates valid")

        # Check 3: Find price outliers
        print("\n💰 Check 3: Detecting price outliers...")
        outliers = self._find_price_outliers()
        results['price_outliers'] = outliers
        if outliers:
            print(f"   ⚠️  Found {len(outliers)} price outliers")
            results['warnings'].append(f"{len(outliers)} price outliers detected")
        else:
            print(f"   ✅ No major price outliers")

        # Check 4: Check for suspicious keywords
        print("\n🚨 Check 4: Checking for suspicious keywords...")
        suspicious = self._find_suspicious_titles()
        results['suspicious_titles'] = suspicious
        if suspicious:
            print(f"   ⚠️  Found {len(suspicious)} suspicious titles")
            results['warnings'].append(f"{len(suspicious)} suspicious titles")
        else:
            print(f"   ✅ No suspicious keywords detected")

        # Calculate overall quality score
        results['total_items'] = self._count_total_items()
        quality_score = self._calculate_quality_score(results)
        results['quality_score'] = quality_score

        # Print summary
        print(f"\n{'='*60}")
        print(f"📊 Validation Summary")
        print(f"{'='*60}")
        print(f"Total items: {results['total_items']}")
        print(f"Total variants: {results['total_variants']}")
        print(f"Quality score: {quality_score:.1%}")
        print(f"Warnings: {len(results['warnings'])}")
        print(f"Errors: {len(results['errors'])}")

        if quality_score >= 0.95:
            print(f"\n✅ Excellent data quality!")
        elif quality_score >= 0.85:
            print(f"\n✅ Good data quality")
        elif quality_score >= 0.70:
            print(f"\n⚠️  Acceptable data quality (some issues)")
        else:
            print(f"\n❌ Poor data quality - needs review!")

        return results

    def _find_duplicates(self) -> List[Dict]:
        """Find items that appear in multiple variants"""
        item_id_map = defaultdict(list)

        # Build map of item_id -> [variants]
        for variant_key, variant_data in self.data.items():
            for listing in variant_data.get('listings', []):
                item_id = listing.get('item_id')
                if item_id:
                    item_id_map[item_id].append({
                        'variant': variant_key,
                        'title': listing.get('title'),
                        'price': listing.get('price'),
                        'url': listing.get('url')
                    })

        # Find duplicates (item_id in more than one variant)
        duplicates = []
        for item_id, appearances in item_id_map.items():
            if len(appearances) > 1:
                duplicates.append({
                    'item_id': item_id,
                    'variants': [a['variant'] for a in appearances],
                    'title': appearances[0]['title'],
                    'appearances': appearances
                })

        return duplicates

    def _validate_dates(self) -> tuple:
        """Validate all sold dates"""
        missing_dates = []
        invalid_dates = []

        for variant_key, variant_data in self.data.items():
            for listing in variant_data.get('listings', []):
                item_id = listing.get('item_id')
                title = listing.get('title', '')[:60]
                sold_date = listing.get('sold_date')

                # Check missing
                if not sold_date or sold_date == '':
                    missing_dates.append({
                        'item_id': item_id,
                        'variant': variant_key,
                        'title': title
                    })
                    continue

                # Check format and validity
                try:
                    # Should be YYYY-MM-DD
                    if not re.match(r'^\d{4}-\d{2}-\d{2}$', sold_date):
                        invalid_dates.append({
                            'item_id': item_id,
                            'variant': variant_key,
                            'title': title,
                            'date': sold_date,
                            'reason': 'Invalid format (expected YYYY-MM-DD)'
                        })
                        continue

                    # Parse date
                    date_obj = datetime.strptime(sold_date, '%Y-%m-%d')

                    # Check if date is in the future
                    if date_obj > datetime.now():
                        invalid_dates.append({
                            'item_id': item_id,
                            'variant': variant_key,
                            'title': title,
                            'date': sold_date,
                            'reason': 'Date is in the future'
                        })

                    # Check if date is too old (before Game Boy Color release: 1998)
                    if date_obj.year < 2000:
                        invalid_dates.append({
                            'item_id': item_id,
                            'variant': variant_key,
                            'title': title,
                            'date': sold_date,
                            'reason': 'Date too old (before 2000)'
                        })

                except ValueError:
                    invalid_dates.append({
                        'item_id': item_id,
                        'variant': variant_key,
                        'title': title,
                        'date': sold_date,
                        'reason': 'Cannot parse date'
                    })

        return missing_dates, invalid_dates

    def _find_price_outliers(self) -> List[Dict]:
        """Find items with suspicious prices"""
        outliers = []

        for variant_key, variant_data in self.data.items():
            listings = variant_data.get('listings', [])
            if not listings:
                continue

            # Get average price for this variant
            avg_price = variant_data.get('stats', {}).get('avg_price', 0)
            if avg_price == 0:
                continue

            for listing in listings:
                price = listing.get('price', 0)
                title = listing.get('title', '')

                flags = []

                # Flag if price > 2x average (likely bundle or CIB)
                if price > avg_price * 2:
                    flags.append(f"Price too high ({price}€ vs avg {avg_price}€)")

                # Flag if price < 0.3x average (likely broken or parts)
                if price < avg_price * 0.3 and price > 0:
                    flags.append(f"Price too low ({price}€ vs avg {avg_price}€)")

                # Flag if price is suspiciously round (often fake/placeholder)
                if price >= 100 and price % 100 == 0:
                    flags.append(f"Suspiciously round price ({price}€)")

                if flags:
                    outliers.append({
                        'item_id': listing.get('item_id'),
                        'variant': variant_key,
                        'title': title[:80],
                        'price': price,
                        'avg_price': avg_price,
                        'flags': flags,
                        'url': listing.get('url')
                    })

        return outliers

    def _find_suspicious_titles(self) -> List[Dict]:
        """Find titles with suspicious keywords that may have slipped through"""
        suspicious_keywords = [
            'lot', 'bundle', 'for parts', 'broken', 'cassé', 'hs',
            'not working', 'pour pièces', 'défectueux', 'jeux',
            'games', 'boite', 'box only', 'cib', 'complete in box'
        ]

        suspicious = []

        for variant_key, variant_data in self.data.items():
            for listing in variant_data.get('listings', []):
                title = listing.get('title', '')
                title_lower = title.lower()

                found_keywords = []
                for keyword in suspicious_keywords:
                    if keyword in title_lower:
                        found_keywords.append(keyword)

                if found_keywords:
                    suspicious.append({
                        'item_id': listing.get('item_id'),
                        'variant': variant_key,
                        'title': title,
                        'price': listing.get('price'),
                        'keywords': found_keywords,
                        'url': listing.get('url')
                    })

        return suspicious

    def _count_total_items(self) -> int:
        """Count total items across all variants"""
        total = 0
        for variant_data in self.data.values():
            total += len(variant_data.get('listings', []))
        return total

    def _calculate_quality_score(self, results: Dict) -> float:
        """
        Calculate overall quality score (0.0 - 1.0)
        Based on: duplicates, missing dates, invalid data, outliers
        """
        if results['total_items'] == 0:
            return 0.0

        # Start with perfect score
        score = 1.0

        # Penalize for duplicates (severe: -0.05 per duplicate)
        duplicate_penalty = min(len(results['duplicates']) * 0.05, 0.30)
        score -= duplicate_penalty

        # Penalize for missing dates (-0.01 per missing, max -0.10)
        missing_date_penalty = min(len(results['missing_dates']) * 0.01, 0.10)
        score -= missing_date_penalty

        # Penalize for invalid dates (-0.02 per invalid, max -0.15)
        invalid_date_penalty = min(len(results['invalid_dates']) * 0.02, 0.15)
        score -= invalid_date_penalty

        # Penalize for outliers (-0.005 per outlier, max -0.20)
        outlier_penalty = min(len(results['price_outliers']) * 0.005, 0.20)
        score -= outlier_penalty

        # Penalize for suspicious titles (-0.01 per suspicious, max -0.15)
        suspicious_penalty = min(len(results['suspicious_titles']) * 0.01, 0.15)
        score -= suspicious_penalty

        return max(score, 0.0)

    def save_report(self, results: Dict, output_path='data_quality_report.json'):
        """Save validation report to JSON"""
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(results, f, ensure_ascii=False, indent=2)
        print(f"\n💾 Report saved to: {output_path}")


def main():
    """Main execution"""
    validator = DataValidator()

    if not validator.data:
        print("\n❌ No data to validate. Run the scraper first:")
        print("   python3 scraper_ebay.py")
        return

    results = validator.validate_scraped_data()
    validator.save_report(results)

    # Show details of issues found
    if results['duplicates']:
        print(f"\n🔍 Duplicate Items (showing first 5):")
        for dup in results['duplicates'][:5]:
            print(f"\n   Item ID: {dup['item_id']}")
            print(f"   Title: {dup['title'][:70]}")
            print(f"   Found in: {', '.join(dup['variants'])}")

    if results['price_outliers']:
        print(f"\n💰 Price Outliers (showing first 5):")
        for outlier in results['price_outliers'][:5]:
            print(f"\n   {outlier['title'][:70]}")
            print(f"   Price: {outlier['price']}€ (avg: {outlier['avg_price']}€)")
            print(f"   Flags: {', '.join(outlier['flags'])}")


if __name__ == "__main__":
    main()
