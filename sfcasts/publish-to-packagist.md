# Publish to Packagist and Release

Our bundle is now hosted on GitHub and our continuous integration with
GitHub Actions is green! To make it installable via Composer, we need to publish
it on Packagist. You won't be able to follow along with this part - we don't
want to flood Packagist with duplicates of our bundle. But you can use
this as a guide to publish your own bundles.

## Submitting to Packagist

Go to [Packagist.org](https://packagist.org) and sign in with your GitHub
account. Click "Submit" from the top menu. For the repository URL, copy the
URL of your GitHub repository and paste it in... then click "Check".

This step looks for existing packages with similar names. If there's already
a package that fits your needs, you might not need to publish a new one.
I see there's already a package with a similar name, but I'll proceed anyway
by clicking "Submit".

And... Nice, we're on our fresh package page! A few things are happening here.
First, Packagist crawls our package metadata to determine all our branches,
tags, and dependencies. It also automatically sets up a GitHub webhook so
that Packagist is notified to re-crawl our package whenever we push new code.

When you no longer see the "This package is not auto-updated" message, that means
the webhook is set up. This can be confirmed when you see the "This package is
auto-updated" message on the right sidebar.

Back on the GitHub repository page, click the "Settings" tab, then "Webhooks"
from the left sidebar. This is the webhook Packagist automatically added for us.

On Packagist now, we only have this 1.x branch because we haven't created any
releases.

## Creating a Release

I'll create a release on GitHub by clicking "Releases" in the right sidebar
and clicking "Create new release". We can choose an existing git tag to use for the
release, but, since we don't have any tags yet, I'll type `v1.0.0`, hit
"Create new tag" and then "Create" in this dialog.

GitHub has this handy button to generate release notes, so I'll click that.
It autofilled the title with our tag name and generated a link to the full
changelog. This isn't too exciting for an initial release but when you add
subsequent releases, it generates a nice list of changes.

I'll just add one note: "Initial release" and click "Publish release".

1.0 release done! Back on our repository page, we can see the 1.0 release listed
as "latest".

This should have triggered the Packagist webhook, so back on the package
page on Packagist, refresh... and... there it is! Our 1.0 release is live
and can now be installed with Composer!

## Testing the Release

Let's see if it works! Somewhere on your system, create a new temporary Symfony app by
running:

```terminal
symfony new --webapp test-my-bundle
```

Wait for it to be created... then navigate to it:

```terminal
cd test-my-bundle
```

Require our bundle!

```terminal
symfony composer require symfonycasts/object-translation-bundle
```

Ok, we have an error, but look up here: Composer downloaded and installed
`v1.0.0` of our bundle!

The error comes from Symfony Flex. It's seeing that we installed a bundle,
so it automatically enabled it in our app. The problem is, our bundle
requires some configuration and an entity: the `translation_class` and
`Translation` entity.

A user could probably stumble though and fix this by reading our bundle
documentation, but it's kind of a bummer...

Next, we'll improve this experience by creating a Symfony Flex recipe for our bundle!
