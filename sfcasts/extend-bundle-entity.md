# Extending our Bundle's Entity

Our bundle has an abstract `Translation` *mapped superclass* and is
registered with Doctrine. Time to create the *real* `Translation` entity
in our app.

At your terminal, run:

```terminal
symfony console make:entity
```

For the class name, use `Translation`. All properties are defined in
the abstract `Translation` class in the bundle, so just hit enter
to finish.

## The *Real* Translation Entity

In your editor, open our new entity in `src/Entity/Translation.php`:

[[[ code('30a8a8eaab') ]]]

Alright, here we go! The ID was added for us, which is all we need.
Now to extend the abstract `Translation` class from the bundle. Since
it has the same name, we need to import it with an alias.

At the top, write `use Translation` and choose the one from our bundle. Then,
`as BaseTranslation`. Extend it with `extends BaseTranslation`.

Sweet, we're done with this!

Eventually, we'll provide a recipe so end users won't have to do this step.

## Making a Migration

New entity? New migration!

Jump back to the terminal and run:

```terminal
symfony console make:migration
```

Take a look at it. Open the new migration file in the `migrations/`
directory.

Check out the `up()` method. It's creating the `translation` table with the
`id`, but also all the columns from our bundle's *mapped superclass*. Perfect!

Add a description: `Add Translation entity`:

[[[ code('b28a12aa12') ]]]

Time to run it. At your terminal, run:

```terminal
symfony console doctrine:migrations:migrate
```

Choose `yes`, and... boom! Database migrated!

Ok, we have the entity in our app, but in order for our bundle to perform queries
on its behalf, the bundle needs to know about it. Perfect job for *bundle
configuration* - that's next!
