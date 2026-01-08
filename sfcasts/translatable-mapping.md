# Translatable Mapping

It's time to dive into how users will *mark* their app's entities for
translation. For our app, we have four entities: `Article`, `Category`,
`Tag`, and `Translation`. The `Translation` entity of course stores
our translations, but the other three need translations. 

For instance, check out `Article`. We need to mark this class as
translatable. And down here, we want the `title` and `content` properties to be
translatable fields on this entity.

## Choosing a Translation Approach

We've got a couple ways to achieve this. One method could be to create some
sort of interface in our bundle. User's would then implement this on entities
they want translatable. But a more modern and slick approach is to use PHP
attributes.

I'm all for slick and modern, so let's go with attributes.

These will be similar to these Doctrine ORM *mapping* attributes. Ours will
*map* translatable entities and properties.

## Creating the `Translatable` Attribute

In our `object-translation-bundle/src` directory, create a new directory,
`Mapping`. This is where we'll store our attributes.

Inside, create a new PHP class called `Translatable`. This attribute will be
used to mark an entity as translatable. Make it `final`. To let PHP
know that this is an attribute, we need to use an attribute! Above the class,
write `#[\Attribute()]`. The first argument is what type of element this
attribute can be applied to. In our case, we want to apply this to classes,
so write `\Attribute::TARGET_CLASS`:

[[[ code('02ed7ee669') ]]]

We need to pass in one argument to this attribute: the `name`. This is that string
*alias* we'll store in the database instead of the class name. Create a constructor
with `public function __construct()`. Now add a single, mandatory argument (that's also
a property): `public string $name`:

[[[ code('a96d3a0e12') ]]]

## Creating the `TranslatableProperty` Attribute

We also need to let our bundle know what properties should be translatable. Now,
we could use an array argument in the `Translatable` attribute to list the
properties... but I think it's cleaner to use a separate attribute for this.

Also in the `Mapping` directory, create another PHP class called
`TranslatableProperty`. This class will be empty, no arguments - it's just
used as a *property marker*.

Mark the class as an attribute with `#[\Attribute()]`. This time, we want to
apply this attribute to properties, so use `\Attribute::TARGET_PROPERTY`:

[[[ code('2505f059c4') ]]]

## Marking Entities as Translatable

Now that our attributes are ready, let's go to our app's entities and mark them.
Start with `Article`. Above the class, add the attribute
`#[Translatable('article')]`:

[[[ code('93fc418149') ]]]

Now, identify the properties that are
going to be translatable. Above `$title`, add
`#[TranslatableProperty]` and do the same for `$content`:

[[[ code('d78daff195') ]]]

Next, mark `Category` as translatable with `#[Translatable('category')]`.
The `$name` property will be a `TranslatableProperty`:

[[[ code('4aa0bf2697') ]]]

Finally, mark `Tag` as `#[Translatable('tag')]` and again, the `$name`
property will be the only `TranslatableProperty`:

[[[ code('88ef4d0773') ]]]

Both our `Tag` and `Category` have a single translatable property.
`Article` has two: `$content` and `$title`. Nice!

## Creating Translation Fixtures

There's one last thing I want to do. If we look in our app's `Story` directory,
we'll find `AppStory` where we're loading the articles for our fixture
data. To test this more effectively, we'll create some translation
fixtures. This way, when we switch to French on our site, we'll display
some mock French data, just like we currently have mock English data.

We need to create a factory using Foundry. So, run:

```terminal
symfony console make:factory
```

Create a factory for the `Translation` entity: `0` in this list.

If we navigate back to our `Factory` directory, here it is: `TranslationFactory`.
In `defaults()`, it's detected all fields and is
generating fake data for them. We're going to override all of these when we
create it, so no need to edit anything here - we just need this factory to exist. 

Now, in `AppStory`, find the first article "Why asteroids taste like bacon".
Assign this created article to a variable with `$article1 = `:

[[[ code('4b6bcfa305') ]]]

We do this because we need to get its ID to link our translations to it.

Below this fixture, create our first translation with `TranslationFactory::createOne()`.
Inside, an array: `'locale' => 'fr'`, `'objectId' => $article1->getId()`,
`'objectType' => 'article'`. Now, the first field we want to translate is the title,
so `'field' => 'title'`. For the value, `'value' => 'French title...'`:

[[[ code('ce5c1e900b') ]]]

Not super creative but it gets the job done.

Now for the content translation. Copy this whole `TranslationFactory::createOne()`
and paste below. Change the `field` to `'content'` and the `value` to
`'French content...'`. Leave everything else the same - as it's for the same
object and locale:

[[[ code('c2ba8ba5a0') ]]]

Good enough to get started!

At your terminal, reload the fixtures with:

```terminal
symfony console foundry:load-fixtures
```

Choose `y` to confirm recreating the database.

Let's do a quick sanity check to ensure these translations were indeed loaded:

***NOTE
Since DoctrineBundle 3.0, the command was renamed to `symfony console dbal:run-sql`
***

```terminal
symfony console doctrine:query:sql 'SELECT * FROM translation'
```

(*translation* is the table name for our `Translation` entity.)

Perfect! Our two translation fixtures are in the database.

Next, we'll write the logic that loads translations from the database
and translates our `Translatable` entities.
