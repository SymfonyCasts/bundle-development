# Translating Content

It's time to actually do some translating! There's two main places you
typically translate content. First: in PHP code, like flash messages in your
controllers. Second: in Twig files. This will be where you do most
of your translating.

## Translator Service

Let's get started with PHP translations. Open `src/Controller/ArticleController.php`,
and in the `index()` method, autowire `TranslatorInterface $translator`. Below,
`dump($translator->trans('Hello World!'))`.

Back in our app, on the English homepage, refresh. Down in the debug toolbar, check
out the dump: "Hello World!". Makes sense, we're on an English page, so it shows
the English version.

Switch to the French version of this page and check the dump: "Hello World!"...
Hmm, shouldn't this be French? Yes, but we haven't created the French translation
for this text yet! Merde! Shouldn't Symfony be smart enough to do this for us?! It's
not *that* magic... yet, so let's create the French translation!

## Creating Translations

In our code, find that empty `translations/` directory and create a new file:
`messages.fr.yaml`. I'll explain the filename in a moment. Trust me for now.

This is a key-value list, where the key is, in our case, the English
version of the text we want to translate - the string we passed to `trans()`,
and the value is the French version.

Add `"Hello World!": "Bonjour le monde!"`.

Go back to the app, refresh the page... and voilà, `Bonjour le monde!` is dumped!

Switch to the Spanish version. Hmm... back to "Hello World!"? This actually makes sense
because we haven't created the Spanish translation yet, and, because our default
locale is English, *it's* text is used as the fallback.

## Translating Content with Twig

Now, let's translate the same text, but in Twig. Open `templates/article/index.html.twig`,
and in this `<div>`, write `{{ 'Hello World!'|trans }}`. Behind the scenes,
the `trans` filter uses the `TranslatorInterface` service.

Refresh our app... we see `Hello World!` because we're on the Spanish page.
So switch to French, and now it's `Bonjour le monde!`.

## Diving Deeper into `TranslatorInterface`

Let's explore the `TranslatorInterface` a bit further. Go back to our
`ArticleController` and dig into the `TranslatorInterface`.
This `trans` method is where the magic happens. The *ID* refers to the
text we want to translate, *parameters* allow you to pass dynamic values to
the text: we'll cover that in the next chapter.

The *domain* is essentially a category, or namespace of translations and
defaults to `messages` (which is why we created that `messages.fr.yaml` file
earlier). Finally, there's the locale. By default, it'll use the current
locale, but you can override it if you're feeling wild.

## Understanding Translation Files

Let's circle back to our `messages.fr.yaml` file. This naming convention
is important.

As I said, `messages` is the translation *domain* - `messages`
being the default. For advanced apps, you might have multiple domains
and 3rd-party bundle's can define their own. For instance,
the KnpTimeBundle, provides a `time` domain for its translations. You can
check them out in this bundle's `translations/` directory.

This takes us to the next part of the filename: the locale code. You can see
all the different locales this bundle supports.

If you want to *override* a bundle's translation, create a
new `<bundle-domain-name>.<locale-code>.yaml` file in your `translations/` directory. These
overrides will be merged over the bundle's so you only need to set the
translations you want to change.

The last part of the translation filename is the extension, this tells Symfony
what *translation loader* to use. The loader's job is to read the
file and normalize the data so Symfony can use it. We're using `yaml` but
the KnpTimeBundle uses `xliff`. Over in the Symfony docs, you can see all
the available loaders: choose your favorite!

The two most common are `yaml` and `xliff`. `yaml` is super
developer-friendly, and easy to read. `xliff` is XML-based so it isn't fun
to read, but it's a standardized format used by many translation services.

Next, let's look at a more manageable way to create translations and
start translating this site!
