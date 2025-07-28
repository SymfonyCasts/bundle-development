# `translation:extract` Comando

Hasta ahora, hemos estado creando claves de traducción y añadiéndolas manualmente, junto con sus valores, a `messages.en.yaml`. ¿Pero sabías que existe un método alternativo que automatiza parte de este proceso?

Vamos a traducir los dos elementos de menú que nos quedan: "Tiempo" y "Diario".

Abre `templates/base.html.twig` y busca el texto de estos elementos de menú. Sigue nuestra rutina habitual de buscar una clave y utilizar el filtro `trans`. `{{ 'base.weather'|trans }}` y `{{ 'base.journal'|trans }}`:

[[[ code('a2e445fc46') ]]]

Bien, en lugar de añadir manualmente estas entradas a nuestro archivo `messages.en.yaml`, dirígete a tu terminal y ejecuta:

```terminal
symfony console translation:extract en --dump-messages
```

Este comando es similar a `debug:translation` en el sentido de que escanea tu código y encuentra claves de traducción. Estas claves en verde son las nuevas que hemos añadido y aún no tienen entradas en nuestro archivo `messages.en.yaml`.

## Añadir nuevas claves al archivo YAML

Podemos utilizar el comando para añadir automáticamente estas nuevas claves a nuestro archivo YAML. Ejecuta de nuevo el comando, pero sustituye `--dump-messages` por `--force`. A continuación, añade `--format=yaml` para que escriba como YAML, y `--as-tree=3` para mantener las claves sangradas a tres niveles de profundidad en el archivo YAML.

```terminal-silent
symfony console translation:extract en --force --format=yaml --as-tree=3
```

¡Vuelve a `messages.en.yaml` y compruébalo! ¡Aquí están nuestras nuevas claves!

[[[ code('68b48a2e83') ]]]

Los valores son los nombres de las claves prefijados con `__` (dos guiones bajos). Esto puede ayudarte a encontrarlas rápidamente si hay muchas. Este prefijo puede personalizarse si lo deseas.

Sustitúyelos por el texto adecuado: "Tiempo" y "Diario":

[[[ code('1eccaf40db') ]]]

Actualiza tu aplicación y listo... no ha cambiado nada, ¡pero sabemos que funciona!

Si estás traduciendo en masa un sitio existente como nosotros, creo que este método puede ser más engorroso que nuestro proceso anterior. Lo que acaba ocurriendo es que, al añadir claves en masa en tu código, una vez que las extraes, ¡te olvidas del texto original!

Pero! este método funciona muy bien cuando desarrollas una nueva función. Puedes añadir las claves para el texto en tu código, y averiguar el texto real más tarde.

## Convertir a diferentes formatos

Este comando tiene otra función genial, ¡quizás no intencionada!

Supongamos que tienes un servicio de traducción que te pide que envíes tu archivo de traducción en inglés, y ellos te lo devolverán en francés. Fácil, ¿verdad? Sólo tienes que enviarles `messages.en.yaml`. Pero... ¡no saben qué demonios es YAML! Quieren el archivo en un formato compatible con su software: `XLIFF`.

¡No te preocupes! ¡El comando `translation:extract` puede convertir a diferentes formatos!

Ejecuta esto:

```terminal
symfony console translation:extract en --force --format=xliff
```

En nuestro directorio `translations/`, tenemos un nuevo archivo: `messages.en.xliff`. Ábrelo.

¡Uf! ¡Qué asco! ¡XML!

No te preocupes: ¡a los programas de traducción les encanta! Sólo tienes que enviarles este archivo y decirles que intercambien el texto de `<target>`, que es inglés, con las traducciones al francés.

Cuando te lo devuelvan, cámbiale el nombre a `messages.fr.xliff` y vuelve a colocarlo en el directorio `translations/`. Sólo tienes que cambiar el primer `<target>`para que diga "(Francés) Asteroides Locales":

[[[ code('066833f235') ]]]

De vuelta en nuestra aplicación, cambia a la versión francesa de esta página y ¡bum! "(Francés) Asteroides Locales".

Vale, ya está bien de XML por hoy: borra `messages.fr.xliff`.

¿Y si no tienes un servicio de traducción al que puedas enviar estos archivos? No hay problema Hay soluciones basadas en la nube que pueden ayudarte, ¡y Symfony tiene integraciones para ellas! ¡Vamos a comprobarlo!
