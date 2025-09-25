# Pruebas unitarias `TranslatedObject`

Tenemos un pequeño contratiempo al utilizar nuestro `TranslatedObject` en Twig. Esto es lo que ocurre En el contexto de esta excepción, puedes ver que está llamando a`article.title` en nuestra plantilla. Cuando la entidad no estaba envuelta en un`TranslatedObject`, funcionaba bien. Twig tiene una lógica ingeniosa para que, al llamar a `.title`, compruebe si existe un método getter:`getTitle()`. Si lo encuentra, llama a ese método. Esa lógica no funciona con nuestra envoltura `TranslatedObject`: este objeto no tiene los getters, sino el objeto subyacente. Twig ve que tenemos el método mágico `__call()`, así que intenta llamar a `title()`.

¡Podemos engancharnos a esto! ¡Hagámoslo mediante pruebas unitarias!

## Pruebas de bundle

En primer lugar, tenemos que configurar nuestro bundle para las pruebas. Si aún no existe, crea un directorio `tests` en la raíz de nuestro bundle. Ahora, abre el archivo `composer.json` de nuestro bundle. Copia toda la sección `autoload` y pégala a continuación. Cámbiale el nombre a `autoload-dev`. Sólo queremos que Composer vea las pruebas cuando estemos en desarrollo.

El mismo espacio de nombres, pero añade `\\Tests` al final. En lugar de `src/`, utiliza`tests/`:

[[[ code('27aefc2851') ]]]

Ahora Composer conoce nuestras pruebas, pero no PhpStorm. Al igual que hicimos con el espacio de nombres principal, tendremos que darle un empujoncito a PhpStorm. Abre Configuración, Directorios. Navega hasta el directorio `tests` de nuestro bundle y márcalo como Raíz de Pruebas.

Utiliza el icono del lápiz para abrir el espacio de nombres del directorio `src` de nuestro bundle. Copia el espacio de nombres, cierra la ventana y abre la configuración del espacio de nombres de nuestro directorio `tests`. Pega el espacio de nombres y añade `Tests\` al final. Haz clic en Aceptar, Aplicar, Ok.

Genial, ¡listo para nuestra primera prueba!

## Crear nuestra primera prueba

Dentro del directorio `tests` de nuestro bundle, crea un directorio `Unit`. Esto nos ayudará a organizar nuestras pruebas por tipos. Dentro, crea una nueva clase PHP llamada`TranslatedObjectTest`. Haz que extienda `TestCase`, de `PHPUnit`:

[[[ code('32c32ba469') ]]]

Nuestra primera prueba se limitará a demostrar que nuestro `TranslatedObject` funciona como esperamos, sin nada de Twig todavía. Crea la prueba con`public function testCanAccessUnderlyingObject()`:

[[[ code('06afaed956') ]]]

## Crear un objeto Stub

Ahora necesitamos un objeto que envolver con nuestro `TranslatedObject`. Crearemos una clase stub justo en este archivo. Debajo de nuestra clase de prueba, crea un `class ObjectForTranslationStub`:

[[[ code('e353ff2be3') ]]]

Lo sé, lo sé, esto no puede autocargarse correctamente cuando se utiliza fuera de esta clase. Nunca querrás hacer esto en tu código de producción, pero en las pruebas, mientras sólo lo utilices en este archivo, creo que está bien. Si lo prefieres, puedes crear una clase fixture propia.

En esta clase, vamos a añadir algunos escenarios diferentes. Una propiedad pública`public string $prop1 = 'value1'`. Dos propiedades privadas:`private string $prop2 = 'value2'` y `private string $prop3 = 'value3'`:

[[[ code('d1b1f0eb33') ]]]

Un método no-objetivo para acceder a `prop2`: `public function prop2(): string`. En su interior,`return $this->prop2;`. Un getter para acceder a `prop3`: `public function getProp3(): string`. Y dentro, `return $this->prop3;`:

[[[ code('d1b1f0eb33') ]]]

## Escribir la prueba

En nuestro método de prueba, crea el objeto con`$object = new TranslatedObject(new ObjectForTranslationStub());`:

[[[ code('fbc2944273') ]]]

Esta es la fase de configuración u organización de nuestra prueba. `$object` es lo que se llama nuestro sistema bajo prueba, una forma elegante de decir "lo que queremos probar".

Ahora pasemos a la fase de las afirmaciones

En primer lugar, vamos a probar el acceso a las propiedades públicas. `$this->assertSame('value1', $object->prop1)` a continuación, `isset()` sobre la propiedad pública: `$this->assertTrue(isset($object->prop1))`. Añade una descripción como segundo parámetro: `Public property should be accessible`. Esta descripción no es necesaria, pero puede ser útil cuando falla una prueba:

[[[ code('8736e6794a') ]]]

Ahora, acceso a la propiedad no pública: `$this->assertFalse(isset($object->prop2))`. Descripción:`Private properties should not be accessible`:

[[[ code('8bc220be78') ]]]

Sobre los métodos: `$this->assertSame('value2', $object->prop2())` y`$this->assertSame('value3', $object->getProp3())`:

[[[ code('d318d0937f') ]]]

## Ejecuta la prueba

¡Vamos a ejecutar esta prueba! No tenemos configurada la prueba para el bundle, pero de momento podemos utilizar la configuración PHPUnit de nuestra aplicación.

En tu terminal, en la raíz de nuestra aplicación (no en el bundle), ejecuta:

```terminal
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

¡Woo! ¡Pasando! La funcionalidad básica de nuestro `TranslatedObject` funciona como esperábamos.

A continuación, vamos a utilizar el verdadero Desarrollo Dirigido por Pruebas (TDD) para resolver el problema de Twig.
