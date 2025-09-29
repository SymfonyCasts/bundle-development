# Translation Logic

We've done a lot of setup work to get to this point, but it's time for the
main event: translating our objects. Let's dive in!

Our database has been loaded with these fixtures. Over in our browser, click
on the first article. This is the English version. When we switch to French,
we should see those French fixture values... But... we have an error...

"Too few arguments to... TranslatedObject::__construct()". Oh yeah, we
added the translations argument, but haven't updated our `ObjectTranslator`
yet. Perfect, that's our goal!

## `translationsFor` Method

In `object-translation-bundle/src`, open `ObjectTranslator`. In the `translate()`
method, where we're creating a new `TranslatedObject`, we need to add a
second argument.

Stub out a method for this: `$this->translationsFor()`. Pass `$object` and
`$locale`.

This method doesn't exist, so generate it with PhpStorm. Cool! It knew the parameter
type-hints! This method needs to return the translated values as an array, keyed
by property names. So, set the return type to `array`.

## Translatable "Name"

First, we need to determine if this object is translatable, and if so, get
the `$name` property from the `Translatable` attribute.

We can use PHP's reflection system to do this. Create a `ReflectionClass`
for the passed object: `$class = new \ReflectionClass($object)`.

Now, we need to fetch the attribute: `$type = $class->getAttributes()`. By default,
this will return *all* attributes used on this class - since we only want
`Translatable` attributes, pass this as the first argument: `Translatable::class`.
`getAttributes()` returns an array of `ReflectionAttribute` objects. Since we
only allow one `Translatable` attribute per class, we can safely get the first element:
`[0]`. This class *might* not have the attribute, so use the null-safe operator, `?->`, and call
`newInstance()`. This instantiates our `Translatable` attribute class. Now that we have the object,
grab the name with `->name`. Because of the null-safe check, this whole thing might
be null, so use the null coalescing operator, `??`, and default to `null`.

Ensure we have a type with `if (!$type)`. Inside,
`throw new \LogicException(sprintf('Class "%s" is not translatable.', $object::class));`.

## `ManagerRegistry` Service

Now we need to fetch the translations from Doctrine. In our constructor, inject
another service: `private ManagerRegistry $doctrine`. This is the "one
Doctrine service to rule them all". You might be used to using the
*Entity Manager* or *em*. The `ManagerRegistry` is a level above. You can get
pretty complex with Doctrine, multiple databases and multiple entity, or object
managers. The `ManagerRegistry` brings them all together. Using this, we can
support all this complexity.

In our bundle's `services.php`, we need to wire this up as a fourth argument.

I know the `ManagerRegistry` is autowireable, so use our handy trick to find the
service ID. Jump over to the terminal and run:

```terminal
symfony console debug:autowiring ManagerRegistry
```

Here we go: `alias:doctrine` - `doctrine` is the service ID - that's a short one!
Copy that and back in `services.php`, define it as the fourth argument:
`service()`, paste. Perfect!

## Fetching Translations

Doctrine's now available in our `ObjectTranslator`, so, down in our new `translationsFor()`
method, add `$translations = $this->doctrine`. To get the repository for
our `Translation` class, write `->getRepository()`. This was the class we
configured and injected previously, so write `$this->translationClass`.

Now that we have the repository, check out the methods we have available.
Look familiar? `findBy()` is what we want. We'll pass an array of criteria,
or filters. First? `'locale' => $locale`. Second, the object type:
`'objectType' => $type`. Finally, object ID: `'objectId' =>`... hmm... what
should we use here? If we look at one of our app's entities, we can see they
all have a `getId()` method. For now, let's just use that: `$object->getId()`.

Make sure we're on the right track by `dd($translations)`, jump over to the browser
and refresh.

Exactly what we expect! An array of two `Translation` objects! But... this needs
to be *normalized* into a simple array of key-value pairs.

## Normalizing Translations

Back in our code, remove the `dd()`. To help with the next step, above `$translations`,
add a docblock comment with `@var $translations`. For the type, `Translation` - import
the one from our bundle. Suffix with `[]` to indicate it's an array of these objects.

Create a new array variable for our normalized translations: `$translationValues = []`.

Loop over the translation entities with `foreach ($translations as $translation)`. Inside,
set the key-value pair: `$translationValues[$translation->field] = $translation->value`.

`dd($translationValues)` to check our work. Jump back to the browser and refresh.
Beautiful! This is exactly the format we need!

Remove the `dd()` and `return $translationValues`.

Moment of truth! Back in the browser, refresh the page. Boom! "French title" and "French content".

Our translations are being successfully fetched and displayed. We can switch between English
and French for this article. Brilliant!

You might have thought this already, but assuming a user's entity has a `getId()` method
isn't a great idea... Let's explore a more robust solution next.
