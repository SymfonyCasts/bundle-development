# Performance Optimization 1: Caching

We have some in-memory memoization happening to reduce database calls, but
only for the duration of a single request. We're still seeing 4 queries on
the French home page. 1 to fetch the articles and 3 to fetch the translations
(1 per article). Since these translations likely won't change that often, let's
implement a more persistent caching strategy.

Dive into the code for the `ObjectTranslator` service. Down here in the
`translationsFor()` method, this `findBy()` is what's making the queries.
This is what we want to cache!

Doctrine does offer some *result caching*, but it can get complex isn't
as flexible as the Symfony Cache component. So, let's use that.

## Injecting the Symfony Cache Component

First things first, we need to inject the `CacheInterface` in the constructor:
`private CacheInterface $cache`. Make sure to import the one from `Symfony\Contracts\Cache`.

Now, you may have used either of the PSR cache-interfaces before. Symfony supports
these, but also provides its own *cache contracts*. For our use case, I think it's
superior.

## Using the Cache in `translationsFor()`

Now, down in the `translationsFor()` method, right after we calculate the
`$id`, write `return $this->cache->get()`. The first argument is the cache key.
This needs to be unique to what we're caching. In double quotes, use
string interpolation to create a key like this: `object_translation.{$locale}.{$type}.{$id}`.

The second argument is where the fun happens - it's a callable `function()...`.

How this works is pretty neat. When you call `get()`, it first checks if the
key exists in the cache. If not, it runs the callable and stores the result.
Now, on subsequent calls with the same key, it returns the cached value and
bypasses the callable entirely! You kinda get cache *set* and *get* all in call!

For the guts of this function, select the remainder of the `translationsFor()`
method, including our normalization code, and cut it. Paste it inside.

PhpStorm is complaining because these variables are *no longer in scope*. Make
them available in the function by adding `use ($locale, $type, $id)` after
`function()`.

## Wiring up the Cache Service

We now need to wire this new constructor argument, so let's use our trick to get
the service ID. At your terminal, run:

```terminal
symfony console debug:autowiring CacheInterface
```

Nice! `cache.app` is what we want.

Back in our code, open our bundle's `services.php` file. Right here, below
`service('doctrine')`, add: `service('cache.app')`.

Time to try this out! Back in your browser, we're on the French homepage.
Refresh the page. Still seeing four queries - this is expected as this
request should be *calculating* the cache. Now, refresh again. BAM! We're down to
one query! That's caching at work!

In the web debug toolbar, you can click the cache icon to get details on the
caching activity for this request. 3 *calls* and 3 *hits*. Down here, we can
see the cache keys that were used.

## Implementing Cache Tags

Bonus time! Symfony Cache Contracts have a really cool feature: *tagging*. This
allows you to group cached items and invalidate them together. I think
our bundle should support this!

Back in `ObjectTranslator::translationsFor()`, this cache callable accepts an argument:
`ItemInterface $item`. This object gives us the opportunity to configure things
about this specific cache item - like adding tags!

One thing about cache tagging, is not all cache adapters support it. For instance
your default `cache.app` does not. Check if it's supported by adding
`if ($this->cache instanceof TagAwareCacheInterface)`. Make sure to import the
one from `Symfony\Contracts\Cache`.

Now we're able to add tags! Use `$item->tag()`. This takes an array. What tags
would be useful? How about `object-translation` and `object-translation-{$type}`.

User's can now invalidate *all* object translations... or, just a specific type.

To see how this would work, jump over to your terminal and run:

```terminal
symfony console cache:pool:invalidate-tags object-translation
```

This says it was successful, but like I said, `cache.app` doesn't support
tagging.

Next, let's add some bundle configuration to allow users to choose a cache pool...
and other options.
