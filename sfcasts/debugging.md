# Debugging & Linting Translations

Did you finish your homework? Don't worry, neither did I! Let's bike shed
that for now and look at debugging translations. This'll be helpful for
that homework anyway!

## Web Debug Toolbar

On the English homepage, you might have already noticed this new icon in
the web debug toolbar. This is for translations. Hover over it to see what
it gives us.

"Default locale: EN" - this is a bit of a misnomer, it isn't our app's
default locale, but rather the locale used for this request.

"Missing messages: 0" - the number of messages we're trying to
translate (using the `trans` Twig filter or `TranslatorInterface` service)
that *don't* have an entry in our `messages.en.yaml` file.

"Fallback messages: 0" - the number of messages that have a translation
in our app's default locale, English, but not in the current locale,

"Defined messages: 2" - the number of messages that were found and used
for the current locale successfully.

These are all within the context of the current request. You'd see different
numbers for different requests.

## Translation Profiler Panel

Click the button to see the "Translation Profiler Panel". This shows us
even more details for each of the above. The domain, messages,
times used, and a preview. Even the parameters used!

Let's see what this looks like for the French homepage. Right away, we can
see the button is being highlighted yellow to indicate a "warning". And
indeed, we don't have the 2 messages defined for French, so it's using the
English fallback. Click into the profiler. 0 messages defined, jump
to the Fallback tab to see the English messages being used as the fallback.

So this is cool for debugging translations on the current request, but what about
getting the global translation status for our whole site?

## `debug:translation` Command

Jump over to your terminal and run:

```bash
symfony console debug:translation en
```

Cool, these are all the English translation messages for our entire site. The
"State" column shows us the status. Empty means good!

Run the same command, but for `fr`:

```bash
symfony console debug:translation fr
```

Oh boy, lots of issues! The "State" in this case is showing as "missing", but
really, it's using the English fallback, which we can see the preview of.

## `lint:translations` Command

There's also command to "lint" our translations:

```bash
symfony console lint:translations
```

All this really does it check to see if all our translation files are valid -
like they can be loaded and parsed correctly. I think if a file was invalid,
you'd find out pretty quick during local development. But it can be useful
in a Continuous Integration (or CI) pipeline, like GitHub Actions. If there's a
problem, this command will fail, and therefore fail your job.

## Continuous Integration

Speaking of CI, a super useful command to include in your jobs is
`debug:translation` for your default locale. This command will fail if
there are any *missing translations*. This could help enforce a rule that
"every Pull Request must have translations for the default locale".

Depending on how strict you want to be, you could even run this command for
each of your supported locales. It'll also fail if there are fallbacks being
used.

Next, let's add some "missing translations" and check out another command to
"extract" them.
