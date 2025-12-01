# Testing with Multiple Symfony Versions

It's time to nail down the PHP and package dependency versions our bundle
supports.

## Adding PHP Version Requirement

If we look at our bundle's `composer.json` file, the first thing I want to
do is set the allowed PHP version. In the `require` section, add
`"php": ">=8.2"`. This means we're requiring PHP 8.2 or anything newer.

Some folks aren't a fan of this because it's a bit open-ended, we're saying we'll
support PHP 10 when it comes out. I get it, but I personally follow
Symfony's lead, and they use this constraint for PHP.

## Adjusting Symfony Package Versions

For the Symfony package dependencies, the allowed versions are pretty
restrictive. `^7.3` means anything from 7.3 up to, but not including,
8.0. Symfony 6.4 still has support for quite some time, so I'd like to
support that as well.

Change the Symfony dependencies to `^6.4|^7.0`. This basically means we're
allowing versions greater than 6.4 but less than 8.

In `require-dev`, we have the `symfony/phpunit-bridge`. It's pretty forgiving
with its requirements so we don't need to really change anything here.

## Test Bootstrap File

Remember how this `var` directory gets created when we run our tests? If switching
between different PHP or Symfony versions locally, it can create problems. If
you run your test suite on Symfony 6.4, then switch to 7.3, the cache
files in the `var` directory won't be compatible.

It's best to clear our that directory before running tests. In our `tests`
directory, create a new file called `bootstrap.php`. Inside,
`require __DIR__ . '/../vendor/autoload.php';` to load the Composer
autoloader.

Below, write `(new Filesystem())`, import the one from the Symfony
Filesystem component. Then call `->remove(__DIR__ . '/../var');` to delete
that directory.

Now we need to tell PHPUnit to use this file before running our tests.
Open up our `phpunit.xml.dist` file and find the
`<phpunit>` tag. See the `bootstrap` attribute? It's currently set to
`vendor/autoload.php`. Change it to `tests/bootstrap.php`. We're basically
wrapping the Composer autoloader with our own bootstrap file.

To verify this is working correctly, jump over to the terminal. Make
sure you're in the `object-translation-bundle` directory and run:

```terminal
symfony php vendor/bin/phpunit
```

Excellent! Our tests are still passing.

## Prefer Lowest

An important dependency variation to test is the lowest possible version that our
bundle supports. This helps ensure our code is compatible with the oldest
versions of our dependencies. In our `composer.json` file, for the Symfony
packages, this means `6.4.0`. For the `doctrine-bundle`, it would be `2.18.0`.

To get these lowest versions installed, over in the terminal, run:

```terminal
symfony composer update --prefer-lowest
```

We can see Composer switched to the lowest versions.

## Troubleshooting Compatibility Issues

Ok, so now let's run our test suite again to see if everything is still
working.

```terminal-silent
symfony php vendor/bin/phpunit
```

Oh boy, we have an error... Scroll up to see the message. "Call to
undefined method `NodeBuilder::stringNode()`... In `ObjectTranslationBundle`,
line 21".

Open this file up and find line 21. Ah, here it is. The `stringNode()` method
was introduced in Symfony 7.2. Since we have 6.4 installed, this method
doesn't exist. If you're wondering how I found this out, I went to the
Symfony repository on GitHub and searched for `stringNode`. I found
the PR that introduced it, which mentioned it was added in 7.2.

Replace all instances of `stringNode` with `scalarNode`, which *is* supported in 6.4 and
does the same thing in this case.

Back in the terminal, run the tests again...

```terminal-silent
symfony php vendor/bin/phpunit
```

Sweet, all tests are passing - the code our tests are covering is compatible with
the lowest versions of our dependencies!

## Installing Development Versions

To go back to the latest version of our dependencies, run:

```terminal
symfony composer update
```

We can see it's installing the 7.3 versions of Symfony packages. Now, I know
Symfony 7.4 is due to be released soon, but it's not yet considered *stable*.

To get ahead of the game, I want to run our tests on 7.4 as well. This is a
great way to help the Symfony community. If you find problems, you can report
them to the Symfony team before the final release.

Open our `composer.json` file. By default, Composer only installs stable versions.
To allow installing development versions, add the following option:
`"minimum-stability": "dev"`. This will now *always* install dev dependencies,
which we don't want. So, add another option: `"prefer-stable": true`. Now,
Composer will prefer stable versions, but *can* install dev versions if a constraint
requires it.

Back in the terminal, if we run the update again, we don't get any dev versions
because of the `prefer-stable` option.

## Installing Symfony Flex Globally

There's a bit of a hidden Symfony Flex trick to help install different versions
of Symfony. We need to install Symfony Flex *globally* on our system, so run:

```terminal
symfony composer global require symfony/flex
```

Now, Symfony Flex is visible to all Composer commands.

## Testing a Development Version of Symfony

Now, to install the 7.4 versions, run:

```terminal
SYMFONY_REQUIRE=7.4.* symfony composer update
```

Cool! We're getting the 7.4 *release candidates*, which aren't considered stable.

Flex picks up this environment variable and adjusts the Symfony package version
constraints accordingly.

Notice this message: "Symfony recipes are disabled: symfony/flex not found
in the root composer.json". Since our bundle doesn't require `symfony/flex`
directly, the normal Flex magic you're used to is disabled.

Run the test suite again:

```terminal-silent
symfony php vendor/bin/phpunit
```

Nice, our tests all pass on 7.4!

## Testing an Alternate Symfony Version

To test on another Symfony version we support, say 7.2, run the composer
update command with the `SYMFONY_REQUIRE` environment variable set to `7.2.*`:

```terminal-silent
SYMFONY_REQUIRE=7.2.* symfony composer update
```

Yep, we got the 7.2 versions installed, so run the test suite again:

```terminal-silent
symfony php vendor/bin/phpunit
```

Awesome, all green on 7.2!

## `composer.lock` Considerations

I mentioned early that, unlike our apps, in 3rd party packages that need to
support a wide range of dependency versions, we don't commit the `composer.lock`
file.

Every time you install different versions, the lock file is updated. Committing
it creates conflicts and other headaches.

Next, we'll add some documentation and other metadata to our bundle.
