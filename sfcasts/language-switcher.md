# Create a Language Switcher

We have localized routing up and running, but we can only switch locales
by manually updating the URL. Let's make it easier for users to switch
by adding a language switcher, up here, next to our search form. This will
have links to each version of the current page, for all our supported
locales. But hmm... how can we generate a link to a route for a different
locale?

If you take a peek into the web profiler for this request, you'll notice a bunch of
underscored request attributes. These are mostly internal to Symfony, but this
`_locale` one is important. When using localized routes, Symfony sets this
to the current locale of the request. What's cool, is that you can use this
as a route parameter to generate URLs to different locales.

For instance, to generate a URL for `app_homepage`, if you pass `_locale: 'fr'`
as a route parameter, the generated URL will be `/fr`, regardless of the
current locale.

Onto the widget!

## Building the Language Switcher

In the `tutorial/` directory, select everything, and copy it. Now, in
`templates/base.html.twig`, find the `<form>` tag, and just above it,
paste.

Head back to our app and refresh, and here it is! It's just a stub right now,
but you can see how it should work. This button will show the current locale,
and when you click it, there's a drop-down with links to the other locales.

Let's wire this baby up!

Back in `base.html.twig`, find the anchor tag with the text "Language:". This
is the button. For this hard-coded text, `en`, we want the current locale,
we know how to do this already, `{{ app.locale }}`.

Down in the `ul`, which is the drop-down menu, before the `li` tag, add
`{% for locale in app.enabled_locales %}`. I told you configuring `enabled_locales`
would come in handy! Under the `li`, add `{% endfor %}` and indent the guts.

This now loops though all the enabled locales, but *also* the *current* locale.
We want to exclude this, so add a condition before the `li` tag:
`{% if locale != app.locale %}`. Add `{% endif %}` below, and indent.

For the link text inside the `li`, use `{{ locale }}`. For the `href`, we
*could* send them to the homepage for that locale... but we can do
better! I want them to stay on the same page, just with a different locale.

Check this out:
`{{ path(app.current_route, app.current_route_parameters|merge({_locale: locale})) }}`.
`app.current_route` gives us the current page's route name, and
`app.current_route_parameters` gives us the current page's route parameters.
We're merging these with a new parameter, `_locale: locale`. This will
generate a URL for the same page, but with a different locale.

## Testing the Language Switcher

Moment of truth! Head back to our app and refresh. We're on the French
homepage and our widget shows "Language: FR". So far, so good! Click
the button, and here's our other locales, but with "FR" excluded. Click
"ES" - Spanish. Boom, we're on the Spanish homepage! Switch to
"EN", yep, we're on the un-prefixed, English homepage.

Click an article, and switch to "FR". Awesome! We're on the French version
of the same article. Our widget works perfectly, and I think, creates an
*awesome* user experience!

## Removing the Trailing Slash

One *super* minor gripe of mine, is that when you're on a locale-prefixed
homepage, like `/fr`, there's a trailing slash: `/fr/`. There is no real
technical issue with this, I just dislike it. And it's super easy to
remove.

Open `config/routes.yaml`, and under the `controllers` key, add the option
`trailing_slash_on_root: false`. That's it!

Go back to our app... refresh... and the trailing slash is gone! Phew!
I can sleep easy tonight!

Ok, enough prep work! Next, let's actually translate some content!
