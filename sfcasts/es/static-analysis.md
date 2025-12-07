# Análisis estático con PHPStan

Mientras que las pruebas verifican que tu código funciona según lo esperado ejecutándolo, el análisis estático examina tu código sin ejecutarlo. Esto ayuda a detectar posibles problemas que las pruebas podrían pasar por alto. Es una forma estupenda de mejorar la calidad y la capacidad de mantenimiento del código. Y no sólo eso, un beneficio secundario es que las mejoras sugeridas a menudo conducen a un mejor autocompletado en IDEs como PhpStorm.

Mi herramienta preferida para el análisis estático es PHPStan.

## Instalación y configuración de PHPStan

En tu terminal, mientras estás en tu directorio bundle, instálalo ejecutando:

```terminal
symfony composer require --dev phpstan/phpstan
```

De vuelta a nuestro IDE, añade un archivo `phpstan.neon` a la raíz de tu bundle:

[[[ code('09ac8e48d1') ]]]

Cópialo del directorio `tutorial` o del script que aparece a continuación. El formato del archivo `neon` es muy similar al de `yaml`.

Echemos un vistazo a esta configuración. En esta sección `parameters` es donde configuramos cómo queremos que funcione PHPStan. PHPStan funciona en diferentes niveles, de cero a diez. Cero es el más permisivo y diez el más estricto.

Elegir un nivel es un acto de equilibrio. Un nivel más bajo significa menos problemas notificados, pero puede pasar por alto algunos problemas potenciales. Un nivel más alto detecta más problemas, pero también puede resultar abrumador el número de problemas notificados.

Una buena práctica consiste en empezar con un nivel bajo y aumentarlo gradualmente a medida que resuelves los problemas notificados. De este modo, puedes mejorar la calidad de tu código de forma incremental sin sentirte abrumado.

Yo he empezado con un nivel moderado de 5. El `path` es un conjunto de rutas que queremos que PHPStan analice. Para empezar, nos centraremos en el directorio `src`. Analizar `tests` también puede ser beneficioso, pero por simplicidad, nos quedaremos sólo con `src`por ahora.

## Ejecutar el análisis PHPStan

¡Es hora de ejecutarlo! En tu terminal, ejecuta:

```terminal
symfony php vendor/bin/phpstan analyse
```

¡Muy bien! PHPStan está analizando los archivos del directorio `src` de nuestro bundle. Parece que tenemos tres errores que solucionar. Vamos a tratarlos uno a uno

## Error de método no encontrado

El primero, en `ObjectTranslationBundle.php` en la línea 19, tiene el mensaje "Call to undefined method `NodeDefinition::children()`".

Bien, vayamos a ese archivo y busquemos la línea 19. Aquí está pero... hmm... la línea 19 está llamando a `->rootNode()`, es la línea 20 la que está llamando a `->children()`. Eso es porque, desde la perspectiva de PHPStan, toda esta cadena de llamadas a métodos se considera una línea y los errores se notifican en la primera línea de la cadena.

Este error es en realidad un falso positivo. A PHPStan le cuesta entender estos árboles de configuración profundamente encadenados. Sabemos que funciona como se espera porque nuestra prueba fallaría al construir el contenedor si hubiera un problema real.

Este error puede ignorarse con seguridad. Podríamos ignorar toda la línea, pero es mejor ignorar sólo la clave específica del error para que, si surge otro error más adelante, no se ignore también. Puedes encontrar esta clave en la salida del terminal, debajo del mensaje de error: `method.notFound`. Cópiala. De vuelta a la línea 19 de`ObjectTranslationBundle.php`, añade a la línea un comentario `@phpstan-ignore <paste>`:

[[[ code('362564df6d') ]]]

## Error de clase desconocida

De nuevo en el terminal, ejecuta de nuevo el análisis:

```terminal-silent
symfony php vendor/bin/phpstan analyse
```

¡Ahora sólo dos errores! En las líneas 12 y 17 de nuestra clase `ObjectTranslatorExtension`, la extensión Twig. Busca y abre esa clase.

PHPStan dice que esta clase `AbstractExtension` no existe. Sin embargo, según PhpStorm, sí existe... Esto se debe a que estamos trabajando en este bundle dentro de un proyecto Symfony completo que tiene Twig instalado. Cuando ejecutamos PHPStan en el contexto de nuestro bundle, esta clase no existe.

Esto tiene fácil solución, sólo necesitamos que nuestro bundle dependa de Twig. Pero no quiero que Twig sea una dependencia dura. Puede que un usuario esté utilizando nuestro bundle en un proyecto de sólo API sin Twig. No quiero que tengan que instalar Twig sólo por nuestro bundle. Por tanto, necesitaremos Twig como dependencia de desarrollo.

En tu terminal, añádelo con:

```terminal
symfony composer require --dev twig/twig
```

Ahora, vuelve a ejecutar el análisis:

```terminal-silent
symfony php vendor/bin/phpstan analyse
```

¡No hay errores! Al añadir Twig se han solucionado los dos errores restantes, ya que ambos estaban relacionados con la falta de clases Twig.

A continuación, vamos a subir nuestro bundle a GitHub y a configurar la integración continua con las Acciones de GitHub.
