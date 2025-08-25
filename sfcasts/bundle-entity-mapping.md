# Configuring our Bundle's Entity

Hey there! Let's dive right into teaching Symfony & Doctrine about our
abstract `translation` class. You know, the one we're planning to extend.
All you need to do is hop on over to your terminal and run this command:

```terminal
symfony console doctrine mapping info
```

What you'll see is a list of all the entities and mapped superclasses that
Doctrine knows about. However, you'll notice that your `translation` mapped
superclass isn't on this list, and we need to change that.

## Working with Doctrine Entities

You might be used to using the mapping attributes for your Doctrine
entities in your end apps, but things are slightly different when you're
dealing with bundles. The official recommendation is to use XML for
this—yes, I know, it's not the most fun thing to work with, but it does
provide the most flexibility.

## Creating the XML File

So, let's get down to business and create that XML file. Go ahead and add a
folder and subfolder called `doctrine mapping` in your `config`. Now, to
save you the pain of watching me write XML, there's a file called
`translation.orm.xml` in the tutorial directory. Just grab that and move it
straight into this mapping directory.

## Understanding the XML File

On first glance, it might look a bit intimidating, but it's really not.
Essentially, you're telling Doctrine that this is a mapping for a mapped
superclass, and you're providing the full class name of your `translation`
mapped superclass. Then, you're defining the fields that correspond to the
properties.

You've got this `objectType` property which will be a column in the
database named `object_type` of string type, and the same goes for
`objectId`. Then you've got the `locale` field and `value`—all strings
except for `value`, which is text. The reason `value` is text is because we
don't want any limitations on the size of this database column.

## Linking Symfony and Doctrine to the XML File

Just creating the XML file isn't enough though, we need to point Symfony
and Doctrine to this file. To do this, in your `source object translation
bundle`, you'll override a class and a method, specifically the `build`
method.

You'll be adding what's called a `compiler pass`. So you'll use `container
add compiler pass` and it's going to be a `Doctrine ORM mappings pass`.
Then you'll create an XML mapping driver. This requires an array that's
keyed by the mapping directory to the namespace that this mapping directory
represents.

You're going to use `__DIR__` dot, and then a string of `slash..slash`, and
then `config Doctrine mapping`. Now, you need the namespace that this
mapping directory represents. To get that, just pop into your `translation`
model and simply copy the namespace.

And there you go!

## Checking Your Work

Let's run the `Doctrine mapping info` command again to see if our
`translation` class is now recognized:

```terminal
symfony console doctrine mapping info
```

And voila! If you look at the bottom, you'll see that it's detecting the
`translation` map superclass.

## Creating the `Translation` Entity

Next up, let's roll up our sleeves and create the real `translation` entity
in your app. Can't wait to see this in action with you!
