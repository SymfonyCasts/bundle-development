# `translation:extract` Command

So far, we've been manually creating translation keys and adding their
entries to our `messages.en.yaml` file. But did you know there's a somewhat
automated way to do this too? Let's check it out using the example of
translating two menu items.

Take a look at your code in `templates`, `base.html.twig` and find
'weather' and 'journal'. Let's create the keys like we usually do, naming
them `base.weather` trans and `base.journal` trans.

Now, instead of manually adding the entries into our `messages.en.yaml`
file, I'm going to show you a nifty command. Head over to your terminal and
run:

```terminal
symfony console translation:extract en --dump-messages
```

This command will show you that it found five messages. The ones
highlighted in green are the new ones - those that don't currently exist in
our `messages.en.yaml` file.

## Adding New Keys to YAML File

To add the new keys to the YAML file, run the command again. But this time,
replace `dump messages` with `force`. Also include `--format=yaml` and
`--as-tree=3`. This ensures that the keys are indented three levels deep
within the YAML file.

```terminal
symfony console translation:extract en --force --format=yaml --as-tree=3
```

Hop back to your `messages.yaml` file and voilà! The new keys are added,
prefixed with two underscores. This prefix helps in easily identifying new
keys for a quick find and replace. You can even customize it if you want.

The next step is to replace the underscores with the appropriate values -
'weather' and 'journal'. Refresh your app and presto - they work like a
charm!

## A Word of Caution

This method works great when you're developing a new feature and only
adding a few keys. However, when you're translating a large site and
creating many keys at once, you might find it a bit cumbersome. I usually
stick to the manual process for such cases.

## Converting To Different Formats With `translation:extract`

Here's another cool tip. Let's say you need to send all English
translations to a translation service to be translated into French or
Spanish. However, your translator is not familiar with YAML. No worries!
`translation:extract` can convert to different formats too.

Try running this command:

```terminal
symfony console translation:extract en --force --format=xliff
```

This creates a new file, `messages.en.xlif`, in the translations directory.
This might not be super readable for us, but translation services can
easily load it into their system.

Let's pretend we got the translated messages back. All we would need to do
is rename the `en` to `fr` in the filename. After updating the
translations, switch to the French version on your page, and voila - your
French version is ready!

Note that we won't be using this `XLIF` file, so feel free to delete it.

## Upcoming: Translating to Other Languages

So far, we've mainly translated from our default English language. But how
about creating translations for French and Spanish? Stay tuned, we'll be
exploring another cool Symfony feature to do just that.
