# Translating Content

Hey there, it's time to dive into translating some content. With Symfony's
translation components, you have two options to translate content: either
directly in your PHP code, such as in your controllers, or within your Twig
files. The first option is perfect for scenarios like translating a flash
message. But, you'll find that the most common way to translate content is
within the Twig files.

## Translating PHP Code

Let's get started with PHP translations. Open `Source, Controller,
ArticleController`.

On our home page, we're going to inject `TranslatorInterface`. Then, just
right here, we're going to `dump(Translator, Trans, and Hello World)`. Now,
if we hop back to our app and hit refresh, you'll notice `Hello World` in
the panel. That's because we're on the English page, which is what we'd
expect.

Let's see what happens if we switch to the French version. It still says
`Hello World`. Don't worry, Symfony isn't confused. We just haven't created
the translations yet.

So, let's get to it.

## Creating Translations

Go back to the code and navigate to the empty translations directory. Here,
create a new file called `messages.fr.yaml` (add it to Git, of course).

The key value for our translation is `Hello World!`, which is the English
version. Let's provide the French version, `Bonjour Le Monde`. Now, refresh
the page and voilà, `Bonjour Le Monde`.

But what if we switch to Spanish? It reverts back to `Hello World`. That's
because English is our fallback. So it reverts back to the original text we
added or tried to translate.

## Translating Content with Twig

Now, let's see how to translate content with Twig. In the code, navigate to
`templates, article, index.html.twig`, and insert `Hello World` followed by
`trans` in any div. That's it, we've applied the `trans` filter.

After a refresh, we see `Hello World` because we're on the Spanish page.
Switch to French, and there it is, `Bonjour Le Monde`.

## Diving Deeper into TranslatorInterface

Let's explore the `TranslatorInterface` a bit further. If we go back to our
`ArticleController` and delve into the `TranslatorInterface`, we'll find
the `trans` method, which is our main entry point. The ID refers to the
text we want to translate, while parameters (we'll cover this more in the
next chapter) allow us to pass parameters to our string.

The domain is essentially a category or namespace of translations and
defaults to `messages` (which is why we created that `messages.yaml` file
earlier). Lastly, there's the locale. By default, it'll use the current
locale, but you can override this within the `trans` method.

## Understanding Translation Files

Our `messages.fr.yaml` consists of `messages` (the domain), `fr` (the
locale), and `.yaml` (the loader). Symfony has several loaders it can use
to load these translation catalogues.

YAML is the go-to for developers, but there are many others. Symfony's
translation documentation lists several options. The most commonly used for
projects is YAML. It's developer-friendly, but might not be ideal if you're
working with a translation service that prefers a different format.

A common format used by translation services is the XLIF file format. It's
basically an XML file but isn't as friendly for developers. So, unless
you're working with a bunch of translations, stick with YAML.

In the next tutorial, we're going to dive into translating our site for
real. Stay tuned!
