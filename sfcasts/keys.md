# Translation "Keys"

We're *almost* ready to start translating this site! But before we get started,
I want to show a little teensy tiny problem with our setup so far.

We still have this "Hello World!" text so jump into `templates/article/index.html.twig`
where we're translating it. Remember, we have the French translation for this setup
in `messages.fr.yaml`...

Suppose we don't want to "yell" this anymore so remove the exclamation mark.
I prefer yelling this but the top brass doesn't like it.

Can you see the problem?

Go to the English homepage and refresh. Ok, we see "Hello World" without
the exclamation mark. But switch to French... we broke our translation,
it's falling back to English!

Of course, the fix *could* be to update the key in `messages.fr.yaml` to match the
English version... But, it's kind of annoying that a minor punctuation change
can break our translations.

## Adopting Best Practices with Translation Keys

A common practice is to use *translation keys* instead of the *real* text.
These are kind of like *cache keys* for your translations. So instead
of `Hello World` in `index.html.twig`, use a
*keyified* version: `hello_world`. But... if you refresh the English page now, it'll
just show this keyified text.

We need a new translations file for English. In the `translations/` folder, create a new
`messages.en.yaml` file, and inside, add `hello_world: "Hello World"`. If
you refresh the page now, it's working again.

On the French page, it's currently falling back to English. Fix that by opening `messages.fr.yaml`, change
the key to `hello_world`, go back to our app, and refresh. *Now* we're in business.

Admittedly, using keys is a *bit* more work, but it pays off in the long run.

Cleanup our testing code by removing everything in `messages.fr.yaml`
and `messages.en.yaml`. In `index.html.twig`, remove the `hello_world`
translation. Go back to our English homepage, perfect! We have a clean slate.

## Translating the Site

Now, *finally*, we can begin translating this site for real!

Start with "Local Asteroids". In `base.html.twig`, find the menu item
with the "Local Asteroids" text. Select this text and cut it, so it's in
your clipboard.

We need to come up with a *key* for this... Keys can be anything unique, so
we could use `local_asteroids`, but a huge flat list can get confusing and hard to work with.
Keys can be split into namespaces, separated by a dot. So we could use
maybe, `menu.local_asteroids`. This is better, but still a bit generic.

What I like to do is name my namespaces based on the context of where the text is used.
In this case, the text is in `base.html.twig`, so, use `base.local_asteroids`. Don't
forget to add `|trans`.

If this translation text was in a controller, you might use a shortened version of the
controller class name or even the route name. It's best to find a good convention,
and stick with it!

What about multiple unique keys with the same text? Honestly, I don't worry too much about this.
Most translation services don't charge extra for duplicate text, and I find this
convention more important than the uniqueness of the text.

We have our key name, and the text in our clipboard, so let's add the translation
to `messages.en.yaml`, our English translation file for now.

We *can* add the keys as a flat list, like with `base.` but...
Because we're using YAML as the format, we can use indentation to create the
namespaces as a way to visually group them! Most of the other translation file formats
don't support this, so this is why devs prefer YAML for translations.

Add `base:`, new line, indent, and add `local_asteroids:`. Then, in quotes,
paste: "Local Asteroids".

Back to our app, refresh... And... Bah! We have a typo in our key!

In `base.html.twig`, PhpStorm's Symfony plugin is even highlighting this key with a warning.
Delete the text after the `_` and check this out! PhpStorm is auto-suggesting
the correct key! Fix that, refresh the page... and sweet, it's working!

I really like this process of translating text: one-by-one, find the text you want to
translate, cut it, come up with a key, add the `|trans` filter, then, in your
default language translation file, add the key, and paste the text.

Next, let's look at how to handle translating text with *dynamic* content!
