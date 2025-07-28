# `translation:extract` Command

So far, we've been creating translation keys and manually adding them,
and their values to `messages.en.yaml`. But did you know there's an
alternative method that automates part of this process?

We'll translate these two remaining menu items: "Weather" and "Journal".

Open `templates/base.html.twig` and find the text for these menu items.
Follow our usual routine of coming up with a key, and using the `trans`
filter. `{{ 'base.weather'|trans }}` and `{{ 'base.journal'|trans }}`:

[[[ code('a2e445fc46') ]]]

Ok, instead of manually adding these entries to our `messages.en.yaml` file,
head over to your terminal and run:

```terminal
symfony console translation:extract en --dump-messages
```

This command is similar to `debug:translation` in that it scans your code
and finds translation keys. These keys in green are those new ones we added
and don't have entries in our `messages.en.yaml` file yet.

## Adding New Keys to YAML File

We can use the command to auto-add these new keys to our YAML file. Run
the command again, but replace `--dump-messages` with `--force`. Then,
add `--format=yaml` so it writes as YAML, and `--as-tree=3` to keep
the keys indented three levels deep in the YAML file.

```terminal-silent
symfony console translation:extract en --force --format=yaml --as-tree=3
```

Hop back to `messages.en.yaml` and check it out! Here's our new keys!

[[[ code('68b48a2e83') ]]]

The values are the key names prefixed with `__` (two underscores). This
can help you quickly find them if there are a bunch. This prefix can be
customized if desired.

Replace these with the proper text: "Weather" and "Journal":

[[[ code('1eccaf40db') ]]]

Refresh your app and presto... well nothing changed - but we know it's
working!

If you're mass translating an existing site like we are, I find this method
can be more cumbersome than our previous process. What ends up happening is,
when mass adding keys in your code, once you extract them, you forget the
original text!

But! This method works great when developing a new feature. You can add the
keys for text in your code, and figure out the actual text later.

## Converting To Different Formats

This command has another cool, perhaps unintended, feature!

Let's say you have a translation service who asks you to send your translations
file in English, and they'll send it back in French. Easy, right? Just send
them `messages.en.yaml`. But... they don't know what the *heck* YAML is! They
want the file in a format their software supports: `XLIFF`.

No worries! The `translation:extract` command can convert to different formats!

Run this:

```terminal
symfony console translation:extract en --force --format=xliff
```

In our `translations/` directory, we have a new file: `messages.en.xliff`.
Open that up.

Ugh! Gross! XML!

No worries - translation software loves it! Just send them this file and tell them to swap
the `<target>` text, which is English, with the French translations.

When you get it back, just rename it to `messages.fr.xliff` and place it
back in the `translations/` directory. Just fudge the first `<target>`
to read "(French) Local Asteroids":

[[[ code('066833f235') ]]]

Back in our app, switch to the French version of this page and boom!
"(French) Local Asteroids".

Ok, that's enough XML for today - delete `messages.fr.xliff`.

So what if you don't have a translation service that you can just send these
files to? No problem! There are cloud-based solutions that can help,
and Symfony has integrations for them! Let's check this out next!
