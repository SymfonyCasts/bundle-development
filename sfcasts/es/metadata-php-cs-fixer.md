# Arreglador de metadatos y PHP CS

La codificación del bundle está terminada, y las pruebas están pasando en todas nuestras versiones de Symfony compatibles. ¡Estamos en la recta final!

## Archivo de licencia

Vamos a añadir un archivo de licencia a nuestro bundle. En el directorio `tutorial` copia el archivo`LICENSE.md` a la raíz de nuestro bundle. Si no ves los archivos que estamos copiando en tu directorio tutorial, ¡no te preocupes! Están todos en el script de abajo.

Esta es la licencia MIT estándar para que coincida con la que tenemos en nuestro archivo `composer.json`.

## Documentación

Ahora, copia el archivo `README.md` del directorio `tutorial` a la raíz de nuestro bundle. Ahora bien, la guía de buenas prácticas de Symfony Bundle recomienda un directorio `doc`para albergar la documentación y que ésta se escriba en formato "reStructuredText" (o `rst`).

Yo me alejo de esta recomendación por un par de razones. En primer lugar, `rst` no se muestra tan bien en GitHub como los archivos Markdown. En segundo lugar, a menos que la documentación empiece a ser realmente grande, me gusta que se muestre inmediatamente cuando alguien visita el repositorio de GitHub. Por tanto, prefiero que la documentación esté en el archivo README.

Hagamos un repaso rápido de lo que he escrito. Comienza con las instrucciones de instalación y cómo activar y configurar el bundle. A continuación, cómo marcar tus entidades como traducibles...

Una sección de uso para mostrar cómo utilizar el servicio `ObjectTranslator` y el filtro Twig `translate_object`.

La sección de gestión de traducciones ofrece detalles sobre la estructura de la base de datos y cómo utilizar los comandos de exportación e importación.

A continuación, algo de información sobre el sistema de caché, incluido el comando warmup.

Por último, me gusta incluir la configuración por defecto completa, ¡porque es muy fácil de generar! ¿Te acuerdas?

En el terminal, asegúrate de que estás en la raíz de nuestra aplicación, no en el bundle. Luego ejecuta:

```terminal
symfony console config:dump-reference symfonycasts_object_translation
```

Me suena. Siempre que cambies la configuración, puedes actualizar fácilmente esta sección volviendo a ejecutar ese comando y copiando/pegando el resultado. ¡Configuración autodocumentada para ganar!

## `.editorconfig` Archivo

A continuación, copia el archivo `.editorconfig` de la raíz de nuestro proyecto al bundle. Este archivo garantiza la coherencia en aspectos como el espaciado y las nuevas líneas. Muchos IDEs, incluido PhpStorm, admiten este archivo. Ayuda a evitar confirmaciones extrañas con diferentes espacios en blanco y caracteres de salto de línea. Esto es especialmente útil cuando alguien está desarrollando en Windows, que utiliza diferentes terminaciones de línea que macOS o Linux.

## `.gitattributes` Archivo

En la raíz de nuestro bundle, crea un nuevo archivo llamado `.gitattributes`. Dentro, añade `/tests export-ignore`. Esto indica a Composer que excluya el directorio `tests`al instalar este paquete como dependencia. No es necesario que las pruebas de nuestro bundle se incluyan en el proyecto de un usuario final.

## Reparador PHP CS

Al igual que la coherencia que proporciona el archivo `.editorconfig` para los espacios en blanco y los saltos de línea, también es importante tener un estilo de codificación coherente. Se trata de cosas como el espacio entre el espacio de nombres y las declaraciones de uso, o dónde colocar las llaves. Un estilo de codificación coherente hace que tu código sea más fácil de leer y ayuda a los colaboradores.

Una gran herramienta para reforzar y automatizar esto es PHP CS Fixer. En el terminal, asegúrate de que estás en el directorio bundle y ejecuta:

```terminal
symfony composer require --dev php-cs-fixer/shim
```

Si ya has utilizado antes esta herramienta pero este paquete `shim` es nuevo para ti, no es más que una versión compilada de PHP CS Fixer que facilita la instalación.

Una vez instalado, coge el archivo `.php-cs-fixer.dist.php` del directorio del tutorial y cópialo en la raíz de nuestro `object-translation-bundle`. Este archivo configura las reglas de estilo de codificación para tu proyecto. Ábrelo y echa un vistazo.

Estamos utilizando el conjunto de reglas de Symfony, por lo que nuestro estilo de codificación coincidirá con el de Symfony. A continuación, le indicamos dónde buscar los archivos PHP que hay que corregir: los directorios `src` y `tests`.

Para ejecutarlo, en el terminal, ejecuta:

```terminal
symfony php vendor/bin/php-cs-fixer fix -v
```

Genial, aquí están todos los archivos que se modificaron y las reglas que se aplicaron.

El primero, `TranslatedObject` aplicó la regla `phpdoc_align`. Abramos ese archivo para ver qué cambió. Ah, añadió espaciado para alinear los nombres de las variables `@param`. En general, son más fáciles de leer cuando están alineados.

Este archivo `.php-cs-fixer.cache` de la raíz de nuestro bundle se generó cuando lo ejecutamos. Es sólo una caché para que las siguientes ejecuciones de PHP CS Fixer sean más rápidas. Añádelo a nuestro archivo `.gitignore` para que no se confirme.

A continuación, vamos a utilizar PHPStan para ejecutar un análisis estático del código de nuestro bundle
