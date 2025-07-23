# Translation Providers

So far, we've mostly focused on translating our site from English to...
English... What about French and Spanish? We've talked a bit about
how we can manually send our `messages.en.yaml` file to a translation
service to make these translations. But there's another option: Symfony's
Translation Providers.

## Understanding Symfony Translation Providers

These are a set of cloud-based services that manage our translations.
If we have a translation "team", they can login to these services and
translate our messages. But many, if not all, can automatically create
the translations for us.

You "push" your default language translations to the provider, and, once
translated, you "pull" the translated messages back down.

The Symfony docs show the supported providers. I don't have a specific
favourite and this course isn't sponsored by any of them, but I noticed
Crowdin has sponsored Symfony in the past, so I'll use it.

## Installing the Crowdin Provider

Start by installing the Crowdin provider, so copy the `composer require`
command, and paste it in your terminal:

```terminal-silent
composer require symfony/crowdin-translation-provider
```

This package included a recipe so run:

```terminal
git status
```

Looks like a new environment variable and some config.

First, open `.env` and scroll down to the bottom, a commented out
`CROWDIN_DSN` has been added.

Next, look at `config/packages/translation.yaml`. The Crowdin provider has been added,
along with its DSN.

Since the DSN is a sensitive environment variable, we don't want it committed.
So add a `.env.local` file at our project's root. Copy the DSN from `.env`
and paste it here. We'll just be using a personal Crowdin account, so remove
the `ORGANIZATION_DOMAIN.` part.

## Setting Up the Crowdin Account

Time to create and configure a Crowdin account! Go to [crowdin.com](https://crowdin.com)
and sign up for a free account. You start with a free trial of their premium
features, but we'll only need the features that are part of their free tier.

Once registered and logged in, you'll be on this dashboard page. First, we
need an API token. Click your avatar in the top right, and select "Settings".
Click the "API" tab and then "New Token".

For the "Token Name", use "Space Bar". For the scopes, select the "Projects"
group and click "Create"... You may need to confirm your credentials, but once done,
you'll see your new token.

Copy it, and in `.env.local`, paste over the `API_TOKEN` text in the DSN.
Back on Crowdin, "Close" the dialog.

## Creating a Crowdin Project

Now we need a project ID, so we need a project! On your dashboard, click
"Create Project". Name it "Space Bar" and keep it as private. This is
sensitive stuff!

Choose the "Source Language" - keep as "English" in our case.

For "Target Languages", find "French" - the generic one, and select it.
Now, "Spanish" - also the generic one, and select it too.

Keep everything else as is, and click "Create Project".

Ok, there is *a lot* to look at here... We'll only focus on a small set
of these features.

To get the "Project ID", click the "Dashboard" tab, then, in this "Details"
section, copy the "Project ID".

Back in our `.env.local` file, select `PROJECT_ID` and paste.

## Adjusting Language Mapping

Back on Crowdin, click on "French". Notice in your browser's URL, it shows
`fr`. Cool, that matches our project's French locale code.

Go back, and click "Spanish". Notice the URL shows `es-ES`. This isn't
the same as our project. We're using just `es`.

When syncing translations, this will cause an issue as Crowdin won't know
that our `es` maps to their `es-ES`. Luckily, Crowdin has a way to help!

Back on the project dashboard, find and click the "Settings" tab. On the left,
choose "Languages". At the bottom, under "Add custom language codes", click
"Language Mapping".

In this dialog, in the "Language" dropdown, select "Spanish". For "Placeholder",
choose "locale", and for "Custom Code", enter "es". Click "Save". Locale code
mapped!

Next, we'll push our English translations up to Crowdin, do some translating, and
pull the translated versions back down.
