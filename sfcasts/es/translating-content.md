# Traducir contenido

Ha llegado el momento de traducir Hay dos lugares principales en los que sueles traducir contenido. Primero: en el código PHP, como los mensajes flash de tus controladores. Segundo: en los archivos Twig. Aquí es donde harás la mayor parte de la traducción.

## Servicio de traducción

Empecemos con las traducciones de PHP. Abre `src/Controller/ArticleController.php`, y en el método `index()`, autocablea `TranslatorInterface $translator`:

[[[ code('89f6686477') ]]]

A continuación, `dump($translator->trans('Hello World!'))`:

[[[ code('8aa9d4a3e7') ]]]

De vuelta en nuestra aplicación, en la página de inicio en inglés, actualiza. Abajo, en la barra de herramientas de depuración, comprueba el volcado "¡Hola Mundo!". Tiene sentido, estamos en una página en inglés, así que muestra la versión en inglés.

Cambia a la versión francesa de esta página y comprueba el volcado: "¡Hola Mundo!"... ¿No debería ser francés? Sí, ¡pero aún no hemos creado la traducción al francés de este texto! ¡Merde! ¡¿No debería ser Symfony lo suficientemente inteligente como para hacer esto por nosotros?! No es tan mágico... todavía, ¡así que vamos a crear la traducción al francés!

## Crear traducciones

En nuestro código, busca ese directorio vacío `translations/` y crea un nuevo archivo:`messages.fr.yaml`. Te explicaré el nombre del archivo en un momento. Confía en mí por ahora.

Se trata de una lista clave-valor, donde la clave es, en nuestro caso, la versión inglesa del texto que queremos traducir -la cadena que pasamos a `trans()`, y el valor es la versión francesa.

Añade `"Hello World!": "Bonjour le monde!"`:

[[[ code('d890083fcc') ]]]

Vuelve a la aplicación, actualiza la página... y voilà, ¡se ha volcado `Bonjour le monde!`!

Cambia a la versión en español. Hmm... ¿volvemos a "¡Hola Mundo!"? En realidad, esto tiene sentido porque aún no hemos creado la traducción al español y, como nuestra configuración regional por defecto es el inglés, su texto se utiliza como alternativa.

## Traducir contenido con Twig

Ahora, traduzcamos el mismo texto, pero en Twig. Abre `templates/article/index.html.twig`, y en este `<div>`, escribe `{{ 'Hello World!'|trans }}`:

[[[ code('66a14c500f') ]]]

Entre bastidores, el filtro `trans` utiliza el servicio `TranslatorInterface`.

Actualiza nuestra aplicación... vemos `Hello World!` porque estamos en la página en español. Así que cambia a francés, y ahora es `Bonjour le monde!`.

## Profundizando `TranslatorInterface`

Exploremos `TranslatorInterface` un poco más. Vuelve a nuestro`ArticleController` y profundiza en el `TranslatorInterface`. Este método `trans` es donde ocurre la magia. El ID se refiere al texto que queremos traducir, los parámetros te permiten pasar valores dinámicos al texto: lo veremos en el próximo capítulo.

El dominio es esencialmente una categoría, o espacio de nombres de traducciones y por defecto es `messages` (por eso creamos antes ese archivo `messages.fr.yaml` ). Por último, está la configuración regional. Por defecto, utilizará la configuración regional actual, pero puedes anularla si te apetece.

## Comprender los archivos de traducción

Volvamos a nuestro archivo `messages.fr.yaml`. Esta convención de nombres es importante.

Como he dicho, `messages` es el dominio de traducción, siendo `messages`el predeterminado. Para aplicaciones avanzadas, puedes tener varios dominios y los bundles de terceros pueden definir los suyos propios. Por ejemplo, el KnpTimeBundle proporciona un dominio `time` para sus traducciones. Puedes consultarlas en el directorio `translations/` de este bundle.

Esto nos lleva a la siguiente parte del nombre de archivo: el código de configuración regional. Puedes ver todas las configuraciones regionales que admite este bundle.

Si quieres anular la traducción de un bundle, crea un nuevo archivo `<bundle-domain-name>.<locale-code>.yaml` en tu directorio `translations/`. Estas anulaciones se fusionarán con las del bundle, por lo que sólo tendrás que configurar las traducciones que quieras cambiar.

La última parte del nombre del archivo de traducción es la extensión, que indica a Symfony qué cargador de traducciones debe utilizar. El trabajo del cargador es leer el archivo y normalizar los datos para que Symfony pueda utilizarlos. Nosotros usamos `yaml` pero el KnpTimeBundle usa `xliff`. En la documentación de Symfony puedes ver todos los cargadores disponibles: ¡elige tu favorito!

Los dos más comunes son `yaml` y `xliff`. `yaml` es superamigable para el desarrollador, y fácil de leer. `xliff` está basado en XML, así que no es divertido de leer, pero es un formato estandarizado que utilizan muchos servicios de traducción.

A continuación, vamos a ver una forma más manejable de crear traducciones y ¡empezar a traducir este sitio!
