# Install our Bundle Locally

We have a bundle "package" and bundle class, and even though it lives within
our app, we still need to "install" it. This is handled with Composer
like normal but the method is a little different. It doesn't exist on
Packagist or GitHub yet, so we have to tell Composer: "Hey, our bundle package exists
in *this* specific folder!"

Open our app's `composer.json` file and add a new top-level key called
"repositories". Composer has a default "repository" - Packagist. This is
where it looks for packages. But we can add our own custom repositories too.

This needs to be an array, inside, a json object. The first key is "type" - what
kind of repository. In our case, it's a "path" (or local) repository. Next,
"url" - for "path" repositories, this is the relative path to our package
folder. Use "object-translation-bundle", since it's in the root of our app.

## Installing the Bundle

Now we can install our bundle package, so head over to your terminal and run:

```terminal
composer require symfonycasts/object-translation-bundle
```

Oh... We're getting an error about not finding a "stable" version of our package. This
actually makes sense as we haven't made any releases and our app is configured to only install stable
versions of packages by default.

We can get around this by explicitly allowing a "dev" version of this package. Run the command
again, but appending `:@dev` to the end of the package name:

```terminal-silent
composer require symfonycasts/object-translation-bundle:@dev
```

Awesome! That worked. What's cool about "path" repositories is that
they're *symlinked*, so changes you make to the bundle are reflected in your
app immediately. Perfect for local development!

## Auto-Flex Recipe

Look at this - the output is telling us Symfony Flex installed a recipe for
this? How? We didn't create a recipe...

Run:

```terminal
git status
```

to see what's up. `composer.json` and `composer.lock` were modified - that's
expected... but look: `config/bundles.php` was modified too...?

Open that up in your editor. Wow! It automatically added our bundle class!

Remember when we created our bundle's `composer.json` file? We set the "type"
to "symfony-bundle"? This told Symfony Flex: "Hey, this is a Symfony bundle!
Look for a bundle class and load it automatically."

Without needing a *true* recipe, whenever someone installs this package,
the bundle is automatically added to your app.

Pretty neat, eh?

We have an empty bundle installed and loaded in our app. Next, let's have
our bundle provide a Symfony service!
