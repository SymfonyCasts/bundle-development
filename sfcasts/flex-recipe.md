# Symfony Flex Recipe

We left off with this gross user experience when installing our bundle
in a Symfony application. Let's create a Symfony Flex recipe to improve
this! The recipe will add the required `Translation` entity, and configure
it.

Over in your browser, navigate to the `symfony/recipes` repository on GitHub.
This is the official recipe repository and is curated by the Symfony core team.
There's another recipe repository: `symfony/recipes-contrib`, which is community
driven. Fellow contributors can approve and merge your recipe.

To add a recipe, first, fork the `symfony/recipes-contrib` repository. I already
have a fork at `kbond/recipes-contrib` so I'll navigate to that. Grab the git
remote url by clicking the "Code" dropdown and copying the "SSH" link.

At your terminal, navigate to a clean directory and clone your fork with:

```terminal
git clone <paste-your-remote-link-here>
```

Navigate to the directory:

```terminal
cd recipes-contrib
```

And create a new branch for your pull request:

```terminal
git checkout -b symfonycasts/object-translation-bundle
```

Now open this directory as a project in your IDE. I have this cloned
elsewhere and already opened in PhpStorm.

You'll see this large list of directories. Each directory corresponds to
an organization or username. Inside each is a directory for each package name, and
inside that is a directory for each version of the package. Let's create
ours!

At the root, create a new directory and subdirectory: `symfonycasts/object-translation-bundle`.
Inside that, create another directory for the version: `1.0`.

Rather than creating the recipe from scratch, I'm going to copy a recipe with a similar
setup: an entity and some configuration. I'll copy the contents of `zenstruck/messenger-monitor-bundle/0.5`
and paste them into our new `1.0` directory.

This `manifest.json` file is required for every recipe. This is the recipe's
instructions. The `copy-from-recipe` section tells Flex to copy the `config`
and `src` directories from this recipe into the user's project.

The `bundles` section tells Flex the bundle classes to enable. Back in our bundle's GitHub
page, I'll navigate to `src/ObjectTranslationBundle.php`, copy the namespace,
and paste over the existing one in the manifest file, adding `\\` to the end.
Now, I'll copy and paste the class name. Our manifest file is good to go.

Onto the `post-install.txt` file. The contents of this file are displayed in the
terminal after the package is installed, as a way to provide next steps to the user.
I'll modify to reference our bundle name and link to our documentation.

Finally, I need to adjust the files that are copied. In the `src` directory, we can delete
`Controller`, and in `Entity`, I'll rename the file to `Translation.php`. Opening that file,
I'll copy the contents from our bundle's readme and paste them in.

In `config/packages`, I'll rename the file to `symfonycasts_object_translation.yaml`
and paste in the configuration from our bundle's readme.

This is ready to go!

## Creating the Recipe PR

Back in the terminal, I'll navigate to the recipe-contrib directory I have open in
PhpStorm, and run:

```terminal
git add .
```

To stage the new files. Then run:

```terminal
git status
```

To make sure I captured everything. Looks good!

Now I'll push to my fork on GitHub:

```terminal
git push origin symfonycasts/object-translation-bundle
```

Oops! I forgot to commit the changes, so I'll run:

```terminal
git commit -m "add symfonycasts/object-translation-bundle recipe"
```

And push again...

Back in the browser, I'll navigate to Symfony's `recipe-contrib` repository. GitHub
has detected the new branch on my fork and is prompting me to create a pull request,
so, I'll click "Compare & pull request".

I'll improve the title a bit and in the description, it's asking for the Packagist URL. I'll
find that, copy, and paste it.

Now, "Create pull request"!

If I wait for a few seconds, there's a bot that automatically comments with instructions
on how to test the recipe. Here we go!

Back in my terminal, in a fresh directory, I'll create that temporary Symfony app again:

```terminal
symfony new --webapp my-bundle-test
```

And navigate to it:

```terminal
cd my-bundle-test
```

Back in the recipe PR instructions, I'll copy this `export SYMFONY_ENDPOINT` command
and paste it into my terminal.

This environment variable changes the default Symfony Flex recipe lookup to a custom endpoint
that includes my PR.

Now, I'll copy the Composer require command for my bundle and paste. Moment of truth...

Ok, I see it installed the bundle and is asking me to execute the recipe! That's a good sign!
The official recipes don't ask you to confirm, but `contrib` ones do by default.

I'll choose "yes"... and... no error! That means our recipe worked as expected!

We can also see that post-install message.

I'll confirm the entity was added by running:

```terminal
ls src/Entity
```

Cool, here's the `Translation.php` file. And if I run:

```terminal
ls config/packages
```

Here we go, I see the `symfonycasts_object_translation.yaml` file.

Now that we've confirmed the recipe works, let's go back to our PR,
copy this `unset SYMFONY_ENDPOINT` command and run it. This resets the
recipe lookup back to the official Symfony endpoint.

And... That's a wrap for this bundle course! But, it's not the end!
Feel free to help me improve our bundle by submitting issues or
pull requests on GitHub. We could definitely use some more tests!

I hope this adventure was fun, helpful, and maybe a little inspiring.

Until next time, happy coding!
