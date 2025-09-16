# Entities in a Bundle

Our bundle needs a way to *store* the object translations. Since we're
translating Doctrine entities from the database, we know a database is
in use. So the best way to store these translations is also in the
database.

Here's the plan: we're going to create a single table for all the
translations of all the different Doctrine objects in your app. To achieve
this, we're going to employ a neat pattern known as an Entity Attribute
Value or, for short, an EAV model.

In this pattern, each row will have an `object_type`, which is basically a
name that represents the entity type, like `article` or `category`. We
*could* use the class name here, but storing class names in the database can
lead to trouble if you refactor or rename a class. So, by using these alias
names, we make the system resilient to refactoring.

The next column is `object_id`. This will be the unique identifier of the
object, as represented by the `object_type` above. We're also going to
store the `locale` (indicating the locale of the translation) and the
`field`. The `field` will be the property on the entity being translated,
and lastly, the `value` — the translated value in that locale.

You might think this pattern is inefficient, and in some ways, you'd be right.
But, we can use it to easily translate dozens of entity types without
having to create a new table for each entity. The trade-off is performance,
this table can, and likely will, grow quite large. However, with proper indexing
and caching, the performance trade-off can be mostly mitigated.

## Creating the Model

There's a trick to having a bundle provide an entity, so let's dive in.

In the bundle's `src` directory, create a new directory. Now, you might think
it should be called `Entity`, like in your apps, but name it `Model` instead.
This is a common practice for bundles. In the future, we might offer support
for additional Doctrine abstraction layers like MongoDB - which uses the
term `Document` instead of `Entity`. `Model` is a more generic term that covers
both `Entity`, `Document`, and others.

Inside this directory, create a new class called `Translation`. Here's the
trick: make this class `abstract`: 

[[[ code('fc2e5d8a8f') ]]]

This is going to be a `MappedSuperclass`
in Doctrine terms. We don't want our bundle to provide the *real* entity.
The user of the bundle will create their own `Translation` entity that
extends this one. All they'll need is an ID field. The rest of the fields
will be inherited from this class. This offers the flexibility for users
to define their own ID strategy, table name, indexes, field name or type
overrides, even adding additional fields if required.

## Setting up Fields

Now for the fields. First: `public string $objectType`, this will be the
*object class alias* we talked about earlier. Next: `public string $objectId`. 
We're using `string` instead of `int` for `objectId` to be able to accommodate
the various types of IDs that can be used on a Doctrine entity. Whether it's an
integer, a UUID or something else, as long as it can be converted into a string,
we're good to go. Now: `public string $locale`. These three fields together will
allow us to query all field translations for a given object, in a given locale.

Finally, `public string $field`, the property name on the entity being translated,
and `public string $value`, the translated value in this *row's* locale.

[[[ code('da0c8132fa') ]]]

That's it!

Next, just like services, we need to tell Symfony and Doctrine about this class!
