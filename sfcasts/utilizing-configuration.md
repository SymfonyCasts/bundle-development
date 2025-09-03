# Using Bundle Configuration

In our bundle, we've defined and validated the `translation_class` configuration
option. In our app, we've configured it as `App\Entity\Translation`. Now, we
need to use it in our bundle's `ObjectTranslator` service.

## `ObjectTranslator` Update

Open this class up, and add it to the constructor as `private string $translationClass`.

Hope over to the browser and click an article. An error: "Too few arguments to
... ObjectTranslator::__construct(), 2 passed... 3 expected".

## `abstract_arg()`

Let's fix this. Open our bundle's `config/services.php`. We need to add it
as a third element in this `args()` array. But... we don't have access to our
configuration here. For now, we can stub it out with a special function:
`abstract_arg()`. For the first argument, describe its purpose: "Translation class".

This isn't strictly required, but it helps with debugging. We're basically letting
Symfony know that we need to configure this argument still.

Back in the browser, refresh. A new error: "Argument 3 of service ...object_translator
is abstract". And we see our description: "Translation class". Perfect!

## Using Bundle Configuration

Now... we need to find the place where we have access to the bundle configuration.

In `ObjectTranslationBundle::loadExtension()`, notice the `array $config` argument.
Inside this method, dump it with: `dd($config)`. Back in the browser, refresh.

Nice! Here's our processed configuration as an array! Time to put it to use!

Below the import, write `$builder->getDefinition()`. Be sure to use `getDefinition()`,
not `get()`. Inside, pass the service ID for our `ObjectTranslator` service:
`symfonycasts.object_translator`.

One of the bonuses of working on your bundle within an app is that you can
get PhpStorm + Symfony Plugin auto-completion for your services.

Next, chain `->setArgument()`. The first argument here is the 0-based index
of the constructor argument we want to set. Quickly jump to `ObjectTranslator`,
`$translationClass` is the third argument, so we need to pass `2`.

The second argument is the value we want to set: `$config['translation_class']`.

Jump back to your browser and refresh. Nice! No errors.

Let's make sure this was given the value we expect. Open `ArticleController::show()`
and `dd($translator)`.

Back in the browser... refresh. Sweet! Here's our `ObjectTranslator` object and indeed,
the `translationClass` property is set to `App\Entity\Translation`.

Remove the `dd()`, refresh, and we're ready to move on!

Next, let's start writing our translation logic in `ObjectTranslator`.
