# Localized Routes

Hey there! So you've successfully installed the translation component and
notified search engines that your page is now localized, defaulting to
English. But how do you identify the language of the users coming to your
site and how do you translate it? Before we dive into translating content,
it's crucial to determine the user's language. There are several ways to do
this.

## Identifying User's Language

One method involves using a user object. If a user is logged in to your
site, one of their account settings could be the language selected from a
dropdown menu featuring all languages supported by your website. But this
doesn't cover non-logged in users. If your site has a non-authenticated
area, what language do you use for that?

Another method involves using the request header. Every browser sends an
`Accept` header with each request, indicating the user's preferred
languages. This language preference is set when you install your operating
system. Symfony even has a `Request::getLanguages()` method that you can
use to fetch their preferred languages and adjust your content accordingly.
However, this method isn't ideal since it imposes a language on a user and
isn't great for search engine optimization if you want all your translated
pages indexed by Google.

## Localized Routing

A more preferred approach involves localized routing or translation
routing. You've likely seen it before, where the language code prefixes all
your paths in your URL. An alternative is a subdomain base, like
`en.example.com/about` or `fr.example.com/about`. But this is a bit more
complicated. So we'll stick to the simpler path-based translations, where
the paths themselves can be translated. For this tutorial, we'll just use
the prefix version with `en`, `fr`, etc.

## Configuring Routes

To configure this, you need to make changes on a per route basis. Let's
dive back into your code, specifically the
`src/Controller/ArticleController.php`. This is your main entry point. For
`app_homepage`, you can replace `#[Route('/', name: 'app_homepage')]` with
an array. Include the locales you want to use here.

Now, if you return to your app and test it out with `/fr`, you'll see that
nothing's changed because you haven't created any translations yet. But if
you view the source of this page, you'll see that the language is now `fr`.
So that part is working. You can use `es` here or `en`.

## Localizing All Routes

But we've hit a small snag. Let's say you're on the `fr` version, and then
you click one of these links here. You're suddenly back to English as the
default locale. It doesn't maintain the language through the clicks. But
there's a solution to this.

Back in the app, let's revert this change here. We'll keep it as is. And
we're going to go into `config/routes.yaml`. Here, we'll make all routes
loaded be localized and have those prefixes. We're going to add `prefix:
en: /en, fr: /fr, es: /es`.

If we go back to our app and go to the home page, we see that we're on the
French home page. And if we click one of these links, we also see the code
there.

To see how this works, let's jump over to our console and run:

```terminal
symfony console debug:router
```

You'll see that it automatically added those language codes as suffixes to
the route names. It's smart enough to know when you're generating a link on
an already translated page. For instance, when you're generating a link for
`app_article_show` on the `fr` home page, it knows to use the
`app_article_show_fr` version of that route.

## Setting a Default Locale

But there's another problem. The home page seems to be gone, which isn't
ideal. You could create a page where users choose their language, but let's
make one of the locales the default instead. This default locale won't be
prefixed by a code. In our case, we'll use English as the default locale.

This is simple to do. In `config/routes.yaml`, just leave the language code
as an empty string. It still has to exist for it to know that it's English
for unprefixed paths. Now, if you go back to the home page and refresh,
you'll see that the English path doesn't have the prefix, and you can still
navigate through your pages.

## Wrapping Up

Great! Now your app's routes are localized and ready for translation. The
next step is to make switching translations easier. Instead of manually
typing it in the address bar, you're going to add a little language
switcher. Stay tuned for that!
