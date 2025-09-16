# Validación de la configuración del bundle

Ya tenemos esta opción de configuración `translation_class` en nuestro bundle. Necesita cierta validación: primero, debe ser obligatoria; segundo, debe ser una cadena no vacía; y tercero, debe ser una clase válida que extienda la clase `Translation` de nuestro bundle.

Como hemos utilizado `stringNode()`, ya tenemos una validación básica: debe ser una cadena. Añadamos el resto.

## Obligatorio y no vacío

Debajo de nuestra llamada a `->example()`, añade una nueva línea y una sangría. Añade `->isRequired()`. Esto asegura que el usuario lo establece, pero queremos evitar que lo establezca como nulo o como una cadena vacía. Así que añade también `->cannotBeEmpty()`:

[[[ code('5b4c33db0f') ]]]

En tu terminal, vuelca la configuración del bundle:

```terminal
symfony console config:dump-reference symfonycasts_object_translation
```

Ooo, ¡un error! "translation_class"... debe estar configurada. Incluso muestra la descripción de nuestro nodo para ayudarnos. ¡Perfecto!

Esto nos obliga ahora a crear esta configuración. En el directorio `config/packages`de tu aplicación, crea un nuevo archivo llamado `symfonycasts_object_translation.yaml`.

Dentro, añade el nombre del nodo de nivel superior, el alias de extensión de nuestro bundle:`symfonycasts_object_translation:`. A continuación, debajo, indenta y añade nuestro nodo: `translation_class`. Establécelo como una cadena vacía por ahora:

[[[ code('fe5353cac7') ]]]

Ejecuta de nuevo el comando en tu terminal:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

Un error diferente: "translation_class" no puede contener un valor vacío. Esto se debe a la opción `cannotBeEmpty()`.

Así que, volviendo a nuestra configuración, establece `translation_class` en sólo `Translation`:

[[[ code('6fa078e34c') ]]]

Vuelve a ejecutar el comando:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

¡Woo! Todo correcto. Incluso ha añadido "Obligatorio" antes del comentario de ejemplo.

## Validación personalizada

Para nuestro tercer requisito: "un nombre de clase válido que extienda la clase `Translation`de nuestro bundle", necesitaremos una validación personalizada.

En la definición `translation_class` de nuestro bundle, después de `cannotBeEmpty()`, añade una nueva línea, haz sangría y añade `->validate()`. Esto inicia una cadena de validación personalizada que también debe cerrarse con `->end()`. Dentro, añade `->ifTrue()` con una función: `fn($v) => !class_exists($v)`. Esta función se ejecuta con el valor proporcionado por el usuario, `$v`. Si la función devuelve `true`, la validación falla. En nuestro caso, si la clase no existe. Ahora, necesitamos lanzar un mensaje de error. Añade`->thenInvalid('The translation class %s does not exist.')`. El `%s` será el valor proporcionado por el usuario.

[[[ code('f4c4166e1a') ]]]

¡Vamos a probarlo! De nuevo en tu terminal, ejecuta de nuevo el comando:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

¡Genial! Nuestro error personalizado: "La clase de traducción Traducción no existe"

En nuestra configuración, seamos descarados y fijémosla en una clase real, pero no en una clase de traducción válida: `App\Entity\Article`:

[[[ code('63146288b6') ]]]

Ejecuta de nuevo el comando...

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

## Mejorar la validación personalizada

Esto pasa nuestra validación, pero sigue sin ser lo que queremos: `Article` no extiende la clase `Translation` de nuestro bundle.

De vuelta a nuestra configuración, podríamos encadenar otra validación después de la primera, pero vamos a simplificarlo. Cambia `class_exists` por `is_a`. Para el segundo argumento, añade `Translation::class` -asegúrate de importar el de nuestro bundle.`is_a` comprueba si un objeto es una instancia de una clase string. Por defecto, `$v`debe ser un objeto real, así que pasa `true` como tercer argumento para permitir que`$v` sea una cadena de clase:

[[[ code('2077b03dd2') ]]]

Ejecuta de nuevo el comando:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

¡Error! "La clase de traducción App\Entity\Article no existe". Hmm, tenemos que actualizar el mensaje de error. De vuelta a nuestra configuración, ajusta el mensaje`thenInvalid()` para que diga "...debe extender SymfonyCasts\ObjectTranslationBundle\Model\Translation"

[[[ code('a6df6135b0') ]]]

Ejecuta de nuevo... "La clase de traducción App\Entity\Article sí debe extender". ¡Qué mala gramática! Elimina el "debe" e inténtalo de nuevo. Perfecto "La clase de traducción App\Entidad\Artículo debe extender..." ¡Mucho mejor!

Arréglalo en nuestra configuración cambiando `Article` por `Translation`:

[[[ code('5222a3264c') ]]]

Ejecuta de nuevo el comando... ¡todo bien!

A continuación, utilizaremos este valor de configuración en el servicio de nuestro bundle
