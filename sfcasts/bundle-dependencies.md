# Bundle Dependencies

At the moment, our bundle is nested within a full-fledged Symfony
application. It's relying on the dependencies of the application without
defining any of its own. So, it's about time we specify some dependencies
for our bundle.

First things first, in your terminal, navigate to our bundle directory with:

```terminal
cd object-translation-bundle
```

## Adding Dependencies

Now, add our first dependency:

```terminal
symfony composer require symfony/framework-bundle
```

Some bundle creators prefer to include the underlying components without
directly requiring the `framework-bundle`. And while that's a perfectly valid
approach, but I'm partial to requiring the `framework-bundle` because it simplifies
things. This bundle can't function without the `framework-bundle`. To me,
that makes it a direct dependency.

Next:

```terminal
symfony composer require symfony/translation
```

We need this because we need the services the translation component
provides, like the `LocaleAwareInterface`. Following that, run:

```terminal
symfony composer require doctrine/doctrine-bundle doctrine/orm
```

Cool, these are the bare minimum dependencies our bundle requires to function.

## Adding Development Dependencies

Now, it's time to add some development dependencies. Run:

```terminal
symfony composer require --dev phpunit/phpunit:"^9.6"
```

The `^` symbol here indicates we're cool with any version greater than (or
equal to) 9.6, but less than 10.0.

I know there are newer versions of `phpunit` but I'm not fully up-to-speed
on how deprecation tracking works in those versions. So, I'm sticking with
9.6 for now.

Speaking of deprecation tracking, also add:

```terminal
symfony composer require --dev symfony/phpunit-bridge
```

This package helps us manage deprecation warnings when we run our tests.

## Configuring PHPUnit

We're almost ready to run our bundle tests *standalone*, but first, we
need a PHPUnit configuration file.

In the `tutorials/` directory, copy `phpunit.xml.dist` into our
`object-translation-bundle/` directory. If you don't see it, no worries,
you can copy it from the script below:

[[[ code('046df39238') ]]]

If you're curious about the `.dist` suffix, it's a convention indicating
that this file is a template. You can copy it to `phpunit.xml` and modify it
as needed without affecting the original template. `phpunit.xml` takes
precedence over `phpunit.xml.dist` if both are present.

Let's unpack this configuration file. It's clearly XML, the root `phpunit` node
has several attributes: some namespace and schema stuff you don't need to worry
about too much except to know it adds autocompletion support which is nice.
The `bootstrap` attribute tells PHPUnit what file to load before running tests.
In our case, it's the Composer autoload file. `colors` enables/disables colored
output in the terminal. `failOnRisky` and `failOnWarning`
configure the behavior of PHPUnit when it encounters risky tests or warnings.
These are good defaults.

The `php` node is where we configure `php.ini` settings and environment variables.
The `SYMFONY_DEPRECATIONS_HELPER` environment variable is particularly important. It tells
Symfony's PHPUnit bridge how to handle deprecation warnings. This value is a great
default for 3rd party package development as it only triggers a failure if
our bundle directly causes a deprecation. We can't do much about deprecations
caused by packages our bundle depends on, so we ignore those.

The `testsuites` node defines our test suites. Here, we have a single suite
for the `tests` directory. Eventually, when we run code coverage reports,
the `coverage` node is where we specify the source directories for coverage analysis.

Finally, in the `listeners` node, we register the test listener provided by the
Symfony PHPUnit bridge.

Time to run our bundle's test suite! At the terminal, run:

```terminal
symfony php vendor/bin/phpunit
```

Sweet! All our tests pass!

Now if you remember from earlier, we only have *unit tests*, which test
individual classes in isolation. Next, we'll add an *integration test*
to verify that our bundle works correctly within a Symfony application.
