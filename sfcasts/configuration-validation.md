# Bundle Configuration Validation

Alright, great job! You've added some bundle translation definitions and
configured your bundle successfully. But now, let's make things a little
more interesting by adding some validation. We'll be making the
`translation_class` a requirement, meaning the user will have to fill this
in.

So, under the example here, indented again, you can use `->isRequired()`.
This makes sure the user sets it, but we want to prevent them from setting
it as null or an empty string. So, we'll also use `->cannotBeEmpty()`.
Let's see this in action.

```terminal
symfony console config dump reference
symfonycasts_object_translation
```

Running the above command in your terminal will now yield an error, which
is good news! It means our `translation_class` is being recognized as
required.

## Configuring Your Bundle

Next, let's set up the configuration. In your app's config directory, under
packages, create a new file named `symfonycasts_object_translation.yaml`.

For your first config, you'll need to specify what you're configuring. So,
under `symfonycasts_object_translation`, add your config,
`translation_class`. Let's leave it as an empty string to see what happens.

Running the command again will throw a different error. It's passing the
`isRequired()` check, but it doesn't like the empty value. Let's try
setting it to `translation_class: 'Translation'` and see if that works.

```terminal
translation_class: 'Translation'
```

Run the command again. Great! Now, it passes, and you'll see it also adds
"required" in here because we've set it to `isRequired()`.

## Adding Custom Validation

Now, let's add some extra validation because we want this to be our app's
translation entity. Back in your bundle configuration, under
`cannotBeEmpty()`, we'll do some custom validation with `validate()` and
then close it with `end()`.

Inside, use `ifTrue()`, and then add a function that checks
`!class_exists(V)`. This checks if the class exists. If it doesn't, throw a
validation exception with `invalid('The translation_class %s does not
exist.')`.

Back in your terminal, running the command again will yield an error - the
translation class `Translation` does not exist. This means you need to set
it to a real class name.

Let's set it to the wrong entity just for fun: `translation_class:
'App\Entity\Article'`. Running the command again will pass, but it's not
correct. It needs to be an instance of your bundle's translation class.

## Advanced Validation

To ensure this, in your bundle validation, chain another `validate()` after
and use `is_a()` to check that the value is an instance of `Translation`
from your `ObjectTranslationBundle` class. Set the third argument to true
so that `v` can be a string name.

Running the command again will throw an error because `App\Entity\Article`
does not extend `SymfonyCasts\ObjectTranslationBundle\Model\Translation`.
Let's improve the message a bit and try again. Great! Now we get a clearer
error message.

To fix the error, change it to `translation_class:
'App\Entity\Translation'` and run the command again. Perfect! You're in.

## Final Thoughts

Now there's one little thing to note. If someone actually used the bundle's
translation class, this would still pass. As a little homework, try adding
another validation to make sure it's not set to the bundle's translation
entity.

Up next, you'll actually use this bundle configuration to configure your
bundle. Exciting, isn't it? Let's keep going!
