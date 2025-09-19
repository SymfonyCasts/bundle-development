# TDD `TranslatedObject` Translations

Great job! Our `TranslatedObject` is doing an excellent job passing all
method calls and property access to the underlying object. However, it's
time we make it handle property translations. Let's get into it using
Test-Driven Development (TDD). First, let's make sure our test suite is all
green by running:

```terminal
Symfony PHP Vendor Bin PHP Unit Object Translation Bundle Tests
```

Perfect! We're all green. This is a fantastic starting point for some TDD.
Let's write a test that reflects the logic we want to implement. 

```php public function testCanTranslateProperties() ```

To kick things off, we'll copy the setup phase from the previous test. To
translate properties, the `TranslatedObject` constructor will accept an
array of translated properties. We'll set all properties to translated
values like so: 

```php 'prop1' => 'translated1', 'prop2' => 'translated2', 'prop3' =>
'translated3', ```

You may notice PHP Storm has turned this gray because the constructor
doesn't yet accept this parameter. Not to worry, we'll tackle that soon.
For now, let's write the logic we want to see when we access the
properties. We'll copy the assertions from the first test and change all
values to `translated`.

```php $this->assertSame('translated1', $object->prop1);
$this->assertSame('translated2', $object->prop2());
$this->assertSame('translated3', $object->getProp3()); ```

## Debugging Our Test

Next, let's head over to our terminal and run the test suite again. As
expected, it fails since we don't have any logic dealing with the
`translated` properties yet. We're failing on line 36, which is where we're
trying to access the property. Time to add the logic to make this test
pass!

In our `TranslatedObject`, let's accept an array in the constructor:

```php private array $_translations, ```

We'll also add a doc block `@param`, which will be an array of strings. The
key will be the property name, and the value will be the translated value,
`translations`.

Next, we'll head down to the `get` method and return `this
translations[name]`. If that doesn't exist, it'll fall back to calling the
inner property.

```php return $this->_translations[$name] ?? $this->_inner->$name; ```

## Fixing the Test Errors

Let's rerun our test suite. We're still seeing an error, but now it's on
line 39 of our test - when it's calling the `prop2` method. So let's modify
our `TranslatedObject`, in the `call` method:

```php if (isset($this->_translations[$name])) {     return
$this->_translations[$name]; } ```

Now it's failing on line 40. It passed the first method call, but it's
failing on the getter for `prop3`. To fix this, we need to account for the
getter.

```php private function translatedValue(string $name): ?string ```

Next, we'll cut this `if` statement and paste it here:

```php if (!str_starts_with($name, 'get')) { return null; } ```

Then, let's generate the property name we want with `property =
lcfirst(substr(name, 3))`.

```php $property = lcfirst(substr($name, 3)); ```

This will chop the first three characters off the string (which is `get`)
and lowercase the first character, giving us the property name. Then we'll
return `this translations[property]`, and if it still doesn't exist, we'll
return null.

```php return $this->translations[$property] ?? null; ```

We need to call this private function:

```php if (translated value = this translated value name, return translated
value) ```

If a translation is found, it will return that value. Let's rerun our test
suite.

There are a few errors, but our third test - the one we've been working on
- is now passing. The issue arises from too few arguments being passed to
the constructor of `TranslatedObject`.

```php $object = new TranslatedObject(new ObjectForTranslationStub(), []);
```

The first tests are not passing the `translations` array. Let's fix this
and rerun the test suite.

```terminal
Symfony PHP Vendor Bin PHP Unit Object Translation Bundle Tests
```

Excellent! All tests are green. Our `TranslatedObject` now correctly
handles the new feature and meets our expectations. In the next step, we'll
pull in translations from the database using our `ObjectTranslator
translate` method. Stay tuned!
