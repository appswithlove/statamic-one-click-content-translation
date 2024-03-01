# One-click Content Translation

![Statamic 3.0+](https://img.shields.io/badge/Statamic-3.0+-FF269E?style=for-the-badge&link=https://statamic.com)

With the One-click Content Translation addon for Statamic, translations are done in a breeze, directly in the CMS.
Thanks to the integration of the DeepL-API and Google Cloud API, text in any language supported by DeepL*/Google can be instantly translated within the Statamic CMS. Say goodbye to endless copy and pasting and experience the luxury of automatic translations for the languages on your website.

One-click Content Translation works for (almost) all text and field types: Titles, paragraphs and even slugs. The addon automatically > detects the language to be translated and translates it into the language of the selected page you are editing in Statamic.

Disclaimer: The API-Limits of DeepL apply and you'll have to use your own API-Key and/or DeepL-Subscription. For the limits of the free version and subscription options, check the DeepL Website.


*DeepL has a list of supported languages here: https://support.deepl.com/hc/en-us/articles/360019925219-Languages-included-in-DeepL-Pro

![One-click Content Translation](https://github.com/appswithlove/statamic-one-click-content-translation/blob/main/awl_translation_addon_image.png?raw=true)

## One-click Content Translation is a Commercial Addon.

You can use it for free while in development, but it requires a license to use on a live site. Learn more or buy a license on [The Statamic Marketplace](https://statamic.com/addons/awl/deepl)!


## Installation

1. Install One-click Content Translation from the `Tools > Addons` section of your control panel, or via composer:

```
composer require appswithlove/statamic-one-click-content-translation
php artisan vendor:publish --tag="statamic-one-click-content-translation"
php artisan vendor:publish --tag="statamic-one-click-content-translation-config"
```

2. Set Deepl Auth Key to .env file `DEEPL_AUTH_KEY=`. 
> To get Auth Key go to https://www.deepl.com/docs-api/api-access/authentication/
> 
> List of countries where Deepl is available https://www.deepl.com/pro-api

Note: The addon will not work without Deepl Auth Key

OR

Set Translate Service to .env file `ONE_CLICK_CONTENT_TRANSLATION_SERVICE=google`. 
Set Google Cloud Credetials path to .env file `ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_CREDENTIALS=`.
Set Google Cloud resource ID to .env file `ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_ID=`.


## Usage

One-click Content Translation addon will add a special button for secondary languages into collection entry edit views.
After click on the button the addon will translate content inside the related field from default language to the selected one.

## Uninstalling

To uninstall, run:

```
composer remove appswithlove/statamic-one-click-content-translation
rm -r public/vendor/statamic-one-click-content-translation
```

## Demo

![Demo One-click Content Translation](https://github.com/appswithlove/statamic-one-click-content-translation/blob/main/demo.gif?raw=true)
