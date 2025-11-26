# Pruebas de integración

Ya tenemos las pruebas unitarias de nuestro bundle funcionando, ahora toca algo un poco más complicado: las pruebas de integración. Estas pruebas utilizarán el contenedor de servicios Symfony completo, como en una aplicación real. Esto nos permite probar los servicios y sus interacciones en un entorno más realista.

En el directorio `tests` de nuestro bundle, junto al directorio `Unit`, crea una nueva carpeta`Integration`.

## `ObjectTranslatorTest`

El principal punto de entrada de nuestro bundle es este servicio `ObjectTranslator`, por lo que es un gran candidato para nuestra primera prueba de integración.

Dentro de este nuevo directorio, crea una nueva clase PHP llamada `ObjectTranslatorTest`.

No tenemos que preocuparnos de marcarla como `final` o `@internal` para las pruebas. Ni siquiera estarán disponibles cuando se utilice el bundle como dependencia.

En lugar de extender desde `TestCase` de PHPUnit , extiende `KernelTestCase`:

[[[ code('c5623768f2') ]]]

Esto nos permite arrancar el núcleo de Symfony y acceder al contenedor de servicios.

Para nuestra primera prueba, empecemos de forma super sencilla: `public function testCanAccessService()`:

[[[ code('b5c95d0311') ]]]

Esto sólo será una prueba temporal para asegurarnos de que nuestra configuración de prueba de integración funciona.

Dentro, cogeremos un servicio que sé que toda aplicación Symfony tiene: el `CacheInterface`. Escribe:`$cache = self::getContainer()->get(CacheInterface::class);` y luego`$this->assertInstanceOf(CacheInterface::class, $cache);`:

[[[ code('826f27f2a1') ]]]

Si esta aserción pasa, ¡nuestra configuración de prueba de integración funciona!

En el terminal, navega hasta el directorio bundle y ejecuta:

```terminal
symfony php vendor/bin/phpunit
```

Hmm... un error, y sí, es de `testCanAccessService`. Dice que hay que configurar esta variable de entorno `KERNEL_CLASS`...

Vale, para una prueba de integración, necesitamos un núcleo Symfony. En nuestra aplicación tenemos un `Kernel`, pero no podemos utilizarlo en nuestras pruebas de bundle... ¡Necesitamos crear un kernel específico para la prueba!

## `TestKernel`

En el directorio `tests` de nuestro bundle, crea una nueva carpeta llamada `Fixture`. Dentro, crea una nueva clase llamada `TestKernel` que extienda `Symfony\Component\HttpKernel\Kernel`y que sea `use MicroKernelTrait`:

[[[ code('12f43f035e') ]]]

¡Kernel de prueba listo! Bueno... no del todo. Necesita algo de configuración. En las aplicaciones reales, utilizamos archivos YAML, y también puedes hacerlo aquí. Este directorio `Fixture` es como una miniapp. Para evitar tener un montón de archivos extra, podemos añadir la configuración directamente en nuestra clase `TestKernel`.

## `TestKernel` Configuración

Ahonda en `MicroKernelTrait` y encuentra este método `configureContainer`. La versión del rasgo busca los archivos `YAML` para la configuración. Podemos anularlo para definir la configuración directamente. Copia la firma del método y pégala en nuestro `TestKernel`, importando las clases necesarias:

[[[ code('4aa982b13f') ]]]

Sólo tenemos que configurar el framework para las pruebas. Escribe:`$builder->loadFromExtension('framework');`. Esta es la clave que estás acostumbrado a ver en los archivos de configuración YAML de tu aplicación. A continuación, `['test' => true]` para activar el modo de pruebas:

[[[ code('399f8abeea') ]]]

## `KERNEL_CLASS` Variable de entorno

Ahora tenemos que informar a nuestras pruebas de integración sobre esto `TestKernel`. La forma más sencilla de hacerlo es establecer esa variable de entorno `KERNEL_CLASS` de la que se quejaba el error de prueba.

Copia el espacio de nombres de `TestKernel` y abre el archivo `phpunit.xml.dist` de nuestro bundle. Dentro del nodo `<php>`, añade otra variable de entorno:`<env name="KERNEL_CLASS" value="{paste the namespace we copied}/TestKernel" />`:

[[[ code('e729d5f2d8') ]]]

¡Genial! Ahora esta variable está disponible en nuestras pruebas y el `KernelTestCase` de Symfony la utilizará para arrancar nuestro `TestKernel`.

De nuevo en el terminal, ejecuta de nuevo las pruebas:

```terminal-silent
symfony php vendor/bin/phpunit
```

¡Woo! ¡Todo en verde! ¡Nuestra prueba de integración funciona!

## Prueba de la caché del contenedor

De vuelta en nuestro IDE, en nuestro directorio bundle, vemos un nuevo directorio `var`. Si lo abres, podrás ver los archivos de caché de nuestro contenedor de pruebas. No queremos confirmar este archivo, así que abre el directorio `.gitignore` de nuestro bundle y añádele `var/`:

[[[ code('5c9d4f6bf7') ]]]

Después de guardar, podemos ver que ahora se ignora `var/`.

Si te preguntas por qué este directorio `var` está aquí y no en otra parte, es porque cuando se crea la caché del contenedor, si no se especifica explícitamente, asciende por la estructura de directorios empezando por nuestro `TestKernel`, hasta que encuentra un archivo `composer.json`. Entonces crea el directorio `var` junto a él. Puedes configurar totalmente esta ubicación si quieres, quizás a un directorio temporal pero, a mí me gusta la ubicación por defecto.

## Ignora `composer.lock`

Mientras estamos en `.gitignore`, vamos a añadir un par de cosas más. ¿Ves el archivo `composer.lock` de nuestro bundle? Añádelo también a `.gitignore`:

[[[ code('96f9151da6') ]]]

Es muy importante que no confirmes este archivo para los bundles. Pronto verás por qué.

Añade también este archivo `.phpunit.result.cache`:

[[[ code('932964494b') ]]]

Está generado por PHPUnit, y no queremos confirmarlo.

A continuación, vamos a sustituir nuestra sencilla prueba de integración por algo real: una prueba que utilice nuestro bundle para traducir una entidad.
