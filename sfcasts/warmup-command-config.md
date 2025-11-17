# Warmup Command Configuration

We have our warmup command class created, now we need to configure it
as a service. In your apps, this would be done automatically because
of the `AsCommand` attribute, but in bundles, like any other service,
we need to configure it manually.

Open our bundle's `services.php` file. Add it as a new service with
`->set('.symfonycasts.object_translator.warmup_command')`. Class:
`ObjectTranslationWarmupCommand`:

[[[ code('7fe24e3822') ]]]

Add some `args()`: First is the object translator service, so write
`service()` and copy-paste that service ID. Next is the mapping manager
`service()`. Copy-paste that service ID as well. The next `service()` is
the locale switcher, it's the same service we used above, so copy-paste that.

The last argument is the enabled locales, write `param('kernel.enabled_locales')`:

[[[ code('11725b5843') ]]]

Finally, mark this service as a console command with `->tag('console.command')`:

[[[ code('47c2b3f47e') ]]]

Let's test this command out in the terminal. Run:

```terminal
symfony console object-translation:warmup -v
```

The `-v` will give us a stack trace of any errors we encounter.

And... we do have an error. Well a warning actually: "Undefined array key 0".
Look at the first line of the stack trace, it's pointing us to line 23
of `TranslatableMappingManager`.

## Undefined Array Key Fix

Check out that class and find the line.

It's subtle but, we're trying to use the *null safe operator* on an array. It
doesn't protect us from undefined keys. To fix this, wrap this in brackets,
and add `?? null`:

[[[ code('4098c0527f') ]]]

Try the command again:

```terminal-silent
symfony console object-translation:warmup -v
```

Cool! That worked!

But there's actually an even more subtle bug that can pop up here...

Clear the cache with no warm-up:

```terminal
symfony console cache:clear --no-warmup
```

Now run the warmup command again:

```terminal-silent
symfony console object-translation:warmup -v
```

Hmm... "Class Proxies\__CG__\App\Entity\Category is not translatable." What's the
deal with this class name?

## Accounting for Doctrine Proxies

What's happening is that Doctrine generates a proxy class that extends
your entity class behind the scenes. In this case, `App\Entity\Category`.
This proxy class is what enables Doctrine to do it's magic.

In `TranslatableMappingManager`, the passed object is sometimes one of these
proxies. And that proxy class doesn't have the `Translatable` attribute, that's
the problem.

Remember, the proxy class extends our entity class. We can use reflection to
get the parent class if we detect that it's a proxy.

After creating the `ReflectionClass`, write `if ($class->implementsInterface(Proxy::class))`.
Import the class from `Doctrine\Persistence`:

[[[ code('9524938cb2') ]]]

All generated proxies have this interface.

Inside, write `$class = $class->getParentClass()` to get the *true* entity class:

[[[ code('ad9a795d34') ]]]

This should fix the issue but confirm by running the cache clear with no warm-up
again:

```terminal-silent
symfony console cache:clear --no-warmup
```

Then run the warm-up command again:

```terminal-silent
symfony console object-translation:warmup -v
```

Nice! No errors this time, and our progress bar shows 12 entities being processed. This
is some stylish output!

## Forcing Cache Recalculation

Now, there's just one last thing we need to do. Currently, when we're warming
up the cache, if a translation is *already* cached, it won't be recalculated.
Currently, this command is only really useful to warm an empty cache.

We need to force the recalculation of translations, even if they're already cached.

Symfony Cache Contracts has our back!

In `ObjectTranslator::translationsFor()`, dig into the cache `get()` method. The one
on `CacheInterface`. See this `float $beta` parameter? Check out it's docblock.
"A float, that as it grows, controls the likeliness of triggering early expiration.
`0` disables it, `INF` *forces* immediate expiration."

This feature helps with cache stampedes, if a bunch of items expire at the same time,
there's a probability that some will expire early to help spread out the load.

For our purpose, we can pass infinity to force immediate expiration.

Back in `ObjectTranslator::translationsFor()`, add a new parameter: `bool $forceRefresh = false`:

[[[ code('3e2767b0ea') ]]]

Now, for the third argument of `cache->get()`, pass `$forceRefresh ? \INF : null`:

[[[ code('fd35867cd6') ]]]


If true, use infinity as the `$beta` to force expiration, otherwise, pass `null` to use
the default behavior.

Up in `translate()`, PhpStorm is complaining that we need to pass this new parameter. Add
a third argument to the method: `array $options = []`:

[[[ code('bb7e60d761') ]]]

We could have used a dedicated parameter
for this, but using an options array will make it easier to add more options in the future.

Expand the `translationsFor()` call and for the third argument: `$options['force_refresh'] ?? false`:

[[[ code('a6ca52bb21') ]]]

Finally, back in our warm-up command, in the innermost loop where we're calling
`translate()`, add a third argument: `['force_refresh' => true]`:

[[[ code('e593fe7327') ]]]

Over in the terminal, run the warm-up command again:

```terminal-silent
symfony console object-translation:warmup -v
```

No errors, that's a good sign!

If you want to test that the refresh is actually happening, you can change some translations
in the database, *then* run the command. You should see these changes reflected
when refreshing the page - and no new database queries.

Up next, we'll add two more commands we need for a 1.0 release!
