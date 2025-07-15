# HTML en traducciones

He aquí otro escenario complejo. En nuestra página de inicio, desplázate hasta el pie de página.

Quiero traducir este texto... pero, contiene un icono y un enlace HTML... Podrías pensar en traducir simplemente las dos partes de texto por separado, pero de nuevo, en idiomas diferentes, puede que deban ordenarse de forma diferente.

Así que necesitamos el HTML en el texto de traducción. Veamos dos enfoques diferentes.

## HTML crudo en el texto de traducción

En `base.html.twig`, busca el texto del pie de página.

La solución más sencilla es añadir directamente el texto, con HTML y todo, como valor de traducción. Para ello, selecciona todo el texto y córtalo. Para la clave, utiliza `'base.footer'|trans`.

En `messages.en.yaml`, añade una nueva clave llamada `base: footer:`, y dentro de las comillas simples, pega. Necesitamos utilizar comillas simples aquí porque el HTML contiene comillas dobles.

Vuelve a nuestra aplicación y actualiza... Vaya, esto no está bien. Twig hace esto por defecto para evitar ataques XSS a partir de datos enviados por el usuario. En este caso concreto, podemos desactivar este comportamiento con seguridad, ya que tenemos el control total del texto.

De vuelta en `base.html.twig`, añade `|raw` después de `trans` para desactivar el escape.

Actualiza... y ¡Voilà! ¡Funciona!

Esto es rápido y fácil, pero tiene algunos inconvenientes. En primer lugar, mezcla HTML en nuestro texto de traducción, lo que puede dificultar su lectura y mantenimiento. En segundo lugar, duplica el HTML en cada archivo de traducción, lo que puede dificultar la actualización del HTML más adelante.

Probemos un enfoque diferente: añadir el HTML como marcadores de posición.

## Marcadores de posición HTML

En `messages.en.yaml`, copia el texto completo, y en `base.html.twig`, debajo de nuestra traducción del pie de página, pégalo temporalmente.

Primero, copia el HTML del icono, y en el filtro `trans`, añádelo como marcador de posición:`'%heart%': ''`, y pégalo. Ahora, copia el HTML del enlace y añádelo como otro marcador de posición: `'%link%': ''` y pega. Como todavía estamos renderizando HTML, el filtro `|raw` sigue siendo necesario.

Borra la línea temporal que hemos pegado y vuelve a `messages.en.yaml`. Sustituye el HTML del icono por `'%heart%'` y el HTML del enlace por`'%link%'`. De inmediato, esto parece mucho más limpio y fácil de leer.

Vale, vuelve a la aplicación y actualiza... ¡Eh! ¡Un error de sintaxis!

Ahh, los marcadores de posición de `trans` deben envolverse en una matriz. En `base.html.twig`, envuélvelos entre llaves.

Actualiza... desplázate hacia abajo... ¡ya está!

Este enfoque es mucho más limpio, pero también tiene un inconveniente. ¿Qué pasaría si el HTML de nuestro enlace tuviera texto o un atributo que hubiera que traducir? No hay una forma sencilla de manejar esto. Tendrías que traducir los elementos por separado o hacer algunas traducciones anidadas: como traducir el marcador de posición. Esperemos que este problema no surja demasiado a menudo.

## Flujo de trabajo de traducción

Esto cubre todas las situaciones habituales. No voy a hacer que me veas traducir el resto del sitio porque sería más de lo mismo, aburrido, Kevin bla bla bla. Así que lo dejaré como tarea para casa: ¡Vous pouvez l'envoyer vers `/dev/null` une fois que c'est fait !

¡A continuación, veremos las herramientas de depuración de traducciones que proporciona Symfony!
