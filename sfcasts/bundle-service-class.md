# Bundle Service Class

Alright, we've successfully installed our bundle into our app. But right
now, it's not doing much. It's just a skeleton with nothing more than that
bundle class. Let's breathe some life into it by creating our bundle's
first service.

In our bundle's `src` directory, we'll whip up a new PHP class. The star of
the show? Our first service, the `ObjectTranslator` service. We're focusing
on translating objects in this bundle, and this service is going to be the
key player.

Let's put our namespace in place and hit enter.

## Establishing the Class Structure

To keep things tidy, we'll mark this class as `final`. We want to make it
clear to our users that this class is not intended to be extended. This
way, we can tweak the internals while ensuring backward compatibility.
We'll explore more smart tricks like this throughout our journey together.

Let's roll up our sleeves and add our first method. We'll name it `public
function translate(object $object): object` and, as you can guess, it will
return an object. For now, let's keep things simple and just return the
object as it is.

## Using PHP Generics

Now, here's something a bit advanced but incredibly useful - PHP generics.
Let's add a doc block and use `@template T of object` at the top. Not only
does this help with static analysis tools like PHPStan, but it also makes
our IDE super happy. It'll be able to recognize when we pass an object to
`object`, and return the same object.

We'll use `@template T`, where `T` can be anything but in this case, it's
an object. We'll replace `object` with `T` in our parameter and also in our
`@return` statement. This will signal to our IDE that any object we pass
here will return the exact same object. Trust me, it'll be really cool when
we see it in action.

## Building the Constructor

Our service needs a constructor to inject a few goodies. We'll say `public
function __construct(private LocaleAwareInterface $localeAware, private
string $defaultLocale)`. We need the `LocaleAwareInterface` service to get
the current locale of the request, and we'll also need our app's default
locale. Let's add some initial logic for when we translate an object.

First off, we'll grab the current locale of the request using
`$this->localeAware->getLocale()`. If someone tries to translate an object
to the default locale, we won't need to do anything. Hence, we'll just
return the raw object in that case with a simple `if ($this->defaultLocale
=== $locale)` check.

For now, let's just add a placeholder for the actual translation logic.
We'll insert `// translate object` and mark this with `// todo`.

## Integrating the ObjectTranslator Service

Let's figure out where to use this new service. Back in our app, on our
article page, we're going to translate the articles. So, let's dive back
into our code. In `src/Controller/ArticleController`, inside the `show`
method, we'll inject our new service, `ObjectTranslator $translator`.

Now, let me show you something cool. If we assign `article` to
`$translator->translate($article)`, we can see all the methods from the
article thanks to our IDE. This might not seem like a big deal, but we're
able to do this thanks to the `template` doc block we added earlier.

If we refresh our app, we'll see a problem. Bundle services, like our
`ObjectTranslator`, are not auto-wired by default. We need to configure our
bundle to let apps know that this service is available and can be
auto-wired. Let's tackle that next.
