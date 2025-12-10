# Publicar en Packagist y liberar

Nuestro bundle ya está alojado en GitHub y nuestra integración continua con las Acciones de GitHub ¡está en verde! Para que se pueda instalar a través de Composer, tenemos que publicarlo en Packagist. No podrás seguir esta parte, no queremos inundar Packagist con duplicados de nuestro bundle. Pero puedes utilizarlo como guía para publicar tus propios bundles.

## Enviar a Packagist

Ve a [Packagist.org](https://packagist.org) e inicia sesión con tu cuenta de GitHub. Haz clic en "Enviar" en el menú superior. Para la URL del repositorio, copia la URL de tu repositorio de GitHub y pégala... luego haz clic en "Comprobar".

Este paso busca paquetes existentes con nombres similares. Si ya existe un paquete que se ajusta a tus necesidades, puede que no necesites publicar uno nuevo. Veo que ya existe un paquete con un nombre similar, pero procederé de todos modos haciendo clic en "Enviar".

Y... Bien, ¡estamos en nuestra nueva página de paquetes! En primer lugar, Packagist rastrea los metadatos de nuestro paquete para determinar todas nuestras ramas, etiquetas y dependencias. También configura automáticamente un webhook de GitHub para que se notifique a Packagist que vuelva a rastrear nuestro paquete cada vez que introduzcamos código nuevo.

Cuando ya no veas el mensaje "Este paquete no se actualiza automáticamente", significa que el webhook está configurado. Puedes confirmarlo cuando veas el mensaje "Este paquete se actualiza automáticamente" en la barra lateral derecha.

De vuelta en la página del repositorio de GitHub, haz clic en la pestaña "Configuración" y, a continuación, en "Webhooks" en la barra lateral izquierda. Este es el webhook que Packagist añadió automáticamente por nosotros.

En Packagist ahora, sólo tenemos esta rama 1.x porque no hemos creado ninguna versión.

## Crear una versión

Crearé una versión en GitHub haciendo clic en "Versiones" en la barra lateral derecha y haciendo clic en "Crear nueva versión". Podemos elegir una etiqueta git existente para utilizarla en la publicación, pero, como aún no tenemos ninguna etiqueta, escribiré `v1.0.0`, pulsaré "Crear nueva etiqueta" y luego "Crear" en este cuadro de diálogo.

GitHub tiene un práctico botón para generar notas de publicación, así que haré clic en él. Autocompletará el título con el nombre de nuestra etiqueta y generará un enlace al registro de cambios completo. Esto no es demasiado emocionante para una versión inicial, pero cuando añades versiones posteriores, genera una bonita lista de cambios.

Sólo añadiré una nota: "Lanzamiento inicial" y haré clic en "Publicar lanzamiento".

1.¡Lanzamiento 0 realizado! De vuelta en la página de nuestro repositorio, podemos ver la versión 1.0 listada como "última".

Esto debería haber activado el webhook de Packagist, así que volvemos a la página del paquete en Packagist, actualizamos... y... ¡ahí está! Nuestra versión 1.0 ya está disponible y puede instalarse con Composer

## Probar la versión

Veamos si funciona En algún lugar de tu sistema, crea una nueva aplicación Symfony temporal ejecutando:

```terminal
symfony new --webapp test-my-bundle
```

Espera a que se cree... y navega hasta ella:

```terminal
cd test-my-bundle
```

¡Requiere nuestro bundle!

```terminal
symfony composer require symfonycasts/object-translation-bundle
```

Vale, tenemos un error, pero mira aquí: ¡Composer ha descargado e instalado`v1.0.0` de nuestro bundle!

El error proviene de Symfony Flex. Está viendo que hemos instalado un bundle, así que lo ha activado automáticamente en nuestra app. El problema es que nuestro bundle requiere cierta configuración y una entidad: la entidad `translation_class` y`Translation`.

Un usuario probablemente podría tropezar y solucionar esto leyendo la documentación de nuestro bundle, pero es un poco fastidioso...

A continuación, mejoraremos esta experiencia creando una receta Symfony Flex para nuestro bundle
