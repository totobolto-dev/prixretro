# PrixRetro Scraper - Instructions pour Claude Code

## 🎯 Objectif du projet
Site de comparaison de prix pour consoles rétro (Game Boy Color) générant €2000-2500/mois via affiliate eBay + display ads.

## 📋 Problèmes critiques à résoudre

### 1. Scraping eBay - Bugs majeurs
**Problème:** Le scraper ne capture pas correctement les données
- ❌ **Dates mal parsées** - Format inconsistant, parfois None
- ❌ **Pagination cassée** - Retourne les mêmes items sur chaque page
- ❌ **Mauvais items scrapés** - Captures parts, bundles, broken items
- ❌ **Duplicates cross-variant** - Même item dans plusieurs catégories

**Fichier:** `scraper_ebay.py`
**Zones critiques:**
- Ligne ~330: Parsing de la date de vente
- Ligne ~75-220: Pagination avec `_skc` (skip count)
- Ligne ~240-280: Filtres broken/parts

### 2. Qualité des données - Beaucoup de garbage
**scraped_data.json contient:**
```json
// Exemple 1: Mauvaise catégorie
{
  "variant": "violet",
  "title": "Nintendo Game Boy Color - Atomic Purple",  // ← Dans violet au lieu de atomic-purple!
  "item_id": "286213849363"
}

// Exemple 2: Prix suspect (bundle ou CIB)
{
  "variant": "violet", 
  "title": "NINTENDO game boy color en boite EUR/FRA",
  "price": 209.9  // ← Avg = 71€, celui-ci est 3x plus cher
}

// Exemple 3: Même item dans 2 variants
{
  "variant": "violet",
  "item_id": "205838904350",
  "title": "Console Atomic Purple + Jeu"
}
// ET AUSSI dans atomic-purple:
{
  "variant": "atomic-purple",
  "item_id": "205838904350",  // ← DUPLICATE!
  "title": "Console Atomic Purple + Jeu"
}
```

### 3. Filtrage intelligent nécessaire
**Besoin:**
- Auto-détection variant correct (chercher "atomic purple" → atomic-purple)
- Detection bundles ("+ jeu", "+ games", "lot de")
- Detection CIB ("en boite", "complete in box", "complète")
- Smart outlier detection (prix > 2x average = suspect)

## 📊 Stats actuelles

**Données dans scraped_data.json:**
- Variant "violet": 474 items trouvés, 259 après filtres
- Variant "atomic-purple": 19 items
- Range prix violet: 53€ - 95€ (avg: 71€)
- Items > 150€ = probablement bundles/CIB

**Problèmes identifiés:**
1. ~40 items "Atomic Purple" mal catégorisés dans "violet"
2. ~15% des prix sont outliers (bundles non détectés)
3. Dates manquantes sur ~5% des items

## 🔧 Fichiers importants

### scraper_ebay.py (PRIORITÉ)
**Ce qui doit être fixé:**
```python
# Ligne ~330 - Date parsing cassé
# ACTUEL (buggy):
sold_date = item.find('span', class_='s-item__endedDate')
if sold_date:
    date_text = sold_date.text  # Format inconsistant!
    
# BESOIN: Parser robuste qui gère:
# - "Vendu le 18 déc. 2025"
# - "Sold Dec 18, 2025" 
# - "18/12/2025"

# Ligne ~75-220 - Pagination
# PROBLÈME: _skc (skip count) ne marche pas bien
# Items identiques sur page 1, 2, 3...

# Ligne ~240-280 - Filtres
# TROP STRICTS: rejette "transparente" (pense que c'est spare part)
# PAS ASSEZ STRICTS: accepte bundles avec 5 jeux
```

### config.json
```json
{
  "variants": {
    "violet": {
      "variant_name": "Violet",
      "search_terms": ["game boy color violet", "gameboy color purple"],
      "keywords": ["violet", "purple", "mauve"]  // ← Pas assez précis!
    },
    "atomic-purple": {
      "variant_name": "Atomic Purple (Violet Transparent)",
      "search_terms": ["game boy color atomic purple", "gameboy color violet transparent"],
      "keywords": ["atomic", "transparent", "clear"]  // ← Devrait match "atomic purple"
    }
  }
}
```

### scraped_data.json
**Structure:**
```json
{
  "violet": {
    "variant_key": "violet",
    "variant_name": "Violet",
    "stats": { "avg_price": 71, "listing_count": 259 },
    "listings": [
      {
        "item_id": "397367129677",
        "title": "Nintendo Game Boy Color Système Portable - Violet",
        "price": 50.0,
        "url": "https://www.ebay.fr/itm/397367129677",
        "sold_date": "2025-12-18",
        "condition": "Occasion"
      }
    ]
  }
}
```

## 🎯 Missions pour Claude Code

