# Proveedores de traducción

Hasta ahora, nos hemos centrado sobre todo en traducir nuestro sitio del inglés al... inglés... ¿Y al francés y al español? Hemos hablado un poco de cómo podemos enviar manualmente nuestro archivo `messages.en.yaml` a un servicio de traducción para que realice estas traducciones. Pero hay otra opción: Los Proveedores de Traducción de Symfony.

## Entender los Proveedores de Traducción de Symfony

Son un conjunto de servicios basados en la nube que gestionan nuestras traducciones. Si tenemos un "equipo" de traducción, pueden acceder a estos servicios y traducir nuestros mensajes. Pero muchos, si no todos, pueden crear automáticamente las traducciones por nosotros.

Tú "empujas" las traducciones de tu idioma predeterminado al proveedor y, una vez traducidas, "tiras" de los mensajes traducidos hacia abajo.

Los documentos de Symfony muestran los proveedores compatibles. No tengo un favorito concreto y este curso no está patrocinado por ninguno de ellos, pero me he dado cuenta de que Crowdin ha patrocinado a Symfony en el pasado, así que lo utilizaré.

## Instalar el proveedor Crowdin

Empieza instalando el proveedor Crowdin, así que copia el comando `composer require`y pégalo en tu terminal:

```terminal-silent
composer require symfony/crowdin-translation-provider
```

Este paquete incluía una receta, así que ejecútala:

```terminal
git status
```

Parece una nueva variable de entorno y algo de configuración.

Primero, abre `.env` y desplázate hasta el final, se ha añadido un`CROWDIN_DSN` comentado.

A continuación, mira `config/packages/translation.yaml`. Se ha añadido el proveedor Crowdin, junto con su DSN.

Como el DSN es una variable de entorno sensible, no queremos que se confirme, así que añade un archivo `.env.local` en la raíz de nuestro proyecto. Copia el DSN de `.env`y pégalo aquí. Sólo utilizaremos una cuenta personal de Crowdin, así que elimina la parte `ORGANIZATION_DOMAIN.`.

## Configurar la cuenta Crowdin

¡Es hora de crear y configurar una cuenta Crowdin! Ve a [crowdin.com](https://crowdin.com) y regístrate para obtener una cuenta gratuita. Empiezas con una prueba gratuita de sus funciones premium, pero sólo necesitaremos las funciones que forman parte de su nivel gratuito.

Una vez registrado y conectado, accederás a esta página del panel de control. En primer lugar, necesitamos un token de API. Haz clic en tu avatar en la parte superior derecha y selecciona "Configuración". Haz clic en la pestaña "API" y luego en "Nuevo token".

Para el "Nombre del token", utiliza la "Barra espaciadora". Para los ámbitos, selecciona el grupo "Proyectos" y haz clic en "Crear"... Puede que tengas que confirmar tus credenciales, pero una vez hecho, verás tu nuevo token.

Cópialo, y en `.env.local`, pégalo sobre el texto `API_TOKEN` en el DSN. De vuelta en Crowdin, "Cierra" el diálogo.

## Crear un proyecto Crowdin

Ahora necesitamos un ID de proyecto, ¡así que necesitamos un proyecto! En tu panel de control, haz clic en "Crear proyecto". Ponle el nombre "Barra Espaciadora" y mantenlo como privado. ¡Esto es algo delicado!

Elige el "Idioma de origen" - en nuestro caso, mantenlo como "Inglés".

En "Idiomas de destino", busca "Francés", el genérico, y selecciónalo. Ahora, "Español", también el genérico, y selecciónalo también.

Deja todo lo demás como está y haz clic en "Crear proyecto".

Vale, hay mucho que ver aquí... Sólo nos centraremos en un pequeño conjunto de estas características.

Para obtener el "ID del proyecto", haz clic en la pestaña "Panel de control" y, en la sección "Detalles", copia el "ID del proyecto".

De vuelta en nuestro archivo `.env.local`, selecciona `PROJECT_ID` y pégalo.

## Ajustar el mapeo de idiomas

De vuelta en Crowdin, haz clic en "Francés". Observa que en la URL de tu navegador aparece`fr`. Genial, coincide con el código regional francés de nuestro proyecto.

Vuelve atrás y haz clic en "Español". Observa que la URL muestra `es-ES`. Esto no coincide con nuestro proyecto. Sólo estamos utilizando `es`.

Al sincronizar las traducciones, esto causará un problema, ya que Crowdin no sabrá que nuestro `es` se corresponde con su `es-ES`. Por suerte, Crowdin tiene una forma de ayudarnos

De vuelta al panel de control del proyecto, busca y haz clic en la pestaña "Configuración". A la izquierda, elige "Idiomas". En la parte inferior, en "Añadir códigos de idioma personalizados", haz clic en "Mapeo de idioma".

En este diálogo, en el desplegable "Idioma", selecciona "Español". En "Marcador de posición", elige "locale", y en "Código personalizado", introduce "es". Haz clic en "Guardar". ¡Código de configuración regional asignado!

A continuación, subiremos nuestras traducciones al inglés a Crowdin, haremos algunas traducciones y volveremos a bajar las versiones traducidas.
