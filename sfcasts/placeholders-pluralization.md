# Placeholders and Pluralization

Onward! Let's find a complex translation scenario. On the article page,
scroll down and find the comments section. This "3 Comments" header is
dynamic, the number changes based on the comment count.

Find this text in `templates/article/show.html.twig`. Here's the dynamic
value: `comments|length`. You might think to just translate "Comments" and
not worry about the dynamic part, but certain languages might have alternate
*word order variations*. Like "3 Comments" in English versus "Comments 3" in
a different language. So, we need the translation text to include the
dynamic part.

How do we handle this? Translation placeholders to the rescue!

## Translation Placeholders

Clear out all this text, and come up with a key: since we're in `article/show.html.twig`,
use `article.show.comments`. Add `|trans`, and as the first argument of the filter,
add an array. This is a key-value array, where the key is the placeholder,
and the value is the dynamic value. For the key, use `%count%`.
Keys wrapped in `%`'s are a standard Symfony convention. For the value, use `comments|length`:

[[[ code('e561aa39e4') ]]]

Now, open our `messages.en.yaml` file and add the new key, `article: show: comments:`.
For the value, use `%count% Comments`:

[[[ code('9c702b7b4a') ]]]

Most translation services know to ignore text in `%`'s, so you shouldn't need to worry about
"count" accidentally being translated.

Back to the app and refresh. Nothing super exciting but it is working!

Let's make sure it changes dynamically. Back in `show.html.twig`, temporarily replace
`comments|length` with just `1`:

[[[ code('64f98f0748') ]]]

Refresh the page, and great! "1 Comments". But, hmm, maybe it's not the end of the world,
but this should really read "1 Comment" - without the "s".

## Pluralization

Back in `messages.en.yaml`, at the beginning of the value, add `1 Comment|`:

[[[ code('e666bb3f8f') ]]]

This pipe (`|`) signals to Symfony that you have multiple versions for
pluralization. Go back and refresh the page, and it now correctly
displays "1 Comment". Make sure it still works with more than one
comment by switching back to `comments|length` in `show.html.twig`:

[[[ code('da6c87dc47') ]]]

Refresh the app, and yep, the pluralization works! Symfony knows to use the
`1 Comment` version when the count is 1, and the `%count% Comments` version
when the count isn't 1.

## ICU MessageFormat

It's worth noting that Symfony supports the official ICU MessageFormat.

To enable it, you need to adjust the filenames of your translation files
to add this `+intl-icu` text.

Placeholders are handled a bit differently. In the translation text, wrap the
placeholder key in curly braces. Then, when passing the placeholder key,
you don't need to wrap it in anything.

There's also a powerful condition system. This example shows how you can
change the message based on a `gender` placeholder.

Pluralization is also more powerful. You can use the condition system
to create really fine-grained pluralization rules.

Something to check out if you need it, but for now, we'll stick with the
basic system.

Next, we'll look at how to handle translation text that contains HTML.
