# HTML in Translations

Here's another complex scenario. On our homepage, scroll down to the footer.

I want to translate this text... but, it contains an icon and link HTML...
You might think to just translate the two text parts separately, but again,
in different languages, they may need to be ordered differently.

So we need the HTML *in* the translation text. Let's look at two different approaches.

## Raw HTML in Translation Text

In `base.html.twig`, find the footer text.

The simplest solution is to just straight up, add the text, HTML and
all, as the translation value. So, select all the text and cut it. For the
key, use `'base.footer'|trans`.

Over in `messages.en.yaml`, add a new key called `base: footer:`, and
inside single quotes, paste. We need to use single quotes here because
the HTML contains double quotes.

Go back to our app and refresh... Whoa, this ain't right. The HTML was escaped.
Twig does this by default to prevent XSS attacks from user-submitted data. For
this particular case, we can safely disable this behavior since we're in full
control of the text.

Back in `base.html.twig`, add `|raw` after `trans` to disable escaping.

Refresh... and Voila! It works!

This is quick and easy, but has some downsides. First, it mixes HTML
into our translation text, which can make it harder to read and maintain.
Second, it duplicates the HTML in every translation file, which can make it
harder to update the HTML down the road.

Let's try a different approach: adding the HTML as placeholders.

## HTML Placeholders

In `messages.en.yaml`, copy the full text, and in `base.html.twig`,
below our footer translation, paste it temporarily.

First, copy the icon HTML, and in the `trans` filter, add it as a placeholder:
`'%heart%': ''`, and paste. Now, copy the link HTML and add it as another
placeholder: `'%link%': ''`, paste. Because we're still rendering HTML,
the `|raw` filter is still required.

Delete the temporary line we pasted and jump back to `messages.en.yaml`.
Replace the icon's HTML with `'%heart%'` and the link's HTML with
`'%link%'`. Right away, this looks a lot cleaner and easier to read.

Ok, back to the app and refresh... Oi! A syntax error!

Ahh, the `trans` placeholders need to be wrapped in an array. In `base.html.twig`,
wrap them in curly braces.

Refresh... scroll down... there we go!

This approach is much cleaner, but also has a downside. What if our link HTML had text or an
attribute that needed to be translated? There's no simple way to handle this.
You'd have to either translate the elements separately or do some nested translations:
like translate the placeholder. Hopefully, this problem doesn't come up too often.

## Translation Workflow

So that covers all the common scenarios. I won't have you watch me translate
the rest of the site as it would be more of the same, and boring... I'll leave
that as a homework assignment!

Next, we'll look at the translation debugging tools Symfony provides!
