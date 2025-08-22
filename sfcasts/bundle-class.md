# Create the Bundle Class

Great job so far! You've now nested your bundle comfortably within your app
and created the `composer.json` file. Your bundle is starting to take
shape. Now, let's move on to creating the heart of the bundle - the bundle
class itself.

In the `src/` directory, you're going to create a new PHP class called
`ObjectTranslationBundle`. But hang on, we've got a small hiccup. You might
notice that the namespace isn't filled out. This is because you have set
the namespace in your `composer.json` file, but PHPStorm isn't aware of it
yet. Let's fix that.

## Updating Your Namespace in PHPStorm

Remember the namespace you set for your `src/` directory? It's
`SymfonyCasts\ObjectTranslationBundle\`. Copy this, and get ready to update
your PHPStorm settings.

Navigate to your PHPStorm settings. There, open up the
`object-translation-bundle/src` directory. Find the `src/` directory and
mark it as the source. Now, you can update the namespace for this source.
Paste in the copied namespace but remove the extra slashes. It should look
like `SymfonyCasts\ObjectTranslationBundle\`.

Hit Apply and OK. You can now close the `composer.json` file.

## Creating Your Class

Now, if you navigate back to `src/` and create your class, you'll see that
PHPStorm automatically fills in the namespace for you. Name your class
`ObjectTranslationBundle`.

Here's a peek at the code you've just written:

```php /object-translation-bundle/src/ObjectTranslationBundle.php <?php
namespace SymfonyCasts\ObjectTranslationBundle; use
Symfony\Component\HttpKernel\Bundle\AbstractBundle; final class
ObjectTranslationBundle extends AbstractBundle { } ```

## Class Declarations

The first thing you would want to do is to declare this class as `final`.
This is a good practice as it prevents anyone from extending this class.
For now, it's going to be empty. But we need it to extend `AbstractBundle`.


If you've built bundles in the past, specifically before Symfony 6, you
would notice a change in the bundle systems. You used to extend a class
called `Bundle`, but `AbstractBundle` has taken its place now. 

This change allows your bundle class to carry more weight. Previously, it
was pretty sparse and almost empty, but now it is capable of containing all
the bundle-specific stuff. This is a huge advantage for simpler apps that
don't need a multitude of files to configure your bundle. You can now do
most of the configuration right within this bundle class.

## Next Steps

There you have it! Your bundle is all set up. The next step is to let your
app know about your shiny new bundle and install it. We'll tackle that
next.
