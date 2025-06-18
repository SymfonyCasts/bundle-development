# Installing the Translation Component

Hey there! Glad you've joined us for a
course on Symfony translations. You might not know this, but the
translation component is one of the oldest—it was introduced with Symfony
2.0. Believe it or not, this is the first time we're going to explore it on
Symfony casts. Our plan is to take an existing English-only site and
translate it into several languages. We'll explore how this works and
what's involved. So, to follow along, download the course code linked with
this tutorial. In the `start` directory, you'll find files that look like
this. Use the `readme` to get started. After you've followed the steps in
the `readme`, start your server, and we'll take a look at what we're
working with.

## Introducing the Space Bar

Welcome to the Space Bar, a fun space news
site that essentially acts like a blog. It's a space-themed blog with a
list of articles. When you click on an article, you can dive into it, read
it, and even check out some comments. Our goal is to translate this site.

But what does that mean? The Symfony translation component is designed for
translating hard-coded data such as menu items or taglines. This data is
hard-coded in your Twig templates. You'll notice that our blog is driven by
a database, which provides dynamic data like titles and blog body content.
The Symfony translation component doesn't handle this. It's primarily for
the frame of your site, dealing with hard-coded text. We'll discuss
database translations towards the end of this course and explore some
options.

## Installing the Translation Component

First, we need to install the
translation component using this command:

```terminal
Symfony, composer, require, translation
```

After installing the package, let's check what we've got by running a `get
status` command. The `composer`, `composer.lock` and `Symfony.lock` files
are standard. The `config/packages/translation.yaml` is for configuration,
and the `translation` folder is where your translations will live. If we go
back to `phpStorm`, we can see the translations folder, which is currently
empty. We'll populate this later. For now, let's focus on
`config/packages/translation.yaml`.

## Configuring the Translation Component

In `config/packages/translation.yaml`, we specify our default path to use the
`translations` directory. When using the translation component, you need a
default locale. If the locale isn't set or if translations don't exist for
a certain locale, it falls back to the `default_locale`, which is English
by default.

A quick note on locale codes: English is `EN`, Arabic is `AR`, Armenian is
`HY`, and so on. You can find a full list of ISO language codes online. You
might've seen five-digit locale codes like `EN_US` or `EN_CA`. These are
locality-specific: `EN_US` is US English, and `EN_CA` is Canadian English.
They're almost identical, but there are slight differences. For example, in
Canada, we spell color `C-O-L-O-U-R`, while in the US, it's `C-O-L-O-R`.
For this course, we'll stick to the two-digit locale code, which should be
sufficient for most cases.

The five-digit locale codes can also be used to localize your site,
accounting for country-specific differences in number, currency, and time
formatting. However, we won't delve into localization in this course. We'll
stick to the two-digit codes.

## Choosing Languages for the Site

Next, we need to choose some other
languages for our site. I was hoping to use Klingon and Romulan, but they
don't have ISO codes yet, so we'll stick to existing ISO codes. This is an
optional step, but it's best practice to add `enabled_locales` to your
`translation.yaml`. We'll include English (`EN`), French (`FR`), and
Spanish (`ES`).

This `enabled_locales` setting doesn't prevent you from using locales
outside of this list, but there are benefits to setting it if you know the
exact locales your site will use. There's a small performance boost as
Symfony knows to cache translations only for those locales. More
importantly, it lets you list these locales in certain situations, like a
language switcher or bulk operations like generating a sitemap for all your
pages.

## Add the `lang` Attribute

The last thing we'll do here, now that we have translation enabled for our
app, is go to `templates/base.html.twig` and add `lang="{{ app.locale }}"`
in the HTML tag. If we refresh our site and view the page source, we can
see `HTML lang="en"`, which indicates that the default locale of this page
is English. This is helpful for search results and SEO, so Google knows the
language of your site.

Next, let's figure out how to determine the
user's locale. This will help us deliver Spanish versions of the page to
Spanish users, and French versions to French users. So, how do we know what
language a user uses? Let's explore this next.
