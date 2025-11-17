# Orden de calentamiento de la caché

De acuerdo, por defecto, nuestras traducciones de objetos se almacenan en caché indefinidamente. Podemos configurar una caducidad, pero ¿y si quisiéramos forzar una actualización anticipada? Quizás hemos actualizado la base de datos con nuevas traducciones, y queremos que el sitio lo refleje. Claro, podríamos borrar toda la caché o invalidar la etiqueta de caché. Pero esto podría afectar a miles de traducciones y, en un sitio con mucho tráfico, podría provocar ralentizaciones mientras se repobla la caché.

Creemos una función de calentamiento de la caché. Esto borrará cada elemento de la caché y simultáneamente la refrescará con la última versión. Para ello, crearemos un comando de consola.

En tu IDE, dentro de nuestro directorio `object-translation-bundle`'s `src`, crea una nueva clase PHP. Llámala `ObjectTranslationWarmupCommand`. Estará en el espacio de nombres `Command`. Con PhpStorm, podemos actualizar el espacio de nombres aquí, y lo creará en el directorio adecuado.

## Configurar el comando

Allá vamos. Primero, marca la clase como `final` y `@internal`. El comando en sí será público, pero el código del comando será interno.

A continuación, haz que extienda `Command` del componente de consola `Symfony`. En la parte superior, añade el atributo `AsCommand`. Nombre: `object-translation:warmup`. Descripción: `Warms up the object translation cache.`.

Dentro de la clase, anula dos métodos: el constructor y `execute()`.

En el constructor, no queremos este argumento `$name`, pero aún así tenemos que llamar al constructor padre, sólo que sin argumentos.

[[[ code('6ee9243bfc') ]]]

## Inyectar servicios

Haz sitio e inyecta los siguientes servicios:`private ObjectTranslator $translator` y `private TranslatableMappingManager $mappingManager`.

A continuación, `private LocaleSwitcher $localeSwitcher`, un servicio especial que nos permite cambiar temporalmente la configuración regional de toda la aplicación mientras ejecutamos código. Esto será útil para calentar las traducciones en todas las locales disponibles.

Por último, necesitamos esas configuraciones regionales, así que inyecta `private array $locales`:

[[[ code('8d39d03bff') ]]]

## `execute()` Método

Abajo en `execute()`, elimina la llamada al método padre, ya que está vacío. Este método debe devolver un número entero como código de estado. Así que, antes de que se nos olvide, devuelve `self::SUCCESS`.

Arriba, escribe, `$io = new SymfonyStyle($input, $output)`. Esto nos proporciona una bonita forma de interactuar con la consola utilizando el estilo recomendado por Symfony.

Añade un título para este comando: `$io->title('Warming up Object Translation Cache')`.

Inicia una variable de recuento: `$count = 0`. Esto nos ayudará a llevar la cuenta de cuántos objetos hemos calentado.

[[[ code('2079d12878') ]]]

## Iterar sobre objetos traducibles

Necesitamos una forma de obtener todos los objetos traducibles. Así que, en nuestro `TranslatableMappingManager`, crea un nuevo método: `public function allTranslatableObjects()`. Tipo de retorno: `iterable`:

[[[ code('2e25f65634') ]]]

Primero, recorre los gestores de objetos: `foreach ($this->doctrine->getManagers() as $om)`:

[[[ code('e6ff9ceb9b') ]]]

Las aplicaciones complejas pueden tener varios gestores de objetos, así que esto nos asegurará que los cubrimos todos.

Dentro de esto, necesitamos obtener todas las clases de entidad que admite este gestor. Así que `foreach ($om->getMetadataFactory()->getAllMetadata() as $metadata)`:

[[[ code('1360732d4b') ]]]

Aquí, obtén el nombre de la clase de entidad con `$class = $metadata->getName()`. Ahora tenemos que omitir las clases que no son traducibles. Hazlo con`if (!(new \ReflectionClass($class))->getAttributes(Translatable::class))`. Esto devolverá una matriz vacía si la clase no tiene el atributo `Translatable`. En este caso, `continue` para pasar a la siguiente clase:

[[[ code('a51884ed46') ]]]

Ahora sabemos que tenemos una clase traducible, así que escribe:`yield from $this->doctrine->getRepository($class)->findAll()`:

[[[ code('daea41bfc6') ]]]

Estamos devolviendo un generador que itera sobre todos los objetos traducibles en todos los gestores de objetos.

Vale, esta no es la forma más eficiente de hacerlo, ya que estamos cargando todas las entidades en memoria - potencialmente 10's de miles... Tomemos nota de esto como una futura mejora.

Volviendo a nuestro comando, `SymfonyStyle` tiene un método superútil para iterar cosas con una barra de progreso. Después de iniciar la variable `$count`, escribe:`foreach ($io->progressIterate($this->mappingManager->allTranslatableObjects()) as $object)`:

[[[ code('f14fbc79e4') ]]]

`progressIterate` esencialmente envuelve el iterable que le demos, y maneja la salida de la barra de progreso en el terminal por nosotros.

Dentro, crea un bucle anidado para las localizaciones: `foreach ($this->locales as $locale)`:

[[[ code('ac38d95687') ]]]

Ahora, escribe `$this->localeSwitcher->runWithLocale()`. Este método toma dos argumentos: la `$locale` a la que queremos cambiar, y un callable. Para ello `use ($object, $locale)`:

[[[ code('282e033682') ]]]

Cambiará la configuración regional de toda la aplicación mientras dure la llamada. Después, volverá a la configuración anterior.

Dentro de la llamada, `$this->translator->`... ¿Dónde está mi autocompletado? Olvidé importar la clase `ObjectTranslator`. Ya está.

Vuelve abajo, escribe `translate($object, $locale)`:

[[[ code('91382b6321') ]]]

Al final del bucle exterior, aumenta la cuenta con `$count++`:

[[[ code('8e72481ee9') ]]]

Por último, antes de volver, añade un mensaje de resumen de éxito:`$io->success("Warmed up the cache for {$count} translatable objects.")`:

[[[ code('314d261a3c') ]]]

## Sobreescribiendo la configuración regional en `translate()`

Arriba, donde estamos llamando a `translate()`, PhpStorm no está contento con este argumento`$locale`. Eso es porque no existe en la firma del método.

Entra en `ObjectTranslator::translate()` y añádelo:`?string $locale = null`:

[[[ code('8ad3b66da9') ]]]

Donde estamos obteniendo la configuración regional actual, intenta utilizar primero la configuración regional pasada: `$locale ??`:

[[[ code('7ca9d3ce2c') ]]]

Si se pasa, se utilizará; si no, se extraerá de la petición.

De vuelta a nuestro comando, ¡PhpStorm está contento!

A continuación, conectaremos este comando y lo probaremos
