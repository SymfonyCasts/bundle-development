# Utilizar la configuración del bundle

En nuestro bundle, hemos definido y validado la opción de configuración `translation_class`. En nuestra aplicación, la hemos configurado como `App\Entity\Translation`. Ahora tenemos que utilizarla en el servicio `ObjectTranslator` de nuestro bundle.

## `ObjectTranslator` Actualiza

Abre esta clase y añádela al constructor como `private string $translationClass`:

[[[ code('253fd1348d') ]]]

Espera al navegador y haz clic en un artículo. Aparece un error: "Demasiados pocos argumentos para ... ObjectTranslator::__construct(), 2 pasados... 3 esperados".

## `abstract_arg()`

Vamos a solucionarlo. Abre el `config/services.php` de nuestro bundle. Tenemos que añadirlo como tercer elemento en esta matriz `args()`. Pero... aquí no tenemos acceso a nuestra configuración. De momento, podemos hacerlo con una función especial:`abstract_arg()`. Para el primer argumento, describe su propósito: "Clase de traducción":

[[[ code('1f29965344') ]]]

Esto no es estrictamente necesario, pero ayuda con la depuración. Básicamente estamos haciendo saber a Symfony que aún necesitamos configurar este argumento.

De vuelta en el navegador, actualiza. Un nuevo error: "Argumento 3 del servicio ...object_translator es abstracto". Y vemos nuestra descripción: "Clase de traducción". ¡Perfecto!

## Utilizar la configuración del bundle

Ahora... tenemos que encontrar el lugar donde tenemos acceso a la configuración del bundle.

En `ObjectTranslationBundle::loadExtension()`, fíjate en el argumento `array $config`. Dentro de este método, vuélcalo con: `dd($config)`:

[[[ code('9d159605d3') ]]]

De vuelta en el navegador, actualiza.

¡Qué bien! ¡Aquí está nuestra configuración procesada como un array! ¡Es hora de ponerla en práctica!

Debajo de la importación, escribe `$builder->getDefinition()`. Asegúrate de utilizar `getDefinition()`, no `get()`. Dentro, pasa el ID de servicio de nuestro servicio `ObjectTranslator`:`symfonycasts.object_translator`:

[[[ code('e930aa2b1c') ]]]

Una de las ventajas de trabajar en tu bundle dentro de una app es que puedes obtener autocompletado de PhpStorm + plugin Symfony para tus servicios.

A continuación, encadena `->setArgument()`. El primer argumento aquí es el índice basado en 0 del argumento del constructor que queremos establecer. Salta rápidamente a `ObjectTranslator`,`$translationClass` es el tercer argumento, así que tenemos que pasar `2`.

El segundo argumento es el valor que queremos establecer: `$config['translation_class']`:

[[[ code('2ba6dd2c44') ]]]

Vuelve a tu navegador y actualízalo. ¡Muy bien! No hay errores.

Asegurémonos de que se le ha dado el valor que esperamos. Abre `ArticleController::show()`y `dd($translator)`:

[[[ code('e3a8319534') ]]]

De nuevo en el navegador... actualiza. ¡Qué bien! Aquí está nuestro objeto `ObjectTranslator` y, efectivamente, la propiedad `translationClass` está establecida en `App\Entity\Translation`.

Elimina el `dd()`, actualiza y ¡ya estamos listos para continuar!

A continuación, empecemos a escribir nuestra lógica de traducción en `ObjectTranslator`.
