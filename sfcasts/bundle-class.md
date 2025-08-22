# The Bundle Class

We have a home for our bundle within our app. And, we've set up it's
`composer.json` file so it's marked as a package for composer. Now it
needs a "bundle class" so it can be loaded as a bundle by Symfony.

This class is the heart of your bundle - the primary entry point.

In the `src/` directory, create a new PHP class... name `ObjectTranslationBundle`.
But hang on, we've got a small hiccup. The "namespace field" isn't being filled
in by PhpStorm like it usually is. PhpStorm isn't yet aware of
our bundle namespace.

Cancel out of this dialog for now. In our bundle's `composer.json` file, we
set the namespace for our `src/` directory to be `SymfonyCasts\ObjectTranslationBundle\`.

Copy the namespace. We need to help PhpStorm a bit here. 

## Updating Your Namespace in PHPStorm

Navigate to your PhpStorm settings and find the "Directories" section. This
is our current project structure. Open `object-translation-bundle` and click
the `src` directory. Above, mark it as "Sources". Now PhpStorm knows it's
a "source" directory, and it was added on the right here.

Click the little pencil icon to set the namespace, or "package prefix" for this
source. Paste, remove the extra slashes, and click "OK".

Hit "Apply" and "OK" to save this update, and close the `composer.json` file.

## Creating Your Class

Again, create a new PHP class in our bundle's `src/` directory. Awesome!
The namespace is now pre-filled! Call it `ObjectTranslationBundle`.

Here's our bundle class. First, mark it as `final` - we don't want anyone
to extend this. Leave it empty for now and have it extend `AbstractBundle`
from the `HttpKernel` component.

## Enhanced Bundle Class

If you've ever built bundles in the past, specifically before Symfony 6, you
might remember bundle classes used to extend `Bundle`, not `AbstractBundle`.

In Symfony 6, the way bundles are built was enhanced. The new `AbstractBundle`
class provides a more streamlined approach to bundle development. You can now
house almost all your bundle configuration and extension logic directly within
your "bundle" class. The old way required several additional classes. Check out this
[blog post](https://symfony.com/blog/new-in-symfony-6-1-simpler-bundle-extension-and-configuration)
for more details.

Next, let's "install" this bundle in our app!
