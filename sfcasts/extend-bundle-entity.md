# Extending our Bundle's Entity

Alright, here we go! We're about to create the real `Entity\Translation`
that extends from our `Math` superclass in our app.

So, you're probably wondering, how do we make entities? It's pretty simple,
we use `makeEntity`.

```terminal
symfony console make:entity
```

When it comes to the class name, let's call it `Translation`. We're not
adding any properties here because we're going to pull everything from the
`Math` superclass. So, you can just breeze through this by hitting enter.

## Discovering Our New Class

Let's hop back to our app, or more specifically, your Integrated
Development Environment (IDE). Inside the `src/Entity` folder, you'll spot
our new class. Here's our shiny new entity. Taking a peek inside, you'll
see everything is neatly mapped. It even added the `ID`, which is exactly
what we had in mind. We want only the `ID` to be user-defined for this
class, with everything else coming from the `Math` superclass.

## Extending Our Translation Model

Now, we need to extend our `Translation` model here. But since it has the
same name, we need to import it with a different alias in the namespace.
How about we use `use
SymfonyCasts\ObjectTranslationBundle\Model\Translation as BaseTranslation`?
Sounds good? Great. Then, we'll just do `extends BaseTranslation`. And
voila, we're done with this bit.

## Making a Migration

Jumping back to our terminal, let's create a migration for this. So, run:

```terminal
symfony console make:migration
```

Alright, now let's have a look at this migration. Just type `migrations`,
and there it is. Check it out. We can see it's creating a table with the
`ID`, but it's also snatching all those columns from the `Math` superclass,
which is exactly what we were aiming for. So, let's just add a description
here. `Add Translation entity`. Cool, right?

Then, back in our terminal, let's run:

```terminal
symfony console doctrine:migrations:migrate
```

Hit yes, and voila! Migration complete.

## Configuring Our Translation Entity

Now that we've got our `Translation` entity in the app, the bundle needs to
know what your end class is. We can't just let it guess that it's an
`App\Entity`. We need to configure it. This is where bundle configuration
comes into play. So let's dive into that next.
