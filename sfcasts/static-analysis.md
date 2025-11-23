# Static Analysis with PHPStan

While *tests* verify your code works as expected by running it, *static analysis*
examines your code without executing it. This helps catch potential issues
that tests might miss. It's a great way to enhance code quality and maintainability.
Not only that, a side benefit is the suggested improvements often lead to better
auto-completion in IDEs like PhpStorm.

My tool of choice for static analysis is PHPStan.

## Installing & Configuring PHPStan

At your terminal, while in your bundle directory, install it by running:

```terminal
symfony composer require --dev phpstan/phpstan
```

Back in our IDE, add a `phpstan.neon` file to the root of your bundle. Either
copy it from the `tutorial` directory or from the script below. The `neon` file
format is very similar to `yaml`.

Let's take a look at this configuration. This `parameters` section is where
we set up how we want PHPStan to work. PHPStan operates at different levels,
from zero to ten. Zero being the most lenient, and ten being the strictest.

Choosing a level is a balancing act. A lower level means fewer reported issues,
but it might miss some potential problems. A higher level catches more issues,
but it can also be overwhelming with the number of reported problems.

A good practice is to start low and gradually increase the level as you address
the reported issues. This way, you can improve your code quality incrementally
without being overwhelmed.

I've started with a moderate level of five. The `path` is a set of paths
we want PHPStan to analyze. To start, we're focusing on the `src` directory.
Analyzing `tests` can be beneficial too, but for simplicity, we'll stick with just `src`
for now.

## Running PHPStan Analysis

Time to run it! Back in your terminal, run:

```terminal
symfony php vendor/bin/phpstan analyse
```

Alright! PHPStan is scanning the files in our bundle's `src` directory. It
seems we have three errors to address. Let's tackle them one by one!

## Method Not Found Error

The first one, in `ObjectTranslationBundle.php` on line 19, has the message
"Call to undefined method `NodeDefinition::children()`".

Ok, let's jump to that file and find line 19. Here it is but... hmm... line
19 is calling `->rootNode()`, it's line 20 that's calling `->children()`.
That's because, from PHPStan's perspective, this whole chain of method calls is considered
one line and errors are reported on the first line of the chain.

This error is actually a false-positive. PHPStan has trouble understanding
these deeply chained configuration trees. We know it works as expected because
our test would fail while building the container if there was an actual issue.

This error can be safely ignored. We could ignore the entire line, but it's better
to ignore just the specific error key so that if a different error arises later,
it's not also ignored. You can find this key back in the terminal output under
the error message: `method.notFound`. Copy that. Back on line 19 of
`ObjectTranslationBundle.php`, suffix the line with a comment `@phpstan-ignore <paste>`.

## Unknown Class Error

Back in the terminal, run the analysis again:

```terminal-silent
symfony php vendor/bin/phpstan analyse
```

Just two errors now! On lines 12 and 17 of our `ObjectTranslatorExtension` class,
the Twig extension. Find and open that class.

PHPStan is saying this `AbstractExtension` class doesn't exist. According to PhpStorm
though, it does... That's because we're working on this bundle within a full Symfony
project that has Twig installed. When we run PHPStan in the context of our bundle,
this class doesn't exist.

This is an easy fix, we just need to have our bundle depend on Twig. But, I don't
want Twig as a *hard dependency*. Maybe a user is making use of our bundle in an
API-only project without Twig. I don't want them to have to install Twig just
because of our bundle. So, we'll require Twig as a *dev dependency* instead.

At your terminal, add it with:

```terminal
symfony composer require --dev twig/twig
```

Now, run the analysis again:

```terminal-silent
symfony php vendor/bin/phpstan analyse
```

No errors! Adding Twig fixed both the remaining errors since they were both related
to missing Twig classes.

Next, we're going to push our bundle up to GitHub and set up continuous integration
with GitHub Actions.
