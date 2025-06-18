# Installing the Translation Component

Hey friends! I'm glad you've joined me for this
course on Symfony translations. You might not know this, but the
translation component is one of the oldest — it was introduced with Symfony
2.0. And, believe it or not, this is the first time we're going to explore it
thoroughly on SymfonyCasts. Our plan is to take an existing English-only site and
*translate* it into several languages. We'll explore how this works and
what's involved.

So, to follow along, download the course code linked above.
In the `start` directory, you'll find files that look like
this. Use the `README` to get set up, and when you're ready, run

```terminal
symfony serve -d
```

## Introducing the Space Bar

Welcome to the Space Bar, a fun space news blogging site. It has all the
standard blog features: articles, categories, tags, and comments.
Our goal is to translate this site!

But what does that mean? The Symfony translation component is designed for
translating hard-coded text such as menu items or something like this tagline.
You'll notice that our blog is driven by a database, which provides the articles.
The Translation component isn't intended to translate this type of *dynamic* data.

A good rule of thumb is: if the text you want to translate is commited to your
repository, like in a Twig template or controller, then the translation
component can translate it. If the text is stored in a database, like
article titles or bodies, then this is not the job of the component.

## Installing the Translation Component

First, we need to install the translation component. Jump over to your terminal
and run:

```terminal
symfony composer require translation
```

After installing the package, check what we've got by running:

```terminal
git status
```

Aside from the standard stuff, a recipe added this `translation.yaml`
configuration file, and a `translations/` folder at the root of our project.

Jump over to PhpStorm and check these out. The `translations/` folder is
where our translations will live - it's empty for now, but we'll
use it later.

## Configuring the Translation Component

`config/packages/translation.yaml` is where we configure the translation
component. This `default_path` tells Symfony where to look for our
translation files, and yep, it's set to that `translations/` directory.

The `default_locale` is super important. If a locale isn't configured or
if a translation doesn't exist for a certain locale, it will fall back to
this. When first taking an existing site and translating it, use the
original language of the site here. In our case, the default, `en` for
English, is what we want.

A quick note on these codes: these are unique ISO codes for languages.
English is `en`, Arabic is `ar`, Armenian is `hy`, and so on. Search for
"ISO language codes" to find this full list.

You might've seen five-digit codes like `en_US` or `en_CA`. These are
locality-specific: `en_US` is US English, and `en_CA` is Canadian English.
They're almost identical, but there are slight differences. For example, in
Canada, we spell color `C-O-L-O-U-R`, while in the US, it's `C-O-L-O-R`.

The five-digit codes can also be used to localize your site,
accounting for country-specific differences in number, currency, and time
formatting.

For this course though, we're not getting into "localization" so we'll stick to the
two-digit language codes for our "locales".

## Choosing Languages for the Site

I want to now configure the other languages our site will support.
I was hoping to use Klingon and Romulan, but they
don't have ISO codes... *yet*, so we'll stick to official codes. This is an
optional step, but it's best practice to add `enabled_locales` to your
`translation.yaml` config. We'll include English (`en`), French (`fr`), and
Spanish (`es`).

This doesn't prevent you from using locales
outside of this list, but there are benefits to setting it if you know the
exact locales your site will use. There's a small performance boost as
Symfony knows to cache translations only for those locales. More
importantly, it easily lets you *list* these locales within your code, like for a
language switcher or bulk operations, like generating a sitemap for all
pages in all supported languages.

## Add the `lang` Attribute

The last thing we'll do here, now that we have translations enabled for our
app, is go to `templates/base.html.twig`. PhpStorm is giving us a warning
about our `<html>` tag. It wants us to add a `lang` attribute. We can set
this dynamically with `lang="{{ app.locale }}"`. This is helpful for
SEO, as search engines can use this to determine the language of the page.

Pop back over to our site... refresh... and view the "page source".
Here we go: `lang="en"` - our default locale.

Next, let's figure out how to determine our end user's locale.
