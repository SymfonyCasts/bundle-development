# Create a Language Switcher

Great job on getting your localized routing up and running! You've seen how
you can manually update the URL with your prefixes, and it's working like a
charm. But you're probably thinking there's a more efficient way to switch
between languages. And you're right! Let's add a language switcher next to
your search form. But before we dive into the code, let me introduce you to
a special route parameter.

If you take a peek into the web profiler, you'll notice a bunch of
underscore request attributes. These are internal to Symfony, and the one
that'll pique your interest is `_locale`. It's set to the locale of the
current page (like `FR`), and we'll be using this to generate URLs for
different locales.

## Building the Language Switcher

Alright, it's time to create your very own language switcher. Let's head
back to the code. In your tutorial directory, grab the language switcher
code, copy it, and paste it into `templates/base.html.twig`, just above the
form tag. Now, if you head back to your app and refresh, you'll see a
placeholder for the language switcher.

Let's breathe life into it. For the anchor tag's language, we need to make
it dynamic. We'll use the current locale, which we know is `app.locale`. In
the `UL` for these `li's`, we'll do a `for each` loop: `for locale in
app.enabled_locales`.

This is where you can leverage those enabled locales you set up earlier.
After that, you'll do an `endfor`. Let's tidy up the code a bit by
indenting this, and we'll also add an `if` statement `if locale !=
app.locale`. Remember, `enabled_locales` includes all locales, so we only
want to show the drop-down options for the locales that you can switch to.
Then, you'll close the `if` statement with an `endif` and indent this.

For the text, you'll insert `{{ locale }}`, and for the `href`, you'll use
`path`. Then, you'll use `app.current_route` — a special feature to fetch
the current route of the page. For the attributes, you'll use
`app.current_route_parameters` and merge this with `locale: _locale:
locale`. This will let you change the locale for this page.

## Testing the Language Switcher

Let's put this to the test. If you're on the French page and refresh, you
can now switch languages — and voila! Here are your other locales. Let's
switch to `ES` and now you're on the `ES` version. Switching to English
will take you to the unprefixed English homepage. And if you navigate to
one of your articles, you should still be able to switch to the French
version without leaving the page.

## Fixing a Minor Issue: Removing the Trailing Slash

One minor gripe you might have is the trailing slash when you go to the
homepage of a prefixed locale. If you're not a fan of that (and I'm not
either), let me show you a quick fix. Head back to your code, go to
`config/routes.yaml`, and add this option: `trailing_slash_on_root: false`.
A quick refresh later, and that pesky slash is gone. It's a small detail,
but little things like these can make a big difference. And don't worry, it
won't break anything if you decide to leave it as is.

## Looking Ahead: Translating Content

With all that prep work out of the way, you're in great shape! Next up,
we're going to dive into the exciting world of content translation.
