## Changelog
## 6.0.0
### What's New

- Added support for **Statamic 6**
- Fixed typos in the configuration file
- Updated configuration structure
- Updated dependencies to the latest versions

### Breaking Changes

After upgrading, it is **important** to republish the configuration:

```bash 
php artisan vendor:publish --tag=statamic-one-click-content-translation-config --force
```
### Update .env Variables

Depending on your Addon version, update your `.env` variables as follows:

| Addon Version 6.x                        | Addon Version ≤ 5.x                              |
|-----------------------------------------|-------------------------------------------------|
| TRANSLATION_SERVICE                      | ONE_CLICK_CONTENT_TRANSLATION_SERVICE          |
| TRANSLATION_DEEPL_AUTH_KEY               | DEEPL_AUTH_KEY                                 |
| TRANSLATION_GOOGLE_APPLICATION_CREDENTIALS | ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_CREDENTIALS |
| TRANSLATION_GOOGLE_APPLICATION_ID        | ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_ID        |
