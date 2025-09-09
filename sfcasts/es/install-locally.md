# Instalar nuestro bundle localmente

Tenemos un "paquete" de bundle y una clase de bundle, y aunque vive dentro de nuestra aplicación, tenemos que "instalarlo". Esto se gestiona con Composer de forma normal, pero el método es un poco diferente. Aún no existe en Packagist ni en GitHub, así que tenemos que decirle a Composer: "¡Eh, nuestro paquete bundle existe en esta carpeta específica!"

Abre el archivo `composer.json` de nuestra app y añade una nueva clave de nivel superior llamada "repositorios". Composer tiene un "repositorio" por defecto: Packagist. Aquí es donde busca los paquetes. Pero también podemos añadir nuestros propios repositorios personalizados.

Esto tiene que ser un array, dentro, un objeto json. La primera clave es "tipo" - qué tipo de repositorio. En nuestro caso, es un repositorio "path" (o local). A continuación, "url" - para los repositorios "path", es la ruta relativa a la carpeta de nuestro paquete. Utiliza "object-translation-bundle", ya que está en la raíz de nuestra aplicación:

[[[ code('1ce160487d') ]]]

## Instalar el bundle

Ahora podemos instalar nuestro paquete bundle, así que dirígete a tu terminal y ejecuta:

```terminal
composer require symfonycasts/object-translation-bundle
```

Nos aparece un error por no encontrar una versión "estable" de nuestro paquete. En realidad, esto tiene sentido, ya que no hemos publicado ninguna versión y nuestra aplicación está configurada para instalar sólo versiones estables de los paquetes por defecto.

Podemos evitarlo permitiendo explícitamente una versión "dev" de este paquete. Ejecuta de nuevo el comando, pero añadiendo `:@dev` al final del nombre del paquete:

```terminal-silent
composer require symfonycasts/object-translation-bundle:@dev
```

¡Genial! Ha funcionado. Lo bueno de los repositorios "ruta" es que están enlazados simbólicamente, por lo que los cambios que hagas en el bundle se reflejarán inmediatamente en tu aplicación. ¡Perfecto para el desarrollo local!

## Receta Auto-Flex

Mira esto - la salida nos está diciendo que Symfony Flex instaló una receta para esto? ¿Cómo? No hemos creado una receta...

Ejecuta::

```terminal
git status
```

para ver qué pasa. `composer.json` y `composer.lock` se modificaron - eso es lo esperado... pero mira: ¿ `config/bundles.php` también se modificó...?

Ábrelo en tu editor. ¡Increíble! ¡Ha añadido automáticamente nuestra clase bundle!

[[[ code('d45eadafd2') ]]]

¿Recuerdas cuando creamos el archivo `composer.json` de nuestro bundle? ¿Pusimos el "tipo" a "symfony-bundle"? Esto le dijo a Symfony Flex: "¡Eh, esto es un bundle Symfony! Busca una clase bundle y cárgala automáticamente"

Sin necesidad de una verdadera receta, cada vez que alguien instale este paquete, el bundle se añadirá automáticamente a tu aplicación.

Muy bonito, ¿eh?

Ya tenemos un bundle vacío instalado y cargado en nuestra app. A continuación, ¡vamos a hacer que nuestro bundle proporcione un servicio Symfony!
