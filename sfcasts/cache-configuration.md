# Cache Configuration

Our object translations are successfully being cached! I now want to
allow the user to configure their cache pool and *how long* to cache
the translations. Let's use our old friend, the bundle configuration to
do this.

## An ArrayNode

Open up our `ObjectTranslationBundle` and find the `configure()` method.
We'll allow two options for caching: the *cache pool* to use, and the
*time to live* or *ttl*. We can group these two options.

Below the `->stringNode()`'s `->end()`, add an `->arrayNode()` and call
it `cache`. Close it with an `->end()`, and add some space. Add a
description with `->info('Cache settings for object translations.')`.

I want the user to be able to disable this whole node to prevent caching entirely. There's
a shortcut for this: `->canBeDisabled()`. This automatically adds a
boolean `enabled` option and defaults to `true`. 

***TIP
There's also a `canBeEnabled()` method if you want the default to be
`false`.
***

## Adding Children

Now to add the two sub-options: Write `->children()` and close it with an
`->end()`.

The first child is going to be `->stringNode('pool')->end()`. Inside,
`->info('The cache pool to use for storing object translations.')`. I
know every Symfony app has a `cache.app` pool, so use this as the default
value with `->defaultValue('cache.app')`.

Next, add an `->integerNode('ttl')->end()` and inside,
`->info('The time-to-live for cached translations, in seconds. null for no expiration')`.
Even though this is an integer node, unless we explicitly disallow it, `null`
can be used. By default, I want no expiration, so `->defaultNull()`.

## Debugging the Config

Double check this all looks right by jumping over to your terminal and running:

```terminal
symfony console config:dump-reference symfonycasts_object_translation
```

Beautiful, nicely documented configuration! Check out the `enabled` option
automatically added by `canBeDisabled()`.

There's another command to actually show the current config (and all the default
values) for a bundle. Run:

```terminal
symfony console debug:config symfonycasts_object_translation
```

Cool, this is the config that our bundle will be loading.

## Optional `CacheInterface`

Now to prepare our code for the new config. Open up `ObjectTranslator.php`. Because
caching is now optional, we need to allow null for the `CacheInterface`.
First, copy this property and paste it above the constructor.

In the constructor, remove `private`, we want this just as a normal argument.
Make it nullable by prefixing the type-hint with `?` and give it a default value
of `null`.

Below, add a new property argument for the ttl: `private ?int $cacheTtl = null`.

We could add some checks to only use caching if a cache adapter was injected...
but... there's a cleaner way to do this. The *null object pattern*.

Inside the constructor, write `$this->cache = $cache ?? new NullAdapter()`.
Sweet, now we don't have to change any code that uses the cache!

Now to use the `cacheTtl` value. Down in `translationsFor()`, inside the callable,
we already injected the `ItemInterface` - this is what we set the expiration on.

Add a check to see if the ttl is set: `if ($this->cacheTtl)`. Inside:
`$item->expiresAfter($this->cacheTtl)`. Done!

## Using the Configuration

Finally, we need to adjust our service definition to *use* our new
configuration.

Open up our bundle's `services.php` and remove the `service('cache.app')`
argument. It's optional now and will be configured based on the user's config.

Now, go to `ObjectTranslationBundle::loadExtension()`. To double-check
what our `$config` looks like, `dd` it... and... back in the browser, refresh.

Perfect, here's our cache array, the two options, plus enabled.

Back in `loadExtension()`, remove the `dd`. Because we're going to be
using this service definition multiple times, create a variable
for it. Copy the `$builder->getDefinition(...)`, and above, write
`$objectTranslatorDef =` and... paste.

Below, refactor to call `->setArgument()` on our new variable. Setting
the `translation_class` is always required, but we only set the cache if
enabled.

Write `if ($config['cache']['enabled'])`. Inside, configure
the cache pool and ttl arguments. First, `$objectTranslatorDef->setArgument()`.
Find the argument index by quickly jumping back to the `ObjectTranslator` constructor,
and count the arguments, 0, 1, 2, 3, 4. Got it!

Use `4` as the first argument, and for the second, we can't just use the raw
`pool` string - it needs to be a service reference. So write
`new Reference()`, be sure to import this from the DependencyInjection namespace.
Inside, pass `$config['cache']['pool']`. 

For the ttl, we *can* use the raw integer, so
`$objectTranslatorDef->setArgument(5, $config['cache']['ttl'])`.

I think we're good to go! First, let's make sure our app's cache is cleared.
At your terminal, run:

```terminal
symfony console cache:clear
```

In the browser, refresh... 4 queries, this should be calculating and setting the cache.
Refresh again... Down to 1 query.

Ok, not much really changed... so let's actually configure it with
a custom pool!

## Custom Cache Pool

In our app's `cache.yaml`, uncomment the `pools` section. Name our pool
`object_translation.cache`. By default, this will just piggyback on our
`cache.app` pool. But let's enable tagging by adding `tags: true`.

Now, in `symfonycasts_object_translation.yaml`, configure our custom
pool by setting: `cache: pool:`. What did we call it here...? `object_translation.cache`
copy that and paste.

Back in the browser, refresh... 4 queries, this should be using the new pool.
Refresh again... Down to 1 query. Perfect!

## Cache Tag Invalidation (for real)

We should be able to really see cache tag invalidation working now. Jump
to your terminal and run:

```terminal
symfony console cache:pool:invalidate-tags object-translation
```

Jump back to the browser and refresh... 4 queries. That means the tagged
cache items were invalidated and had to be calculated again!

Ok, our `ObjectTranslator` has grown into a bit of a monster. Next, let's
refactor this beast!
