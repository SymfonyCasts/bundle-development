# Pruebas de Doctrine

Es hora de convertir este stub de prueba de integración en una prueba adecuada para nuestro bundle. ¡Vamos a utilizar una base de datos real y entidades reales para ello!

En el terminal, instala Zenstruck Foundry para que nos ayude a gestionar nuestra base de datos de pruebas y nuestras entidades:

```terminal
symfony composer require --dev zenstruck/foundry
```

## Entidades de prueba

De vuelta a nuestro IDE, en el directorio `tutorial`, copia la carpeta `Entity` en el directorio `tests/Fixture` de nuestro bundle. Si no ves estas entidades, cópialas del script que aparece a continuación.

`Entity1` es nuestra entidad simulada que traduciremos en nuestras pruebas. Es traducible, tiene un ID y una propiedad traducible.

La entidad `Translation` es igual que la de nuestra aplicación.

## Más configuración de `TestKernel` 

En nuestro `TestKernel`, tenemos que decirle qué bundles cargar. Anula el método `registerBundles()`. Dentro, `yield new FrameworkBundle()`,`yield new DoctrineBundle()`, `yield new ZenstruckFoundryBundle()`, y finalmente, nuestro bundle, `yield new ObjectTranslationBundle()`.

Ahora necesitamos más configuración. En `configureContainer()`, añade`$builder->loadFromExtension('symfonycasts_object_translation', ['translation_class' => Translation::class])`. Es difícil de ver en esta pequeña pantalla, pero necesitamos importar el de nuestras instalaciones de prueba. Creo que es ésta. Me desplazaré hasta los espacios de nombres para confirmarlo. Sí, es éste.

Ahora a configurar Doctrine. Añade`$builder->loadFromExtension('doctrine', [])`. Primero configura `dbal` con`'url' => 'sqlite:///%kernel.project_dir%/var/data.db'`.

Para la configuración de `orm`, voy a pegar este fragmento (puedes cogerlo del script de abajo). Esto indica al ORM de Doctrine dónde encontrar nuestras entidades de prueba.

## La prueba `ObjectTransator::translate()` 

De vuelta a nuestra clase de prueba, borra el método de prueba existente. En primer lugar, tenemos que crear una instancia de nuestra entidad simulada, `Entity1`. Para ello utilizaremos una fábrica de Foundry. Podríamos crear una clase de fábrica real, pero para simplificar las cosas, utilizaremos una fábrica dinámica. Escribe `$entity = persist()`, importa la función de Foundry `Entity1::class`
como primer argumento, y un array con `'property1' => 'value1'` como segundo.

Ahora la entidad `Translation`: `persist(Translation::class)`. De nuevo, asegúrate de importar la de nuestras instalaciones de prueba. El array será `'objectType' => ''`, pasa a nuestra clase `Entity1` para confirmar el alias: `entity1`. A continuación, `'objectId' => $entity->id`,`'locale' => 'fr'`, `'field' => 'property1'`, y por último, `'value' => 'translated1'`.

A continuación, obtén nuestro servicio traductor de objetos con`$translator = self::getContainer()->get(ObjectTranslator::class)`. Traduce la entidad:`$translated = $translator->translate($entity)` y pasa `fr` como segundo argumento para forzar la traducción al francés.

Por último, afirma que la propiedad traducida es la que esperamos`$this->assertSame('translated1', $translated->property1);`

¡El momento de la verdad! De vuelta al terminal, ejecuta nuestras pruebas:

```terminal
symfony php vendor/bin/phpunit
```

¡Maldita sea! Un error: "Foundry aún no se ha iniciado"

Ohhh, olvidé los rasgos necesarios de Foundry. De vuelta a la clase de prueba,`use Factories`, que inicializa Foundry, y `ResetDatabase`, que reinicia la base de datos antes de cada prueba.

Momento de la verdad, toma dos:

```terminal-silent
symfony php vendor/bin/phpunit
```

¡Genial! ¡Todo verde! ¡Nuestra prueba de integración con una base de datos real funciona!

A continuación, probaremos nuestro bundle con diferentes versiones de Symfony para garantizar la compatibilidad.
