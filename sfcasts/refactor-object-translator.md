# Refactoring `ObjectTranslator`

Our `ObjectTranslator` is growing a bit large, and our `translationsFor`
method has ballooned. To tackle this, we'll create a new service
that will take care of all the Doctrine-related tasks. This involves
dealing with the `Translation` class and all the other Doctrine operations
we're using.

First, create the new class in our bundle's `src` directory,
call it `TranslatableMappingManager`. Admittedly, the term "manager" is
a bit of a fallback when I'm not sure what to name something, but
it'll do for now. We'll mark this class as `final` and `@internal`.

[[[ code('9ffeb8acc0') ]]]

This way, if we come up with a better name later, we can easily rename it.

## `translatableTypeFor` Method

First up, create a method called `public function translatableTypeFor`.
This method will take an object as a parameter, `object $object` and return a
string.

In `ObjectTranslator`, find `translationsFor` and copy the type-related logic.
Paste it in our new `translatableTypeFor` method, and import the necessary class.
At the bottom, `return $type`:

[[[ code('87f796794b') ]]]

## The Constructor

Next, create a constructor for this class... and open it up to give us some space.
Now copy the Doctrine-related properties from `ObjectTranslator`'s
constructor (`$translationClass` and `$doctrine`) and paste them into our
new constructor:

[[[ code('a3a95f581f') ]]]

## `idFor` Method

This paves the way for our next method, `public function idFor()`, which will
once again take an object and return a string. For this, we'll return to
`translationsFor` in `ObjectTranslator`, copy the logic to fetch the ID,
and paste it in our new method. At the end... `return $id`. Oh, PhpStorm
is telling me we can inline the return. So, `return reset($id)` and remove
the return below:

[[[ code('847c19b926') ]]]

## `translationsFor` Method

Finally, create another method: `public function translationsFor()`. This
will accept three parameters: `string $locale`, `string $type`, `string $id`
and return an `array`. Inside this method, grab the logic that fetches and
normalizes the translations in `ObjectTranslator::translationsFor()`, and paste
it here:

[[[ code('8c139d6532') ]]]

## Using `TranslatableMappingManager` in `ObjectTranslator`

Now that we've got our new class, inject it into `ObjectTranslator`.
In the constructor, replace the two Doctrine properties with
`private TranslatableMappingManager $mappingManager`:

[[[ code('63fa273f42') ]]]

First, replace the *type* fetching logic with
`$type = $this->mappingManager->translatableTypeFor($object)`:

[[[ code('69348b8b01') ]]]

Next replace the Doctrine code for fetching the id with
`$id = $this->mappingManager->idFor($object)`:

[[[ code('5008572d22') ]]]

Finally, in the cache-get callable, replace the Doctrine logic for fetching
and normalizing translations with
`return $this->mappingManager->translationsFor($locale, $type, $id)`:

[[[ code('cddeb100d6') ]]]

Nice!

## Updating Service Definitions

We've refactored the code but need to update our service definitions.

In `services.php`, add our new service with
`->set('.`, to make it a hidden service,
`symfonycasts.object_translator.mapping_manager`. Class: `TranslatableMappingManager`. 
For the args, use
`->args([])` and expand. In the `ObjectTranslator` definition above,
cut the Doctrine-related arguments, and paste as our new service's
arguments:

[[[ code('c090bc5e91') ]]]

Back up in the `ObjectTranslator` definition, add a new argument:
`service('.symfonycasts.object_translator.mapping_manager')`:

[[[ code('8443a22d07') ]]]

Love that autocompletion!

## Adjusting the Configuration Processing

Finally, since we adjusted arguments, we need to update our configuration
processing. This is the last step, I swear!

Open `ObjectTranslationBundle` and find our `loadExtension()` method.

In our cache configuration, these argument indexes have shifted. Take a
peek at the `ObjectTranslator` constructor to figure our the new indexes.
0, 1, 2, *3*, *4*. Back in `loadExtension()` update these two `setArgument()`
calls to use `3, 4` instead of `5, 6`:

[[[ code('23613c9d26') ]]]

The `translation_class` needs to be moved to our new service. So, write
`$builder->getDefinition('.symfonycasts.object_translator.mapping_manager')`.
Copy the `setArgument` call above and paste it here. For the index, check
`TranslatableMappingManager`'s constructor. It's `0`, so back in
`loadExtension()`, change the index to `0` and delete this rogue variable above:

[[[ code('091cdbcc01') ]]]

Phew! That's it for the refactor. Let's test this baby out.

In the browser, refresh the French homepage... and... Sweet! The translations
are still working!

Next, we'll dive into bundle console commands, starting with a translation
cache warmer command.
