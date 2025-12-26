#!/usr/bin/env python3

"""
Data Deduplication Tool for PrixRetro

Removes duplicate items across variants and assigns each item to its best-matching variant.
Uses smart classification based on title keywords and confidence scores.
"""

import json
import re
from collections import defaultdict
from auto_classify import VariantClassifier


class DataDeduplicator:
    def __init__(self, data_path='scraped_data.json'):
        """Load scraped data and classifier"""
        self.data_path = data_path
        
        with open(data_path, 'r', encoding='utf-8') as f:
            self.data = json.load(f)
            
        self.classifier = VariantClassifier()
        
        # Stats tracking
        self.stats = {
            'original_items': 0,
            'duplicate_items_removed': 0,
            'items_moved': 0,
            'final_items': 0
        }
    
    def find_all_duplicates(self):
        """Find all items that appear in multiple variants"""
        item_locations = defaultdict(list)
        
        # Map each item_id to all variants it appears in
        for variant_key, variant_data in self.data.items():
            for item in variant_data.get('listings', []):
                item_id = item['item_id']
                item_locations[item_id].append({
                    'variant': variant_key,
                    'item': item
                })
                self.stats['original_items'] += 1
        
        # Find duplicates (items in multiple variants)
        duplicates = {
            item_id: locations 
            for item_id, locations in item_locations.items() 
            if len(locations) > 1
        }
        
        print(f"🔍 Found {len(duplicates)} duplicate items across {len(item_locations)} total items")
        return duplicates
    
    def classify_best_variant(self, item, current_variants):
        """Determine the best variant for an item using smart classification"""
        title = item['title']
        
        # Try auto-classifier first
        suggested_variant, confidence = self.classifier.classify_item(title, None)
        
        if suggested_variant and confidence >= 0.90:
            # High confidence suggestion
            return suggested_variant, confidence, "auto_classify"
        
        # Manual classification for special cases
        title_lower = title.lower()
        
        # Priority 1: Special editions (highest priority)
        if any(word in title_lower for word in ['pikachu', 'pokemon pikachu edition', 'pokemon center']):
            return 'pikachu', 1.0, "special_edition"
        
        if any(word in title_lower for word in ['gold', 'silver', 'or', 'argent']):
            return 'pokemon-gold-silver', 1.0, "special_edition"
        
        # Priority 2: Clear variant indicators
        if any(word in title_lower for word in ['atomic purple', 'atomic violet', 'transparent clear purple']):
            return 'atomic-purple', 1.0, "clear_indicator"
        
        # Priority 3: Color keywords
        color_patterns = {
            'jaune': ['jaune', 'yellow', 'dandelion'],
            'rouge': ['rouge', 'red', 'berry red'],
            'bleu': ['bleu', 'blue', 'teal', 'turquoise'],
            'vert': ['vert', 'green', 'kiwi', 'lime'],
            'violet': ['violet', 'purple', 'grape']  # Lower priority than atomic-purple
        }
        
        for variant, keywords in color_patterns.items():
            if any(keyword in title_lower for keyword in keywords):
                # Extra check for atomic purple in violet category
                if variant == 'violet' and any(word in title_lower for word in ['atomic', 'transparent', 'clear']):
                    continue  # Skip, should be atomic-purple
                return variant, 0.8, "color_keyword"
        
        # Priority 4: Fallback to most common variant among current locations
        variant_counts = defaultdict(int)
        for variant in current_variants:
            variant_counts[variant] += 1
        
        best_variant = max(variant_counts, key=variant_counts.get)
        return best_variant, 0.3, "fallback_common"
    
    def deduplicate(self):
        """Remove duplicates and assign each item to best variant"""
        duplicates = self.find_all_duplicates()
        
        # Track which items to remove from each variant
        items_to_remove = defaultdict(list)  # variant -> [item_ids]
        items_to_add = defaultdict(list)     # variant -> [items]
        
        moves_summary = []
        
        for item_id, locations in duplicates.items():
            # Get all variants this item appears in
            current_variants = [loc['variant'] for loc in locations]
            
            # Use the first occurrence as reference (they should be identical)
            reference_item = locations[0]['item']
            
            # Classify the best variant for this item
            best_variant, confidence, method = self.classify_best_variant(
                reference_item, current_variants
            )
            
            # Track the move
            moves_summary.append({
                'item_id': item_id,
                'title': reference_item['title'][:60] + "...",
                'from_variants': current_variants,
                'to_variant': best_variant,
                'confidence': confidence,
                'method': method
            })
            
            # Mark item for removal from all variants
            for variant in current_variants:
                items_to_remove[variant].append(item_id)
            
            # Add item to best variant (will overwrite if already there)
            items_to_add[best_variant].append(reference_item)
        
        # Apply removals
        for variant_key in self.data.keys():
            if variant_key in items_to_remove:
                original_count = len(self.data[variant_key]['listings'])
                
                # Remove duplicates
                self.data[variant_key]['listings'] = [
                    item for item in self.data[variant_key]['listings']
                    if item['item_id'] not in items_to_remove[variant_key]
                ]
                
                removed_count = original_count - len(self.data[variant_key]['listings'])
                print(f"  📤 {variant_key}: Removed {removed_count} duplicate items")
        
        # Apply additions
        for variant_key, items in items_to_add.items():
            if variant_key in self.data:
                # Add items, avoiding duplicates
                existing_ids = {item['item_id'] for item in self.data[variant_key]['listings']}
                
                new_items = [item for item in items if item['item_id'] not in existing_ids]
                self.data[variant_key]['listings'].extend(new_items)
                
                if new_items:
                    print(f"  📥 {variant_key}: Added {len(new_items)} items")
        
        # Update statistics
        for variant_key, variant_data in self.data.items():
            self.update_variant_stats(variant_key, variant_data)
        
        self.stats['duplicate_items_removed'] = len(duplicates)
        self.stats['items_moved'] = len([m for m in moves_summary if len(m['from_variants']) > 1])
        self.stats['final_items'] = sum(len(v['listings']) for v in self.data.values())
        
        return moves_summary
    
    def update_variant_stats(self, variant_key, variant_data):
        """Recalculate statistics for a variant after deduplication"""
        listings = variant_data['listings']
        
        if not listings:
            variant_data['stats'] = {
                'avg_price': 0,
                'min_price': 0,
                'max_price': 0,
                'listing_count': 0,
                'total_found': 0,
                'price_history': {}
            }
            return
        
        # Calculate price statistics
        prices = [item['price'] for item in listings if item['price'] > 0]
        
        if prices:
            avg_price = round(sum(prices) / len(prices))
            min_price = min(prices)
            max_price = max(prices)
        else:
            avg_price = min_price = max_price = 0
        
        # Update stats
        variant_data['stats'].update({
            'avg_price': avg_price,
            'min_price': int(min_price),
            'max_price': int(max_price),
            'listing_count': len(listings),
            'total_found': len(listings)  # After dedup, these are the same
        })
    
    def save_cleaned_data(self, output_path='scraped_data_clean.json'):
        """Save deduplicated data"""
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(self.data, f, indent=2, ensure_ascii=False)
        
        print(f"💾 Cleaned data saved to: {output_path}")
        return output_path
    
    def save_dedup_report(self, moves_summary, output_path='deduplication_report.json'):
        """Save detailed report of deduplication"""
        report = {
            'summary': self.stats,
            'moves': moves_summary[:50],  # First 50 moves for review
            'total_moves': len(moves_summary)
        }
        
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(report, f, indent=2, ensure_ascii=False)
        
        print(f"📋 Deduplication report saved to: {output_path}")


