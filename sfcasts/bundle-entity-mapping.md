# Configuring our Bundle's Entity

We created the abstract `Translation` class in our bundle. Now, we need
to tell Doctrine about it so we can extend and use it in our app as 
a *real* entity.

First, hope over to your terminal and run:

```terminal
symfony console doctrine:mapping:info
```

This shows all the entities that Doctrine knows about. The `Category`,
`Tag`, and `Article` entities from our app, is all we see right now. This
command *also* shows mapped superclasses, so we need to do some work
to get our `Translation` class to show up here.

You're probably used to using *mapping attributes* for your Doctrine entities.
In bundles, it's a bit different. The official recommendation is to use XML for
the mapping. I know, I know, XML is *not* the most fun thing to work with, but it
does provide the most flexibility.

## Creating the XML File

Let's get down to business. In your bundle's `config` directory, create a new
directory and subdirectory: `doctrine/mapping`. To save you the pain of
watching me struggle to write the XML, in the `tutorial` directory, there's a
`Translation.orm.xml` file. Grab that file and move it in into our new
`doctrine/mapping` directory.

Note the `.orm.xml` suffix. This is important as it tells Doctrine that this
is mapping information for the ORM. In the future, if we add support for MongoDB, we'd create
a `Translation.mongodb.xml` file with the appropriate mapping for that.

## Understanding the XML File

Open `Translation.orm.xml` in your editor. Gross... let's unpack this.
First, we're declaring a top-level `doctrine-mapping` element with some
XML namespace stuff. Inside, a `mapped-superclass` element with a
`name` attribute for the full class name of our bundle's `Translation` class.

Inside this, is our `field` mappings. The `name` attribute corresponds to
the property name in our `Translation` class, and the `column` attribute
is the column name we want to use in the database. If omitted, Doctrine
will use the property name as the column name. I've *snake_cased* the
`objectType` and `objectId` column names to follow common database
naming conventions.

The `type` attribute indicates the Doctrine type for the column. We'll use
`string` for all except for `value`, which is `text`. `string` columns
have a maximum length, typically around 200-250 characters, depending on
the database platform and configuration. This is fine for most of our fields,
but the `value` column needs to hold more. The `text` type can hold much larger strings,
so it's perfect for our translated values.

## Loading the XML Mapping

Just creating the XML file isn't enough though, we need to let Doctrine
know about this file. In `ObjectTranslationBundle`, override the `build()`
method and add the `void` return type. This parent method call can be removed,
as it's empty.

`loadExtension()` is where we load and configure things for *this* bundle. `build()`
is called later in the process, after all other bundles have been registered. This
allows us to modify the service container after all other bundles have had
a chance to register their services. Here, we can tell Doctrine about our
bundle's mappings.

We'll do this with a *compiler pass*, a sort of *build hook* that has access to
the fully built service container. Write `$container->addCompilerPass()`.

Doctrine provides a compiler pass specifically for loading mappings. Write
`DoctrineOrmMappingsPass` and import this class from the Doctrine bundle.
Here we can see the static methods to create mappings. Choose `createXmlMappingDriver()`.

The first argument is an array. The keys are the directories where the mapping
files are located, and the values are the *namespaces* that these mapping files
represent. We only need one.

For the key, use `__DIR__.'/../config/doctrine/mapping'` - this is the relative path
to our mapping directory. For the value, jump over to the `Translation` class and
copy its namespace `SymfonyCasts\ObjectTranslationBundle\Model`. Go back
to `ObjectTranslationBundle` and paste it in as the value.

That's it!

Jump over to your terminal and run the `doctrine:mapping:info` command again:

```terminal-silent
symfony console doctrine:mapping:info
```

Sweet! Now we can see our `Translation` mapped superclass listed.

Next, we'll create the *real* `Translation` entity in our app!
