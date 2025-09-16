# Bundle Configuration Validation

Alight, we have this `translation_class` configuration option in our bundle.
It needs some validation: first, it should be required, second, it should be
a non-empty string, and third, it should be a valid class that extends our
bundle's `Translation` class.

Since we used `stringNode()`, we already have some basic validation, it must
be a string. Let's add the rest.

## Required and Non-Empty

Below our `->example()` call, add a new line and indent. Add `->isRequired()`.
This makes sure the user sets it, but we want to prevent them from setting
it as null or an empty string. So, also add `->cannotBeEmpty()`:

[[[ code('a525c1572d') ]]]

At your terminal, dump the bundle configuration:

```terminal
symfony console config:dump-reference symfonycasts_object_translation
```

Ooo, an error! "translation_class"... must be configured. It even shows our
node's description to help us. Perfect!

This is now forcing us to create this configuration. In your app's `config/packages`
directory, create a new file named `symfonycasts_object_translation.yaml`.

Inside, add the top level node name, our bundle's *extension alias*:
`symfonycasts_object_translation:`. Then, underneath, indent and add
our node: `translation_class`. Set it as an empty string for now:

[[[ code('fe5353cac7') ]]]

Re-run the command again in your terminal:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

A different error: "translation_class" cannot contain an empty value. This is
because of that `cannotBeEmpty()` option.

So, back in our configuration, set `translation_class` to just `Translation`:

[[[ code('6fa078e34c') ]]]

Re-run the command:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

Woo! All good. It even added "Required" before the example comment.

## Custom Validation

For our third requirement: "a valid class name that extends our bundle's `Translation`
class", we'll need some *custom* validation.

In our bundle's `translation_class` definition, after `cannotBeEmpty()`, add
a new line, indent, and add `->validate()`. This starts a custom validation
chain which also needs to closed with `->end()`. Inside, add `->ifTrue()` with
a function: `fn($v) => !class_exists($v)`. This function runs with the user-supplied
value, `$v`. If the function returns `true`, the validation fails. In our case,
if the class does not exist. Now, we need to throw an error message. Add
`->thenInvalid('The translation class %s does not exist.')`. The `%s` will be
the user-supplied value.

[[[ code('f4c4166e1a') ]]]

Let's test this out! Back in your terminal, run the command again:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

Sweet! Our custom error: "The translation class Translation does not exist."

In our configuration, let's be cheeky and set it to a real class, but not
a valid translation class: `App\Entity\Article`:

[[[ code('63146288b6') ]]]

Run the command again...

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

## Improving Custom Validation

This passes our validation but is still not what we want - `Article` doesn't
extend our bundle's `Translation` class.

Back in our configuration, we *could* chain another validation after the first one
but let's keep it simple. Change the `class_exists` to `is_a`. For the second
argument, add `Translation::class` - make sure to import the one from our bundle.
`is_a` checks if an object is an instance of a class string. By default, `$v`
should be an actual object, so pass `true` as the third argument to allow
`$v` to be a class string:

[[[ code('2077b03dd2') ]]]

Run the command again:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

Error! "The translation class App\Entity\Article does not exist". Hmm, we
need to update the error message. Back in our configuration, adjust the
`thenInvalid()` message to read "...must extend
SymfonyCasts\ObjectTranslationBundle\Model\Translation."

[[[ code('a6df6135b0') ]]]

Run again... "The translation class App\Entity\Article does must extend". Ew,
that's some bad grammar! Remove the "does" and try again. Perfect! "The
translation class App\Entity\Article must extend..." Much better!

Fix this in our config by swapping `Article` for `Translation`:

[[[ code('5222a3264c') ]]]

Run the command again... all good!

Next, we'll actually use this configuration value in our bundle's service!
