# "Cableando" nuestro servicio Bundle

Hemos creado la clase que queremos utilizar como servicio en nuestro bundle, pero cuando intentamos inyectarla, obtenemos este error "no se puede autocablear el argumento...".

En tus aplicaciones finales, puede que estés acostumbrado a que esto funcione. Esto se debe a que la autoconexión de servicios está activada por defecto para todas las clases del directorio `src`de tu aplicación.

Los bundles, sin embargo, son otra historia. Si llevas usando Symfony tanto tiempo como yo, puede que recuerdes que en los viejos tiempos, ¡la autoconexión no existía! Tenías que definir manualmente cada... servicio en un archivo YAML o XML. Era duro...

Por suerte, para las aplicaciones, esto ya no es así (en su mayor parte). Pero aún tienes que hacerlo para los bundles. Esto se debe a que los bundles están pensados para ser reutilizables, y quieres dar al usuario final la mayor flexibilidad posible.

Sin embargo, todavía hay algunas mejoras en este aspecto.

## Crear un archivo de servicios

¿Recuerdas que creamos ese directorio vacío `config` en nuestro bundle? Aquí es donde definimos los servicios de nuestro bundle. En él, crea un archivo PHP normal (no una clase): `services.php`.

Si has escrito bundles en el pasado, puede que hayas utilizado XML para definir los servicios. Esta era la mejor práctica anterior. Pero hoy en día se prefiere utilizar PHP. En tus aplicaciones finales, si necesitas definir servicios manualmente, es probable que utilices YAML. No hay nada que te impida utilizar YAML, pero esto requeriría que tu bundle dependiera del componente YAML. Así pues, ¡que sea PHP!

Primero, añade `namespace Symfony\Component\DependencyInjection\Loader\Configurator`:

[[[ code('96b14330d1') ]]]

Esto nos permitirá utilizar las clases y funciones de ayuda a la definición del servicio que necesitamos sin necesidad de importar cada una de ellas.

A continuación, `return static function (ContainerConfigurator $container)`:

[[[ code('837a032f92') ]]]

Dentro, escribe `$container->services()`. Encadenaremos nuestras definiciones de servicio a partir de esto:

[[[ code('837a032f92') ]]]

## Definir los servicios del bundle

¡Ahora viene la parte divertida! Añade `->set()` - el primer argumento es el ID del servicio. En tus aplicaciones, suele ser el nombre de la clase, pero en los bundles, utilizamos una cadena sin formato - de nuevo, para obtener la máxima flexibilidad. Este ID tiene que ser único, así que ponle como prefijo un espacio de nombres que tenga sentido para tu bundle. En este caso, utilizaremos `symfonycasts.`. Ahora el nombre de nuestro servicio: `object_translator`. El segundo argumento es el nombre completo de la clase: `ObjectTranslator::class`
(asegúrate de importarlo):

[[[ code('97fb40b378') ]]]

## Informar a Symfony sobre el servicio

Ahora tenemos este archivo... más o menos... aleatorio `services.php` en nuestro bundle, ¡así que tenemos que decírselo a Symfony!

En `ObjectTranslationBundle`, anula el método `loadExtension()` de`AbstractBundle`. Este método es llamado cuando Symfony carga el bundle.

El método padre está vacío, así que podemos eliminar esta llamada. Importa nuestro archivo`services.php` utilizando `$container->import()`. La ruta es relativa a nuestro archivo actual, así que escribe `../config/services.php`:

[[[ code('97fb40b378') ]]]

¿Esto es todo lo que necesitamos? Veamos. Vuelve al navegador y actualiza la página de error. Hmm, el mismo error. Pero ahora tenemos más detalles: "Tal vez deberías asignar un alias de esta clase al servicio `symfonycasts.object_translator`existente"

Esto sigue siendo un progreso: puesto que Symfony sugiere el ID del servicio, ¡eso significa que lo conoce!

## Alias de nuestro servicio

En `ArticleController::show()`, donde estamos intentando inyectar `ObjectTranslator`, podríamos añadir el atributo `#[Autowire]` con el ID del servicio... Esto funcionaría... ¡pero podemos hacerlo mejor! Quiero que este servicio sea autoconectable. Lo hacemos estableciendo el nombre de la clase como alias del servicio.

En `services.php`, debajo de `set()`, escribe `->alias()`. El primer argumento es el alias que queremos crear: `ObjectTranslator::class`. El segundo argumento es el ID del servicio que definimos antes: `symfonycasts.object_translator`:

[[[ code('3fa31097b8') ]]]

De vuelta en el navegador, actualiza. Y... sigue apareciendo un error, pero diferente. "Demasiados pocos argumentos pasados a ObjectTranslator".

## Definir los argumentos del servicio

Recuerda que en `ObjectTranslator`, tenemos dos dependencias necesarias:`LocaleAwareInterface`, un servicio, y `$defaultLocale`, un parámetro contenedor. Tenemos que decirle a Symfony cómo suministrarlos.

Sé que podemos autoconectar `LocaleAwareInterface`, pero en los bundles, necesitamos configurarlo manualmente, por lo que necesitamos su ID de servicio. Para encontrarlo, en tu terminal, ejecuta:

```terminal
symfony console debug:autowiring LocaleAware
```

No necesitamos el nombre completo, basta con la primera parte. Perfecto, aquí está: `translation.locale_switcher`. Cópialo.

Vuelve a `services.php`, justo debajo de `set()`, haz una sangría para mantener esto organizado, y escribe `->args()` con una matriz. Estos elementos coinciden con el orden de los argumentos del constructor, así que el primer argumento es el servicio. Utiliza la función `service()` y pega el ID del servicio que acabamos de encontrar:

[[[ code('0e13ecf373') ]]]

Ahora el segundo argumento, `$defaultLocale`. Se trata de un parámetro contenedor. Podemos listar todos los parámetros en el terminal ejecutando:

```terminal
symfony console debug:container --parameters
```

Lista grande... fíltralo ejecutando el mismo comando pero con `| grep locale` al final:

```terminal-silent
symfony console debug:container --parameters | grep locale
```

`kernel.default_locale` ¡es lo que buscamos! Cópialo.

De vuelta en `services.php`, para el segundo elemento de la matriz `args`, utiliza la función `param()` y... pega:

[[[ code('84fd51c09f') ]]]

Volvemos a nuestro navegador... refrescamos... ¡y éxito! ¡Ningún error significa que el servicio está correctamente definido e inyectado!

A continuación, veremos cómo nuestro bundle almacenará las traducciones.