def main():
    print("=" * 60)
    print("🔧 PrixRetro Data Deduplicator")
    print("=" * 60)
    
    # Load and process data
    deduplicator = DataDeduplicator()
    
    print(f"📊 Original data: {deduplicator.stats['original_items']} total items")
    print()
    
    # Perform deduplication
    print("🔄 Deduplicating items...")
    moves_summary = deduplicator.deduplicate()
    
    print()
    print("=" * 60)
    print("📊 Deduplication Summary")
    print("=" * 60)
    
    stats = deduplicator.stats
    print(f"Original items: {stats['original_items']}")
    print(f"Duplicate items found: {stats['duplicate_items_removed']}")
    print(f"Items moved between variants: {stats['items_moved']}")
    print(f"Final clean items: {stats['final_items']}")
    print(f"Items saved: {stats['original_items'] - stats['duplicate_items_removed']}")
    print()
    
    # Show variant distribution after cleanup
    print("📈 Final variant distribution:")
    for variant_key, variant_data in deduplicator.data.items():
        count = len(variant_data['listings'])
        avg_price = variant_data['stats']['avg_price']
        print(f"  {variant_key}: {count} items (avg: {avg_price}€)")
    
    print()
    
    # Save results
    clean_file = deduplicator.save_cleaned_data()
    deduplicator.save_dedup_report(moves_summary)
    
    print()
    print("✅ Deduplication complete!")
    print(f"🎯 Quality improvement: {stats['original_items']} → {stats['final_items']} items")
    print(f"📁 Clean data: {clean_file}")


if __name__ == "__main__":
    main()