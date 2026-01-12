3. Artisan Command (app/Console/Commands/ValidateListingUrls.php)

Batch validation with options:

# Validate all listings (with 2-second delay)

php artisan listings:validate-urls

# Validate only unvalidated listings

php artisan listings:validate-urls --only-pending

# Validate first 50 listings

php artisan listings:validate-urls --limit=50

# Auto-reject invalid URLs

php artisan listings:validate-urls --auto-reject

# Custom delay (3 seconds to be safer)

php artisan listings:validate-urls --delay=3000

# Combine options

php artisan listings:validate-urls --only-pending --auto-reject --delay=3000

4. Admin Panel Integration (SortListings page)

Added to /admin/sort-listings:

-   New column: "URL Status" badge with color coding
    -   Green = Valid
    -   Red = Invalid
    -   Yellow = CAPTCHA
    -   Gray = Not Checked
-   Individual action: "Validate URL" button per listing
-   Bulk action: "Validate URLs" for selected listings (auto-rejects invalid ones)

5. Updated Listing Model

Added helper methods:

-   scopeUrlValid() - filter valid URLs
-   scopeUrlInvalid() - filter invalid URLs
-   scopeUrlNotValidated() - filter unchecked URLs
-   isUrlValid() - check if URL is valid
-   needsUrlValidation() - check if validation needed

How It Works

Detection Logic:

1. Redirects to different items:


    - Original: ebay.fr/itm/123456789
    - Redirects to: ebay.fr/itm/987654321
    - → REJECTED (different item ID)

2. Redirects to error pages:


    - Redirects to homepage, category, or search
    - → REJECTED (item removed)

3. CAPTCHA challenges:


    - Detects /splashui/captcha, /verify, HTTP 503
    - → MARKED AS CAPTCHA (try again later)
    - These are NOT automatically rejected

4. 404 errors:


    - → REJECTED (item not found)
