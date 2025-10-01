# Lógica de traducción

Hemos hecho un montón de trabajo de configuración para llegar a este punto, pero ha llegado el momento del evento principal: traducir nuestros objetos. ¡Manos a la obra!

Nuestra base de datos se ha cargado con estos accesorios. En nuestro navegador, haz clic en el primer artículo. Esta es la versión en inglés. Cuando cambiemos al francés, deberíamos ver esos valores de fixture franceses... Pero... tenemos un error...

"Demasiados pocos argumentos para... TranslatedObject::__construct()". Ah, sí, hemos añadido el argumento traducciones, pero aún no hemos actualizado nuestro `ObjectTranslator`. Perfecto, ¡ese es nuestro objetivo!

## `translationsFor` Método

En `object-translation-bundle/src`, abre `ObjectTranslator`. En el método `translate()`, donde estamos creando un nuevo `TranslatedObject`, tenemos que añadir un segundo argumento.

Crea un método para ello: `$this->translationsFor()`. Pasa `$object` y`$locale`:

[[[ code('2e9447f2d1') ]]]

Este método no existe, así que genéralo con PhpStorm. ¡Genial! ¡Conocía las sugerencias de tipo de los parámetros! Este método tiene que devolver los valores traducidos como una matriz, con las claves de los nombres de las propiedades. Así que establece el tipo de retorno en `array`:

[[[ code('bcb1bf9ee0') ]]]

## Traducible "Nombre"

Primero, tenemos que determinar si este objeto es traducible, y si es así, obtener la propiedad `$name` del atributo `Translatable`.

Para ello podemos utilizar el sistema de reflexión de PHP. Crea un `ReflectionClass`para el objeto pasado: `$class = new \ReflectionClass($object)`:

[[[ code('ad63aaf539') ]]]

Ahora tenemos que obtener el atributo: `$type = $class->getAttributes()`. Por defecto, esto devolverá todos los atributos utilizados en esta clase - como sólo queremos atributos`Translatable`, pasa esto como primer argumento: `Translatable::class`.`getAttributes()` devuelve una matriz de objetos `ReflectionAttribute`. Como sólo permitimos un atributo `Translatable` por clase, podemos obtener con seguridad el primer elemento:`[0]`. Puede que esta clase no tenga el atributo, así que utiliza el operador a prueba de nulos, `?->`, y llama a`newInstance()`. Esto instanciará nuestra clase de atributo `Translatable`. Ahora que tenemos el objeto, coge el nombre con `->name`. Debido a la comprobación a prueba de nulos, todo esto podría ser nulo, así que utiliza el operador de coalescencia de nulos, `??`, y llama por defecto a `null`:

[[[ code('ac68c3c065') ]]]

Asegúrate de que tenemos un tipo con `if (!$type)`. Dentro,`throw new \LogicException(sprintf('Class "%s" is not translatable.', $object::class));`:

[[[ code('db23ed3c28') ]]]

## `ManagerRegistry` Servicio

Ahora tenemos que obtener las traducciones de Doctrine. En nuestro constructor, inyecta otro servicio: `private ManagerRegistry $doctrine`:

[[[ code('5db729302f') ]]]

Este es el "servicio Doctrine por excelencia". Puede que estés acostumbrado a utilizar el Gestor de Entidades o em. El `ManagerRegistry` está un nivel por encima. Doctrine puede ser muy complejo: varias bases de datos y varios gestores de entidades u objetos. El `ManagerRegistry` los reúne a todos. Con él, podemos soportar toda esta complejidad.

En el `services.php` de nuestro bundle, tenemos que conectarlo como cuarto argumento.

Sé que el `ManagerRegistry` es autocable, así que utiliza nuestro práctico truco para encontrar el ID del servicio. Salta al terminal y ejecuta:

```terminal
symfony console debug:autowiring ManagerRegistry
```

Allá vamos: `alias:doctrine` - `doctrine` es el ID del servicio - ¡qué corto! Cópialo y vuelve a `services.php`, defínelo como cuarto argumento:`service()`, pégalo:

[[[ code('309d7a1635') ]]]

¡Perfecto!

## Obtener traducciones

Doctrine ya está disponible en nuestro `ObjectTranslator`, así que, abajo en nuestro nuevo método `translationsFor()`, añade `$translations = $this->doctrine`. Para obtener el repositorio de nuestra clase `Translation`, escribe `->getRepository()`. Esta fue la clase que configuramos e inyectamos anteriormente, así que escribe `$this->translationClass`.

Ahora que tenemos el repositorio, comprueba los métodos que tenemos disponibles. ¿Te resulta familiar? `findBy()` es lo que queremos. Pasaremos una matriz de criterios, o filtros. ¿El primero? `'locale' => $locale`. Segundo, el tipo de objeto:`'objectType' => $type`. Por último, el ID del objeto: `'objectId' =>`... hmm... ¿qué deberíamos usar aquí? Si miramos una de las entidades de nuestra app, veremos que todas tienen un método `getId()`. Por ahora, vamos a utilizarlo: `$object->getId()`:

[[[ code('9e164e97d4') ]]]

Asegúrate de que vamos por buen camino con `dd($translations)`, salta al navegador y actualiza.

¡Exactamente lo que esperamos! ¡Un array de dos objetos `Translation`! Pero... esto hay que normalizarlo en una simple matriz de pares clave-valor.

## Normalizar las traducciones

De vuelta a nuestro código, elimina el `dd()`. Para ayudarte con el siguiente paso, encima de `$translations`, añade un comentario docblock con `@var $translations`. Para el tipo, `Translation` - importa el de nuestro bundle. Sufijo con `[]` para indicar que es una matriz de estos objetos:

[[[ code('b286e053d0') ]]]

Crea una nueva variable de matriz para nuestras traducciones normalizadas: `$translationValues = []`:

[[[ code('8fb47fffd4') ]]]

Haz un bucle sobre las entidades de traducción con `foreach ($translations as $translation)`. Dentro, establece el par clave-valor: `$translationValues[$translation->field] = $translation->value`:

[[[ code('ba99e95dc7') ]]]

`dd($translationValues)` para comprobar nuestro trabajo. Vuelve al navegador y actualiza ¡Hermoso! ¡Este es exactamente el formato que necesitamos!

Elimina `dd()` y `return $translationValues`:

[[[ code('6048411f77') ]]]

¡Momento de la verdad! Vuelve al navegador y actualiza la página. ¡Boom! "Título en francés" y "Contenido en francés".

Nuestras traducciones se están obteniendo y mostrando correctamente. Podemos cambiar entre inglés y francés para este artículo. ¡Genial!

Puede que ya lo hayas pensado, pero suponer que la entidad de un usuario tiene un método `getId()` no es una gran idea... Exploremos a continuación una solución más sólida.
