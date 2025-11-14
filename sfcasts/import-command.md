# Translations Import Command

Before we tackle the import command, I noticed a
bug in our `ObjectTranslator`. Open that up and take a look at
the `translate()` method. We're using a `WeakMap`, keyed by the object.
The problem here is if you translate an object with the French locale, then
later, translate the same object with the Spanish locale, you'll get the French
version back, because that's what's in the `WeakMap`.

A solution would be to include the locale in the cache key, but `WeakMap`
doesn't support complex keys. We could use a nested structure: an array
of `WeakMap`'s, each keyed by locale. But to keep things simple for now,
let's just remove the `WeakMap` entirely. I think our cache system is robust
enough that we won't run into performance issues. If we do, we can always
reintroduce it later.

So, remove the `$translatedObjects` property and all references to it below.

## The Import Command

Alright, with that out of the way, let's turn our attention to the import
command. In the `tutorial/` folder, copy `ObjectTranslationImportCommand.php`
to our bundle's `Command/` directory. If you don't see this file, you can
copy it from the script below.

Let's walk though it. It's pretty similar to the export command. In `configure()`,
the first argument is the CSV file to import, and the second argument is the locale
we're importing for.

Down in `execute()`, we're grabbing the `file` and the `locale` from the
`$input`, creating the `$io` object, and then opening the passed file
as readable. This time, we're creating a progress bar manually instead of using
`progressIterate()`. We start the progress bar, then use `fgetcsv` to move past
the first row of the CSV file (the headers) which we don't want to import.

Next, we loop over all the rows of the CSV file, expanding them into
variables, then pass them to this not yet created `upsert()` method on
our mapping manager. Still in this loop, we advance the progress bar and
finally, close the file, finish the progress bar, and output a success message.

Cool! Let's wire it up. In our bundle's `services.php` file, copy the definition
for the export command and paste it below. Fix the indentation, rename the
id to `import_command` and change the class to `ObjectTranslationImportCommand`.

## Implementing the `upsert()` Method

Now for that `upsert()` method. Back in our command, find the `upsert()` call
and add the method to `TranslatableMappingManager`. Set all the parameter
types to `string` and the return type to `void`.

If you're not familiar with the term "upsert", it's a combination of "update" and
"insert". It means to update an existing record if it exists, or insert a new
one if it doesn't. This is exactly what we want to do when importing translations.

First, grab the "Object Manager" for the object translation class using
`$om = $this->doctrine->getManagerForClass($this->translationClass)`. Now
try and find an existing translation:
`$translation = $om->getRepository($this->translationClass)->findOneBy()`.
For the criteria: `'objectType' => $type`, `'objectId' => $id`,
`'locale' => $locale`, and `'field' => $field`. These 4 properties uniquely
identify a translation.

If we get a translation from the database, this is an update, if not, it's an insert.

Check if this translation doesn't exist with `if (!$translation)`. In this
case, we need to create a new one. So, instantiate a translation object:
`$translation = new ($this->translationClass)()`. Yep, you can totally instantiate
a class with a variable like this!

Quickly pop into our `Model/Translation` class... cool, all the properties are public.
So, back in our `upsert()` method, `$translation->objectType = $type`,
`$translation->objectId = $id`, `$translation->locale = $locale`, and
`$translation->field = $field`.

Below, set the value with `$translation->value = $value`.

Finally, save the translation with `$om->persist($translation)` and
`$om->flush()`. The persist call is required for new translations, but
it's safe to call it for existing ones too.

Back in the command, the undefined method error is gone.

## Testing the Import Command

Testing time! If you recall from the last chapter, we exported our object
translations to this `var/export.csv` file. I took this file and translated
the `value` column to Spanish and French using GitHub Copilot. In the `tutorial/`
directory, you'll find
[`import.es.csv`](https://raw.githubusercontent.com/SymfonyCasts/bundle-development/refs/heads/bundle/tutorial/import.es.csv)
and [`import.fr.csv`](https://raw.githubusercontent.com/SymfonyCasts/bundle-development/refs/heads/bundle/tutorial/import.fr.csv)
with the translated values. If you don't see them, they are linked in the script below.

Over in the terminal, import the Spanish translations by running:

```terminal
symfony console object-translation:import tutorial/import.es.csv es
```

Cool, 15 translations imported. Now for the French:

```terminal
symfony console object-translation:import tutorial/import.fr.csv fr
```

Because we've made changes to the database, we need to update our translation
cache. Luckily, we have our warmup command:

```terminal
symfony console object-translation:warmup
```

Over in the browser, we're on the French homepage and see our French
fixture data here. Refresh... and... Sweet! All the articles are translated
into French. Switch to Spanish and... Boom! Spanish articles! Click
an article to see the full translated content.

Ok, the initial bundle code for a 1.0 release is done! Next, let's start
working on the *meta* stuff we need to release this bundle to the world.
