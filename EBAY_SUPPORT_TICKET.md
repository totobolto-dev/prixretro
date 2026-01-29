# eBay Developer Support Ticket

**Use this template when contacting eBay Developer Support**

---

## Subject
Error 10001 (Rate Limit) on Finding API - No Usage Statistics Visible

---

## Issue Description

I am receiving Error 10001 (Rate Limit Exceeded) on all Finding API calls using my Production credentials, despite:

1. **No visible usage statistics** in the Developer Portal (expected to see X/5,000 calls)
2. **Only making 1-2 test calls** (nowhere near 5,000 daily limit)
3. **Error persisting for 5+ days** (should reset daily at midnight PST)
4. **Retry logic implemented** (up to 2 retries with exponential backoff)

I suspect my application may not be fully provisioned for the Finding API, or there is a restriction I'm unaware of.

---

## Application Details

**Application Name:** PrixRetro

**App ID (Client ID):** [Your Production App ID - found at https://developer.ebay.com/my/keys]

**Environment:** Production

**API:** Finding API v1.13.0

**Operations Attempted:**
- `findCompletedItems` (sold listings)
- `findItemsByKeywords` (active listings)

**Both operations return the same error.**

---

## Error Details

**HTTP Status:** 500

**Error Response:**
```json
{
  "errorMessage": [{
    "error": [{
      "errorId": ["10001"],
      "domain": ["Security"],
      "subdomain": ["RateLimiter"],
      "severity": ["Error"],
      "category": ["System"],
      "message": ["Service call has exceeded the number of times the operation is allowed to be called"],
      "parameter": [
        {"@name": "Param1", "__value__": "findCompletedItems"},
        {"@name": "Param2", "__value__": "FindingService"}
      ]
    }]
  }]
}
```

---

## Request Details

**API Endpoint:** https://svcs.ebay.com/services/search/FindingService/v1

**Sample Request Parameters:**
```
OPERATION-NAME: findCompletedItems
SERVICE-VERSION: 1.13.0
SECURITY-APPNAME: [Your App ID]
RESPONSE-DATA-FORMAT: JSON
keywords: game boy color
paginationInput.entriesPerPage: 10
GLOBAL-ID: EBAY-FR
itemFilter(0).name: SoldItemsOnly
itemFilter(0).value: true
```

---

## What I've Tried

1. ✅ Verified using **Production credentials** (not Sandbox)
2. ✅ Implemented retry logic (2 retries with exponential backoff)
3. ✅ Tested both `findCompletedItems` and `findItemsByKeywords`
4. ✅ Tested with minimal requests (1 item per page)
5. ✅ Waited 5+ days for potential limit reset
6. ❌ **Cannot see usage statistics** in Developer Portal (suspicious)

---

## Questions for Support

1. **Is my application fully provisioned for Finding API access?**
   - I can see my Production keyset, but no usage statistics

2. **Is there a restriction or pending approval on my account?**
   - No alerts/warnings visible in the portal

3. **Why am I getting rate limit errors with near-zero usage?**
   - Expected to see usage counter (e.g., "5 / 5,000 calls today")

4. **Is there an "Enable API" or approval step I missed?**

5. **Should I see usage statistics in the portal?**
   - Currently see: App ID, Dev ID, Cert ID
   - Do NOT see: Call usage, analytics, graphs

---

## Use Case

**Application Purpose:** Price aggregation for retro gaming consoles

**Expected API Usage:**
- ~100-500 Finding API calls per day
- Scraping sold/completed listings for price analysis
- Displaying average prices to French consumers

**Target Market:** France (EBAY-FR, site ID 71)

---

## Request

Please:
1. **Investigate** why I'm receiving rate limit errors with minimal usage
2. **Enable usage statistics** in my Developer Portal (if disabled)
3. **Verify** my application is fully provisioned for Finding API
4. **Provide** any required approval steps or additional setup

---

## Contact Information

**Developer Account Email:** [Your eBay developer email]

**Application ID:** [Your Production App ID]

**Preferred Contact Method:** Email / Developer Portal messages

---

## Urgency

**Medium** - This is blocking production scraping, but not a critical business outage.

---

**Thank you for your assistance!**
