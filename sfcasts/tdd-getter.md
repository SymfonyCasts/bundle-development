# TDD Getter Behaviour

We have our first test for `TranslatedObject`, which is passing. When we
wrote that test, we didn't *truly* use Test Driven Development, because
the logic already existed - we just confirmed it with a test.

*True* TDD is when you write a test for something that *doesn't* yet exist,
in a way you want it to work, see it fail, then write the code to make it pass.
So, let's do that now!

## Another Test

We want `TranslatedObject::__call()` to first check if there's a getter
available. When calling `title()`, if this method doesn't exist on the 
inner object, try and call `getTitle()`.

In `TranslatedObjectTest`, below our first test, create a new one
`public function testCallUsesGetterIfAvailable()`. Inside, our *setup*
will be the same so copy the `$object =` from the test above and paste
it here.

Now for the assertion. In our *stub* object, we have a `prop3` property with
a getter: `getProp3()`. So, if calling the `prop3()` method on our wrapper
(without the `get`) we want to forward this call to `getProp3()` on the inner object.

`$this->assertSame('value3', $object->prop3())` - that's it!

At your terminal, run the tests with:

```terminal
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

This test *fails* - but that's what we expect! This basically matches the error
we're seeing from Twig. Now we need to write the code to make it pass.

## Implementing the Logic

In `TranslatedObject::__call()`, at the beginning of the method, set
`$method = $name`. Now, add a check: `if (!method_exists($this->_inner, $name))`.
Inside, write `$method = 'get'.ucfirst($name)` - this capitalizes the first
letter of the name and prepends `get`.

Below, in the `return`, change `$name` to `$method`. That should be it!

## Verifying the Behavior

Back in your terminal, run the tests again:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Green! TDDS - Test Driven Development Success!

Note that both our tests pass - that's important as it means we didn't
break any existing functionality.

Now for the *true* test - let's see if this fixes our Twig issue.

In your browser, on the article page that has the error, refresh... and
perfect! Error's gone. The title and content are properly being pulled
from the underlying object.

There's likely more edge cases we need to consider, but this is a great
start. Now that we have this test, if we do find an edge case, we can
add a test for it, see it fail, then implement the logic to make it pass.
TDD Goodness!

Next, let's again use TDD to bang out a feature - the actual object translations!
