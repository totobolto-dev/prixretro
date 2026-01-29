# eBay API Error 10001 Diagnostics

## Error Details
```
HTTP 500 - {"errorMessage":[{"error":[{"errorId":["10001"],"domain":["Security"],"subdomain":["RateLimiter"],"severity":["Error"],"category":["System"],"message":["Service call has exceeded the number of times the operation is allowed to be called"]}]}]}
```

## Error ID 10001 = Rate Limit Exceeded

This error means eBay is blocking the API calls because:
1. **Daily call limit exceeded** (5,000 calls/day for Finding API)
2. **Hourly/per-second rate limits exceeded**
3. **App ID not approved for production** (still in sandbox mode)
4. **App ID suspended** (policy violation or incomplete verification)

## Troubleshooting Steps

### 1. Check eBay Developer Portal
Visit: https://developer.ebay.com/my/api_keys

**What to check:**
- [ ] Is your Application in **Production** status? (not Sandbox)
- [ ] Are there any **warnings/alerts** on your app?
- [ ] Is the Application **Active** (not suspended)?
- [ ] Check **API call limits** - have you exceeded daily quota?
- [ ] Check **Keyset Status** - should be "Active" with green checkmark

### 2. Verify Credentials Environment
Your current config (from `config/services.php`):
```php
'finding_api_url' => 'https://svcs.ebay.com/services/search/FindingService/v1',  // PRODUCTION
'site_id' => 71,  // EBAY-FR (correct)
```

**Sandbox URL would be:** `https://svcs.sandbox.ebay.com/services/search/FindingService/v1`

You're using **production URL** ✓ but might be using **sandbox credentials**.

### 3. Check if App ID is for Production
In eBay Developer Portal:
1. Go to "User Tokens" or "Application Keys"
2. Look for **Environment** dropdown - should be set to **"Production"**
3. If you see "Sandbox" keys, you need to:
   - Switch environment to "Production"
   - Get new Production App ID/Cert ID
   - Update your .env file

### 4. Check Application Status
Common issues:
- **Incomplete verification**: eBay may require additional info (company details, usage description)
- **Pending approval**: Production access requires manual approval (can take 2-4 weeks)
- **Rate limit tier**: New apps start with low limits, need to request increase

### 5. Test with Different Operation
Try a simpler operation to isolate if it's:
- All API calls blocked (credentials issue)
- Just `findCompletedItems` blocked (operation-specific limit)

## Recommended Actions

### Immediate: Check Developer Portal
1. Login to https://developer.ebay.com/
2. Navigate to **My Account** → **Application Keys**
3. Screenshot the Application Status page
4. Check "Production" vs "Sandbox" environment
5. Look for any error messages or pending actions

### If Using Sandbox Credentials:
Generate production credentials:
1. In Developer Portal, switch to **Production** environment
2. Copy the **Production App ID** and **Cert ID**
3. Update `.env` file:
   ```bash
   EBAY_APP_ID=<production-app-id>
   EBAY_CERT_ID=<production-cert-id>
   ```

### If Production but Blocked:
1. Check if you hit daily limit (unlikely if not testing heavily)
2. Submit a support ticket to eBay explaining:
   - Your use case (price aggregation for retro consoles)
   - Expected call volume (~100-500 calls/day)
   - Request production access activation

### If App Needs Approval:
eBay may require:
- Business verification
- Use case description
- Compliance with Developer Program policies
- Terms acceptance

## Testing Commands

Once credentials are updated:
```bash
# Simple test (should return results)
php artisan ebay:test-search "game boy color"

# Check rate limit headers (if available)
# eBay doesn't return rate limit headers in Finding API,
# so you'll only know by hitting limits
```

## Rate Limits Reference
- **Finding API (findCompletedItems)**: 5,000 calls/day
- **Shopping API**: 5,000 calls/day per user token
- **Browse API**: Varies by subscription tier

## Next Steps
1. Login to eBay Developer Portal
2. Verify environment (Production vs Sandbox)
3. Check Application Status
4. Update credentials if needed
5. Test again

## Contact Support
If blocked despite correct setup:
- eBay Developer Support: https://developer.ebay.com/support
- Developer Forums: https://community.ebay.com/t5/Developer-Networks/ct-p/developer
