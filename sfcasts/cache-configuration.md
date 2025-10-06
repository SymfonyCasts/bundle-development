# Cache Configuration

Great! We've set up caching for our object translations. Now, let's go a
step further and add some configuration to our bundle. This way, we can
give our end users the ability to tweak it a bit.

So, the first thing we're going to do is dive into our `object translation
bundle`. Here, we have a `configure` section and just below the end of the
`string node`, we're going to add an `array node`. We're doing this because
we want to provide a few options for caching configuration, which we'll
call `cache`. Finish this off with an `end`.

The first thing we'll add is some `info` cache settings for object
translations. The exciting part is that the user can disable this whole
setup if they want to. We can set this up with the `add arrow can be
disabled` command.

# Adding Child Nodes

Now, we're going to add some 'children' to this `array node`. As usual,
let's not forget to add an `end` at the end.

The first child is going to be a `string node` for the `pool` or the `cache
pool` that the user wants to use. We'll finish this off with an `end` and
add some `info` regarding the `cache pool` to use for storing object
translations. For the default value, we'll use the `cache app` which every
Symfony app has. Sounds neat, right?

Next, we're going to add one more thing - the `time to live` or how long we
want the cache to live before it's considered invalid and then refreshes
itself. For this, we'll add an `integer node` because it'll be the number
in seconds. We'll call it `ttl`. If the user does not want, they can
disable expiration entirely with this. Our default for this will be
`default null`. Even though it's an `integer node`, you're still allowed to
have a `default null` for this. That's going to be our default bundle
settings.

# Debugging the Config

Let's now debug this config to see what it looks like. We can do this by
jumping over to our terminal and running the following command:

```terminal
symfony console config dump reference symfony casts object
translation
```

Great! So, we have our new node setting. Notice how `can be disabled`
automatically added `enabled true` to it. This gives the user the ability
to disable this whole setup if they want to.

There's another command we can use to dump the current config as it's
configured. This is:

```terminal
symfony console debug config symfony casts object translation
```

# Injecting the ObjectTranslator

Now, we need to work on getting this injected. In our `ObjectTranslator`,
we're going to make caching optional. We'll move this property up and take
away the `private` from here, and make this `nullable`.

Then we're going to inject an `integer` for the `ttl` - `private int cache
ttl equals null`. We could do a check to see if the cache has been
injected, but let's use the null object pattern here so we can do `this
cache equals cache or new null adapter`. This way, we won't have to worry
about doing a check for this.

# Configuring for Use

Finally, we need to configure this to use the config. We'll go to our
bundle services and just remove `cache app`, which will be set later.

Now, we can go back to our `translation bundle` and down where we're
loading the config, we can quickly see what we're dealing with.

If we refresh the page, we can see our `cache array`. We can see `enabled
true`, and then here's our `pool`.

Now, let's set `objectTranslatorDef` as a variable because we're going to
be reusing this definition and inject the `translation class`.

Down here, we will check if caching is enabled with `if config cache
enabled`. If it is, we'll inject the `cache pool` with `setArgument` and,
finally, the `ttl` with `set argument`.

Let's quickly clear our whole cache to make sure that we're starting fresh.


```terminal
symfony console cache clear
```

# Testing and Refactoring

Now, if we go back to our French homepage and refresh, we can see it's
using our cache.

To illustrate how the tagging system works, let's set up a custom pool. In
our app's `config packages cache`, down in `pools`, uncomment this and
we'll create a custom pool called `object translation cache`.

Finally, we can configure it for our bundle in `symfony cast object
translation bundle`.

With that done, we can see how the tag invalidation works by running:

```terminal
symfony console cache pool invalidate tags tags object
translation
```

Great! The tag invalidation is working as well. Now that we've got this
far, let's look at doing a bit of refactoring to clean up our rather large
`ObjectTranslator`. It's getting pretty big, so let's see what we can do to
make it easier to work with.
