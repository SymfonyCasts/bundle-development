# Export Translations Command

Alright, we're almost done the coding for a 1.0 release. But, we've got one
thing left to tackle - managing the translations. Eventually, I'd like this
bundle to include perhaps a `FormType` that can allow users to integrate
management of translations into their admin UI. But for now, let's create
two console commands: one for exporting translatable fields in the default
locale, English in our case, to a CSV file. The user would then take this
file to a translation service that translates the values into their supported
locales. Then, we'll have import command to suck these translated files
back in.

## The Export Command

First things first, let's build that export command. To make things
quicker, I've already created it. In the `tutorial` directory, copy
`ObjectTranslationExportCommand.php` into the bundle's `src/Command`
directory. If you don't see it in your `tutorial` directory, don't worry.
You can copy it from the script below.

Now, let's dissect this command a bit. This should look familiar. Command
name: `object-translation:export`, description: `Exports object translations to a CSV`.

For the constructor, we're just injecting our `TranslatableMappingManager` - that's
all we'll need.

The `configure()` method is where we set up arguments and options. This command
will take one argument: the file path to export the CSV to. The user can pass
an absolute or relative path.

In `execute()`, create the `$io`, grab the `$file` argument, then output
a title. Next, we `fopen` the file for writing. Since this will be a
CSV, we're using `fputcsv()` to write the column headers: type, ID, field, and value.

Just like our warmup command, we *progress iterate* over all our *translatable objects*.
Grab the type and ID for each object, then iterate over this mapping manager
`translatableValuesFor()` method, which we'll create soon. This method iterates over
the translatable object's translatable field names and values.

Inside, we `fputcsv()` a row with the type, ID, field, and value.

Finally, below the two loops, we `fclose()` the file and output a success message.

## Wiring Up the Command

Now we need to wire up this command. Head over to our bundle's `services.php` file.
Add `->set('.symfonycasts.object_translator.export_command, ObjectTranslationExportCommand::class)`.
For the `args()`, just the one service: copy and paste this from the command above.

Finally, `->tag('console.command')`.

## Creating a New Method

Back in the `ObjectTranslationExportCommand`, this `translatableValuesFor()`
method on our mapping manager service doesn't exist. Create it. Use
`object` as the argument type and `iterable` as the return type.

We'll use reflection to grab these properties, so first, get the reflection
class with `$class = new \ReflectionClass($object);`. Next, loop over the
properties with `foreach ($class->getProperties() as $property)`.

How do we know what properties are translatable? Remember the
`TranslatableProperty` attribute we created earlier? We haven't used it yet
but now is it's time to shine! In our app's entities, this attribute
marks the translatable properties.

First, exclude the properties that don't have this attribute.
`if (!$property->getAttributes(TranslatableProperty::class))`, `continue`.

Now we know the property is translatable, so,
`yield $property->getName() => $property->getValue($object)`. Even if the property
is private or protected, when using reflection like this, we can get the value.

Back in the command... sweet! No more warning!

Time to test 'er out! Over in the terminal, run:

```terminal
symfony console object-translation:export var/export.csv
```

Nice! The progress bar shows 12 objects were exported and saved
to our project's `var` directory as `export.csv`.

Back in your IDE, find that file and open it!

Boom! Here it is! First row is our column headers, and each row
below is the exported English version of our translatable object's
translatable fields. This looks kind of nasty because of all the
line breaks in our article's content fields, but rest assured, this
is a valid CSV.

Next, we'll create the import command to bring the translated version of this
file back into our project.
