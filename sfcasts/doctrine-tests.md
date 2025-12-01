# Doctrine Tests

Time to change this integration test stub into a proper test for our bundle.
We're going to use a real database and real entities for this!

Over in the terminal, install Zenstruck Foundry to help us manage our test
database and fixtures:

```terminal
symfony composer require --dev zenstruck/foundry
```

## Test Entities

Back in our IDE, in the `tutorial` directory, copy the `Entity` folder
into our bundle's `tests/Fixture` directory. If you don't see these
entities, copy them from the script below.

`Entity1` is our mock entity that we'll be translating in our tests. It's
*translatable*, has an ID, and a *translatable property*.

The `Translation` entity is just like the one in our app.

## More `TestKernel` Configuration

Over in our `TestKernel`, we need to tell it what bundles to load. Override
the `registerBundles()` method. Inside, `yield new FrameworkBundle()`,
`yield new DoctrineBundle()`, `yield new ZenstruckFoundryBundle()`, and
finally, our bundle, `yield new ObjectTranslationBundle()`.

Now we need more configuration. In `configureContainer()`, add
`$builder->loadFromExtension('symfonycasts_object_translation', ['translation_class' => Translation::class])`.
It's hard to see on this small screen, but we need to import the one from
our test fixtures. I think this one is it. I'll scroll up to the namespaces
to confirm. Yep, that's the one.

Now to configure Doctrine. Add
`$builder->loadFromExtension('doctrine', [])`. First configure `dbal` with
`'url' => 'sqlite:///%kernel.project_dir%/var/data.db'`.

For the `orm` configuration, I'm going to paste this snippet (you can grab it
from the script below). This tells the Doctrine ORM where to find our test entities.

## The `ObjectTransator::translate()` Test

Back in our test class, clear the existing test method. First, we need to create
an instance of our mock entity, `Entity1`. We'll use a Foundry factory for this.
We could create a real factory class, but to keep things simple, we'll use a dynamic
factory. Write `$entity = persist()`, import the function from Foundry. `Entity1::class`
as the first argument, and an array with `'property1' => 'value1'` as the second.

Now for the `Translation` entity: `persist(Translation::class)`. Again, ensure you import
the one from our test fixtures. The array will be `'objectType' => ''`, pop over to
our `Entity1` class to confirm the alias: `entity1`. Then `'objectId' => $entity->id`,
`'locale' => 'fr'`, `'field' => 'property1'`, and finally, `'value' => 'translated1'`.

Down below, get our object translator service with
`$translator = self::getContainer()->get(ObjectTranslator::class)`. Translate the entity:
`$translated = $translator->translate($entity)` and pass `fr` as the second argument to
force translating to French.

Finally, assert that the translated property is what we expect:
`$this->assertSame('translated1', $translated->property1);`

Moment of truth! Back in the terminal, run our tests:

```terminal
symfony php vendor/bin/phpunit
```

Darn! An error - "Foundry is not yet booted."

Ohhh, I forgot the required Foundry traits. Back in the test class,
`use Factories`, which initializes Foundry, and `ResetDatabase`, which resets
the database before each test.

Moment of truth, take two:

```terminal-silent
symfony php vendor/bin/phpunit
```

Sweet! All green! Our integration test with a real database is working!

Next, we'll test our bundle against different Symfony versions to ensure compatibility.
