# Receta Symfony Flex

Lo dejamos con esta asquerosa experiencia de usuario al instalar nuestro bundle en una aplicación Symfony. ¡Vamos a crear una receta Symfony Flex para mejorar esto! La receta añadirá la entidad `Translation` necesaria, y la configurará.

En tu navegador, navega hasta el repositorio `symfony/recipes` en GitHub. Este es el repositorio oficial de recetas y está gestionado por el equipo central de Symfony. Hay otro repositorio de recetas: `symfony/recipes-contrib` que está impulsado por la comunidad. Los colaboradores pueden aprobar y fusionar tu receta.

Para añadir una receta, primero haz un fork del repositorio `symfony/recipes-contrib`. Yo ya tengo una bifurcación en `kbond/recipes-contrib`, así que navegaré hasta allí. Obtén la url remota de git haciendo clic en el desplegable "Código" y copiando el enlace "SSH".

En tu terminal, navega a un directorio limpio y clona con él tu fork:

```terminal
git clone <paste>
```

Navega hasta el directorio:

```terminal
cd recipes-contrib
```

Y crea una nueva rama para tu pull request:

```terminal
git checkout -b symfonycasts/object-translation-bundle
```

Ahora abre este directorio como proyecto en tu IDE. Yo lo tengo clonado en otro sitio y ya abierto en PhpStorm.

Verás esta gran lista de directorios. Cada directorio corresponde a una organización o nombre de usuario. Dentro de cada uno hay un directorio para cada nombre de paquete, y dentro de éste hay un directorio para cada versión del paquete. ¡Vamos a crear el nuestro!

En la raíz, crea un nuevo directorio y subdirectorio: `symfonycasts/object-translation-bundle`. Dentro de éste, crea otro directorio para la versión: `1.0`.

En lugar de crear la receta desde cero, voy a copiar una receta con una configuración similar: una entidad y algo de configuración. Copiaré el contenido de `zenstruck/messenger-monitor-bundle/0.5`y lo pegaré en nuestro nuevo directorio `1.0`.

Este archivo `manifest.json` es necesario para todas las recetas. Son las instrucciones de la receta. La sección `copy-from-recipe` indica a Flex que copie los directorios `config`y `src` de esta receta en el proyecto del usuario.

La sección `bundles` indica a Flex las clases del bundle que debe activar. De vuelta en la página GitHub de nuestro bundle, navegaré hasta `src/ObjectTranslationBundle.php`, copiaré el espacio de nombres y lo pegaré sobre el existente en el archivo de manifiesto, añadiendo `\\` al final. Ahora, copiaré y pegaré el nombre de la clase. Nuestro archivo de manifiesto está listo.

Pasamos al archivo `post-install.txt`. El contenido de este archivo se muestra en el terminal una vez instalado el paquete, como una forma de proporcionar los siguientes pasos al usuario. Lo modificaré para que haga referencia al nombre de nuestro bundle y enlace a nuestra documentación.

Por último, tengo que ajustar los archivos que se copian. En el directorio `src`, podemos eliminar`Controller`, y en `Entity`, cambiaré el nombre del archivo a `Translation.php`. Al abrir ese archivo, copiaré el contenido del README de nuestro bundle y lo pegaré.

En `config/packages`, cambiaré el nombre del archivo a `symfonycasts_object_translation.yaml`y pegaré la configuración del README de nuestro bundle.

¡Ya está listo!

## Crear la receta PR

De vuelta al terminal, navegaré hasta el directorio recipe-contrib que tengo abierto en PhpStorm, y ejecutaré:

```terminal
git add .
```

Para escanear los nuevos archivos. Luego ejecuta:

```terminal
git status
```

Para asegurarme de que he capturado todo. ¡Tiene buena pinta!

Ahora haré un push a mi fork en GitHub:

```terminal
git push origin symfonycasts/object-translation-bundle
```

¡Uy! Me olvidé de confirmar los cambios, así que ejecutaré:

```terminal
git commit -m "add symfonycasts/object-translation-bundle recipe"
```

Y push de nuevo...

De vuelta en el navegador, navegaré al repositorio `recipe-contrib` de Symfony. GitHub ha detectado la nueva rama en mi bifurcación y me pide que cree un pull request, así que haré clic en "Comparar y pull request".

Mejoraré un poco el título y en la descripción me pide la URL de Packagist. La buscaré, la copiaré y la pegaré.

Ahora, ¡"Crear pull request"!

Si espero unos segundos, aparece un bot que comenta automáticamente con instrucciones sobre cómo probar la receta. ¡Ya está!

De vuelta a mi terminal, en un directorio nuevo, crearé de nuevo esa aplicación temporal de Symfony:

```terminal
symfony new --webapp my-bundle-test
```

Y navego hasta ella:

```terminal
cd my-bundle-test
```

De vuelta en las instrucciones de la receta PR, copiaré este comando `export SYMFONY_ENDPOINT` y lo pegaré en mi terminal.

Esta variable de entorno cambia la búsqueda por defecto de la receta Symfony Flex por una ruta personalizada que incluye mi PR.

Ahora, copiaré el comando require de Composer para mi bundle y lo pegaré. Momento de la verdad...

Vale, ¡veo que se ha instalado el bundle y me pide que ejecute la receta! Eso es buena señal! Las recetas oficiales no te piden confirmación, pero las de `contrib` sí lo hacen por defecto.

Elijo "sí"... y... ¡no hay error! ¡Eso significa que nuestra receta ha funcionado como esperábamos!

También podemos ver el mensaje posterior a la instalación.

Confirmaré que la entidad se ha añadido ejecutando:

```terminal
ls src/Entity
```

Genial, aquí está el archivo `Translation.php`. Y si ejecuto:

```terminal
ls config/packages
```

Aquí está, veo el archivo `symfonycasts_object_translation.yaml`.

Ahora que hemos confirmado que la receta funciona, volvamos a nuestro RP, copiemos este comando `unset SYMFONY_ENDPOINT` y ejecutémoslo. Esto restablece la búsqueda de recetas a la ruta oficial de Symfony.

Y... Con esto terminamos este curso bundle No dudes en ayudarme a mejorar nuestro bundle enviando incidencias o peticiones en GitHub. ¡Nos vendrían bien más pruebas!

Espero que esta aventura haya sido divertida, útil y quizás un poco inspiradora.

Hasta la próxima, ¡feliz programación!
