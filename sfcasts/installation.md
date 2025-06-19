# Installing the Translation Component

Hey friends! e alegro de que te hayas unido a mí en este curso de
sobre las traducciones de Symfony! In this tutorial, we're celebrating
one of the oldest components in Symfony, but for the first time here
on SymfonyCasts!

Can it make my English site available in German? Ja! Can it translate my
Canadian accent to American? Probably not eh?

The plan is daring, but simple: take an existing English-only site and
*translate* it into several languages. including a language switcher
and even integration with services that can handle the actual translating!
To translate the most knowledge into your noggin, download the course code
linked above.
In the `start/` directory, you'll find files that look like
this. Use the `README` to get set up, and when you're ready, run:

```terminal
symfony serve -d
```

## Introducing the Space Bar

Welcome to the Space Bar, a fun space news blogging site. It has all the
standard blog features: articles, categories, tags, and comments.
Let's get this site translated! The Spanish-speaking quadrant of the galaxy awaits!

The Symfony translation component is designed for translating hard-coded text
like menu items or something like this tagline.
You'll notice that our blog is driven by a database.
The Translation component isn't intended to translate this type of *dynamic* data.
But we'll talk about that later.

A good rule of thumb is: if the text you want to translate is commited to your
repository, like in a Twig template or controller, then the translation
component is your friend. If the text is stored in a database, like an
article title or body, you'll need something different.

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

Aside from the standard stuff, the recipe added this `translation.yaml`
file, and a `translations/` folder at the root of our project.

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
Canada, we spell color correctly: `C-O-L-O-U-R`, while in the US,
they invented their own spelling `C-O-L-O-R`. Just kidding, they're both correct.

The five-digit codes can also be used to localize your site,
accounting for country-specific differences in number, currency, and date
formatting.

For this course though, we're not getting into "localization" so we'll stick to the
two-digit language codes for our "locales".

## Choosing Languages for the Site

Let's configure the other languages our site will support.
I was hoping to use Klingon and Romulan, but they don't have ISO codes... *yet*,
so we'll stick to official codes. This is optional, but it's a best practice to
add `enabled_locales` to the `translation.yaml` config. We'll include English (`en`),
French (`fr`), and Spanish (`es`).

This doesn't prevent you from using locales
outside of this list, but there are benefits to setting it if you know the
exact locales your site will use. There's a small performance boost as
Symfony knows to pre-cache translations only for those locales. More
importantly, it easily lets you *list* these locales in your code, like for a
language switcher or bulk operations, like generating a sitemap for all
pages in all supported languages.

## Add the `lang` Attribute

Now that we have translations enabled, go to `templates/base.html.twig`.
PhpStorm is giving us a warning about our `<html>` tag. It wants us to add a
`lang` attribute to the current language. Get this dynamically with
`lang="{{ app.locale }}"`. This is helpful for SEO.

Pop back over to our site... refresh... and view the "page source".
Here we go: `lang="en"` - our default locale.

Next, let's see how to figure out which language our user wants.
Adelante!
