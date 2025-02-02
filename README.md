# One-click Content Translation

![Statamic 4.0+](https://img.shields.io/badge/Statamic-4.0+-FF269E?style=for-the-badge&link=https://statamic.com)

With the One-click Content Translation addon for Statamic, translations are done in a breeze, directly within the CMS.
Thanks to the integration of the DeepL-API or Google Cloud API, text in any language supported by DeepL/Google* can be instantly translated within the Statamic CMS. Say goodbye to endless copy and pasting and experience the luxury of automatic translations for the languages on your website.

You can use One-click Content Translation for almost all text and field types. Titles, paragraphs – you name it! The add-on automatically detects the language you're translating and puts it into the language of the selected page you're editing in Statamic.

Of course the API limits apply, so you'll need to use your own API key and/or DeepL subscription. For more info on the free version and subscription options, check out the DeepL website. Same thing for Google Cloud Translation, obviously.


*DeepL has a list of supported languages here: [https://support.deepl.com/hc/en-us/articles/360019925219-Languages-included-in-DeepL-Pro](https://support.deepl.com/hc/en-us/articles/360019925219-Languages-included-in-DeepL-Pro)

The list of languages supported by Google can be found here: [https://cloud.google.com/translate/docs/languages](https://cloud.google.com/translate/docs/languages)

![One-click Content Translation](https://github.com/appswithlove/statamic-one-click-content-translation/blob/main/awl_translation_addon_image.png?raw=true)

## One-click Content Translation is a Commercial Addon.

You can use the addon for free while in development, but it requires a license to use on a live site. Learn more or buy a license on [The Statamic Marketplace](https://statamic.com/addons/awl/deepl)!


## Installation

1. Install One-click Content Translation from the `Tools > Addons` section of your control panel, or via composer:

```
composer require appswithlove/statamic-one-click-content-translation
php artisan vendor:publish --tag="statamic-one-click-content-translation"
php artisan vendor:publish --tag="statamic-one-click-content-translation-config"
```

2. Set Deepl Auth Key to .env file `DEEPL_AUTH_KEY=`.
To get an Auth Key go to [https://www.deepl.com/docs-api/api-access/authentication/](https://www.deepl.com/docs-api/api-access/authentication/)
[List of countries where Deepl is available.](https://www.deepl.com/pro-api)

Note: The addon will not work without a Deepl Auth Key

OR

Set Translate Service in .env file to `ONE_CLICK_CONTENT_TRANSLATION_SERVICE=google`. 
Set Google Cloud Credentials path in .env file `ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_CREDENTIALS=`.
Set Google Cloud resource ID in .env file `ONE_CLICK_CONTENT_TRANSLATION_GOOGLE_APPLICATION_ID=`.


## Usage

The One-click Content Translation addon will add a special button for secondary languages into collection entry edit views.
After a click on the button the addon will translate content inside the related field from the default language to the selected one.

If you have a field with language suffix, like `title_en` you will see two buttons: translate to selected language and translate to EN language.

For using the functionality for `globals` or `assets` you have to add the `one_click_content_translation_inputs` field to  each required blueprint.

## Uninstalling

To uninstall, run:

```
composer remove appswithlove/statamic-one-click-content-translation
rm -r public/vendor/statamic-one-click-content-translation
```

## Demo

![Demo One-click Content Translation](https://github.com/appswithlove/statamic-one-click-content-translation/blob/main/demo.gif?raw=true)

## Google Cloud
- Setup info can be found here: [https://cloud.google.com/translate/docs/setup](https://cloud.google.com/translate/docs/setup)
- Page for troubleshooting permissions problems: [https://console.cloud.google.com/iam-admin/troubleshooter](https://console.cloud.google.com/iam-admin/troubleshooter)
- Principal email: `client_email` from credetentials.json
- Resource: `project_id` from credetentials.json
- Permission: `cloudtranslate.generalModels.predict`
- Page for granting permissions: [https://console.cloud.google.com/iam-admin/iam](https://console.cloud.google.com/iam-admin/iam)
