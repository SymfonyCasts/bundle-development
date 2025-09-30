# `translate_object` Twig Filter

We have our `ObjectTranslator` service fully working. It can be injected and
used in PHP code like we're doing in `ArticleController::show()`. Now, let's
spice things up a bit and create a Twig filter for it.

## Twig Extension Class

In `object-translation-bundle/src`, create a new directory called `Twig`. Inside,
a new PHP class named `ObjectTranslatorExtension`.

Mark it as `final` and add a class-level docblock. In it, write `@internal`. This
is another trick to let users know this class isn't meant to be used directly
in their apps. Shh... it's a secret!

Next, have it extend `AbstractExtension` from Twig. Now, override a single method:
`getFilters()`, and mark the return type as `array`. Inside, return an array with a
single element: `new TwigFilter()`. The first argument is the name of our filter,
`translate_object`.

## Wiring Up the Extension

Time to wire this baby up! In your bundle's `services.php`, below the `alias()`,
define a new service with `->set('symfonycasts.object_translator.twig_extension')`.
Now, because this is an internal service we don't want users to see, we can mark it
as *hidden* by prefixing the service ID with `.`. I'll show you later how this affects
things.

The second argument is the class name: `ObjectTranslatorExtension::class`. Check this out,
PhpStorm formats the class name with a strikethrough. This is because of that `@internal` flag. It's
detecting we're trying to use it outside our bundle's `src` directory. In this case, it's a false
positive - we really need to use it here. But in a user's app, this strikethrough would hopefully
discourage them from using it directly.

Finally, tag it as a `twig.extension` with `->tag('twig.extension')`.

## Extension Code

Back to `ObjectTranslatorExtension`. This Twig filter isn't doing anything yet. The
second argument to `TwigFilter` is a *callable* that does the work. We could inject
our `ObjectTranslator` into a constructor and call its `translate()` method here but...
this isn't the best idea. Every Twig extension is loaded whenever Twig is loaded. Even if
we're not using this filter, it would still instantiate this object and our `ObjectTranslator`
as well. We want to avoid that, and make it lazy. Twig Runtimes to the rescue!

## Twig Runtime

For the second argument, after `translate_object`, create an array:
`[ObjectTranslator::class, 'translate']`. This isn't a *true* callable since
`translate()` isn't static. But Twig understands `ObjectTranslator` might be a runtime and will
lazily load this class and call `translate()` on it when the filter is used.

We just need to mark `ObjectTranslator` as a Twig runtime. Over in `services.php`, under
the first service, our object translator, add a tag: `->tag('twig.runtime')`.

Now, if you've written Twig runtimes before, you might have created a dedicated class for it.
This isn't strictly required! You can make any service as a runtime with this tag. Then, in your
extensions, you can reference it like we did here.

## Using our Twig Filter

Let's give it a whirl! Our article show page is already translating object fine, so let's
use the filter in the index - where we list all articles. Open `templates/article/index.html.twig`.

Inside the articles loop, at the very top, override the `article` with the translated
version: `{% set article = article|translate_object %}`.

Jump over to the browser and refresh. We're on the English homepage, so switch to French...

Voila! The first article title and content are in French. Our Twig filter is working! Of
course, the other articles are still in English since we haven't set up translations for them yet.

## Understanding Hidden Services

Let's take a quick detour to understand how hidden services, the ones prefixed with `.` work. At your
terminal, run:

```terminal
symfony console debug:container symfonycasts
```

Ok, we see two services, `symfonycasts.object_translator` and `ObjectTranslator` as a class - this is our alias.
We don't see our Twig extension service because it's *hidden*. Hidden doesn't mean "not there", or even "not usable",
they're just excluded by default when listing services. Run the command again, but add `--show-hidden`:

```terminal-silent
symfony console debug:container symfonycasts --show-hidden
```

There we go! The first entry is our hidden Twig extension service.

Next, let's work on some performance optimizations to keep database queries under control.
