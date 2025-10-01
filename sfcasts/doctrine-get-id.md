# Fetch the Object ID with Doctrine

Currently, our French translation data is being successfully displayed on
the page. However, our current method of fetching the ID using
`$object->getId()` is a bit shaky. It assumes that the user always has a `getId()`
method on their entities, which isn't always the case. We can do better...
and Doctrine has our back!

## Fetching the *Object Manager*

Over in our `ObjectTranslator::translationsFor()` method, above fetching the translations,
make some space. First, grab the *object manager* from Doctrine:
`$om = $this->doctrine->getManagerForClass()`. This needs a class name, so use
the passed object's class: `$object::class`:

[[[ code('e16347d4e2') ]]]

When using the ORM, you've used something called the *entity manager*. This is what
we're getting here - *object manager* is a more general interface for it. Again, using
these abstract interfaces makes our bundle more flexible.

`getManagerForClass()` might return null so check for that:
`if (!$om) { throw new \LogicException(sprintf('No object manager found for class "%s".', $object::class)); }`:

[[[ code('e8675ab2bb') ]]]

## Fetching the ID from the Object Manager

Next, `$id = $om->getClassMetadata($object::class)`. This returns a special object
that knows all about the Doctrine mapping for this class. `->getIdentifierValues()`
is what we want. Pass the `$object` instance:

[[[ code('0cfaa3bab2') ]]]

This retrieves the ID for the passed object, regardless of how it's implemented.

`dd($id)` to see what we are dealing with here. In the browser, refresh. Hmm... It's
an array of values... Doctrine supports *composite* IDs, which basically means multiple
ID fields for an entity. This is a pretty advanced feature, and our bundle, at
least initially, won't support this.

Even if your entity only has a single ID field, Doctrine still returns it
as an array. Remove the `dd()` and add a check: `if (count($id) > 1)`. Inside,
`throw new \LogicException(sprintf('Class "%s" must have a single identifier to be translatable', $object::class))`:

[[[ code('0cfaa3bab2') ]]]

Grab the first element of the array with `$id = reset($id)`:

[[[ code('562315bed4') ]]]

`dd()` that... and refresh the browser. Perfect, just a single value!

Remove the `dd()` and down in our `findBy()` array, replace `$object->getId()` with
`$id`:

[[[ code('ff46a00b22') ]]]

Test it out in the browser... and it worked!

Our `ObjectTranslator` is working, and pretty solid. Next, let's create a Twig filter
for it!
