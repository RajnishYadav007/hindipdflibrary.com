# Phase 11 — Traffic, Device & Geographic Classification

## 1. Traffic Source Classification (`ReferrerClassifier`)
The HTTP `Referer` header is inspected deterministically:
1. **Direct**: Referer header is missing or empty.
2. **Google**: Referer host contains `google.` or `googleadservices.`.
3. **Social**: Referer matches known social networks:
   - `facebook.com`, `m.facebook.com`
   - `twitter.com`, `x.com`, `t.co`
   - `instagram.com`
   - `youtube.com`
   - `linkedin.com`
   - `reddit.com`
   - `pinterest.com`
   - `whatsapp.com`, `web.whatsapp.com`
   - `telegram.org`, `t.me`
   - `quora.com`
4. **Referral**: Any other valid external HTTP host.
5. **Other**: Internal application host requests or unparseable URLs.

## 2. Device Classification (`DeviceClassifier`)
Evaluates the `User-Agent` string with zero external packages:
- **Tablet**: Matches `/(ipad|tablet|(android(?!.*mobile))|kindle|silk)/i`.
- **Mobile**: Matches `/(mobile|iphone|ipod|android.*mobile|blackberry|iemobile)/i`.
- **Desktop**: Matches `/(windows nt|macintosh|mac os x|linux|x11)/i`.
- **Unknown**: Fallback when no pattern matches.

## 3. Geographic Classification
- Relies on reverse proxy / CDN headers (`CF-IPCountry`, `X-Country-Code`, `GEOIP_COUNTRY_CODE`).
- Defaults to `unknown` if absent. Zero blocking IP lookups or heavy GeoIP databases.
