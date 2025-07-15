# Pushing & Pulling Translations

So we've set up our blank Spacebar project on Crowdin, configured it for
French and Spanish. Now let's go ahead and push those translations up. In
your terminal, simply run:

```terminal
symfony console translation:push
```

And voila! It looks like
it has successfully sent new translations for EN, FR, ES — our enabled
locales, specifically for the messages domain. Perfect.

## Refreshing Crowdin

Jumping back to Crowdin and hitting refresh, it may seem like nothing's
changed. However, if you click on "View Strings", you'll find all our
English versions neatly laid out. You can select the languages you want and
start editing these translations on the go.

Let's get started! You can do all the translations right here in this
user-friendly interface.

Let's start with `local_asteroids`. Click on the French version, pick a
translation suggestion, and hit "Save". As soon as you save, it'll
automatically move on to the Spanish version. Pick the first suggestion for
each one and keep going down the list.

## Saving and Pulling Translations

Once you reach the end of the file, I believe it auto-saves. Now, heading
back to our project dashboard, we can see that we have 100% French and 100%
Spanish translations. Time to pull them.

These translations are now maintained and ready on Crowdin. But let's bring
them back to our project. Head back to your terminal and run:

```terminal
symfony console translation:pull --domains=messages
--format=yaml --as-tree=3
```

This command pulls down the translations for EN, FR, and ES. If you head
back to your editor, you'll see brand new Spanish and French versions of
`/translations/messages.yaml`. Pretty cool, huh?

## Testing Your Translations

To make sure everything works, let's switch the language of our app to
French. Perfect! The translations are correct, and even the footer is in
proper French. Now switch to Spanish and you'll see the same thing.
Everything is in Spanish, including the footer.

## Wrapping Up

And that's a wrap for the basics of translations with Symfony. You can
certainly dive deeper. For instance, if you need translations in your
JavaScript, there's a Symfony UX translator component that helps maintain
your translations within the usual `Symfony messages.en.yaml` files. And
when you build your JavaScript, it imports the ones you're using in
JavaScript, making them available within your JavaScript. Neat stuff!

A question I often get is how to translate database entries. The most
popular solution is the doctrine extensions package, which provides a
translatable behavior for doctrine. It works great, but we'll be diving
deeper into this in my next course, where we'll be creating a third-party
bundle for a fresh, modern approach to translating doctrine entities.

Happy coding and happy translating, everyone!
