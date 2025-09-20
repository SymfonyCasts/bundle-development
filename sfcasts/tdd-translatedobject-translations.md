# TDD `TranslatedObject` Translations

The current logic of our `TranslatedObject` is solid. It effectively passes
all method calls and property access to the underlying object. And... we have
the tests to prove it!

We previously used TDD to fix that Twig method call issue. Now, let's use
it to bang out a feature!

Our `TranslatedObject` needs to handle property translations. Before calling
the underlying object's method or property, it should check if a translated
value exists. If it does, it should return that translated value instead.

Let's get started!

## Start with Green Tests

First, before doing anything, confirm our test suite is all green. At your
terminal, run:

```terminal
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Green, great! Starting a new feature with TDD is a fools errand if your tests
are already failing.

## New Test

Back in `TranslatedObjectTest`, add a new test with:
`public function testCanTranslateProperties()`.

We write this test with the *logic we want to see*. So, copy the setup phase
from the test above and paste here.

Now, for the second argument of the `TranslatedObject` constructor, this'll be
an array of translated properties. We'll translate all properties for our
`ObjectForTranslationStub` below. Inside the array, write
`'prop1' => 'translated1', 'prop2' => 'translated2', 'prop3' => 'translated3',`.

You can see PhpStorm is marking all this as gray since the constructor doesn't
accept this parameter yet.

I think this looks pretty good. When you pass an array of translated values,
keyed by property name, when accessing these properties, we should get the
translated values back.

For the assertions, copy these from the first test and paste here. Now,
change all the expected values from `value` to `translated`. `translated1`,
`translated2`, and `translated3`.

I think we all know this isn't going to work but... let's let the tests tell us
that!

At your terminal, run the tests again:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Fail! But totally expected. Failed asserting that "value1" is "translated1"
on line 36.

Back in the test, line 36 is where we're accessing the `prop1` property. So,
let's add the logic to make this test pass!

This test... is driving... our development - get it?!

## Injecting Translations

Over in `TranslatedObject`, add a new property to the constructor:
`private array $_translations,` - remember, the `_` prefix is a convention
we're using just because this is a *mixin*.

Above, add a doc block `@param` for this new parameter, type: `array`. Let's
be clever and specify the key and value types of this array.
Inside angle brackets, write `string,string`. The first `string` is the key type,
the property name, and the second `string` is the value type, the translated value.
Finally, write `$_translations` to finish this doc block.

## Translating Property Access

Remember, our test is failing when accessing a property. So, down in the
`__get()` method, before returning the inner property, write
`$this->_translations[$name] ??`. This will check if a translated value exists
for this property name. If it does, it'll return that. If not, it'll fall back
to returning the inner object property.

Ok, back to the terminal and run the tests again:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Still failing... but look closely - it's now failing for "translated2" and "value2"
not matching - and on line 39.

Jump back to the test. Line 39 is where we're calling the `prop2()` method. That it
got this far means our translated property access logic on line 36 is working! Sweet!

## Translating Method Calls

Now to handle method calls. Over in `TranslatedObject::__call()`, at the top,
add `if (isset($this->_translations[$name]))`. Inside,
`return $this->_translations[$name];`. This checks if a translated value
exists for this exact method name. If it does, it returns that value.

You know what to do! Back in the terminal, run the tests again:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Failing on line 40 now - "translated3" and "value3". Check this line our in
our test. Ahh... the getter method... We need to account for this but kind
of in the opposite way we did for the Twig method call issue. We need to check
if the method name exists as a translated property *without* the `get` prefix.
Tricky!

## Translating Getter Methods

Check back in with `TranslatedObject::__call()`. This method is getting a bit
long, so let's refactor and add our new logic in a private method. Below,
write `private function translatedValue(string $name): ?string`. This will
accept the method name and return the translated value as a string, or null
if it doesn't exist.

Back up in `__call()`, cut the `if (isset(...))` statement and paste it in
our new private method. This checks if the exact method name exists as a
translated property.

Next, write `if (!str_starts_with($name, 'get'))`. This checks if the method
name is *not* a getter. There's nothing to do in this case, so, `return null`.

Below, write `$property = lcfirst(substr($name, 3))`. `substr` chops the
first 3 characters off the name - which we know is `get`. `lcfirst` lowercases
the first character, leaving us with the property name.

Finally, `return $this->_translations[$property] ?? null`. Return the translated
value for this property if it exists, otherwise, return `null`.

Back up in `__call()`, check if a translated value exists with
`if ($translatedValue = $this->translatedValue($name))`. Inside,
`return $translatedValue`. 

Run tests, run!

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Hmm, we have some errors. Scroll up a bit to see the summary. The first two
tests errored, but our third test, the translated properties one, *is* passing.
It's the original tests that have issues. This is why it was important to
run the tests before starting this feature! We know for sure, we did something
that broke existing functionality.

## Fixing Existing Functionality

Checkout the error: "Too few arguments to... TranslatedObject::__construct()"

Ahh, we added a new required parameter to this class's constructor. The first two
tests aren't passing the `$_translations` array.

Over in our test class, scroll up to the first two tests. PhpStorm
is even warning us about this. In both tests, pass an empty array as the second
argument.

Are we done?! Find out by running our tests again:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Woo! All tests are passing! New feature successfully added!

Next, we'll take a side step and look at how we'll *mark* our app's
entities for translation.
