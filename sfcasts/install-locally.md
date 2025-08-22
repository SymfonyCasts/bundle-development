# Install our Bundle Locally

Alright, so we've got our basic code ready to install a bundle with
Symfony. We've got our `composer.json` file and our bundle class in place.
But the installation method is a bit different from what you might be used
to.

We still need our app's `composer.json` file, but the bundle doesn't exist
on Packagist or GitHub yet. So we kind of have to tell Composer, "Hey, our
bundle exists in this specific folder!"

Scroll down and add a new key called `repositories` to our `composer.json`.
This should be an array. Inside this array, we'll have another array
object. Here, we're going to specify the type as `path`. This tells
Composer that our bundle exists somewhere on our file system. After that,
we'll add a URL, which will be the relative path to the folder containing
the `composer.json` file. So, relative to our `composer.json`, it would be
`object-translation-bundle`.

## Installing the Bundle

With that done, we're all set to install the bundle! Let's head over to our
terminal and use the command:

```terminal
composer require symfonycasts/object-translation-bundle
```

Now, this might give us an error because there isn't a stable version of
the bundle yet. To get around this, we can run the same command but append
`: @dev`. This tells Composer we just want the latest development version.
This is typically needed when working with paths because Composer can't
figure out the version on its own.

Once that's done, you'll see that Symfony Flex is used to auto-generate a
recipe. We don't have a Symfony Flex recipe for this bundle yet, but it
still did something when we installed this bundle.

## Checking What's Been Added

Let's do a `git status` to see what's been added. There's `composer.json`,
`composer.lock`... but hey, look at this— our `config/bundles.php` has
been modified. How did that happen?

If we check the config bundles, we'll see this line:
`SymfonyCasts\ObjectTranslationBundle\ObjectTranslationBundle::class =>
['all' => true]`. The bundle is being installed in all environments.

Now, remember in the first chapter when we set our package type to
`symfony-bundle`? This is where it comes in handy. Flex has some logic to
detect when you're trying to install a Composer package marked as a
`symfony-bundle`. It then looks for our bundle class based on a standard
naming convention. Once it finds the bundle class, it automatically adds it
to the bundles. How cool is that?

Without needing a recipe, we can install the bundle right out of the box.
Whenever someone installs this package, the bundle is automatically
installed.

At this point, we have our bundle installed, but it doesn't do anything
yet. So next, let's add our first service to our bundle.
