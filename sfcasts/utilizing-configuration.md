# Using Bundle Configuration

Alright, so you've defined a translation class and added some validation to
the bundle configuration. You've also configured your app to recognize the
correct entity class name. Now it's time to inject this class into your
`ObjectTranslator`.

## Updating Your ObjectTranslator Class

Go ahead and open up your `ObjectTranslator` and add `private string
$translationClass`.

## Addressing the Error

When you go back to your app and select an article, you're likely to run
into an error: "Too few arguments". This happens because you need to define
this argument in your service. Let's fix that.

Open your `config/services.php` file. Here you'll need to add the argument
as the third parameter. But, currently, you don't have access to that
configuration.

## Using Abstract Argument for Debugging

Here's a neat trick: use the `abstract_arg` function and give it a brief
description of what you expect. This will help you remember what you need
to inject, and will also assist with debugging later. Let's label this
'translation class'.

After doing that, refresh your page. Perfect! Argument three of this
service is abstract. That's your argument and it's giving you a helpful
little message: 'translation class'.

## Configuring Injection in ObjectTranslationBundle

Now, where do you configure this to be injected? This will be in your
`ObjectTranslationBundle`, within the `loadExtension()`. Pay attention to
the `config` argument.

Let's do a quick `dd($config)`. Refresh your page. That's it! You're now
looking at the fully built config for your bundle.

## Using Your Config

Now it's time to put it to use. Remove the `dd()` and then, right after the
import, write this line:

```terminal
$builder->getDefinition('symfonycasts.object_translator')->setArgument(2,
$config['translation_class']);
```

Side note: one of the cool things about working on your bundle within your
app is that you get all the PHPStorm auto-completion for the services
you've added. Nice little bonus, right?

## Confirming Your Changes

Jump back to your browser and hit refresh. And voila! It worked. To make
doubly sure, do a `dd($translator)` in your `ArticleController`. There you
have it. You can see that the `translationClass` was successfully set to
`app entity translation`.

Remove the `dd()`, refresh, and you're all set!

## Adding Logic to Your Translate Method

Next, it's time to start adding some logic to your `translate()` method.
Let's get to it.
