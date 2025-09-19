# "Wiring Up" our Bundle Service

We created the class we want to use as a service in our bundle, but when
we try to inject it, we get this "cannot autowire argument..." error.

In your end apps, you might be used to this *just working*. That's because
*service autowiring* is enabled by default for all classes in your app's `src`
directory.

Bundles, however, are a different story. If you've been using Symfony
as long as I have, you might remember that in the old days, autowiring
*wasn't* a thing! You had to manually define every... single... service
in a YAML or XML file. It was rough...

Luckily, for apps, this is no longer the case (for the most part). But you
still have to do it for bundles. This is because bundles are meant to be
reusable, and you want to give the end user the most flexibility possible.

There are still some improvements in this department though.

## Creating a Services File

Remember we created that empty `config` directory in our bundle? This is
where we define our bundle's services. In it, create a regular PHP
file (not a class): `services.php`.

If you've written bundles in the past, you might have used XML for
defining services. This was the previous best practice. But nowadays, using
PHP is preferred. In your end-apps, if you do need to define services
manually, YAML is likely what you use. There's nothing stopping you from
using YAML here, but this would require your bundle to depend on the YAML
component. So, PHP it is!

First, add `namespace Symfony\Component\DependencyInjection\Loader\Configurator`:

[[[ code('96b14330d1') ]]]

This'll let us use the service definition helper classes and functions we require
without the need to import each one.

Next, `return static function (ContainerConfigurator $container)`:

[[[ code('837a032f92') ]]]

Inside, write `$container->services()`. We'll chain our service definitions off this:

[[[ code('837a032f92') ]]]

## Defining the Bundle's Services

Now comes the fun part! Add `->set()` - the first argument is the service ID.
In your apps, this is usually just the class name, but in bundles, we use
a plain string - again, for maximum flexibility. This ID needs to be unique,
so prefix it with a namespace that makes sense for your bundle. Here, we'll
use `symfonycasts.`. Now the name of our service: `object_translator`. The
second argument is the fully qualified class name: `ObjectTranslator::class`
(make sure you import it):

[[[ code('97fb40b378') ]]]

## Letting Symfony Know About the Service

We now have this... sort of... random `services.php` file in our bundle,
so we need to tell Symfony about it!

In `ObjectTranslationBundle`, override the `loadExtension()` method from
`AbstractBundle`. This method is called when the bundle is loaded by
Symfony.

The parent method is empty, so we can remove this call. Import our
`services.php` file using `$container->import()`. The path is relative to
our current file, so write `../config/services.php`:

[[[ code('3c23c68b1d') ]]]

Is this all we need? Let's see. Jump back to the browser and refresh the
error page. Hmm, the same error. But now we have more
details: "You should maybe alias this class to the existing `symfonycasts.object_translator`
service."

This is still progress - since Symfony is suggesting the service ID, that means
it knows about it!

## Alias our Service

In `ArticleController::show()`, where we're trying to inject `ObjectTranslator`,
we *could* add the `#[Autowire]` attribute with the service ID... This would work...
but we can do better! I want this service to be autowire-able. We do this
by setting the class name as a *service alias*.

In `services.php`, below `set()`, write `->alias()`. The first argument
is the alias we want to create: `ObjectTranslator::class`. The second
argument is the service ID we defined earlier: `symfonycasts.object_translator`:

[[[ code('3fa31097b8') ]]]

Back in the browser, refresh. And... still an error - but a different one. "Too
few arguments passed to ObjectTranslator".

## Defining Service Arguments

Remember, in `ObjectTranslator`, we have two required dependencies:
`LocaleAwareInterface`, a service, and `$defaultLocale`, a *container parameter*.
We need to tell Symfony how to supply these.

I know we can autowire `LocaleAwareInterface`, but in bundles, we need to
manually configure it, so we need it's service ID. To find this, at your
terminal, run:

```terminal
symfony console debug:autowiring LocaleAware
```

We don't need the full name, just the first part should be enough. Perfect!
Here it is: `translation.locale_switcher`. Copy that.

Back in `services.php`, right below `set()`, indent to keep this organized, and
write `->args()` with an array. These elements match the order of the constructor
arguments, so the first argument is the service. Use the `service()` function
and paste the service ID we just found:

[[[ code('0e13ecf373') ]]]

Now for the second argument, `$defaultLocale`. This is a container parameter.
We can list *all* parameters in the terminal by running:

```terminal
symfony console debug:container --parameters
```

Big list... filter it by running the same command but with `| grep locale` at the
end:

```terminal-silent
symfony console debug:container --parameters | grep locale
```

`kernel.default_locale` is what we're looking for! Copy that.

Back in `services.php`, for the second `args` array element, use the `param()` function
and... *paste*:

[[[ code('84fd51c09f') ]]]

Go back to our browser... refresh... and success! No error means the service is correctly
defined and injected!

Next, we'll look at how our bundle will *store* translations.
