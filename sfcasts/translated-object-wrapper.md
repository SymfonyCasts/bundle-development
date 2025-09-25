# Translated Object Wrapper

Alright, so now we have a way to store our translations. Let's dive into
how we can make use of them. If you pull up our `ObjectTranslator`, we have
this *todo* waiting for us.

There's a couple ways we can approach this. One way is to directly modify
the properties on the object. But since this is a Doctrine entity, it'll
be marked as *modified*. If you unintentionally flush, you'll overwrite
your default locale translations on the entity itself. We definitely don't
want that.

Another approach would be to first clone the object, then modify the properties.
This is better... but if you accidentally try and persist this new object,
it might save a new version to the database, which would be bad.

Let's explore my preferred option: creating a wrapper for the object, you can
think of this as a *view model*. This wrapper will pass all method calls
and property access to the underlying object. Then, when a property is
accessed, it will first check if a translation exists for that property. If
so, it'll return the translated value; otherwise, it will return the
original value.

## `TranslatedObject` Class

First, in your bundle's `src` directory, create a new class named `TranslatedObject`.
Mark it as `final` and create a constructor. It'll accept a single parameter,
`private object $_inner`:

[[[ code('1b2b540462') ]]]

The underscore is a convention to ensure there's no
conflict when calling methods or properties on this wrapper.

## Adding Generics for Better IDE Support

We're going to use PHP generics again here, but at a class level. Add a class doc
block, and add `@template T of object`. Then, make this a mixin by
adding `@mixin T`:

[[[ code('c9ee64bd1a') ]]]

This essentially means that this object will have all
the same methods and properties as the `T` template object.

Next, add a doc block to the constructor, and instead of `@param object`,
replace with `@param T`:

[[[ code('a2daed4ab1') ]]]

This lets PHPStorm know that we're injecting this
class-level template object here.

## Magic!

So, how do we forward method and property calls to the inner object? Magic
methods! Override three methods: `__get()`, `__isset()`, and `__call()`.

`__call()` is *called* when a method is used that doesn't exist on *this*
object. Set the return type to `mixed`. Inside, write
`return $this->_inner->$name(...$arguments)`.

[[[ code('08ecf400eb') ]]]

This forwards any method calls to
the inner object by using the `$name` variable as the method name. PHP is cool like this!

`__get()` is invoked when trying to access a *property* on this object that
doesn't exist. Set the return type to `mixed`, and inside, forward to the inner
object with `return $this->_inner->$name`.

[[[ code('612478e939') ]]]

Again, using the `$name` variable
as the property name.

Finally, `__isset()` is called when using `isset()` on a property that doesn't
exist. Forward the call with `return isset($this->_inner->$name)`:

[[[ code('45a0c40dc7') ]]]

Now, if you've written this type of object before, you might be thinking:
"Wait a minute, what about the `__set()` magic method?" Good question!
We want this to be a *read-only* wrapper, so we're skipping this magic
method.

## Using `TranslatedObject`

Let's put this new class to work! Go to `ObjectTranslator::translate()` and
remove the *todo*. Wrap this `$object` in `new TranslatedObject()`:

[[[ code('0f318a39f4') ]]]

Remember, in our `ArticleController::show()` method, we're running the article
object through this translator and passing it to Twig.

Time to try this out! At your browser, visit an article page. Ok, so far so good...
But we're on the English version - our default locale. Remember, in
`ObjectTranslator::translate()`, if we're on the default locale, we're just
returning the object as is. This isn't using our new `TranslatedObject` class.

So, back in the browser, switch to the French version... Uh oh, an error: "Call
to undefined method `Article::title()`". Hmm, this worked no problem with the *real*
article object. What's going on here?

This is a Twig nuance we'll need to account for in our wrapper. We'll tackle
this next, and with a unit test to boot!
