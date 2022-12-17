
# Set up a basic app with Laravel

In this tutorial we will setup a basic app and add it to the appstore. We will learn you how to set up the app, set up widgets, authenticate and communicate with the API and receive webhooks.

We will use [Laravel with php 8.1](https://laravel.com)  for this example.

## Prerequirements

1. You need basic knowlage of git and we work with the [Github CLI](https://cli.github.com).
2. You need composer to install the needed dependancies. [Install Composer](https://getcomposer.org/doc/00-intro.md)
3. To store tokens and logs we use MySQL

### Install 

Clone the basic app GIT repository to your local machine.

```bash
git clone https://github.com/CampingCare/basic-app-laravel.git

cd basic-app-laravel/
```

Install the depandencies with composer

```bash
composer install
```

You need to setup your MySQL DB in the .env, copy the example .env and create a key for laravel.

```bash
cp .env.example .env
php artisan key:generate
```

Now we need to set up the modals in your mysql DB by running a migration

```bash
php artisan migrate
```

### Start development server

Start your server to check if everything is working

```bash
php artisan serve
```

Now the application will run on your local device, something like: `http://localhost:8000/`. You will see the default app url and it looks like this.

![basic laravel app](../../static/img/tutorials/basic-laravel.png)

## Create the app in the AppStore

Go to the App Store and create a new app. [Open the App Store](https://app.camping.care/apps)

![Add app](../../static/img/appstore/add-app.png)

:::tip
Need to know more about the App installation process, please read our App Store documentation. [App Store Documentation](../appstore/getting-started)
:::

### Widgets

This app supports a default app url and several widgets. We have added an reservation tab widget as an example. Learn more about widgets.

[App Store Widgets](../appstore/widgets)

### Webhooks

In this example we have setup an webhook receiver `http://localhost:8000/api/webhooks` (only for POST requests)

:::countion
In this example we did not secure the webhooks. Make sure you check the webhook private key with the key we send over in the request.
:::

For this example we used the `rates.update_prices` webhook trigger, so we will get an notification once a price has been changed. You can add multiple triggers to a single webhook.

## Install the app

To test the app you'll need to install it after you saved it. [Read more about the install process](../appstore/install)

After installing the app you will see this screen.

![basic laravel app installed](../../static/img/tutorials/basic-app-laravel-installed.png)

Congratulations! You are now done, the basic app works!

## Go Live

The final think you need to do is upload your application to an public host. This can be any hosting company / cloud platform you prefer. As long as it runs php / mysql with an updated version.
