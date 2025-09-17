# Configurar la entidad de nuestro bundle

Hemos creado la clase abstracta `Translation` en nuestro bundle. Ahora tenemos que informar a Doctrine sobre ella para que podamos extenderla y utilizarla en nuestra aplicación como una entidad real.

Primero, ve a tu terminal y ejecuta:

```terminal
symfony console doctrine:mapping:info
```

Esto muestra todas las entidades que Doctrine conoce. Las entidades `Category`,`Tag`, y `Article` de nuestra app, es todo lo que vemos por ahora. Este comando también muestra las superclases mapeadas, así que tenemos que hacer algo para que nuestra clase `Translation` aparezca aquí.

Probablemente estés acostumbrado a utilizar atributos de mapeo para tus entidades Doctrine, pero en los bundles es un poco diferente. La recomendación oficial es utilizar XML para el mapeo. Lo sé, lo sé, XML no es la cosa más divertida con la que trabajar, pero proporciona la mayor flexibilidad.

## Crear el archivo XML

Pongámonos manos a la obra. En el directorio `config` de tu bundle, crea un nuevo directorio y un subdirectorio: `doctrine/mapping`. Para ahorrarte la molestia de verme esforzarme en escribir el XML, en el directorio `tutorial` hay un archivo`Translation.orm.xml`. Coge ese archivo y muévelo a nuestro nuevo directorio`doctrine/mapping`.

Fíjate en el sufijo `.orm.xml`. Esto es importante, ya que indica a Doctrine que se trata de información de mapeo para el ORM. En el futuro, si añadimos soporte para MongoDB, crearemos un archivo `Translation.mongodb.xml` con la asignación adecuada para ello.

## Comprender el archivo XML

Abre `Translation.orm.xml` en tu editor:

[[[ code('d3446f1a4f') ]]]

Qué asco... vamos a desempaquetar esto. En primer lugar, estamos declarando un elemento `doctrine-mapping` de nivel superior con algunas cosas del espacio de nombres XML. Dentro, un elemento `mapped-superclass` con un atributo`name` para el nombre completo de la clase `Translation` de nuestro bundle.

Dentro de éste, están nuestras asignaciones `field`. El atributo `name` corresponde al nombre de la propiedad de nuestra clase `Translation`, y el atributo `column` es el nombre de la columna que queremos utilizar en la base de datos. Si se omite, Doctrine utilizará el nombre de la propiedad como nombre de la columna. He entrecomillado los nombres de las columnas`objectType` y `objectId` para seguir las convenciones habituales de nomenclatura de las bases de datos.

El atributo `type` indica el tipo de Doctrine para la columna. Utilizaremos`string` para todas excepto para `value`, que es `text`. Las columnas `string` tienen una longitud máxima, que suele rondar los 200-250 caracteres, dependiendo de la plataforma y la configuración de la base de datos. Esto está bien para la mayoría de nuestros campos, pero la columna `value` necesita contener más. El tipo `text` puede contener cadenas mucho mayores, por lo que es perfecto para nuestros valores traducidos.

## Cargar la asignación XML

No basta con crear el archivo XML, tenemos que hacer que Doctrine lo conozca. En `ObjectTranslationBundle`, anula el método `build()`y añade el tipo de retorno `void`. Esta llamada al método padre puede eliminarse, ya que está vacía.

[[[ code('1de87770eb') ]]]

`loadExtension()` es donde cargamos y configuramos las cosas para este bundle. `build()`
se llama más adelante en el proceso, después de que se hayan registrado todos los demás bundles. Esto nos permite modificar el contenedor de servicios después de que todos los demás bundles hayan tenido la oportunidad de registrar sus servicios. Aquí podemos informar a Doctrine de las asignaciones de nuestro bundle.

Lo haremos con un pase del compilador, una especie de gancho de compilación que tiene acceso al contenedor de servicios completamente compilado. Escribe `$container->addCompilerPass()`.

Doctrine proporciona un pase de compilador específico para cargar asignaciones. Escribe`DoctrineOrmMappingsPass` e importa esta clase del bundle Doctrine. Aquí podemos ver los métodos estáticos para crear mapeos. Elige `createXmlMappingDriver()`.

El primer argumento es un array. Las claves son los directorios donde se encuentran los archivos de mapeo, y los valores son los espacios de nombres que representan estos archivos de mapeo. Sólo necesitamos uno.

Para la clave, utiliza `__DIR__.'/../config/doctrine/mapping'` - es la ruta relativa a nuestro directorio de mapeo. Para el valor, salta a la clase `Translation` y copia su espacio de nombres `SymfonyCasts\ObjectTranslationBundle\Model`. Vuelve a `ObjectTranslationBundle` y pégalo como valor:

[[[ code('62ebf10ae2') ]]]

¡Ya está!

Salta a tu terminal y ejecuta de nuevo el comando `doctrine:mapping:info`:

```terminal-silent
symfony console doctrine:mapping:info
```

¡Genial! Ahora podemos ver nuestra superclase mapeada `Translation` en la lista.

A continuación, crearemos la entidad `Translation` real en nuestra aplicación
