# Initialize the Bundle

Hey friends! Welcome to the *Reusable Symfony Bundle* course. I'm Kevin, and I'll
be your *bundle navigator* on this journey. I've built a few bundles in my
day, so I'm super excited to share my knowledge with you. 

We're *not* just going to create a demo bundle for this course, we're crafting a real,
usable, and hopefully useful, bundle. And the cherry on top?
We'll release it as open source on GitHub and add it to Packagist. If you
find this bundle handy, you'll be free to incorporate it into your own projects.
Maybe you'll even help with its future development!

## What is a Bundle?

So, what exactly is a bundle? It's a chunk of reusable code that can be easily
included into *any* Symfony project. They can be shared privately, across
all your projects, or made available to the wider Symfony community. Anything
you can add to a Symfony app can be *bundled up*: controllers,
services, entities, forms, even Twig templates!

Picture your app as a space station - bundles are like additional modules that you can
attach to add new features.

## Our App

To follow along, make sure you download the course code from this page.
Check out the README to get the app configured and running. Once you've
completed the final step and run the web server, you should land on a
page like the one shown here.

Welcome to the Space Bar---

Wait a minute, this is a full-fledged Symfony app, I thought we were creating
a bundle? When first bootstrapping a bundle, I find it helpful to start
developing it within an existing Symfony app - one that requires the
functionality your new bundle will provide. This makes the initial development
phase much easier, and you can test your bundle's functionality in a *real-world*
scenario, right away.

So... the Space Bar. 

This is a space-news blogging platform. The homepage lists available articles and
clicking on one, takes you to the article page.

If you followed our [Translations course](https://symfonycasts.com/screencast/translations),
you might recognize this. We're picking up where that course left off. This 
language switcher toggles between English, which is our default language, French, and Spanish.

We used the Symfony Translation component to handle translating the *static* stuff on
our site - like this header and footer. But the articles themselves are stored
as entities in our database, and the Translation component doesn't support translating
these.

We need a different solution for these translations...

Some bundles already exist out there that are capable of this, but I wanted to approach
it fresh, with a slightly different perspective.

So that's what our bundle will do: translate Doctrine entities!

## Create the Bundle Folder

Let's get started! 

Jump over to the code for this project in your IDE - I'm using [PhpStorm](https://www.jetbrains.com/phpstorm/).
At the root of the project, create a new folder for our bundle. Call it
`object-translation-bundle`. We *could* name it *entity*-translation-bundle, but
I prefer to use the more abstract term, *object*. Remember, entities *are*
objects. We'll just support the Doctrine ORM to start, but we could add support
for additional object mappers in the future, like MongoDB.

Inside this directory, create three subfolders: `src`, `tests`, and `config`.

## Bundle `composer.json`

Since this bundle will eventually be a standalone package, it needs a `composer.json`
file. Instead of creating it manually, there's a handy dandy wizard command!

Over in your terminal, at the root of our project, move to our new
bundle folder:

```terminal
cd object-translation-bundle
```

Now run:

```terminal
symfony composer init
```

It's asking us for the package name. This default is almost correct, but I want to
use the `symfonycasts` vendor name (it's defaulting to my computer name). So
enter `symfonycasts/object-translation-bundle`. Description? `Translate your entities!`
Author? Looks like it's somehow finding my name and email address, so I'll accept that.
Minimum Stability? We don't need to worry about that right now, so just enter through.
Package Type? This one is important, and we'll see why later. Enter `symfony-bundle`.
License? We'll eventually release this under the MIT license, so enter `MIT`.

Now it's asking about dependencies. Don't worry about that right now, so enter `n`
to skip. Same for *dev* dependencies.

Now, autoloading. It's already guessing a namespace for us based on our package name,
`Symfonycasts\ObjectTranslationBundle`. Nice! Accept this.

Finally, we need to confirm the generation. This looks good, so hit enter.

`composer.json` generated!

Go back to our project root by running:

```terminal
cd ..
```

Jump back to our IDE - we have some new stuff inside our bundle folder. For some
reason, it created this empty `composer` directory - just delete that.

Like our actual app, our bundle now has a `vendor` directory. To avoid it from being
committed, in our *bundle* directory, create a `.gitignore` file and add `vendor/`.

Let's check out our freshly baked `composer.json` file!

This is a solid starting point. The only tweak I'll make is to
capitalize the "C" in "SymfonyCasts".

Next, let's add some code - we'll start with the all-important *Bundle* class.
