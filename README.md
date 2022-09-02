# Translate Me

> Translate Me is a Statamic addon that does something pretty neat.

## Features

This addon does:

- This
- And this
- And even this

## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require statamic/statamic-translate-me
```

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