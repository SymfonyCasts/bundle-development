# `translate_object` Filtro Twig

Ya tenemos nuestro servicio `ObjectTranslator` en pleno funcionamiento. Se puede inyectar y utilizar en código PHP como estamos haciendo en `ArticleController::show()`. Ahora, vamos a animar un poco las cosas y crear un filtro Twig para él.

## Clase de extensión Twig

En `object-translation-bundle/src`, crea un nuevo directorio llamado `Twig`. Dentro, una nueva clase PHP llamada `ObjectTranslatorExtension`.

Márcala como `final` y añade un docblock a nivel de clase. En él, escribe `@internal`. Este es otro truco para que los usuarios sepan que esta clase no está pensada para ser utilizada directamente en sus aplicaciones. Shh... ¡es un secreto!

A continuación, haz que extienda `AbstractExtension` desde Twig:

[[[ code('3be97cff16') ]]]

Ahora, anula un único método:`getFilters()`, y marca el tipo de retorno como `array`. Dentro, devuelve un array con un único elemento: `new TwigFilter()`. El primer argumento es el nombre de nuestro filtro,`translate_object`:

[[[ code('664453d150') ]]]

## Cablear la extensión

¡Es hora de conectar este bebé! En tu bundle `services.php`, debajo de `alias()`, define un nuevo servicio con `->set('symfonycasts.object_translator.twig_extension')`. Ahora, como se trata de un servicio interno que no queremos que vean los usuarios, podemos marcarlo como oculto anteponiendo al ID del servicio `.`. Más adelante te mostraré cómo afecta esto a las cosas.

El segundo argumento es el nombre de la clase: `ObjectTranslatorExtension::class`:

[[[ code('bc092d5fc8') ]]]

Fíjate, PhpStorm formatea el nombre de la clase con un tachado. Esto se debe a la bandera `@internal`. Detecta que estamos intentando utilizarla fuera del directorio `src` de nuestro bundle. En este caso, es un falso positivo: realmente necesitamos utilizarlo aquí. Pero en la aplicación de un usuario, es de esperar que este tachado le disuada de utilizarlo directamente.

Por último, etiquétalo como `twig.extension` con `->tag('twig.extension')`:

[[[ code('35d9edefc4') ]]]

## Código de extensión

Vuelve a `ObjectTranslatorExtension`. Este filtro Twig aún no está haciendo nada. El segundo argumento de `TwigFilter` es un callable que hace el trabajo. Podríamos inyectar nuestro `ObjectTranslator` en un constructor y llamar aquí a su método `translate()` pero... no es la mejor idea. Todas las extensiones de Twig se cargan cada vez que se carga Twig. Incluso si no estamos utilizando este filtro, se instanciaría este objeto y también nuestro `ObjectTranslator`. Queremos evitar eso y hacerlo perezoso. ¡Twig Runtime al rescate!

## Tiempo de ejecución Twig

Para el segundo argumento, después de `translate_object`, crea una matriz:`[ObjectTranslator::class, 'translate']`:

[[[ code('48daacf147') ]]]

No es una verdadera llamada, ya que`translate()` no es estática. Pero Twig entiende que `ObjectTranslator` puede ser un runtime y cargará perezosamente esta clase y llamará a `translate()` en ella cuando se utilice el filtro.

Sólo tenemos que marcar `ObjectTranslator` como tiempo de ejecución Twig. En `services.php`, bajo el primer servicio, nuestro traductor de objetos, añade una etiqueta: `->tag('twig.runtime')`:

[[[ code('9bfef3af7d') ]]]

Ahora bien, si has escrito tiempos de ejecución Twig antes, puede que hayas creado una clase dedicada para ello, ¡pero esto no es estrictamente necesario! Puedes hacer que cualquier servicio sea un tiempo de ejecución con esta etiqueta. Luego, en tus extensiones, puedes referenciarlo como hemos hecho aquí.

## Utilizar nuestro filtro Twig

¡Vamos a probarlo! Nuestra página de presentación de artículos ya está traduciendo bien el objeto, así que vamos a utilizar el filtro en el índice, donde listamos todos los artículos. Abre `templates/article/index.html.twig`.

Dentro del bucle de artículos, en la parte superior, sustituye `article` por la versión traducida: `{% set article = article|translate_object %}`:

[[[ code('ed58b7aa9c') ]]]

Salta al navegador y actualiza. Estamos en la página de inicio en inglés, así que cambia al francés...

¡Ya está! El título y el contenido del primer artículo están en francés. ¡Nuestro filtro Twig funciona! Por supuesto, los demás artículos siguen en inglés, ya que aún no hemos configurado traducciones para ellos.

## Comprender los servicios ocultos

Vamos a dar un pequeño rodeo para entender cómo funcionan los servicios ocultos, los que llevan el prefijo `.`. En tu terminal, ejecuta:

```terminal
symfony console debug:container symfonycasts
```

Vale, vemos dos servicios, `symfonycasts.object_translator` y `ObjectTranslator` como clase -este es nuestro alias-. No vemos nuestro servicio de extensión Twig porque está oculto. Oculto no significa "que no esté", ni siquiera "que no se pueda utilizar", simplemente están excluidos por defecto al listar los servicios. Ejecuta de nuevo el comando, pero añade `--show-hidden`:

```terminal-silent
symfony console debug:container symfonycasts --show-hidden
```

¡Ya está! La primera entrada es nuestro servicio oculto de extensión Twig.

A continuación, vamos a trabajar en algunas optimizaciones de rendimiento para mantener las consultas a la base de datos bajo control.
