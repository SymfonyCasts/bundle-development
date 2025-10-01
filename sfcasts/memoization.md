# Performance Optimization 1: Memoization

Our Twig filter is working as expected, and we're using it here in our article
index. Right now, we're overriding the article variable with its translated
version at the start of the loop. I think there's another valid way to use this
filter - only when we need it.

## Using the Filter Only When Needed

First, delete the `set article` override. Then, down here, where we actually
need the translated version - for title and content. Change `article.title`
to `article|translate_object.title` and do the same for content:
`article|translate_object.content`:

[[[ code('eae757dd6f') ]]]

Jump back to our browser - we're on the French homepage. Before refreshing,
notice the query count in the web debug toolbar. 4, one query to fetch all
articles, and then one query per article to grab translations. Now refresh
the page. Ok, all still works... but look at that query count - 7! One query
for all articles, and now *two* queries per article. The translated values
are being loaded twice, even though they're identical.

Let's fix this with a technique called *memoization*. This is a fancy word,
and I really like saying it, but it just means storing data in memory to
prevent redundant processing.

## WeakMap

We'll implement this in our `ObjectTranslator`, so open that up. We could just
add an array property to store translated objects by ID or something, but this
could lead to memory leaks. In long-running processes, this array could grow
indefinitely.

Instead, we'll use a special PHP core object called a `WeakMap`. Add a new property,
`private \WeakMap $translatedObjects`:

[[[ code('fec6df920b') ]]]

This is like an array, but it's keyed by objects, not strings or integers like you're used to.
In PHP, when an object is created and passed around, it's actually a reference to
that object. As long as something is using that object, it stays in memory. When
nothing is using it anymore, PHP automatically cleans it up. If we used a standard
array to store translated objects, that reference would never be removed. A `WeakMap`
is different, it holds the object but doesn't prevent it from being cleaned up - a
*weak reference*. This is perfect for our use case!

To use it, we need to instantiate it first, so in the constructor,
`$this->translatedObjects = new \WeakMap()`:

[[[ code('f135e5fa61') ]]]

Down in the `translate()` method, this `translationFor()` call is expensive, it's making
a database query. So, let's save this whole `TranslatedObject` in our `WeakMap`.
Right after `return`, write `$this->translatedObjects` - what to key it by? The original `$object`!
Now, use the null coalescing assignment operator `??=` and then create the `TranslatedObject`:

[[[ code('960494a0c8') ]]]

This checks if the translated object is already in the weak map (for the original object). If so,
return that. Otherwise, create a new `TranslatedObject`, store it in the weak map, and return it.
If the original object is cleaned up by PHP, it'll automatically be removed from the weak map.

Let's see it in action! Back in your browser, we see the 7 queries. Now refresh... still works...
and look at the query count - back to 4! The memoization is working!

Next, let's continue this performance optimization process with non-memory caching.
