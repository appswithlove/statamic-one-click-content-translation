# Statamic Translate Me

> The addon helps translate content in the CMS automatically

## Features

This addon does:

- translates text field from Default language to selected language
- extends Bard text field
- extends Input[type="text"] field

## Demo
![Demo animation](https://gitlab.appswithlove.net/statamic/statamic-translate-me/-/blob/master/demo.gif "Demo")

### Installation

See also: https://gitlab.appswithlove.net/help/user/packages/composer_repository/index#install-a-composer-package

Run:

```bash
composer config repositories.gitlab.appswithlove.net/123 '{"type": "composer", "url": "https://gitlab.appswithlove.net/api/v4/group/123/-/packages/composer/packages.json"}'
composer config gitlab-domains gitlab.appswithlove.net
```

Create a gitlab access token with `read_api` permission:

-   https://gitlab.appswithlove.net/-/profile/personal_access_tokens

Run

```bash
composer config gitlab-token.gitlab.appswithlove.net <personal_access_token>
```

Edit generated auth.json and add:

```json5
{
    'gitlab-domains': ['gitlab.appswithlove.net'],
    // ...
}
```

And then add the addon to your project by running:

```bash
composer require statamic/statamic-translate-me
php artisan vendor:publish --tag="statamic-translate-me-config"
```

Set DEEPL_AUTH_KEY= in .env file

## How to Use

Here's where you can explain how to use this wonderful addon.


## How push a new version

https://gitlab.appswithlove.net/help/user/packages/composer_repository/index#install-a-composer-package

git add .
git commit -m "New feature"
git tag v1.0.1
git push
git push origin v1.0.1
curl --data tag=v1.0.1 "https://__token__:<PUT_YOUR_TOKEN>@gitlab.appswithlove.net/api/v4/projects/3597/packages/composer"