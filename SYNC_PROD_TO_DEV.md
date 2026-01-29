# Sync Production Data to Dev Environment

## Quick Command (All-in-One)

```bash
# 1. Dump production database
mysqldump -h ba2247864-001.eu.clouddb.ovh.net -P 35831 \
  -u prixretro -p \
  ba2247864 \
  --single-transaction \
  --quick \
  --lock-tables=false \
  > production-backup-$(date +%Y%m%d).sql

# 2. Import to Sail MySQL
./vendor/bin/sail mysql < production-backup-$(date +%Y%m%d).sql

# 3. Clear caches
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan view:clear

# 4. Verify
./vendor/bin/sail artisan db:show
./vendor/bin/sail mysql -e "SELECT COUNT(*) FROM variants"
```

## Step-by-Step Process

### 1. Start Sail Environment
```bash
cd /home/ganzu/Documents/web-apps/prixretro
./vendor/bin/sail up -d
```

### 2. Export Production Database
```bash
# Password will be prompted (check .env.production or OVH dashboard)
mysqldump -h ba2247864-001.eu.clouddb.ovh.net -P 35831 \
  -u prixretro \
  -p \
  ba2247864 \
  --single-transaction \
  --quick \
  --lock-tables=false \
  --no-tablespaces \
  > production-backup-$(date +%Y%m%d).sql
```

**Options explained:**
- `--single-transaction`: Consistent snapshot without locking tables
- `--quick`: Stream rows instead of buffering (for large tables)
- `--lock-tables=false`: Don't lock tables (safe with --single-transaction)
- `--no-tablespaces`: Avoid permission issues with TABLESPACE

### 3. Verify Dump File
```bash
# Check file size (should be 1-50 MB depending on data)
ls -lh production-backup-*.sql

# Check content (should see CREATE TABLE, INSERT statements)
head -50 production-backup-*.sql
```

### 4. Import to Sail MySQL
```bash
# Drop existing database and recreate (to avoid conflicts)
./vendor/bin/sail mysql -e "DROP DATABASE IF EXISTS prixretro; CREATE DATABASE prixretro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import the dump
./vendor/bin/sail mysql prixretro < production-backup-$(date +%Y%m%d).sql

# Or if you want to see progress:
pv production-backup-*.sql | ./vendor/bin/sail mysql prixretro
```

### 5. Verify Import
```bash
# Check tables
./vendor/bin/sail mysql -e "SHOW TABLES" prixretro

# Check counts
./vendor/bin/sail mysql prixretro -e "
SELECT
  'consoles' as table_name, COUNT(*) as count FROM consoles
UNION ALL
SELECT 'variants', COUNT(*) FROM variants
UNION ALL
SELECT 'listings', COUNT(*) FROM listings
UNION ALL
SELECT 'current_listings', COUNT(*) FROM current_listings;
"
```

### 6. Clear Laravel Caches
```bash
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan route:clear
```

### 7. Test Application
```bash
# Check database connection
./vendor/bin/sail artisan db:show

# Test queries
./vendor/bin/sail tinker
>>> \App\Models\Console::count()
>>> \App\Models\Variant::count()
>>> \App\Models\Listing::count()
>>> exit
```

Visit: http://localhost
- Homepage should show consoles
- Variant pages should have data
- Admin panel should work

## Alternative: Direct MySQL Connection

If mysqldump is slow or fails:

```bash
# Connect to production MySQL
mysql -h ba2247864-001.eu.clouddb.ovh.net -P 35831 -u prixretro -p ba2247864

# Inside MySQL prompt:
SELECT TABLE_NAME, TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'ba2247864';

# Export specific tables if full dump is too large
mysqldump -h ba2247864-001.eu.clouddb.ovh.net -P 35831 \
  -u prixretro -p \
  ba2247864 \
  consoles variants listings current_listings \
  > production-partial-$(date +%Y%m%d).sql
```

## Troubleshooting

### "Access denied" Error
- Check password in OVH CloudDB dashboard
- Verify IP is whitelisted in OVH CloudDB firewall settings
- Use VPN if OVH requires specific IP range

### "Connection timed out"
- Check OVH CloudDB is running
- Verify port 35831 is not blocked by firewall
- Try from a different network

### "Unknown database"
- Database name is `ba2247864` (from DB_DATABASE in .env)
- Use `-e "SHOW DATABASES"` to list available databases

### Import Fails with "Tablespace" Error
- Add `--no-tablespaces` to mysqldump command
- Or remove TABLESPACE lines from SQL file:
  ```bash
  sed -i 's/TABLESPACE=.*//g' production-backup-*.sql
  ```

### Sail MySQL Container Not Running
```bash
./vendor/bin/sail up -d mysql
./vendor/bin/sail ps
```

## Security Notes

- **Don't commit** production backups to git (.gitignore includes `production-data*.sql`)
- **Delete dumps** after import to save disk space
- **Use separate credentials** for dev/prod (currently same CloudDB for both)

## Quick Reference

**Production DB:**
- Host: ba2247864-001.eu.clouddb.ovh.net
- Port: 35831
- User: prixretro
- Database: ba2247864

**Dev DB (Sail):**
- Host: localhost (or mysql container)
- Port: 3306
- User: sail
- Password: password
- Database: prixretro
