# Configuración del bundle

Tenemos que informar a nuestro bundle de nuestra nueva y brillante entidad `Translation`. La configuración del bundle es la mejor forma de hacerlo.

Seguro que ya has utilizado antes la configuración de bundle. Son los archivos `YAML` dentro del directorio `config/packages/` de tu aplicación. Por ejemplo, `doctrine.yaml`es la configuración del bundle Doctrine.

## Definir la configuración de tu bundle

Podemos definir la configuración de nuestro bundle dentro de nuestra clase bundle Abre`ObjectTranslationBundle.php` en el directorio `src` de tu bundle.

Anula otro método: `configure()`. El método padre también está vacío, así que podemos eliminar esta llamada:

[[[ code('be1876a297') ]]]

Ahora la definición. Escribe`$definition->rootNode()`, éste es el nodo de configuración de nivel superior, que se define como una matriz. Como es una matriz, escribe `->children()` - la definición de la matriz. Para las definiciones, necesitamos llamar siempre a `->end()` para marcarla como finalizada. Hazlo enseguida, para que no se nos olvide.

Dentro, añade nuestro primer nodo: `->stringNode('translation_class')`. En nuestra configuración, ésta será la clave del array. De nuevo, llama a `->end()` para terminarlo:

[[[ code('b26b11a5ee') ]]]

## Listado de configuraciones de bundle disponibles

En tu terminal, lista las configuraciones de bundle disponibles con:

```terminal
symfony console config:dump-reference
```

Sin argumentos. Esto lista todos los bundles cargados, y su clave de configuración (o alias de extensión). `DoctrineBundle`, alias `doctrine`, `DoctrineMigrationsBundle`, alias `doctrine_migrations`. Aquí está nuestro bundle: `ObjectTranslationBundle` alias`object_translation`.

Me gustaría ponerle el prefijo `symfonycasts`, como en el bundle Tailwind que aparece a continuación.

## Cambiar el alias de extensión de tu bundle

He aquí cómo hacerlo. En `ObjectTranslationBundle`, indaga en la clase `AbstractBundle`.`protected string $extensionAlias` lo que necesitamos anular. Por defecto, está vacía y se detecta automáticamente a partir del nombre del bundle (en mayúsculas sin el sufijo `Bundle` ). Copia la propiedad y, de nuevo en nuestra clase bundle, pégala. Cámbiala a `symfonycasts_object_translation`:

[[[ code('0f3c9ee195') ]]]

## Visualizar la configuración de tu bundle

Genial, ahora vuelve a ejecutar el comando:

```terminal-silent
symfony console config:dump-reference
```

Ya está, `symfonycasts_object_translation`. Ejecuta de nuevo el comando, pero esta vez, añádelo como argumento:

```terminal
symfony console config:dump-reference symfonycasts_object_translation
```

Genial, aquí está nuestra configuración por defecto del bundle en YAML. Muestra `translation_class` como null, que es lo que significa esta tilda.

Este resultado es muy útil para nuestros usuarios y es una especie de documentación. Podemos mejorarla aún más.

## Mejorar tu configuración con ejemplos e información

Vuelve a nuestra clase bundle y amplía este `stringNode` para mover el `->end()`a una nueva línea. Aquí es donde podemos añadir configuración adicional para este nodo. Utilizar sangría es muy importante - estas definiciones pueden llegar a ser bastante complejas y grandes.

A veces, PhpStorm pierde la noción de la sangría, así que tienes que pelearte un poco con ella.

Primero, añade `->info()` - es una breve descripción del nodo. Escribe`The class name of your Translation entity`. En una nueva línea, escribe`->example('App\Entity\Translation')` para mostrar un valor de ejemplo:

[[[ code('a525c1572d') ]]]

De vuelta al terminal, ejecuta de nuevo el comando:

```terminal-silent
symfony console config:dump-reference symfonycasts_object_translation
```

¡Genial! Se ha añadido una descripción encima de `translation_class` y nuestro ejemplo a su lado. Están comentados, así que puedes copiarlos/pegarlos fácilmente en tus propios archivos YAML.

A continuación, añadiremos algo de validación para este nodo.
