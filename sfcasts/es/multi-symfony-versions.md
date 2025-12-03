# Pruebas con varias versiones de Symfony

Es hora de concretar las versiones de PHP y de las dependencias de paquetes que admite nuestro bundle.

## Añadir requisito de versión de PHP

Si miramos el archivo `composer.json` de nuestro bundle, lo primero que quiero hacer es establecer la versión de PHP permitida. En la sección `require`, añade`"php": ">=8.2"`:

[[[ code('b2e362cf4e') ]]]

Esto significa que requerimos PHP 8.2 o una versión más reciente.

A algunas personas no les gusta esto porque es un poco abierto, estamos diciendo que soportaremos PHP 10 cuando salga. Lo entiendo, pero yo personalmente sigo el ejemplo de Symfony, y ellos utilizan esta restricción para PHP.

## Ajustar las versiones de los paquetes Symfony

Para las dependencias de paquetes Symfony, las versiones permitidas son bastante restrictivas. `^7.3` significa cualquier cosa desde 7.3 hasta, pero sin incluir, 8.0. Symfony 6.4 aún tiene soporte durante bastante tiempo, así que me gustaría dar soporte también a esa versión.

Cambia las dependencias de Symfony a `^6.4|^7.0`:

[[[ code('b0f64c9928') ]]]

Esto significa básicamente que estamos permitiendo versiones superiores a la 6.4 pero inferiores a la 8.

En `require-dev`, tenemos el `symfony/phpunit-bridge`. Es bastante indulgente con sus requisitos, por lo que no necesitamos cambiar realmente nada aquí.

## Archivo Bootstrap de prueba

¿Recuerdas cómo se crea este directorio `var` cuando ejecutamos nuestras pruebas? Si cambias entre diferentes versiones de PHP o Symfony localmente, puede crear problemas. Si ejecutas tu conjunto de pruebas en Symfony 6.4, y luego cambias a 7.3, los archivos de caché en el directorio `var` no serán compatibles.

Es mejor borrar ese directorio antes de ejecutar las pruebas. En nuestro directorio `tests`, crea un nuevo archivo llamado `bootstrap.php`. Dentro,`require __DIR__ . '/../vendor/autoload.php';` para cargar el cargador automático de Composer:

[[[ code('03577eec03') ]]]

A continuación, escribe `(new Filesystem())`, importa el del componente Symfony Filesystem. A continuación, llama a `->remove(__DIR__ . '/../var');` para eliminar ese directorio:

[[[ code('24d59aab0f') ]]]

Ahora tenemos que decirle a PHPUnit que utilice este archivo antes de ejecutar nuestras pruebas. Abre nuestro archivo `phpunit.xml.dist` y busca la etiqueta`<phpunit>`. ¿Ves el atributo `bootstrap`? Actualmente está configurado como`vendor/autoload.php`. Cámbialo a `tests/bootstrap.php`:

[[[ code('b202d72750') ]]]

Básicamente estamos envolviendo el autocargador de Composer con nuestro propio archivo bootstrap.

Para comprobar que esto funciona correctamente, salta al terminal. Asegúrate de que estás en el directorio `object-translation-bundle` y ejecuta:

```terminal
symfony php vendor/bin/phpunit
```

¡Excelente! Nuestras pruebas siguen pasando.

## Preferir lo más bajo

Una variación de dependencia importante que hay que probar es la versión más baja posible que admita nuestro bundle. Esto ayuda a garantizar que nuestro código es compatible con las versiones más antiguas de nuestras dependencias. En nuestro archivo `composer.json`, para los paquetes Symfony, esto significa `6.4.0`. Para el `doctrine-bundle`, sería `2.18.0`.

Para obtener estas versiones más bajas instaladas, en el terminal, ejecuta:

```terminal
symfony composer update --prefer-lowest
```

Podemos ver que Composer ha cambiado a las versiones más bajas.

## Resolución de problemas de compatibilidad

Bien, ahora vamos a ejecutar de nuevo nuestro conjunto de pruebas para ver si todo sigue funcionando.

```terminal-silent
symfony php vendor/bin/phpunit
```

Vaya, tenemos un error... Desplázate hacia arriba para ver el mensaje "Llamada a método indefinido `NodeBuilder::stringNode()`... En `ObjectTranslationBundle`, línea 21".

