# Translation Providers

So far, we've mostly focused on converting our site from English to
translated English. Now, it's time to explore how we can get translations
for other languages. One way to do that is by sending your
`messages.en.yaml` file to a translation service that can provide you with
the French or Spanish equivalent. You just need to rename the returned file
and place it in the translations directory. But there's another option -
Symfony's translation providers.

## Understanding Symfony Translation Providers

If you visit the Symfony translation documentation, you'll find a list of
providers. These are essentially cloud services that can handle
translations for you. They allow you to upload your English translations,
and then translate them either by using their team of translators, or by
using automatic translation tools. There are four to choose from, and for
this tutorial, I'll be using Crowdin. There's no particular reason for
that, other than the fact that they've sponsored the Symfony framework in
the past.

Now, let's start by installing the provider. Copy the `composer require`
command, and paste it in your terminal:

```terminal
composer require symfony/crowdin-translation-provider
```

Once installed, it will also run a recipe. You can check what's been added
by running `git status`. You'll notice a new `.env` entry and an addition
to our translation config.

## Updating the .env and Config Files

Firstly, if we open `.env` and scroll down to the bottom, we see that a dsn
has been added.

Next, let's look at `config/packages/translation.yaml`. Here, you'll find
that the Crowdin provider has been added, along with its dsn:

Given that this is a sensitive environment variable, we should create a new
file at the root called `.env.local`. This file is ignored by git, so it
won't be committed.

Let's copy the dsn from `.env` and paste it into `.env.local`. We're not
going to use `ORGANIZATION_DOMAIN`, so we can remove that part. We'll be
using a personal account instead.

## Setting Up the Crowdin Account

For this setup, we need an API token and a project ID. Head over to the
Crowdin homepage, sign up for a free tier (which includes a trial of some
premium features) and once your account is set up, navigate to your
dashboard or profile page.

To get the API key, go to settings, then API, then `Personal Access Token >
New Token`. We'll name this token `Space Bar`.

For the scopes, select the project scope. Once you confirm your
credentials, you'll get the key. Copy it and paste it into the `API_TOKEN`
field in `.env.local`.

## Creating a Crowdin Project

Next, we need to create a project. Head back to your Crowdin dashboard,
click on `Create Project` and name it `Space Bar`. Make sure to keep it as
a private project. Our source language is English, so leave that as
default. For target languages, select French and Spanish.

Keep the project type as file-based and click on 'Create Project'. Once the
project has been created, you'll find the project ID under 'Details' on
your dashboard. Copy this ID and paste it into the `PROJECT_ID` field in
`.env.local`.

## Adjusting Language Mapping

One last thing: If you click on the French link on your dashboard, you'll
see `fr` in the URL, which matches our project's locale code. But for
Spanish, the URL shows `es-ES`, which does not match our `es` locale code.

To fix this, we need to create a custom mapping. Go to settings, then
languages, and open the 'Language Mapping' section. Choose Spanish, use
`locale` as the placeholder, and `es` as the custom code.

Great, that's it! This will help Symfony synchronize the translations.

## Up Next: Synchronization

Next, we'll push our raw English translations up to Crowdin, do some
translating, and pull the translated versions back down. Stay tuned for
that!
