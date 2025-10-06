# Performance Optimization 1: Caching

So, you've got some caching in place to cut down on database calls. That's
a great start, but we're still seeing four queries here. We can do better
than that! Especially since the French translations shouldn't change that
much. Let's dive into your `ObjectTranslator` and see where we can make
improvements. Our culprits are the `findBy` method and your normalization
process. We need to cache these.

Doctrine does offer some result caching, but it can get a bit complex. So,
let's keep things simple and use the Symfony cache component.

## Injecting the Symfony Cache Component

First things first, we need to inject the cache component. Let's do this in
the constructor. We're going to use `private CacheInterface $cache` from
the Symfony contracts cache.

## Using the Cache in translationsFor

Next, in your `translationsFor` method, just after you calculate the `id`,
we're going to use `this->cache->get`. The first argument of `get` is the
key. We need to create a unique key for this cache. Let's call it
`object_translation.{$locale}.{$type}.{$id}`.

The second argument for `get` is a function. Here's the cool part: this
function fetches the value and saves it in one go. If it finds the `id` it
just returns, if it doesn't, it runs the function and caches the result.
And if it finds the `id` next time, it skips the function. It's a neat way
to handle caching.

Now, we're going to cut the rest of the function and paste it inside this
callable. You might get some errors because we need to inject a few things.
No biggie, we can do this with `use ($locale, $type, $id)`.

## Configuring the Cache

Now, we need to configure caching. Run this command in console to get the
service you need:

```terminal
symfony console debug:autowiring Cache
```

You'll see `cache.app` listed there. That's our default cache. Now, in your
`object-translations-bundle/config/services`, right below doctrine, insert
`service('cache.app')`.

Let's head over to your app, specifically the French home page. Refresh the
page once. You're still seeing four queries, but if you refresh again...
BAM! We're down to one query. That's caching at work! You can even check
out the cache calls to see how `cache.app` is performing.

## Implementing Cache Tags

Now, for a bonus round. Caching has this cool feature called tags. We're
going to add some to our cache. Let's go back to our `ObjectTranslator`.
Inside our cache callable, inject `ItemInterface $item` from contracts
cache. This function always gives you the ability to use this item, which
allows us to add some special configurations to our cache.

First, check if the cache adapter you injected is an instance of
`TagAwareCacheInterface` from cache contracts. If you try to tag an item
without this, you'll get an error. Now, we can add some tags:
`$item->tag(['object-translation', "object-translation-{$type}"])`.

In your terminal, run:

```terminal
symfony console cache:pool:invalidate-tags object-translation
```

This invalidates the tags, but your default `cache.app` pool does not use
tagging by default. So, when you refresh your screen, you'll still see it's
caching one query.

The final step is to enable some configuration for your cache with your
bundle config. And voila! You're now a Symfony caching pro!
