# Pushing & Pulling Translations

Our "Space Bar" project on Crowdin is all set up and configured for our
app's locales. Let's push up our translations!

At your terminal, run:

```terminal
symfony console translation:push
```

Ok, all our enabled locale's translations are pushed up, but of course, only English
has any.

Jump back over to Crowdin and refresh... Doesn't look like anything changed,
French and Spanish are still at 0% translated.

## Automatic Translations

Click "View Strings". Here we can see all our English translations on the left.
On the right, we have the French and Spanish versions, which are empty. If
you don't see our alternate locales, click "Languages" at the top, select
them, and click "Apply".

If you have a translation team, you can invite them to this project, and they
can start filling in the missing translations!

My translation team is off planet on leave... but we can automate the translations
with Crowdin's suggestions feature. Then my team can review when they return.

Select the empty "French" input for the first string. This panel on the right shows suggestions
from various translation engines. These all look pretty much the same, so
hover over this one and click this "Disk" button to apply it.

Sweet! The French translation for "Local Asteroids" is now filled in and it
auto-switched to the Spanish input. Do the same for this one.

Now it jumped to the next French input - "Apply" this suggestion. Hmm, looks like
Crowdin is telling us there's a spelling mistake... I'm going to just go
with it as is. This is a good spot to note that with automatic translations,
you should always have someone fluent in the language review them. Also, with
translations, context matters, and these small text snippets often lack this.
For instance, expressions like "The man on the moon" might be translated literally
and not make sense in the target language.

There's probably a way to bulk-apply these suggestions, but I think Crowdin
is designed to encourage manual review. I'll quickly go through and apply
the rest of the suggestions. Notice how it knows not to translate the placeholders?

## Saving and Pulling Translations

Looks like this is all auto-saved so just hit the "back arrow" to return to
the project dashboard. Awesome, 100% for both languages! Time to pull them back to
our project!

Head back to your terminal and run:

```terminal
symfony console translation:pull --domains=messages --format=yaml --as-tree=3
```

When we pushed, it knew to push all domains, but for pulling, you need to
specify the domain. We want them in the YAML format, and the "as-tree"
option will indent the translations for us.

Green means good! Let's check them out!

Back in your IDE, in the `translations` directory, `messages.fr.yaml` was
updated and a new `messages.es.yaml` was created. Open them up! Oh yeah,
sweet, sweet translations!

[[[ code('880cb487a5') ]]]

[[[ code('3240d9ac1f') ]]]

## Testing Your Translations

Moment of truth! Switch back to our app and change the language to "French".
Awesome! The menu is French, and if we scroll down, here's the French footer!

Switch to Spanish, menu's Spanish, footer's Spanish, nice!

You can repeat this push, translate on Crowdin, and pull process, over and
over as you add more translations. Crowdin will keep track of what you've
already translated, so you can just focus on the new strings.

And that's a wrap for the basics of translations with Symfony, but you can
certainly dive deeper.

## UX Translator Component

For instance, if you need translations in JavaScript, there's a
[Symfony UX Translator](https://symfony.com/bundles/ux-translator/current/index.html)
component. You manage your translation files like we've shown here, but can also include the keys
in your JavaScript files. Neat stuff!

## Database Translations

There's a big space cat in the room that we haven't really addressed yet: Database
translations... As I said earlier, Symfony's translation component
doesn't handle database translations. We'll need to rely on a third-party
solution for that. A popular one is this
[Doctrine Extensions](https://github.com/doctrine-extensions/DoctrineExtensions) package.
It has a ["Translatable" behavior](https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/translatable.md)
that allows you to translate your entities. There's also a
[Symfony Bundle](https://symfony.com/bundles/StofDoctrineExtensionsBundle/current/index.html)
to wire it all up.

It works just fine, but can be a bit heavy and complex. Thinking out loud,
creating a new, modern bundle for translating Doctrine entities would be an
awesome addition to the Symfony ecosystem!

Alright, 'til next time! Happy coding!
