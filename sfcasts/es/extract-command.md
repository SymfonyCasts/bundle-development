# `translation:extract` Comando

Hasta ahora, hemos estado creando manualmente claves de traducción y añadiendo sus entradas a nuestro archivo `messages.en.yaml`. ¿Pero sabías que también hay una forma automatizada de hacerlo? Vamos a comprobarlo utilizando el ejemplo de traducir dos elementos de menú.

Echa un vistazo a tu código en `templates`, `base.html.twig` y busca 'tiempo' y 'diario'. Creemos las claves como hacemos habitualmente, nombrándolas `base.weather` trans y `base.journal` trans.

Ahora, en lugar de añadir manualmente las entradas en nuestro archivo `messages.en.yaml`, voy a mostrarte un ingenioso comando. Ve a tu terminal y ejecútalo:

```terminal
symfony console translation:extract en --dump-messages
```

Este comando te mostrará que ha encontrado cinco mensajes. Los resaltados en verde son los nuevos, los que no existen actualmente en nuestro archivo `messages.en.yaml`.

## Añadir nuevas claves al archivo YAML

Para añadir las nuevas claves al archivo YAML, vuelve a ejecutar el comando. Pero esta vez, sustituye `dump messages` por `force`. Incluye también `--format=yaml` y`--as-tree=3`. Así te asegurarás de que las claves tengan una sangría de tres niveles dentro del archivo YAML.

```terminal
symfony console translation:extract en --force --format=yaml --as-tree=3
```

Vuelve a tu archivo `messages.yaml` y ¡voilà! Se han añadido las nuevas claves, prefijadas con dos guiones bajos. Este prefijo ayuda a identificar fácilmente las nuevas claves para encontrarlas y reemplazarlas rápidamente. Incluso puedes personalizarlo si quieres.

El siguiente paso es sustituir los guiones bajos por los valores adecuados: "tiempo" y "diario". Actualiza tu aplicación y listo: ¡funcionan a las mil maravillas!

## Unas palabras de precaución

Este método funciona muy bien cuando estás desarrollando una nueva función y sólo añades unas pocas teclas. Sin embargo, cuando estás traduciendo un sitio grande y creando muchas claves a la vez, puede resultarte un poco engorroso. Yo suelo ceñirme al proceso manual para estos casos.

## Convertir a diferentes formatos con `translation:extract`

Aquí tienes otro truco genial. Supongamos que necesitas enviar todas las traducciones al inglés a un servicio de traducción para que las traduzcan al francés o al español. Sin embargo, tu traductor no está familiarizado con YAML. No te preocupes!`translation:extract` también puede convertir a otros formatos.

Prueba a ejecutar esta orden:

```terminal
symfony console translation:extract en --force --format=xliff
```

Esto crea un nuevo archivo, `messages.en.xlif`, en el directorio de traducciones. Puede que no sea super legible para nosotros, pero los servicios de traducción pueden cargarlo fácilmente en su sistema.

Imaginemos que recuperamos los mensajes traducidos. Lo único que tendríamos que hacer es cambiar el nombre de `en` por `fr` en el nombre del archivo. Después de actualizar las traducciones, cambia a la versión francesa en tu página, y voilá: ¡tu versión francesa está lista!

Ten en cuenta que no vamos a utilizar este archivo `XLIF`, así que siéntete libre de eliminarlo.

## Próximamente: Traducir a otros idiomas

Hasta ahora, hemos traducido principalmente desde nuestro idioma por defecto, el inglés. Pero, ¿qué te parece crear traducciones al francés y al español? Permanece atento, vamos a explorar otra función genial de Symfony para hacer precisamente eso.
