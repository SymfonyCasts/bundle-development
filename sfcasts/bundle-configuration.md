# Bundle Configuration

So you've successfully set up your translation entity in your app. Great
job! Now it's time to let your bundle know about it. The most effective way
to do this is by using bundle configuration, which you'll find living
inside your app's `config packages` file. For instance, take a look at the
`Doctrine bundle` - we want to do something similar for your bundle.

To achieve this, inside your bundle's source `ObjectTranslationBundle`,
you're going to override a method called `configure()`. Since the parent
method is empty, feel free to remove it. Write `definition`, and inside
that, write `definition->rootNode()`. Then off of that, call `children()`.
This sets up your top-level configuration. Remember, each time you create a
node, you must call `end()`. It's a good idea to do it immediately so you
don't forget. Your first configuration node will be a `stringNode`, called
`translation_class`. Don't forget to call `end()` again.

## Visualizing Your Bundle Configuration

To see what you've just done, there's a handy terminal command. Head over
to your terminal and enter:

```terminal
symfony console config:dump-reference
```

With no arguments, this command shows all the bundles that can be
configured. You'll see `Doctrine`, `Doctrine migrations`, and yours,
`ObjectTranslationBundle`, `ObjectTranslation`.

You probably want to prefix this with `symfonycasts`, similar to the
`Tailwind` bundle. Here's how: in your translation bundle, pop into
`abstract bundle`. You'll see `protected string $extensionAlias`. This is
automatically detected as `ObjectTranslation`, but if you want your own
prefix, simply override it in your bundle. Do this at the top, and your
`extensionAlias` will be `symfonycasts_object_translation`.

Run the command again, and voila, you'll see
`symfonycasts_object_translation`. Running the command again will show the
full reference of this, as `symfonycasts` argument, `translation`,
`symfonycasts_object_translation`.

```terminal
symfony console config:dump-reference
symfonycasts_object_translation
```

This shows your extension and what the default config looks like. You'll
see `translation_class` is null, which is signified by a squiggly line.
Here, you can add more details to provide clearer instructions to your
users.

## Enhancing Your Configuration With Example and Info

Head back to your `stringNode` and expand it a bit. Remember to keep things
tidy with proper indentation, especially when dealing with large configs.
Here, you're going to provide some info, like 'The class name of your
Translation entity'. You can also provide an example to assist your user
when they run that command, so `example` will be `App\Entity\Translation`.

Keep in mind, this isn't a default value, it's just a handy example. Run
the command again, and you'll see the info and example now appearing.

Next up, you'll add some validation to your `translation_class stringNode`,
ensuring it's indeed the class name of your app's translation entity. Get
ready, that's your next mission!
