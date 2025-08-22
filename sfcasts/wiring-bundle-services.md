# "Wiring Up" our Bundle Service

Hey there, remember when we were trying to inject our new bundle service
into the `ArticleController` show method and we ran into this error? Yeah,
that happened because Symfony wasn't aware that it's a service. So we have
to let Symfony in on the secret by letting it know our bundle has this
service available.

Now, this might be a bit more complex than what you're used to. If you've
been using Symfony since its early days, you might remember doing this for
every service, even in your end apps. But that was before auto wiring came
along to make things easier. So, even though we get to enjoy the simplicity
of auto wiring in our end apps, when it comes to bundles, we need to do
things the old school way. And the reason for this is it gives your bundle
the most flexibility and extensibility.

## Creating a Services File

First off, let's go into our `ObjectTranslationBundle`'s `config` directory
and create a `services.php` file. Remember, this isn't a class. Next, give
it a namespace of
`Symfony\Component\DependencyInjection\Loader\Configurator`.

```php <?php namespace
Symfony\Component\DependencyInjection\Loader\Configurator; return static
function (ContainerConfigurator $container): void { }; ```

## Defining the Bundle's Services

Now comes the fun part - defining our bundle's services. We'll be doing
this in PHP format, but you could also use YAML or XML if you prefer.
However, the current best practice for bundles is to use PHP to define your
services.

Time to set our first service. Remember to name it appropriately. In
bundles, always use your bundle's namespace as the prefix for all your
services. 

```php use SymfonyCasts\ObjectTranslationBundle\ObjectTranslator;
$container->services() ->set('symfonycasts.object_translator',
ObjectTranslator::class) ; ```

## Letting Symfony Know About the Service

We've got our `services.php` file all set, but we still need to get Symfony
in the loop. To do this, go into `ObjectTranslationBundle` and override the
`loadExtension()` method. 

```php use Symfony\Component\DependencyInjection\ContainerBuilder; use
Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
public function loadExtension(array $config, ContainerConfigurator
$container, ContainerBuilder $builder): void {
$container->import('../config/services.php'); } ```

Let's make things even smoother by aliasing `ObjectTranslator::class` to
`'symfonycasts.object_translator'`.

```php ->alias(ObjectTranslator::class, 'symfonycasts.object_translator')
```

## Troubleshooting the Error

Alright, we still have an error, but at least now we have more details. It
seems like we should alias this class to the existing
`symfonycasts.object_translator` service. But, guess what? We can tell our
bundle to enable auto wiring for this class in just a few easy steps.

```php ->args([ service('translation.locale_switcher'),
param('kernel.default_locale'), ]) ```

## Identifying Required Dependencies

Looking at our `ObjectTranslator`, we can see we have two required
dependencies: `LocaleAwareInterface` and the `defaultLocale`. To find the
service ID for `LocaleAwareInterface`, simply run `symfony console
debug:autowiring LocaleAware`. 

```terminal
symfony console debug:autowiring LocaleAware
```

Once we have the service ID, we can add it as the second argument using
`param()`. Let's give it a try and refresh. And... voilà! It works. We now
know that the service is being injected properly.

## Locating the DefaultLocale Parameter

The other thing we need in our `ObjectTranslator` is the `defaultLocale`.
This is a container parameter provided by Symfony. To find this, run
`symfony console debug:container --parameters |grep locale`.

```terminal
symfony console debug:container --parameters |grep locale
```
This command lists all the parameters. So, let's run it again, and there we
go. `Kernel.default_locale` - that's the parameter we've been looking for.

```php ->args([service('translation.locale_switcher'),
param('kernel.default_locale')]) ``` Let's refresh one more time. And...
success! The service is being injected properly. Now that we've got that
under our belt, we can move on to the next step.
