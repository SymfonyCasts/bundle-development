# Metadata & PHP CS Fixer

Bundle coding is done, and tests are passing on all our supported Symfony
versions. We're on the home stretch!

## License File

Let's add a license file to our bundle. In the `tutorial` directory copy the
`LICENSE.md` file to the root of our bundle. If you don't see the files
we're copying in your tutorial directory, don't worry! They're all in the
script below.

This is the standard MIT license to match what we have in our `composer.json`
file.

## Documentation

Now, copy the `README.md` file from the `tutorial` directory to the root of our
bundle. Now, the Symfony Bundle best practice guide recommends a `doc`
directory to house documentation and it to be written in the "reStructuredText"
(or `rst`) format.

I stray from this recommendation for a couple of reasons. First, `rst` isn't
rendered as nicely on GitHub as Markdown files are. Second, unless the
documentation starts getting really large, I like to have it shown
immediately when someone visits the GitHub repository. So, I prefer
to have documentation in the readme file.

Let's take a quick review of what I've written. It starts with installation
instructions and how to enable and configure the bundle. Next, how to mark
your entities as translatable...

A *usage* section to show how to use the `ObjectTranslator` service and
the `translate_object` Twig filter.

The *managing translations* section gives details about the database structure
and how to use the export and import commands.

Next, some information about the cache system, including the warmup command.

Finally, I like to include the *full default configuration* because it's so
easy to generate! Do you remember?

In the terminal, make sure you're at the root of our application, not in the
bundle. Then run:

```terminal
symfony console config:dump-reference symfonycasts_object_translation
```

Hey! That looks familiar. Whenever you change your configuration, you can
easily update this section by re-running that command and copy/pasting the
output. Self-documenting configuration for the win!

## `.editorconfig` File

Next, copy the `.editorconfig` file from the root of our project into the
bundle. This file ensures consistency in things like spacing and new lines. Many
IDEs, including PhpStorm, support this file. It helps prevent weird commits
with different white space and line break characters. This is especially helpful
when someone is developing on Windows, which uses different line endings than macOS
or Linux.

## `.gitattributes` File

At the root of our bundle, create a new file called `.gitattributes`. Inside,
add `/tests export-ignore`. This tells Composer to exclude the `tests`
directory when installing this package as a dependency. There's no need for
our bundle's tests to be included in an end user's project.

## PHP CS Fixer

Kind of like the consistency the `.editorconfig` file provides for white space and
line breaks, it's also important to have a consistent coding style. This is things like
a space between namespace and use statements, or where to put braces. A consistent
coding style makes your code easier to read and helps contributors.

A great tool to enforce and automate this is PHP CS Fixer. Over in the terminal,
make sure you're in the bundle directory and run:

```terminal
symfony composer require --dev php-cs-fixer/shim
```

If you've used this tool before but this `shim` package is new to you, it's just a
compiled version of PHP CS Fixer that makes installation easier.

Once installed, grab the `.php-cs-fixer.dist.php` file from the tutorial directory
and copy it into the root of our `object-translation-bundle`. This file configures
the coding style rules for your project. Open it up and take a look.

We're using the Symfony rule set, so our coding style will match Symfony's. Below,
we're telling it where to look for PHP files to fix: the `src` and `tests` directories.

To run it, at the terminal, run:

```terminal
symfony php vendor/bin/php-cs-fixer fix -v
```

Cool, here's all the files that were modified and the rules that were applied.

The first one, `TranslatedObject` applied the `phpdoc_align` rule. Let's open that
file to see what changed. Ah, it added spacing to align the `@param` variable names.
Generally, they are easier to read when aligned.

This `.php-cs-fixer.cache` file in the root of our bundle was generated when we
ran it. It's just a cache to make subsequent runs of PHP CS Fixer faster. Add
this to our `.gitignore` file so it doesn't get committed.

Next, we're going to use PHPStan to run static code analysis on our bundle's code!
