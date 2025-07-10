# Rutas localizadas

Hemos instalado el componente de traducción. ¡Adelante! Además, nuestras páginas se anuncian a través del atributo `lang` como nuestra configuración regional por defecto: `en`. Pero, ¿cómo podemos saber qué idioma quiere un usuario? Hay varias opciones.

## Identificar el idioma del usuario

Un método consiste en utilizar la entidad `User`. Si un usuario ha iniciado sesión en nuestro sitio, uno de los ajustes de su cuenta podría ser "idioma". Pero entonces, ¿qué pasa con los usuarios no autentificados? Ya sabes, los tímidos que merodean por el borde de nuestro sitio.

Otro método es utilizar una cabecera de petición. La mayoría de los navegadores envían una cabecera`Accept-Language` con cada petición, anunciando qué idiomas prefiere el usuario. Esto suele configurarse cuando instalas tu sistema operativo. Symfony tiene incluso un método `Request::getLanguages()` que puedes utilizar para obtener sus idiomas preferidos. El problema es que esto no es bueno para el SEO. Como una página tendrá la misma URL tanto si está en italiano, español o francés, los motores de búsqueda sólo pueden indexar uno de ellos. ¡Maldita sea!

## Enrutamiento localizado

Un enfoque mejor es el enrutamiento localizado, o traducido. Probablemente lo hayas visto antes, donde el código del idioma pone el prefijo a cada ruta, como `/en/about` o `/fr/about`. Otro método es utilizar subdominios, como`en.example.com/about` o `fr.example.com/about`. También puedes traducir el texto de la ruta, como `/about` para el inglés y `/a-propos` para el francés.

Para este tutorial, sólo utilizaremos la versión con prefijo de ruta: `/en` `/fr` , etc.

## Configurar rutas

Podemos configurar esto por rutas. En `src/Controller/ArticleController.php`. En el atributo `#[Route]` para el método `index()`, sustituye el primer argumento por una matriz. Dentro, utiliza el código de configuración regional como clave y la ruta como valor:`['en' => '/en', 'fr' => '/fr', 'es' => '/es']`:

[[[ code('c984ff862f') ]]]

Vuelve a nuestra aplicación y pruébalo añadiendo `/fr` a la URL. Hmm, parece lo mismo... Eso es porque aún no hemos creado ninguna traducción. Pero si inspeccionas el código fuente de esta página, el atributo `lang` de la etiqueta`<html>` es ahora `fr`. ¡Funciona! También puedes ir a `/es` y `/en`.

## Localización de todas las rutas

Sin embargo, tenemos un pequeño error. Supongamos que estás en la versión `/fr` y haces clic en el enlace de un artículo. Ahora estamos en una ruta no localizada. Si inspeccionamos la fuente, veremos que el atributo `lang` ha vuelto a `en`. No es una gran experiencia para el usuario. Si estás en una página francesa y haces clic en un enlace, deberías permanecer en una página francesa, ¿no?

Una solución es duplicar nuestro mapa de localización de rutas para cada ruta... pero... eso es un fastidio. Por suerte, ¡hay una solución más fácil!

De vuelta a nuestro código, revierte el mapa de configuración regional que añadimos, y abre `config/routes.yaml`. Esta entrada `controllers` le está diciendo a Symfony que cargue todos los métodos marcados con el atributo `#[Route]` en nuestro directorio `src/Controller`. Podemos añadir aquí nuestro mapa de prefijo de configuración regional para aplicarlo a todas las rutas cargadas:`prefix:`, `en: /en`, `fr: /fr`, `es: /es`:

[[[ code('c5adfb5f2e') ]]]

En nuestra aplicación, vamos a la página de inicio en francés: `/fr` y hacemos clic en un artículo ¡Boom! ¡Estamos en la página de artículos en francés! Cambia a la versión `/es` de esta página y pulsa el logotipo para volver a la página de inicio. ¡Estupendo! Ahora estamos en la página de inicio en español. ¡La UX ha ganado!

Para ver cómo funciona, salta a la consola y ejecuta:

```terminal
symfony console debug:router
```

Nuestras rutas se duplican para cada configuración regional, y se les añade el código de la configuración regional. Symfony es lo suficientemente inteligente como para saber que la versión sin sufijo del nombre de la ruta, la que estás acostumbrado a usar, es la versión "mágica". Entre bastidores, utiliza la configuración regional actual para determinar qué versión sufijada de la ruta utilizar. Por ejemplo, cuando generas un enlace para`app_article_show` en la página de inicio `fr`, sabe que debe utilizar la versión`app_article_show.fr` de esa ruta. Genial, ¿eh?

## Establecer una localización por defecto

Pero hay otro problema: ve a la página de inicio real, la que no tiene prefijo de configuración regional. Oh, esta es la página especial de "inicio" de Symfony que aparece si no tienes definida una ruta de "página de inicio" - recuerda, nuestra página de inicio es en realidad `/<locale code>`. Si te fijas en la parte inferior izquierda, se trata en realidad de un error 404.

¿Qué podemos hacer aquí? Bueno, podríamos crear una ruta de página de inicio no prefijada que permita al usuario elegir su idioma, o tal vez, redirigirle a la versión prefijada para el idioma de su navegador. Pero hagamos que nuestro idioma por defecto, el inglés, no esté prefijado por su código de configuración regional.

Esto es super sencillo de hacer. En `config/routes.yaml`, cambia el prefijo `/en` por sólo una cadena vacía:

[[[ code('73d9abd447') ]]]

Ahora, vuelve a la página de inicio real y actualiza. ¡Funciona! Navega hasta un artículo, y estaremos en la versión sin prefijo de la página del artículo. Ve a la página de inicio de `/fr`, haz clic en un artículo... bien, estamos en la versión `/fr` del artículo.

Ahora podemos navegar a la página de cualquier idioma, pero... tenemos que cambiar manualmente la URL en la barra de direcciones... ¡qué pena! Siguiente paso: ¡creemos un widget para cambiar de idioma!
