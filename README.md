# PrixRetro

**Tracker de prix pour consoles retrogaming d'occasion**

Suivez l'évolution des prix du marché secondaire pour consoles retrogaming avec analyse des ventes récentes.

🔗 **Live**: [www.prixretro.com](https://www.prixretro.com)

---

## 📊 Fonctionnalités

### Frontend Public
- **Pages consoles**: Vue d'ensemble avec statistiques agrégées
- **Pages variantes**: Historique des prix et graphiques d'évolution
- **Classements**: Top variantes les plus vendues par console
- **Ventes en cours**: Listings eBay actuels
- **Consoles similaires**: Suggestions de consoles liées

### Admin Panel (Filament)
- Import de données scrapées (JSON)
- Classification manuelle des listings
- Gestion consoles, variantes, listings
- Synchronisation base de données

### SEO & Monétisation
- **Liens affiliés**: Amazon Associates & eBay Partner Network
- **Schema.org**: Product, BreadcrumbList, WebSite, Organization
- **Meta tags**: Descriptions dynamiques, Open Graph, Twitter Cards
- **Sitemap XML**: Régénéré quotidiennement via GitHub Actions
- **robots.txt**: Optimisé pour SEO

---

## 🛠️ Stack Technique

- Laravel 12.44.0 (PHP 8.4+)
- Filament 4.3.1 (Admin Panel)
- MySQL 8.4
- Chart.js (Graphiques de prix)
- GitHub Actions (CI/CD automatique)

---

## 📦 Installation

### Prérequis
- Docker Desktop
- PHP 8.4+
- Composer

### Configuration

```bash
# Clone
git clone https://github.com/totobolto-dev/prixretro.git
cd prixretro

# Install dependencies
composer install

# Start Docker (Laravel Sail)
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Seed database (optional)
./vendor/bin/sail artisan db:seed
```

### Fichier .env

Créer un fichier `.env` avec les variables nécessaires:

```env
APP_NAME=PrixRetro
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=prixretro
DB_USERNAME=sail
DB_PASSWORD=password

# Affiliate tags (optional)
AMAZON_ASSOCIATE_TAG=your-tag
```

---

## 📈 Structure

```
app/
├── Http/Controllers/
│   ├── ConsoleController.php    # Aggregate console data
│   ├── VariantController.php    # Individual variant pages
│   └── ContentController.php    # Ranking pages
├── Console/Commands/
│   ├── GenerateSitemap.php      # XML sitemap generation
│   └── SyncFromProduction.php   # DB sync command
└── Services/
    ├── ConsoleDescriptionGenerator.php
    └── VariantDescriptionGenerator.php

resources/views/
├── layout.blade.php              # Master layout
├── home.blade.php                # Homepage (console grid)
├── console/show.blade.php        # Console page (aggregate + variants)
├── variant/show.blade.php        # Variant page (price history + chart)
├── content/ranking.blade.php     # Top variants ranking
└── errors/404.blade.php          # Custom 404 avec suggestions
```

---

## 📝 Commandes Utiles

```bash
# Régénérer sitemap
./vendor/bin/sail artisan sitemap:generate

# Clear caches
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan view:clear

# Run migrations
./vendor/bin/sail artisan migrate
```

---

## 🤝 Contributing

Contributions are welcome! Please follow Laravel best practices and ensure all changes are tested.

---

## 📄 License

Proprietary - PrixRetro © 2026
