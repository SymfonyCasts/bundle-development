# Bundle Configuration

We need to let our bundle know about our shiny new `Translation` entity. Bundle
configuration is the best way to do this.

You've totally used bundle configuration before. These are the `YAML` files
inside your app's `config/packages/` directory. For instance, `doctrine.yaml`
is the configuration for the *Doctrine bundle*.

## Defining Your Bundle Configuration

We can define our bundle configuration right inside our bundle class! Open
`ObjectTranslationBundle.php` in the `src` directory of your bundle.

Override another method: `configure()`. The parent method is also empty,
so we can remove this call. Now for the definition. Write
`$definition->rootNode()`, this is the top-level configuration node, which
is defined as an *array*. Since it's an array, write `->children()` - the
array's definition. For definitions, we need to always call `->end()` to mark
it as *finished*. Do this right away, so we don't forget.

Inside, add our first node: `->stringNode('translation_class')`. In our
configuration, this will be the *array key*. Again, call `->end()` to
finish it.

## Listing Available Bundle Configurations

Over at your terminal, list the available bundle configurations with:

```terminal
symfony console config:dump-reference
```

With no arguments. This lists all the loaded bundle's, and their configuration
*key* (or *extension alias*). `DoctrineBundle`, alias `doctrine`, `DoctrineMigrationsBundle`,
alias `doctrine_migrations`. Here's our bundle: `ObjectTranslationBundle`, alias
`object_translation`.

Hmm, I'd like to prefix it with `symfonycasts`, like the Tailwind bundle below.

## Changing Your Bundle's Extension Alias

Here's how. In `ObjectTranslationBundle`, dig into the `AbstractBundle` class.
`protected string $extensionAlias` what we need to override. By default, it's
empty and automatically detected from the bundle name (snake-cased without
the `Bundle` suffix). Copy the property and, back in *our* bundle class,
paste. Change it to `symfonycasts_object_translation`.

## Visualizing Your Bundle Configuration

Cool, now run the command again:

```terminal-silent
symfony console config:dump-reference
```

There we go, `symfonycasts_object_translation`. Run the command again, but this
time, add that as an argument:

```terminal
symfony console config:dump-reference symfonycasts_object_translation
```

Sweet, here's our default bundle configuration as YAML. It shows `translation_class` as null,
which is what this *tilda* means.

This output is super useful for our users and is sort of like documentation. We can
make it even better.

## Enhancing Your Configuration With Example and Info

Head back to our bundle class and expand this `stringNode` to move the `->end()`
to a new line. This is where we can add additional configuration for this node.
Using indentation is very important - these definitions can get pretty complex and
large.

Sometimes, PhpStorm loses track of the indentation, so you have to fight with
it a bit.

First, add `->info()` - this is a short description of the node. Write
`The class name of your Translation entity`. On a new line, write
`->example('App\Entity\Translation')` to show an example value.

Back in the terminal, run the command again:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

Awesome! A description was added above `translation_class` and our example
was added beside it. These are commented out, so you can easily copy/paste
into your own YAML files.

Next, we'll add some validation for this node.
