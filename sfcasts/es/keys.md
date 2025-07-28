# Traducción "Claves

¡Ya casi estamos listos para empezar a traducir este sitio! Pero antes de empezar, quiero mostrarte un pequeño problemilla con nuestra configuración hasta ahora.

Todavía tenemos este texto "¡Hola Mundo!" así que salta a `templates/article/index.html.twig`donde lo estamos traduciendo. Recuerda que tenemos la traducción al francés de esta configuración en `messages.fr.yaml`...

Supongamos que ya no queremos "gritar" esto, así que elimina el signo de exclamación:

[[[ code('04d63b07e7') ]]]

Prefiero gritar esto, pero a los mandamases no les gusta.

¿Ves el problema?

Ve a la página principal en inglés y actualízala. Vale, vemos "Hello World" sin el signo de exclamación. Pero si pasamos al francés... ¡hemos roto la traducción, vuelve al inglés!

Por supuesto, la solución podría ser actualizar la clave en `messages.fr.yaml` para que coincida con la versión inglesa... Pero, es un poco molesto que un pequeño cambio de puntuación pueda romper nuestras traducciones.

## Adoptar las mejores prácticas con las claves de traducción

Una práctica habitual es utilizar claves de traducción en lugar del texto real. Son algo así como claves de caché para tus traducciones. Así, en lugar de `Hello World` en `index.html.twig`, utiliza una versión con clave: `hello_world`. Pero... si ahora actualizas la página en inglés, sólo mostrará este texto con clave.

Necesitamos un nuevo archivo de traducciones para el inglés. En la carpeta `translations/`, crea un nuevo archivo`messages.en.yaml`, y dentro, añade `hello_world: "Hello World"`:

[[[ code('7700ddbf8f') ]]]

Si ahora actualizas la página, vuelve a funcionar.

En la página en francés, está volviendo al inglés. Arréglalo abriendo `messages.fr.yaml`, cambia la clave a `hello_world`:

[[[ code('33d8ce5e38') ]]]

Vuelve a nuestra aplicación y actualiza. Ahora ya funciona.

Hay que admitir que el uso de claves supone un poco más de trabajo, pero a la larga compensa.

Limpia nuestro código de pruebas eliminando todo lo que haya en `messages.fr.yaml`y `messages.en.yaml`. En `index.html.twig`, elimina la traducción `hello_world`. Volvemos a nuestra página de inicio en inglés, ¡perfecto! Tenemos una pizarra limpia.

## Traducir el sitio

Ahora, por fin, ¡podemos empezar a traducir este sitio de verdad!

Empieza por "Asteroides Locales". En `base.html.twig`, busca el elemento del menú con el texto "Asteroides Locales". Selecciona este texto y córtalo, para que esté en tu portapapeles.

Tenemos que inventar una clave para esto... Las claves pueden ser cualquier cosa única, así que podríamos utilizar `local_asteroids`, pero una lista plana enorme puede resultar confusa y difícil de manejar. Las claves pueden dividirse en espacios de nombres, separados por un punto. Así que podríamos utilizar quizás, `menu.local_asteroids`. Esto es mejor, pero sigue siendo un poco genérico.

A mí me gusta nombrar mis espacios de nombres en función del contexto en el que se utiliza el texto. En este caso, el texto está en `base.html.twig`, así que utiliza `base.local_asteroids`. No olvides añadir `|trans`:

[[[ code('3a42d5066e') ]]]

Si este texto traducido estuviera en un controlador, podrías utilizar una versión abreviada del nombre de la clase del controlador o incluso del nombre de la ruta. Lo mejor es encontrar una buena convención, ¡y ceñirse a ella!

¿Qué pasa si hay varias claves únicas con el mismo texto? Sinceramente, no me preocupo demasiado por esto. La mayoría de los servicios de traducción no cobran extra por texto duplicado, y considero que esta convención es más importante que la unicidad del texto.

Tenemos el nombre de nuestra clave y el texto en el portapapeles, así que vamos a añadir la traducción a `messages.en.yaml`, nuestro archivo de traducción al inglés por ahora.

Podemos añadir las claves como una lista plana, como con `base.` pero... Como estamos utilizando YAML como formato, ¡podemos utilizar la sangría para crear los espacios de nombres como forma de agruparlos visualmente! La mayoría de los demás formatos de archivo de traducción no admiten esto, por eso los desarrolladores prefieren YAML para las traducciones.

Añade `base:`, nueva línea, sangría, y añade `local_asteroids:`. Luego, entre comillas, pega: "Asteroides Locales":

[[[ code('20d9d050a0') ]]]

Volvemos a nuestra aplicación, actualizamos... Y... ¡Bah! ¡Tenemos una errata en nuestra clave!

En `base.html.twig`, el plugin Symfony de PhpStorm incluso resalta esta clave con una advertencia. Borra el texto después de `_` y ¡mira esto! ¡PhpStorm está auto-sugiriendo la clave correcta! Arréglalo, actualiza la página... ¡y ya funciona!

Me gusta mucho este proceso de traducir texto: uno a uno, encuentra el texto que quieres traducir, córtalo, inventa una clave, añade el filtro `|trans`, y luego, en tu archivo de traducción del idioma por defecto, añade la clave y pega el texto.

A continuación, ¡vamos a ver cómo manejar la traducción de texto con contenido dinámico!
