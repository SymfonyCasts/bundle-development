# Entities in a Bundle

Alright, let's dive into the fascinating world of object translations. This
is all about translating entities in a database. And since we're dealing
with a database, the optimal way to store translations is in an entity.

So, here's the plan: we're going to create a single table for all the
translations of all the different objects inside your system. To achieve
this, we're going to employ a neat pattern known as an Entity Attribute
Value or, for short, an EAV model.

In this pattern, each row will have an `object_type`, which is basically a
name that stands for the entity, like `article` or `category`. Sure, you
could use the class name here, but storing class names in the database can
lead to trouble if you refactor or rename a class. So, by using these alias
names, we make the system resilient to refactoring.

Next in line is `object_id`. This will be the unique identifier of the
object, as represented by the `object_type` above. We're also going to
store the `locale` (indicating the locale of the translation) and the
`field`. The `field` will be the property on the entity being translated,
and lastly, the `value`—the translated value in that locale.

## Creating the Model

Here's what you'll do next: in your bundle directory source, create a new
directory. Now, you might think it should be called `entity`, like in your
apps, but let's call it `model`. This is a common practice when storing
entities in a bundle because it allows compatibility with other storage
layers that Doctrine provides, like MongoDB. And, just so you know, MongoDB
refers to their entities as documents. So `model` is a more generic term
that covers both documents and entities.

Inside this directory, let's create a new class called `Translation.php`.
This is going to be an abstract class, because when you're putting an
entity in a bundle, you should avoid using the full entity. Instead, make
it an abstract class which will be represented as a mapped superclass in
Doctrine.

## Setting up Fields

Time to set up your fields. We're going to have a first field of `public
string $objectType`, then `public string $objectId`, `public string
$locale`. Now, for the `objectId`, we're using a string because it best
accommodates the various types of IDs that can be used on a Doctrine
entity. Whether it's an integer, a UUID or something else, as long as it
can be converted into a string, we're good to go. Following this we have
`public string $locale`, then `public string $field`, and finally `public
string $value`.

Congrats! You now have your `Translation` model in your bundle. Next up,
we're going to let Symfony and Doctrine in on it. So grab a cup of coffee,
take a deep breath, and let's dive into the next part.
