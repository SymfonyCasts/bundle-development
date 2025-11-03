# Refactorización de `ObjectTranslator`

Nuestro `ObjectTranslator` está creciendo un poco, y nuestro método `translationsFor`se ha hinchado. Para hacer frente a esto, crearemos un nuevo servicio que se encargará de todas las tareas relacionadas con Doctrine. Esto implica ocuparse de la clase `Translation` y de todas las demás operaciones Doctrine que estamos utilizando.

En primer lugar, crea la nueva clase en el directorio `src` de nuestro bundle, llámala `TranslatableMappingManager`. Hay que reconocer que el término "gestor" es un poco rebuscado cuando no estoy seguro de cómo llamar a algo, pero por ahora servirá. Marcaremos esta clase como `final` y `@internal`.

[[[ code('9ffeb8acc0') ]]]

De este modo, si más adelante se nos ocurre un nombre mejor, podremos renombrarla fácilmente.

## `translatableTypeFor` Método

En primer lugar, crea un método llamado `public function translatableTypeFor`. Este método tomará un objeto como parámetro, `object $object` y devolverá una cadena.

En `ObjectTranslator`, busca `translationsFor` y copia la lógica relacionada con el tipo. Pégala en nuestro nuevo método `translatableTypeFor`, e importa la clase necesaria. En la parte inferior, `return $type`:

[[[ code('87f796794b') ]]]

## El Constructor

A continuación, crea un constructor para esta clase... y ábrelo para dejarnos espacio. Ahora copia las propiedades relacionadas con la Doctrine del constructor de `ObjectTranslator`(`$translationClass` y `$doctrine`) y pégalas en nuestro nuevo constructor:

[[[ code('a3a95f581f') ]]]

## `idFor` Método

Esto allana el camino para nuestro siguiente método, `public function idFor()`, que una vez más tomará un objeto y devolverá una cadena. Para ello, volveremos a`translationsFor` en `ObjectTranslator`, copiaremos la lógica para obtener el ID y la pegaremos en nuestro nuevo método. Al final... `return $id`. PhpStorm me dice que podemos inlinear el retorno. Así que, `return reset($id)` y elimina el retorno a continuación:

[[[ code('847c19b926') ]]]

## `translationsFor` Método

Por último, crea otro método: `public function translationsFor()`. Este aceptará tres parámetros: `string $locale`, `string $type`, `string $id`y devolverá un `array`. Dentro de este método, coge la lógica que obtiene y normaliza las traducciones en `ObjectTranslator::translationsFor()`, y pégala aquí:

[[[ code('8c139d6532') ]]]

## Usando `TranslatableMappingManager` en `ObjectTranslator`

Ahora que tenemos nuestra nueva clase, inyéctala en `ObjectTranslator`. En el constructor, sustituye las dos propiedades Doctrine por`private TranslatableMappingManager $mappingManager`:

[[[ code('63fa273f42') ]]]

En primer lugar, sustituye la lógica de obtención de tipos por`$type = $this->mappingManager->translatableTypeFor($object)`:

[[[ code('69348b8b01') ]]]

A continuación, sustituye el código Doctrine para obtener el id por`$id = $this->mappingManager->idFor($object)`:

[[[ code('5008572d22') ]]]

Por último, en la llamada a la caché, sustituye la lógica de Doctrine para obtener y normalizar las traducciones por`return $this->mappingManager->translationsFor($locale, $type, $id)`:

[[[ code('cddeb100d6') ]]]

¡Muy bien!

## Actualización de las definiciones de servicio

Hemos refactorizado el código, pero necesitamos actualizar nuestras definiciones de servicio.

En `services.php`, añade nuestro nuevo servicio con`->set('.`, para convertirlo en un servicio oculto,`symfonycasts.object_translator.mapping_manager`. Para los args, utiliza`->args([])` y expande. En la definición `ObjectTranslator` anterior, corta los argumentos relacionados con Doctrine y pégalos como argumentos de nuestro nuevo servicio:

[[[ code('c090bc5e91') ]]]

En la definición `ObjectTranslator`, añade un nuevo argumento:`service('.symfonycasts.object_translator.mapping_manager')`:

[[[ code('8443a22d07') ]]]

¡Me encanta el autocompletado!

## Ajustar el proceso de configuración

Por último, ya que hemos ajustado los argumentos, tenemos que actualizar el procesamiento de la configuración. Éste es el último paso, ¡lo juro!

Abre `ObjectTranslationBundle` y busca nuestro método `loadExtension()`.

En nuestra configuración de caché, estos índices de argumentos se han desplazado. Echa un vistazo al constructor `ObjectTranslator` para averiguar los nuevos índices. 0, 1, 2, 3, 4. En `loadExtension()` actualiza estas dos llamadas a `setArgument()`para que utilicen `3, 4` en lugar de `5, 6`:

[[[ code('23613c9d26') ]]]

Hay que trasladar el `translation_class` a nuestro nuevo servicio. Por tanto, escribe`$builder->getDefinition('symfonycasts.object_translator.mapping_manager')`. Copia la llamada `setArgument` anterior y pégala aquí. Para el índice, comprueba el constructor de`TranslatableMappingManager`. Es `0`, así que vuelve a`loadExtension()`, cambia el índice a `0` y elimina la variable de arriba:

[[[ code('091cdbcc01') ]]]

¡Uf! Eso es todo en cuanto a la refactorización. Vamos a probarlo.

En el navegador, actualiza la página de inicio en francés... y... ¡Genial! ¡Las traducciones siguen funcionando!

A continuación, nos sumergiremos en los comandos de la consola de bundle, empezando por el comando de calentar la caché de traducción.
