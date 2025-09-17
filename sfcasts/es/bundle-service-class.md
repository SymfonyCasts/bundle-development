# Clase de servicio bundle

Bien, nuestro bundle está instalado y listo para funcionar. Es hora de añadir algo de funcionalidad añadiendo nuestra primera clase de servicio. Ésta será la estrella del espectáculo. Nuestro bundle es para traducir objetos, así que éste parece el lugar adecuado para empezar.

En el directorio `src` de nuestro bundle, crea una nueva clase PHP: `ObjectTranslator`.

## `ObjectTranslator`

En primer lugar, marca esta clase como final. No está pensada para ser ampliada. Al desarrollar bundles, es importante ser explícito sobre el diseño de tus clases y sus intenciones. Esto facilita mantener la compatibilidad hacia atrás. Eliminar `final`más adelante no es un cambio de ruptura, pero añadir `final` sí lo es. Exploraremos más trucos de este tipo a medida que avancemos:

[[[ code('eda36dbdac') ]]]

Crear un método: `public function translate(object $object)`. Tipo de retorno: `object`. Con el tiempo, albergará la lógica para traducir objetos. Por ahora, sólo devuelve el`$object` pasado:

[[[ code('afffbf9ff1') ]]]

Nuestro servicio necesita un constructor para inyectar algunas cosas. Añade `public
function __construct(private LocaleAwareInterface $localeAware, private
string $defaultLocale)`. Necesitamos el servicio `LocaleAwareInterface` para obtener la configuración regional actual de la petición, y también necesitaremos la configuración regional por defecto de nuestra aplicación:

[[[ code('f134be4b58') ]]]

Abajo, en `translate()`, podemos añadir algo de lógica sencilla. Obtén la configuración regional actual con`$locale = $this->localeAware->getLocale()`. Ahora bien, si la configuración regional actual es la misma que la configuración regional por defecto, no necesitamos hacer ninguna traducción, así que añade un `if ($this->defaultLocale === $locale)` y, en este caso, sólo devuelve el objeto sin procesar:

[[[ code('0bf83206dc') ]]]

A continuación es donde añadiremos la lógica de traducción real, pero de momento añade un comentario: `todo translate object`:

[[[ code('7eb0b0c396') ]]]

Utilicemos este nuevo servicio en `ArticleController::show()`. Amplía un poco este método e inyéctalo: `ObjectTranslator $translator`:

[[[ code('fde71e6395') ]]]

Ejecuta el `Article` inyectado a través de nuestro nuevo servicio:`$article = $translator->translate($article)`:

[[[ code('f1f1395dd8') ]]]

¡Genial!

## Genéricos PHP

Observa que si intentamos acceder a un método de `$article` antes de ejecutarlo a través de nuestro servicio, PhpStorm puede autocompletar todos los métodos de `$article`. Pero... si intentamos acceder a un método en `$article` después de ejecutarlo a través de`$translator->translate()`, no tenemos autocompletado. PhpStorm no tiene ni idea de qué es ahora `$article` - sólo que es "un objeto". Esto es un fastidio... ¡Pero podemos arreglarlo con los genéricos de PHP!

Los genéricos son una forma de proporcionar información de tipo adicional a nuestro editor.

Fíjate en esto: encima de `ObjectTranslator::translate()`, genera algunos bloques doc. Esto sólo coincide con la firma del método y no es superútil... así que añade`@template T of object` encima. Esto declara un tipo de plantilla `T` que debe ser un objeto. `T` es como un alias, o marcador de posición y puede ser cualquier cadena.

Ahora, para `@param` y `@return`, sustituye `object` por `T`:

[[[ code('3a1638f44e') ]]]

Esto le dice a nuestro editor: "Sea cual sea el tipo de objeto que se pase a este método, el tipo de retorno será el mismo tipo de objeto"

De vuelta en `ArticleController::show()`, después de llamar a `translate()`, intenta autocompletar de nuevo en `$article`. ¡Pum! Ahora PhpStorm sabe exactamente qué es `$article`. ¡Esto me encanta!

Elimina ese código extra - el artículo traducido se pasa ahora a nuestra plantilla por lo que nuestro trabajo aquí está hecho.

Volvemos a nuestro navegador y visitamos la página del artículo... Un error... "No se puede autohilar el argumento $translator..."

Symfony no conoce el servicio de nuestro bundle - sigue siendo una simple clase PHP...

¡Arreglémoslo a continuación!
