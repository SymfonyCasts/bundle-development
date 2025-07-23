# Depurar y limpiar traducciones

¿Has terminado los deberes? No te preocupes, ¡yo tampoco! Dejemos eso de lado por ahora y veamos cómo depurar traducciones. De todas formas, ¡esto te será útil para los deberes!

## Barra de herramientas de depuración web

En la página principal en inglés, puede que ya hayas notado este nuevo icono en la barra de herramientas de depuración web. Es para las traducciones. Pasa el ratón por encima para ver lo que nos ofrece.

"Configuración regional predeterminada: ES" - se trata de un error, no es la configuración regional por defecto de nuestra aplicación, sino la configuración regional utilizada para esta petición.

"Mensajes perdidos: 0" - el número de mensajes que estamos intentando traducir (utilizando el filtro Twig de `trans` o el servicio `TranslatorInterface` ) que no tienen una entrada en nuestro archivo `messages.en.yaml`.

"Mensajes fallidos: 0" - el número de mensajes que tienen una traducción en la configuración regional por defecto de nuestra aplicación, el inglés, pero no en la configuración regional actual,

"Mensajes definidos: 2" - el número de mensajes que se han encontrado y utilizado con éxito para la configuración regional actual.

Todo ello en el contexto de la petición actual. Verás números diferentes para peticiones distintas.

## Panel del Perfil de Traducción

Haz clic en el botón para ver el "Panel del Perfil de Traducción". Esto nos muestra aún más detalles para cada una de las anteriores. El dominio, los mensajes, los tiempos utilizados y una vista previa. ¡Incluso los parámetros utilizados!

Veamos qué aspecto tiene esto para la página de inicio en francés. Enseguida vemos que el icono se resalta en amarillo para indicar una "advertencia". Y efectivamente, no tenemos los 2 mensajes definidos para el francés, así que está utilizando el fallback en inglés. Haz clic en el perfil. 0 mensajes definidos, salta a la pestaña Fallback para ver los mensajes en inglés que se están utilizando como fallback.

Esto está bien para depurar las traducciones de la petición actual, pero ¿qué pasa con la obtención del estado global de la traducción de todo nuestro sitio?

## `debug:translation` Comando

Salta a tu terminal y ejecuta:

```terminal
symfony console debug:translation en
```

Genial, estos son todos los mensajes de traducción al inglés de todo nuestro sitio. ¿Cómo funciona? Busca en todos tus archivos PHP y Twig, y encuentra todos los lugares en los que utilizas el filtro `TranslatorInterface` y `trans`. A continuación, busca las claves que se están utilizando y comprueba si tienen una traducción disponible para la configuración regional en cuestión.

Una cosa a tener en cuenta es que esto no puede analizar traducciones dinámicas, como`$translator->trans($variable)`. Sólo funciona con cadenas estáticas, como`$translator->trans('some.key')` o `{{ 'some.key'|trans }}`.

La columna "Estado" nos muestra el estado. ¡Vacío significa bueno!

Ejecuta el mismo comando, pero para `fr`:

```terminal
symfony console debug:translation fr
```

Vaya, ¡muchos problemas! El "Estado" en este caso aparece como "missing", pero en realidad está utilizando el fallback inglés, del que podemos ver la vista previa.

## `lint:translations` Comando

También hay un comando para "limpiar" nuestras traducciones:

```terminal
symfony console lint:translations
```

Lo único que hace es comprobar si todos nuestros archivos de traducción son válidos, es decir, si se pueden cargar y analizar correctamente. Creo que si un archivo no fuera válido, te darías cuenta bastante rápido durante el desarrollo local. Pero puede ser útil en una canalización de Integración Continua (o CI), como las Acciones de GitHub. Si hay un problema, este comando fallará, y tu trabajo fallará.

## Integración Continua

Hablando de IC, un comando superútil para incluir en tus trabajos es`debug:translation` para tu configuración regional predeterminada. Este comando fallará si falta alguna traducción. Esto podría ayudar a imponer la norma de que "cada Pull Request debe tener traducciones para la configuración regional por defecto".

Dependiendo de lo estricto que quieras ser, podrías incluso ejecutar este comando para cada una de las configuraciones regionales admitidas. También fallará si se utilizan fallbacks.

A continuación, vamos a añadir algunas "traducciones que faltan" y a comprobar otro comando para "extraerlas".
