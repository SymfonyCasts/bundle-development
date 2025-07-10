# Instalación del Componente de Traducción

Hola amigos! e alegro de que te hayas unido a mí en este curso de sobre las traducciones de Symfony! En este tutorial, estamos celebrando uno de los componentes más antiguos de Symfony, ¡pero por primera vez aquí en SymfonyCasts!

¿Puede hacer que mi sitio en inglés esté disponible en alemán? ¡Sí! ¿Puede traducir mi acento canadiense al americano? Probablemente no, ¿eh?

El plan es atrevido, pero sencillo: tomar un sitio existente sólo en inglés y traducirlo a varios idiomas. ¡Incluyendo un conmutador de idiomas e incluso la integración con servicios que puedan encargarse de la traducción en sí! Para traducir la mayor cantidad de conocimientos a tu cabeza, descarga el código del curso enlazado más arriba. En el directorio `start/`, encontrarás archivos con este aspecto. Utiliza `README` para configurarlo y, cuando estés listo, ejecútalo:

```terminal
symfony serve -d
```

## Presentación de la Barra Espacial

Bienvenido a la Barra Espacial, un divertido sitio de blogs de noticias espaciales. Tiene todas las características estándar de los blogs: artículos, categorías, etiquetas y comentarios. ¡Vamos a traducir este sitio! ¡El cuadrante hispanohablante de la galaxia te espera!

El componente de traducción de Symfony está diseñado para traducir texto codificado, como elementos de menú o algo como este eslogan. Verás que nuestro blog está controlado por una base de datos. El componente de traducción no está diseñado para traducir este tipo de datos dinámicos, pero hablaremos de ello más adelante.

Una buena regla general es: si el texto que quieres traducir está consignado en tu repositorio, como en una plantilla o controlador Twig, entonces el componente de traducción es tu amigo. Si el texto está almacenado en una base de datos, como el título o el cuerpo de un artículo, necesitarás algo diferente.

## Instalar el componente de traducción

En primer lugar, tenemos que instalar el componente de traducción. Salta a tu terminal y ejecuta:

```terminal
symfony composer require translation
```

Después de instalar el paquete, comprueba lo que tenemos ejecutando:

```terminal
git status
```

Aparte de las cosas estándar, la receta añadió este archivo `translation.yaml`, y una carpeta `translations/` en la raíz de nuestro proyecto.

Entra en PhpStorm y compruébalo. La carpeta `translations/` es donde vivirán nuestras traducciones; por ahora está vacía, pero la utilizaremos más adelante.

## Configurar el componente de traducción

`config/packages/translation.yaml` es donde configuramos el componente de traducción. Este `default_path` le dice a Symfony dónde buscar nuestros archivos de traducción, y sí, está configurado en ese directorio `translations/`.

El `default_locale` es súper importante. Si una configuración regional no está configurada o si no existe una traducción para una determinada configuración regional, recurrirá a esto. Cuando tomes por primera vez un sitio existente y lo traduzcas, utiliza aquí el idioma original del sitio. En nuestro caso, lo que queremos es el predeterminado, `en` para el inglés.

Una nota rápida sobre estos códigos: son códigos ISO únicos para los idiomas. El inglés es `en`, el árabe es `ar`, el armenio es `hy`, etc. Busca "códigos de idioma ISO" para encontrar la lista completa.

Puede que hayas visto códigos de cinco dígitos como `en_US` o `en_CA`. Son específicos de cada localidad: `en_US` es inglés de EE.UU. y `en_CA` es inglés de Canadá. Son casi idénticos, pero hay ligeras diferencias. Por ejemplo, en Canadá escribimos correctamente color: `C-O-L-O-U-R`, mientras que en EE.UU. inventaron su propia ortografía `C-O-L-O-R`. Es broma, ambas son correctas.

Los códigos de cinco dígitos también se pueden utilizar para localizar tu sitio, teniendo en cuenta las diferencias específicas de cada país en el formato de los números, la moneda y la fecha.

Para este curso, sin embargo, no vamos a entrar en la "localización", así que nos ceñiremos a los códigos de idioma de dos dígitos para nuestros "locales".

## Elegir idiomas para el sitio

Configuremos los demás idiomas que admitirá nuestro sitio. Esperaba utilizar el klingon y el romulano, pero no tienen códigos ISO... todavía, así que nos ceñiremos a los códigos oficiales. Esto es opcional, pero es una buena práctica añadir `enabled_locales` a la configuración de `translation.yaml`. Incluiremos el inglés (`en`), el francés (`fr`) y el español (`es`):

[[[ code('d5da149201') ]]]

Esto no impide que utilices configuraciones regionales fuera de esta lista, pero es beneficioso configurarlo si conoces las configuraciones regionales exactas que utilizará tu sitio. Hay una pequeña mejora en el rendimiento, ya que Symfony sabe que debe pre-cachear las traducciones sólo para esas localizaciones. Y lo que es más importante, te permite listar fácilmente estas localizaciones en tu código, por ejemplo para un cambiador de idioma o para operaciones masivas, como generar un mapa del sitio para todas las páginas en todos los idiomas soportados.

## Añade el atributo `lang` 

Ahora que tenemos las traducciones activadas, ve a `templates/base.html.twig`. PhpStorm nos da una advertencia sobre nuestra etiqueta `<html>`. Quiere que añadamos un atributo`lang` al idioma actual. Consíguelo dinámicamente con`lang="{{ app.locale }}"`.

[[[ code('63df257fde') ]]]

Esto es útil para el SEO.

Vuelve a nuestro sitio... actualiza... y visualiza la "fuente de la página". Allá vamos: `lang="en"` - nuestra configuración regional por defecto.

A continuación, vamos a ver cómo averiguar qué idioma quiere nuestro usuario ¡Adelante!
