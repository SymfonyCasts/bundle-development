# Cache Warmup Command

Alright, so by default, our object translations are cached
indefinitely. We can configure an expiry, but what if we wanted to force an early
refresh? Perhaps we've updated the database with new translations, and we
want the site to reflect that. Sure, we could clear the whole cache or
invalidate the cache tag. But this could be 1000's of translations, and
on a busy site, could lead to slow-downs while the cache repopulates.

Let's create a *cache warmup* functionality. This will clear each cache item
and simultaneously refresh it with the latest version. To do this, we'll 
create a console command.

In your IDE, within our `object-translation-bundle`'s `src` directory, create a new
PHP class. Call this `ObjectTranslationWarmupCommand`. This will be in
the `Command` namespace. With PhpStorm, we can update the namespace here,
and it will create it in the proper directory.

## Setting Up the Command

Here we go. First, mark the class as `final` and `@internal`.
The command itself will be public but the *code* for the command will be
internal.

Next, have it extend `Command` from the `Symfony` console component. At the
top, Add the `AsCommand` attribute. Name: `object-translation:warmup`.
Description: `Warms up the object translation cache.`.

Within the class, override two methods: the constructor and `execute()`.

In the constructor, we don't want this `$name` argument, but we still
need to call the parent constructor - just with no arguments.

## Injecting Services

Make some room, and inject the following services.
`private ObjectTranslator $translator` and `private TranslatableMappingManager $mappingManager`.

Next, `private LocaleSwitcher $localeSwitcher`, this is a special service
that allows us to temporarily switch the locale for the entire application while
we run some code. This will be useful for warming up translations in all
available locales.

Finally, we need those locales, so inject `private array $locales`.

## `execute()` Method

Down in `execute()`, remove the parent call as the parent method is empty.
This method must return an integer as the status code. So, before we forget,
return `self::SUCCESS`.

Above, write, `$io = new SymfonyStyle($input, $output)`. This gives us a
nice way to interact with the console using Symfony's recommended styling.

Add a title for this command: `$io->title('Warming up Object Translation Cache')`.

Initiate a count variable: `$count = 0`. This will help us keep track of how many
objects we warmed up.

## Iterating Over Translatable Objects

We need a way to get all the translatable objects. So, over in our `TranslatableMappingManager`,
create a new method: `public function allTranslatableObjects()`. Return type: `iterable`.

First, loop over the object managers: `foreach ($this->doctrine->getManagers() as $om)`.
Complex apps can have multiple object managers, so this will ensure we cover them all.

Inside this, we need to get all the entity classes that this manager
supports. So `foreach ($om->getMetadataFactory()->getAllMetadata() as $metadata)`.

Here, get the entity class name with `$class = $metadata->getName()`. Now we need to skip
classes that aren't translatable. Do this with
`if (!(new \ReflectionClass($class))->getAttributes(Translatable::class))`. This will
return an empty array if the class doesn't have the `Translatable` attribute. In this
case, `continue` to move onto the next class.

We now know we have a translatable class, so write:
`yield from $this->doctrine->getRepository($class)->findAll()`. We're returning
a generator that iterates over all translatable objects in all object managers.

Ok, this isn't the most efficient way to do this, as we're loading all
entities into memory - potentially 10's of thousands... Let's just
make note of this as a future improvement.

Back in our command, `SymfonyStyle` has a super handy method for iterating
things with a progress bar. After initiating the `$count` variable,
write:
`foreach ($io->progressIterate($this->mappingManager->allTranslatableObjects()) as $object)`.

`progressIterate` essentially wraps the iterable we give it, and handles
the progress bar output in the terminal for us.

Inside, create a nested loop for the locales: `foreach ($this->locales as $locale)`.

Now, write `$this->localeSwitcher->runWithLocale()`. This method takes two arguments:
the `$locale` we want to switch to, and a callable. For this `use ($object, $locale)`.
This will switch the locale app-wide for the duration of the callable. After,
it'll switch it back to whatever it was before.

Inside the callable, `$this->translator->`... Hey! Where's my autocompletion?!
Oops, I forgot to import the `ObjectTranslator` class. There we go.

Back down, write `translate($object, $locale)`.

At the end of the outer loop, increase the count with `$count++`.

Finally, before returning, add a success summary message:
`$io->success("Warmed up the cache for {$count} translatable objects.")`.

## Overriding the Locale in `translate()`

Up where we're calling `translate()`, PhpStorm isn't happy with this
`$locale` argument. That's because it doesn't exist in the method signature.

Jump into `ObjectTranslator::translate()` and add it:
`?string $locale = null`. Where we're fetching the current locale,
attempt to use the passed locale first: `$locale ??`.

When passed, *it* will be used, otherwise, it'll be pulled from the request.

Back over in our command, PhpStorm is happy!

Next, we'll wire up this command and give it a test drive!
