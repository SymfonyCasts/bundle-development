# Unit Testing `TranslatedObject`

We have a little hiccup when using our `TranslatedObject` in Twig. Here's
what's going on. In the context of this exception, you can see it's calling
`article.title` in our template. When the entity wasn't wrapped in a
`TranslatedObject`, it worked fine. Twig has some nifty logic so that
when calling `.title`, it checks if there's a getter method:
`getTitle()`. If it finds one, it calls that method. That logic
doesn't work with our `TranslatedObject` wrapper - this object
doesn't have the *getters* - the underlying object does. Twig *does*
see we have the magic `__call()` method, so it just tries to call `title()`.

We can hook into this! Let's do it via unit tests!

## Bundle Tests

First, we need to set up our bundle for testing. If it doesn't exist already,
create a `tests` directory in the root of our bundle. Now, open
our bundle's `composer.json` file. Copy the entire `autoload` section and paste it below.
Rename it to `autoload-dev`. We only want composer to see tests when we're in development.

Same namespace, but append `\\Tests` to the end. Instead of `src/`, use
`tests/`.

Composer now know about our tests, but not PhpStorm. Like we did for the main
namespace, we'll need to give PhpStorm a little nudge. Open Settings, Directories.
Navigate to our bundle's `tests` directory and mark it as a *Test Root*.

Use the *pencil* icon to open the namespace for our bundle's `src` directory. Copy
the namespace, close the window, and open the namespace settings for our `tests`
directory. Paste the namespace, and add `Tests\` to the end. Click OK, Apply, Ok.

Great, ready for our first test!

## Creating Our First Test

Inside our bundle's `tests` directory, create a `Unit` directory. This will help
us organize our tests by *type*. Inside, create a new PHP class named
`TranslatedObjectTest`. Have it extend `TestCase`, from `PHPUnit`.

Our first test will just prove that our `TranslatedObject` works as
we currently expect - no Twig stuff yet. Create the test with
`public function testCanAccessUnderlyingObject()`.

## Creating a Stub Object

Now we need an object to wrap with our `TranslatedObject`. We'll create a *stub* class
right in this file. Below our test class, create a `class ObjectForTranslationStub`.

I know, I know, this totally can't be autoloaded correctly when used outside this class.
You'd never want to do this in your production code - but in tests, as long as you're only
using it in this file, I think it's ok. Feel free to create a proper *fixture* class if you prefer that.

In this class, let's add some different *scenarios*. A public property
`public string $prop1 = 'value1'`. Two private properties:
`private string $prop2 = 'value2'` and `private string $prop3 = 'value3'`.

A *non-getter* method to access `prop2`: `public function prop2(): string`. Inside,
`return $this->prop2;`. A *getter* to access `prop3`: `public function getProp3(): string`.
And inside, `return $this->prop3;`.

## Writing the Test

In our test method, create the object with
`$object = new TranslatedObject(new ObjectForTranslationStub());`.

This is the setup, or *arrange* phase of our test. `$object` is what's called our *system under test* - a
fancy way to saying "the thing we want to test".

Now for the *assertions* phase!

First, let's test public property access. `$this->assertSame('value1', $object->prop1)`.
Next, `isset()` on the public property: `$this->assertTrue(isset($object->prop1))`. Add
a description as the second parameter: `Public property should be accessible`. This description
isn't required but can be handy to have when a test fails.

Now, non-public property access: `$this->assertFalse(isset($object->prop2))`. Description:
`Private properties should not be accessible`.

Onto the methods: `$this->assertSame('value2', $object->prop2())` and
`$this->assertSame('value3', $object->getProp3())`.

## Running the Test

Let's run this test! We don't have testing configuration setup for the bundle,
but we can just use our application's PHPUnit configuration for now.

At your terminal, at the root of our app (not the bundle), run:

```terminal
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Woo! Passing! The basic, vanilla functionality of our `TranslatedObject` is working
like we expect.

Next, let's use *true* Test Driven Development (TDD) to tackle that
Twig issue.