Abre este archivo y busca la línea 21. Ah, aquí está. El método `stringNode()` se introdujo en Symfony 7.2. Como tenemos instalada la 6.4, este método no existe. Si te preguntas cómo lo he averiguado, fui al repositorio de Symfony en GitHub y busqué `stringNode`. Encontré el PR que lo introdujo, que mencionaba que se había añadido en la 7.2.

Sustituye todas las instancias de `stringNode` por `scalarNode`, que se admite en 6.4 y hace lo mismo en este caso:

[[[ code('a16222195b') ]]]

De nuevo en el terminal, ejecuta de nuevo las pruebas...

```terminal-silent
symfony php vendor/bin/phpunit
```

Genial, todas las pruebas pasan: ¡el código que cubren nuestras pruebas es compatible con las versiones más bajas de nuestras dependencias!

## Instalar versiones de desarrollo

Para volver a la última versión de nuestras dependencias, ejecuta:

```terminal
symfony composer update
```

Podemos ver que está instalando las versiones 7.3 de los paquetes Symfony. Ahora bien, sé que Symfony 7.4 se lanzará pronto, pero aún no se considera estable.

Para adelantarme, quiero ejecutar nuestras pruebas también en 7.4. Esta es una buena forma de ayudar a la comunidad Symfony. Si encuentras problemas, puedes informar de ellos al equipo de Symfony antes del lanzamiento final.

Abre nuestro archivo `composer.json`. Por defecto, Composer sólo instala versiones estables. Para permitir la instalación de versiones de desarrollo, añade la siguiente opción:`"minimum-stability": "dev"`. Esto instalará siempre las dependencias de desarrollo, cosa que no queremos. Entonces, añade otra opción: `"prefer-stable": true`:

[[[ code('c82e5db917') ]]]

Ahora, Composer preferirá las versiones estables, pero puede instalar versiones dev si una restricción lo requiere.

De vuelta al terminal, si volvemos a ejecutar la actualización, no obtendremos ninguna versión dev gracias a la opción `prefer-stable`.

## Instalar Symfony Flex globalmente

Hay un pequeño truco oculto de Symfony Flex para ayudar a instalar diferentes versiones de Symfony. Necesitamos instalar Symfony Flex globalmente en nuestro sistema, así que ejecuta:

```terminal
symfony composer global require symfony/flex
```

Ahora, Symfony Flex es visible para todos los comandos de Composer.

## Probar una versión de desarrollo de Symfony

Ahora, para instalar las versiones 7.4, ejecuta:

```terminal
SYMFONY_REQUIRE=7.4.* symfony composer update
```

¡Genial! Estamos recibiendo las versiones candidatas 7.4, que no se consideran estables.

Flex recoge esta variable de entorno y ajusta las restricciones de la versión del paquete Symfony en consecuencia.

Observa este mensaje "Las recetas Symfony están deshabilitadas: symfony/flex no se encuentra en el composer.json raíz". Como nuestro bundle no requiere `symfony/flex`directamente, la magia normal de Flex a la que estás acostumbrado está desactivada.

Ejecuta de nuevo el conjunto de pruebas:

```terminal-silent
symfony php vendor/bin/phpunit
```

Bien, ¡todas nuestras pruebas pasan en 7.4!

## Probar una versión alternativa de Symfony

Para probar otra versión de Symfony compatible, por ejemplo la 7.2, ejecuta el comando composer update con la variable de entorno `SYMFONY_REQUIRE` establecida en `7.2.*`:

```terminal-silent
SYMFONY_REQUIRE=7.2.* symfony composer update
```

Sí, tenemos instaladas las versiones 7.2, así que ejecuta de nuevo el conjunto de pruebas:

```terminal-silent
symfony php vendor/bin/phpunit
```

¡Genial, todo en verde en la 7.2!

## `composer.lock` Consideraciones

Al principio mencioné que, a diferencia de nuestras aplicaciones, en los paquetes de terceros que necesitan admitir una amplia gama de versiones de dependencias, no confirmamos el archivo `composer.lock`.

Cada vez que instalas versiones diferentes, se actualiza el archivo de bloqueo. Confirmarlo crea conflictos y otros quebraderos de cabeza.

A continuación, añadiremos documentación y otros metadatos a nuestro bundle.
