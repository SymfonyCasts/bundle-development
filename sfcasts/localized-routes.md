# Localized Routes

We installed the translation component. Go us! Also our pages are 
advertised via the `lang` attrubute as our default locale: `en`. But how can we figure, which
language a user wants? There are several options.

## Identifying User's Language

One method involves using the `User` entity. If a user is logged in to our
site, one of their account settings could be "language". But then, what about
non-authenticated users? Ya know, the shy ones lurking around the edge of our site.

Another method involves using a request header. Most browsers send an
`Accept-Language` header with each request, advertising which languages the user prefers.
This is typically set when you install your operating
system. Symfony even has a `Request::getLanguages()` method that you can
use to fetch their preferred languages. Then you can translate into that.
The trouble is, it's not great for SEO. Because a page will have the same URL
whether it's in Italian, Spanish or Fremen, search engines can only index
one of them. Boo!

## Localized Routing

A better approach is localized, or translated,
routing. You've probably seen it before, where the language code prefixes every
the path, like `/en/about` or `/fr/about`. Another method is using subdomains, like
`en.example.com/about` or `fr.example.com/about`. You can also translate
the path text, such as `/about` for English and `/a-propos` for French.

For this tutorial, we'll just use the path-prefix version: `/en`, `/fr`, etc.

## Configuring Routes

We can configure this on a per-route basis.
In `src/Controller/ArticleController.php`. In the `#[Route]` attribute for
the `index()` method, replace the first argument with an array. Inside,
use the locale code as the key, and the path as the value:
`['en' => '/en', 'fr' => '/fr', 'es' => '/es']`.

Go back to our app and test it out by adding `/fr` to the URL.
Hmm, looks the same... That's because we haven't created any translations
yet. But if you inspect the source of this page, the
`<html>` tag's `lang` attribute is now `fr`. It's working! You can
also go to `/es` and `/en`.

## Localizing All Routes

We have a little bug though. Let's say you're on the `/fr` version, and
click and article link. We're on a non-locale-prefixed route now. If we
inspect the source, we see that the `lang` attribute is back to `en`.
Not a great user experience. If you're on a French page and click
a link, you should stay on a French page, right?

One solution is to duplicate our route locale map for every route... but...
that's a bummer. Luckily, there's an easier solution!

Back in our code, revert the locale mapping we added. Open `config/routes.yaml`.
This `controllers` entry is telling Symfony to load all methods marked
with the `#[Route]` attribute in our `src/Controller` directory. We can
add our locale prefix map here to apply it to all the routes loaded:
`prefix:`, `en: /en`, `fr: /fr`, `es: /es`.

In our app, go to the French homepage: `/fr` and click on an article.
Boom! We're on the French article page! Swap to the `/es` version of
this page and click the logo to go back to the homepage. Awesome! We're
on the Spanish homepage now. UX win!

To see *how* this works, jump over to the console and run:

```terminal
symfony console debug:router
```

Our routes are duplicated for each locale, and suffixed with the locale
code. Symfony is smart enough to know that the unsuffixed version of the
route name, what you're used to using, is the "magic" version. Behind the
scenes, it uses the current locale to determine which suffixed version of
the route to use. For instance, when you're generating a link for
`app_article_show` on the `fr` homepage, it knows to use the
`app_article_show.fr` version of that route. Cool huh?

## Setting a Default Locale

There's another problem though: go to the *real* homepage, the one without a
locale prefix. Oh, this is the special Symfony "getting started" page
that displays if you don't have a "homepage" route defined - remember,
our homepage is actually `/<locale code>`. If you look at the bottom left,
this is actually a 404 error.

What can we do here? Well, we could create a non-prefixed homepage route
that allows the user to choose their language, or maybe, redirects them
to the prefixed version for their browser's language. But let's make
our default language, English, not prefixed by its locale code.

This is super simple to do. In `config/routes.yaml`, change the `/en` prefix
to just an empty string.

Now, go back to the *real* homepage and refresh. It works! Navigate to
an article, and we are on the un-prefixed version of the article page. Go to
the `/fr` homepage, click and article... nice, we're on the `/fr` version
of the article.

We can now navigate to any language page but... we have to
manually chang the URL in the address bar... lame! So next: let's create a
language switcher widget!
