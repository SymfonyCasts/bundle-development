# Marcadores de posición y pluralización

¡Adelante! Vamos a buscar un escenario de traducción complejo. En la página del artículo, desplázate hacia abajo y encuentra la sección de comentarios. Este encabezado "3 Comentarios" es dinámico, el número cambia en función del número de comentarios.

Encuentra este texto en `templates/article/show.html.twig`. Aquí está el valor dinámico: `comments|length`. Podrías pensar en traducir simplemente "Comentarios" y no preocuparte por la parte dinámica, pero ciertos idiomas pueden tener variaciones alternativas en el orden de las palabras. Como "3 Comments" en inglés frente a "Comments 3" en otro idioma. Por tanto, necesitamos que el texto de traducción incluya la parte dinámica.

¿Cómo lo hacemos? ¡Marcadores de posición de traducción al rescate!

## Marcadores de posición de traducción

Borra todo este texto e inventa una clave: como estamos en `article/show.html.twig`, utiliza `article.show.comments`. Añade `|trans`, y como primer argumento del filtro, añade una matriz. Se trata de una matriz clave-valor, donde la clave es el marcador de posición y el valor es el valor dinámico. Para la clave, utiliza `%count%`. Las claves envueltas en `%`'s son una convención estándar de Symfony. Para el valor, utiliza `comments|length`.

Ahora, abre nuestro archivo `messages.en.yaml` y añade la nueva clave, `article: show: comments:`. Para el valor, utiliza `%count% Comments`. La mayoría de los servicios de traducción saben ignorar el texto en `%`'s, por lo que no deberías preocuparte de que "count" se traduzca accidentalmente.

Vuelve a la aplicación y actualízala. Nada muy emocionante, ¡pero funciona!

Asegurémonos de que cambia dinámicamente. Vuelve a `show.html.twig`, sustituye temporalmente`comments|length` por sólo `1`.

Actualiza la página, y ¡genial! "1 Comentario". Pero, hmm, tal vez no sea el fin del mundo, pero esto debería leerse realmente "1 Comentario" - sin la "s".

## Pluralización

En `messages.en.yaml`, al principio del valor, añade `1 Comment|`.

Esta tubería (`|`) indica a Symfony que tienes varias versiones para la pluralización. Vuelve atrás y actualiza la página, y ahora mostrará correctamente "1 Comentario". Asegúrate de que sigue funcionando con más de un comentario volviendo a `comments|length` en `show.html.twig`.

Actualiza la aplicación, y sí, ¡la pluralización funciona! Symfony sabe que debe utilizar la versión`1 Comment` cuando el recuento es 1, y la versión `%count% Comments` cuando el recuento no es 1.

## Formato de mensaje ICU

Vale la pena señalar que Symfony soporta el MessageFormat oficial de ICU.

Para activarlo, tienes que ajustar los nombres de archivo de tus archivos de traducción para añadir este texto `+intl-icu`.

Los marcadores de posición se manejan de forma un poco diferente. En el texto de traducción, encierra la clave del marcador de posición entre llaves. Luego, cuando pases la clave del marcador de posición, no necesitas envolverla en nada.

También hay un potente sistema de condiciones. Este ejemplo muestra cómo puedes cambiar el mensaje basándote en un marcador de posición `gender`.

La pluralización también es más potente. Puedes utilizar el sistema de condiciones para crear reglas de pluralización realmente precisas.

Es algo que debes comprobar si lo necesitas, pero por ahora nos quedaremos con el sistema básico.

A continuación, veremos cómo tratar el texto de traducción que contiene HTML.
