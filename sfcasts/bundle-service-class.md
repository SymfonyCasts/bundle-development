# Bundle Service Class

Ok, our bundle is installed and ready to roll. Time to add some functionality
by adding our first service class. This will be the star of the show. Our
bundle is for translating objects, so this seems like the place to start.

In our bundle's `src` directory, create a new PHP class: `ObjectTranslator`.

## `ObjectTranslator`

First, mark this class as final. This isn't meant to be extended. When developing
bundles, it's important to be explicit about your class design and their
intentions. This makes it easy to keep backwards compatibility. Removing `final`
later isn't a breaking change, but adding `final` is. We'll explore more of these
tricks as we go along:

[[[ code('eda36dbdac') ]]]

Create a method: `public function translate(object $object)`. Return type: `object`. This will
eventually house the logic for translating objects. For now, just return the passed
`$object`:

[[[ code('afffbf9ff1') ]]]

Our service needs a constructor to inject a few goodies. Add `public
function __construct(private LocaleAwareInterface $localeAware, private
string $defaultLocale)`. We need the `LocaleAwareInterface` service to get
the current locale of the request, and we'll also need our app's default
locale:

[[[ code('f134be4b58') ]]]

Down in `translate()`, we can add some easy logic. Grab the current locale with
`$locale = $this->localeAware->getLocale()`.
Now, if the current locale is the same as the default locale, we don't need to
do any translating, so add an `if ($this->defaultLocale === $locale)` and
just return the raw object in this case:

[[[ code('0bf83206dc') ]]]

Below is where we'll eventually add the *real* translation logic, but just
add a comment for now: `todo translate object`:

[[[ code('7eb0b0c396') ]]]

Let's use this new service in `ArticleController::show()`. Expand this method
a bit and inject it: `ObjectTranslator $translator`:

[[[ code('fde71e6395') ]]]

Run the injected `Article` through our new service:
`$article = $translator->translate($article)`:

[[[ code('f1f1395dd8') ]]]

Sweet!

## PHP Generics

Notice if we try and access a method on `$article` *before* running through
our service, PhpStorm can auto-complete all the methods on `$article`. But...
if we try and access a method on `$article` *after* running it through
`$translator->translate()`, we don't have auto-completion. PhpStorm has no idea
what `$article` is now - just that it's "an object". This is a drag...
But we can fix this with *PHP generics*!

Generics are a way to provide additional type information to our editor.

Check this out: above `ObjectTranslator::translate()`, generate some doc blocks.
This just matched the method signature and isn't super helpful... so add
`@template T of object` above. This declares a template type `T` that must
be an object. `T` is like an alias, or placeholder and can be any string.

Now, for `@param` and `@return`, replace `object` with `T`:

[[[ code('3a1638f44e') ]]]

This tells our editor: "Whatever object type is passed to this method, the return type
will be the same object type."

Back in `ArticleController::show()`, after we call `translate()`, try auto-completing
again on `$article`. Boom! PhpStorm knows exactly what `$article` is now. I
love this!

Remove that extra code - the *translated* article is now passed to our template
so our work here is done.

Jump back to our browser and visit an article page... An error... "Cannot
autowire argument $translator..."

Symfony doesn't know about our bundle's service - it's just a plain PHP class
still...

Let's fix that next!
