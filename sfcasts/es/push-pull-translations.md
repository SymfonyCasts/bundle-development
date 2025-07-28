# Enviar y recibir traducciones

Nuestro proyecto "Barra Espacial" en Crowdin está listo y configurado para las localizaciones de nuestra aplicación. ¡Vamos a subir nuestras traducciones!

Ejecuta en tu terminal:

```terminal
symfony console translation:push
```

Vale, todas las traducciones de nuestras localizaciones activadas se han subido, pero, por supuesto, sólo el inglés tiene alguna.

Vuelve a Crowdin y actualiza... No parece que haya cambiado nada, el francés y el español siguen traducidos al 0%.

## Traducciones automáticas

Haz clic en "Ver cadenas". Aquí podemos ver todas nuestras traducciones al inglés a la izquierda. A la derecha, tenemos las versiones en francés y español, que están vacías. Si no ves nuestros locales alternativos, haz clic en "Idiomas" en la parte superior, selecciónalos y haz clic en "Aplicar".

Si tienes un equipo de traducción, puedes invitarles a este proyecto, ¡y podrán empezar a completar las traducciones que faltan!

Mi equipo de traducción está fuera del planeta de permiso... pero podemos automatizar las traducciones con la función de sugerencias de Crowdin. Así podrán revisarlas cuando vuelvan.

Selecciona la entrada vacía "Francés". Este panel de la derecha muestra sugerencias de varios motores de traducción. Todas se parecen bastante, así que pasa el ratón por encima de la primera y haz clic en este botón "Disk" para aplicarla.

¡Genial! La traducción al francés de "Asteroides locales" ya está rellenada y ha cambiado automáticamente a la entrada en español. Haz lo mismo con ésta.

Ahora salta a la siguiente entrada en francés: "Aplicar" esta sugerencia. Hmm, parece que Crowdin nos está diciendo que hay un error ortográfico... Voy a dejarlo como está. Este es un buen momento para señalar que, con las traducciones automáticas, siempre debes contar con alguien que domine el idioma para que las revise. Además, con las traducciones, el contexto importa, y estos pequeños fragmentos de texto a menudo carecen de él. Por ejemplo, expresiones como "Un hombre en la luna" pueden traducirse literalmente y no tener sentido en la lengua de destino.

Probablemente haya una forma de aplicar en bloque estas sugerencias, pero creo que Crowdin está diseñado para fomentar la revisión manual. Voy a repasar y aplicar rápidamente el resto de las sugerencias. ¿Notas cómo sabe que no debe traducir los marcadores de posición?

## Guardar y extraer traducciones

Parece que todo esto se guarda automáticamente, así que sólo tienes que pulsar la "flecha atrás" para volver al panel de control del proyecto. Genial, ¡100% para los dos idiomas! ¡Es hora de volver a cargarlas en nuestro proyecto!

Vuelve a tu terminal y ejecuta:

```terminal
symfony console translation:pull --domains=messages --format=yaml --as-tree=3
```

Cuando empujamos, supo empujar todos los dominios, pero para tirar, necesitas especificar el dominio a tirar. Los queremos en formato YAML, y la opción "as-tree" sangrará las traducciones por nosotros.

¡Verde significa bien! ¡Vamos a comprobarlas!

De vuelta a tu IDE, en el directorio `translations`, se ha actualizado `messages.fr.yaml` y se ha creado un nuevo `messages.es.yaml`. ¡Ábrelos! Oh sí, ¡dulces, dulces traducciones!

[[[ code('880cb487a5') ]]]

[[[ code('3240d9ac1f') ]]]

## Probando tus traducciones

¡El momento de la verdad! Volvemos a nuestra aplicación y cambiamos el idioma a "francés" ¡Impresionante! El menú es francés, y si nos desplazamos hacia abajo, ¡aquí está el pie de página en francés!

Cambia a español, el menú es español, el pie de página es español, ¡bien!

Puedes repetir este proceso de empujar, traducir en Crowdin, y tirar, una y otra vez a medida que añades más traducciones. Crowdin hará un seguimiento de lo que ya has traducido, para que tú sólo tengas que centrarte en las nuevas cadenas.

Y esto es todo sobre los aspectos básicos de las traducciones con Symfony Seguro que puedes profundizar más.

## Componente Traductor UX

Por ejemplo, si necesitas traducciones en JavaScript, existe un componente [Symfony UX Translator](https://symfony.com/bundles/ux-translator/current/index.html). Gestionas tus archivos de traducción del mismo modo, pero también puedes incluir las claves en tus archivos JavaScript. ¡Muy útil!

## Traducciones de bases de datos

Hay un gran gato espacial en la habitación que aún no hemos tratado: Las traducciones de bases de datos... Como he dicho antes, el componente de traducción de Symfony no gestiona las traducciones de bases de datos. Tendremos que recurrir a una solución de terceros para ello. Una muy popular es este paquete [Doctrine Extensions](https://github.com/doctrine-extensions/DoctrineExtensions). Tiene un comportamiento ["Translatable"](https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/translatable.md) que te permite traducir tus entidades. También hay un [Symfony Bundle](https://symfony.com/bundles/StofDoctrineExtensionsBundle/current/index.html) para conectarlo todo.

Funciona bien, pero puede ser un poco pesado y complejo. Pensando en voz alta, ¡crear un bundle nuevo y moderno para traducir entidades Doctrine sería una adición impresionante al ecosistema Symfony!

¡Hasta la próxima! ¡Feliz programación!