### Mission 1: Analyse des données (30 min)
1. Load scraped_data.json
2. Identifier tous les duplicates (même item_id dans multiple variants)
3. Trouver items mal catégorisés ("atomic purple" dans "violet")
4. Lister les outliers de prix (> 2x average)
5. Générer rapport: `data_quality_report.json`

### Mission 2: Fix scraping (2h)
1. **Date parsing robuste**
   - Parser tous les formats eBay FR
   - Fallback graceful si pas de date
   - Tests unitaires

2. **Pagination fix**
   - Vérifier que _skc fonctionne
   - Détecter vraie dernière page
   - Éviter duplicates

3. **Filtres intelligents**
   - Améliorer detection bundles
   - Smart variant matching
   - Pas trop strict sur "transparente", "clear", etc.

### Mission 3: Auto-classification (1h)
Créer `auto_classify.py`:
```python
def classify_item(title, current_variant):
    """
    Suggère le bon variant basé sur le titre
    Returns: (suggested_variant, confidence_score)
    """
    # Si "atomic purple" ou "transparent" → atomic-purple
    # Si "violet clair" ou "purple" seulement → violet
    # Etc.
```

### Mission 4: Quality checks (30 min)
Créer `validate_data.py`:
```python
def validate_scraped_data():
    """
    Vérifie la qualité des données:
    - Pas de duplicates
    - Dates valides
    - Prix dans range raisonnable
    - Variant matching correct
    
    Returns: rapport avec warnings
    """
```

## 📝 Exemples de patterns à détecter

### Bundles (à flagger):
- "console + 5 jeux"
- "lot de 3 gameboy"
- "bundle avec pokemon"
- Prix > 150€ souvent = bundle

### CIB - Complete In Box (à flagger):
- "en boite"
- "complete in box"
- "CIB"
- "avec boite et notice"
- Prix > 120€ souvent = CIB

### Parts/Broken (à rejeter):
- " hs" (hors service)
- "pour pièces"
- "not working"
- "no sound"
- "broken screen"

### Mauvais variant:
```python
# Si title contient "atomic purple" OU "transparent" OU "clear purple"
# → Devrait être dans "atomic-purple"

# Si title contient juste "purple" ou "violet" sans "atomic"
# → OK dans "violet"
```

## 🚀 Démarrage rapide

```python
# 1. Clone le repo (URL fournie par user)
# 2. Analyse initiale:
python3 -c "
import json
with open('scraped_data.json') as f:
    data = json.load(f)
    
# Compte les items
for variant, vdata in data.items():
    print(f'{variant}: {len(vdata.get(\"listings\", []))} items')
    
# Trouve duplicates
all_ids = {}
for variant, vdata in data.items():
    for item in vdata.get('listings', []):
        item_id = item['item_id']
        if item_id in all_ids:
            print(f'DUPLICATE: {item_id} in {all_ids[item_id]} AND {variant}')
        all_ids[item_id] = variant
"

# 3. Run le scraper en mode test (1 page seulement)
python3 scraper_ebay.py  # Voir les bugs en action
```

## 📐 Critères de succès

**Le scraper doit:**
- ✅ Capturer 100% des dates correctement
- ✅ Pas de duplicates cross-variant
- ✅ < 5% faux positifs dans les filtres
- ✅ Auto-détecter 90%+ des mauvaises catégorisations
- ✅ Flagger 95%+ des bundles/CIB

**Output attendu:**
```json
// data_quality_report.json
{
  "total_items": 493,
  "duplicates": [
    {"item_id": "286213849363", "variants": ["violet", "atomic-purple"]},
    {"item_id": "205838904350", "variants": ["violet", "atomic-purple"]}
  ],
  "misclassified": [
    {"item_id": "286213849363", "current": "violet", "suggested": "atomic-purple", "confidence": 0.95}
  ],
  "outliers": [
    {"item_id": "267505033721", "price": 209.9, "avg": 71, "reason": "possible_bundle"}
  ],
  "missing_dates": ["397367129677"],
  "quality_score": 0.82
}
```

## 💡 Notes importantes

- User est développeur PHP senior niveau, connaît bien le code
- Projet personnel = passive income, pas de deadline stricte
- Préfère data-driven approach vs articles/reviews
- Budget: domaine payé jusqu'à avril 2026
- eBay Partner Network en attente (2 rejections)
- Manual filter UI déjà créé pour review final

## 🔗 Contexte additionnel

**Why Game Boy Color?**
- Niche retro gaming active
- Prix stables (50-100€)
- Facile à authentifier
- Pas dominé par Idealo/autres comparateurs

**Revenue model:**
- eBay affiliate: 50-70% commission sur prix final
- Display ads: €1-3 CPM
- Target: 200-500 visiteurs/jour pour €2500/mois

**User location:** Finlande
**Langue site:** Français (marché FR)
**Stack:** Python scraping + HTML static + Chart.js
