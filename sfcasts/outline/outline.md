# Symfony Bundle Development: Share the Love

## Initial Bundle Files

- https://symfony.com/doc/current/bundles.html
- "package" vs "bundle"
- `mkdir object-translation-bundle`
- `cd object-translation-bundle`
- `mkdir src tests config`
- `composer init`
  - `symfonycasts/object-translation-bundle`
  - `Translate your entities!`
  - type: `symfony-bundle`
  - license: `MIT`
- delete `composer` dir ??
- add `.gitignore`
  - `vendor/`
  - `composer.lock` (don't commit for bundles)
- `composer.json`
  - rename psr-4 to `SymfonyCasts\\ObjectTranslationBundle\\` (uppercase C)

## Create Bundle Class

- in `src/`, create new class... namespace isn't detected
- PhpStorm settings... directories
  - set `object-translation-bundle/src` as "Sources"
  - edit prefix, use `SymfonyCasts\ObjectTranslationBundle\`
- now, create new class: `ObjectTranslationBundle`
- proper namespace!
- `final`, `extends AbstractBundle`
- https://symfony.com/blog/new-in-symfony-6-1-simpler-bundle-extension-and-configuration
  - Symfony 6.1+ "new" bundle format

## Install our Bundle Locally

- In our app's `composer.json`, add:
  - `"repositories": [ { "type": "path", "url": "object-translation-bundle" } ]`
- In app root, `composer require symfonycasts/object-translation-bundle`
  - Error about stability
- `composer require symfonycasts/object-translation-bundle:@dev`
  - "symlinked" in vendor
  - Flex automatically added to `config/bundles.php`
    - composer type is `symfony-bundle`
    - our bundle class follows a naming convention flex so flex can detect it

## Bundle Service Class

- in `src/`, create `ObjectTranslator.php`
- mark as `final` - using final in packages is important
  - marks to end-users that this is not meant to be extended
  - allows us to easily keep backwards compatibility
- add method: `translate(object $object): object`
  - docblock:
    - `@template T of object`
    - `@param T $object`
    - `@return T`
- add constructor
  - `private LocaleAwareInterface $localeAware`
  - `private string $defaultLocale`
- in translate():
  - `$locale = $this->localeAware->getLocale()`
  - `if ($this->defaultLocale === $locale) { return $object; }` (nothing to do)
  - `// translate object`
  - `return $object;` (for now)
- In `ArticleController::show()` inject `ObjectTranslator $translator`
  - demo wrapping article object to show autocompletion

## "Wiring Up" our Bundle Service

- Visit an article page - error!
- Doesn't work like you're used to - no autowiring in a bundle
- Create `config/services.php` in the bundle
  - `namespace Symfony\Component\DependencyInjection\Loader\Configurator;`
  - `return static function (ContainerConfigurator $container): void {`
  - `$container->services()`
  - `->set('symfonycasts.object_translator', ObjectTranslator::class)`
- We need to tell Symfony about this file
- In `ObejctTranslationBundle`
  - override `loadExtension()`
  - `$container->import('../config/services.php');`
- Refresh page - error but with more details - we need to allow autowiring
- In `services.php`
  - `->alias(ObjectTranslator::class, 'symfonycasts.object_translator')`
- Refresh again - a different error - we need to wire arguments
- Look at `ObjectTranslator`
  - we need to inject `LocaleAwareInterface` - a service
  - and `defaultLocale` - a parameter
- In the terminal
  - `symfony console debug:autowiring LocaleAware` - get the service id
  - `symfony console debug:container --parameters |grep locale` - get `kernel.default_locale`
- In `services.php`
  - `->args([service('translation.locale_switcher'), param('kernel.default_locale')])`
- Refresh - works!

## Entities in a Bundle

- Since we're translating entities, let's store the translations in the database
  - Single entity for all translations
  - Entity–Attribute–Value (EAV) model
  - Each row will have:
    - `object_type` - name that represents the entity type (like `article`)
      - we could use the class name, but this way it is resilient to refactoring
    - `object_id` - the id
    - `locale` - the locale for this "row"
    - `field` - the entity property name (like `title`)
    - `value` - the translated value
- Create `Model` directory
  - Not `Entity` - we might enable using other storage laters - like Mongo later
  - Create `Translation.php`
  - make abstract - the real entity will extend this and live in our project
    - this allows maximum flexibility
  - `public string $objectType`
  - `public string $objectId` (not int to we can support uuids)
  - `public string $locale`
  - `public string $field`
  - `public string $value`

## Configuring our Bundle's Entity

- No attributes - use xml - this is recommended for bundles
- In `config`, create `doctrine/mapping`
- Copy `Translation.orm.xml` from the tutorial
  - "MappedSuperclass" - real entities extend
- In the terminal, run: `symfony console doctrine:mapping:info`
  - This shows us all the entities and mapped superclasses doctrine knows about
- We need to tell Symfony/Doctrine about this mapped superclass
- In `ObjectTranslationBundle` - override `build()`
  - `$container->addCompilerPass()` - compiler pass is a way to modify the container
  - `DoctrineOrmMappingsPass::createXmlMappingDriver()`
    - Doctrine provides a compiler pass to register mappings
  - `[__DIR__.'/../config/doctrine/mapping' => 'SymfonyCasts\ObjectTranslationBundle\Model'],`
    - mapping directory -> namespace
- Run command again
- Sweet! We see our `Translation` mapped superclass!

## Extending our Bundle's Entity

- In the terminal run: `symfony console make:entity`
  - `Translation`
  - exit immediately - we don't need any fields other than id
- In `Entity\Translation`
  - `use SymfonyCasts\ObjectTranslationBundle\Model\Translation as BaseTranslation`
  - `extends BaseTranslation`
- `symfony console make:migration`
- Check it out
  - Adjust description to "Add Translation entity"
  - Note the properties from the mapped superclass
- `symfony console doctrine:migrations:migrate`

## Bundle Configuration

- Our bundle will need to know the User's Translation entity
- Bundle configuration is the best method!
- In `ObjectTranslationBundle` override `configure()`
  ```php
  $definition->rootNode()
      ->children()
          ->stringNode('translation_class')->end()
      ->end()
  ;
  ```
- In your terminal run: `symfony console config:dump-reference` (no args)
  - Lists available bundle configurations (and their aliases)
  - Ours is `object_translation`
  - This should be prefixed by the `symfonycasts` namespace
- In `ObjectTranslationBundle` - customize the "alias" name
  - `protected string $extensionAlias = 'symfonycasts_object_translation'`
- Re-run the command, we see the proper alias now
- Run `symfony console config:dump-reference symfonycasts_object_translation`
  - We see our `translation_class` option
- We can improve the output of this command by adding a description/example
- In `ObjectTranslationBundle::configure()`
  ```php
  $definition->rootNode()
      ->children()
          ->stringNode('translation_class')
              ->info('The class name of your translation entity.')
              ->example('App\Entity\Translation')
          ->end()
      ->end()
  ;
  ```

## Bundle Configuration Validation

- `translation_class` is required
- In `ObjectTranslationBundle::configure()`
  - add `->isRequired()` to mark as required
  - add `->cannotBeEmpty()` to ensure not empty string
- Run `symfony console config:dump-reference symfonycasts_object_translation`
  - error!
- In our app, create `config/packages/symfonycasts_object_translation.yaml`
  ```yaml
  symfonycasts_object_translation:
      translation_class: '' # empty string to test cannotBeEmpty
  ```
- Run command again - we see a different error - cannot be empty
- Set to `Translation`
- Run command again - works...
- Check our current config with
  - `symfony console debug:config symfonycasts_object_translation`
- This needs to be the full class name... so let's validate that
- In `ObjectTranslationBundle::configure()`
  ```php
  ->validate()
      ->ifTrue(fn(string $v) => !class_exists($v))
      ->thenInvalid('The translation_class %s does not exist.')
  ->end()
  ```
- Run command again - error!
- Be cheeky and set to `App\Entity\Article`
- Run command again - no error but isn't correct... needs to be our Translation entity
- We can chain more validation but change `class_exists` to:
  - `is_a($v, Translation::class, true)`
    - ensure `Translation` is from our bundle namespace
    - true allows this method to check class names as strings
  - Change error message to `The translation_class %s must extend SymfonyCasts\ObjectTranslationBundle\Model\Translation.`
- Run command again - error!
- Change to `App\Entity\Translation`
- Homework: make sure it isn't the `Translation` class from the bundle!

## Using Bundle Configuration

- We need the class name in `ObjectTranslator` so add as a property
  - `private string $translationClass`
- Go to an article page - error!
- In `config/services.php` - we need to wire the argument
  - But what do we use here? We can't access bundle config here.
  - add `abstract_arg('translation class')`
  - marks this argument as required
- Refresh page - error about this missing argument
- In `ObjectTranslationBundle::loadExtension()`
  - `dd($config)` and refresh page - there's our config!
  - remove `dd()`
  - `$builder->getDefinition('symfonycasts.object_translator')`
    - This gets our service "definition" - not the "real" service
  - `->setArgument(2, $config['translation_class'])`
    - 2 - third argument (0-based)
- Refresh page - works!
- In `ArticleController::show()`
  - `dd($translator)`
  - refresh
  - looks right!
  - remove `dd()`

## Translated Object Wrapper

- In `ObjectTranslator::translate()` we need to "translate" this object
  - Find fields in the db and set them on the object
  - Can't just update fields on this object because it could be flushed() and updated in the db
  - Cloning could work... but brings up object identity issues...
- Let's create a "wrapper" or "view" object
- Create `src/TranslatedObject.php` - final
  - this will pass all property and method calls to the wrapped object
  - `public function __construct(private object $_inner)`
    - We use `_` to avoid any potential conflicts with the wrapped object
  - add `@template T of object` - "generics" in PHP
  - add `@mixin T` - this tells IDEs that this class is a "mixin"
  - add `@param T $_inner` to note that this is the wrapped object
  - override `__call()`, `__get()` and `__isset()` magic methods
    - __call():mixed - `return $this->_inner->$name(...$arguments);`
    - __get():mixed - `return $this->_inner->$name;`
    - __isset():bool - `return isset($this->_inner->$name);`
- In `TranslatedObject`, return `new TranslatedObject($object)` in `translate()`
- In `ArticleController::show()`, translate the article
- Visit english article page - works... but look at `ObjectTranslator::translate()`
- When on the default locale, we return the original object
- Switch to the `fr` article page - error!
- Let's fix this by building via a Unit Test

## Unit Testing `TranslatedObject`

- This error is because of how Twig works with magic methods
  - In our template, we're using `{{ article.title }}` but the actual method is `getTitle()`
  - When using the real object, Twig knows to call `getTitle()`
  - But it doesn't know to call `getTitle()` on our `TranslatedObject`
  - So let's help it out!
- We'll create a unit test
- In our bundle's `composer.json`, add
    ```json
    "autoload-dev": {
        "psr-4": {
            "SymfonyCasts\\ObjectTranslationBundle\\Tests\\": "tests/"
        }
    },
    ```
- In PhpStorm settings... directories
  - add `object-translation-bundle/tests` as "Tests"
  - edit and set prefix to `SymfonyCasts\ObjectTranslationBundle\Tests\`
- create `tests/Unit` directory
- create `TranslatedObjectTest.php` - extends `TestCase`
- First, let's make sure the default wrapping works as expected
- `testCanAccessUnderlyingObject`
  - Create a dummy object
    ```php
    class ObjectForTranslationStub
    {
        public string $prop1 = 'value1';
        private string $prop2 = 'value2';
        private string $prop3 = 'value3';
    
        public function prop2(): string
        {
            return $this->prop2;
        }
    
        public function getProp3(): string
        {
            return $this->prop3;
        }
    }
    ```
    ```php
    $object = new TranslatedObject(new ObjectForTranslationStub());

    $this->assertSame('value1', $object->prop1);
    $this->assertTrue(isset($object->prop1), 'Public property should be accessible');
    $this->assertFalse(isset($object->prop2), 'Private property should not be accessible');
    $this->assertSame('value2', $object->prop2());
    $this->assertSame('value3', $object->getProp3());
    ```
- Run our tests with `vendor/bin/phpunit object-translation-bundle/tests`
  - This is using our application's phpunit config, but it's fine for now

## TDD Getter Behaviour

- `testCallUsesGetterIfAvailable`
  ```php
    $object = new TranslatedObject(new ObjectForTranslationStub());
    
    $this->assertSame('value3', $object->prop3());
        ```
  ```
- Run tests - error!
- In `TranslatedObject::__call()`
  - `$method = $name;`
  - `if (!method_exists($this->_inner, $method)`
  - `$method = 'get'.ucfirst($name);`
  - `return $this->_inner->$method(...$arguments);`
- Run tests - passes!
- Check the french article page - works!!
- Probably more edge cases, but now we have a unit test to add them to!

## Translatable Entity Mapping

- Need to "mark" our entities as translatable and what properties are translatable
- Could use an interface, but let's use attributes!
- This is a "Mapping", so create a `Mapping` directory in our bundle
- Create `Translatable.php` - mark final
  - `#[\Attribute(\Attribute::TARGET_CLASS)]` - only usable on classes
  - `public function __construct()`
  - `public string $type,` - this will be the `object_type` in the database
  - We could allow defining the properties here (as an array), but let's use another attribute
- Create `TranslatableProperty.php` - mark final
  - `#[\Attribute(\Attribute::TARGET_PROPERTY)]` - only usable on properties
  - We could make the field name configurable, but let's keep it simple right now
    and just use the property name
- Let's map our entities!
  - Article - add `#[Translatable('article')]`
    - `#[TranslatableProperty]` to `title` and `content`
  - Category - add `#[Translatable('category')]`
    - `#[TranslatableProperty]` to `name`
  - Tag - add `#[Translatable('tag')]`
    - `#[TranslatableProperty]` to `name`
