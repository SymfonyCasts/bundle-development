# Envoltorio de objetos traducidos

Bien, ya tenemos una forma de almacenar nuestras traducciones. Vamos a ver cómo podemos utilizarlas. Si abres nuestro `ObjectTranslator`, tenemos este todo esperándonos.

Hay un par de formas de enfocar esto. Una forma es modificar directamente las propiedades del objeto. Pero como se trata de una entidad Doctrine, se marcará como modificada. Si lo modificas sin querer, sobrescribirás tus traducciones de configuración regional por defecto en la propia entidad. Definitivamente, no queremos eso.

Otro enfoque sería clonar primero el objeto y luego modificar las propiedades. Esto es mejor... pero si accidentalmente intentas persistir este nuevo objeto, podría guardar una nueva versión en la base de datos, lo que sería malo.

Exploremos mi opción preferida: crear una envoltura para el objeto, puedes pensar en esto como un modelo de vista. Esta envoltura pasará todas las llamadas a métodos y accesos a propiedades al objeto subyacente. Entonces, cuando se acceda a una propiedad, primero comprobará si existe una traducción para esa propiedad. Si es así, devolverá el valor traducido; en caso contrario, devolverá el valor original.

## `TranslatedObject` Clase

Primero, en el directorio `src` de tu bundle, crea una nueva clase llamada `TranslatedObject`. Márcala como `final` y crea un constructor. Aceptará un único parámetro,`private object $_inner`:

[[[ code('1b2b540462') ]]]

El guión bajo es una convención para garantizar que no haya conflictos al llamar a métodos o propiedades de esta envoltura.

## Añadir genéricos para mejorar la compatibilidad con el IDE

Vamos a utilizar de nuevo los genéricos de PHP, pero a nivel de clase. Añade un bloque doc de clase y añade `@template T of object`. A continuación, conviértelo en un mixin añadiendo `@mixin T`:

[[[ code('c9ee64bd1a') ]]]

Esto significa esencialmente que este objeto tendrá todos los mismos métodos y propiedades que el objeto de plantilla `T`.

A continuación, añade un bloque doc al constructor, y en lugar de `@param object`, sustitúyelo por `@param T`:

[[[ code('a2daed4ab1') ]]]

Esto permite a PHPStorm saber que estamos inyectando este objeto plantilla a nivel de clase aquí.

## ¡Mágico!

Entonces, ¿cómo reenviamos las llamadas a métodos y propiedades al objeto interno? ¡Métodos mágicos! Anula tres métodos: `__get()`, `__isset()`, y `__call()`.

`__call()` se llama cuando se utiliza un método que no existe en este objeto. Establece el tipo de retorno en `mixed`. En el interior, escribe`return $this->_inner->$name(...$arguments)`.

[[[ code('08ecf400eb') ]]]

Esto reenvía cualquier llamada a un método al objeto interior utilizando la variable `$name` como nombre del método. ¡PHP es así de guay!

`__get()` se invoca cuando se intenta acceder a una propiedad de este objeto que no existe. Establece el tipo de retorno en `mixed`, y dentro, reenvía al objeto interno con `return $this->_inner->$name`.

[[[ code('612478e939') ]]]

De nuevo, utilizando la variable `$name` como nombre de la propiedad.

Por último, se llama a `__isset()` cuando se utiliza `isset()` en una propiedad que no existe. Reenvía la llamada con `return isset($this->_inner->$name)`:

[[[ code('45a0c40dc7') ]]]

Ahora bien, si ya has escrito antes este tipo de objetos, puede que estés pensando: "Espera un momento, ¿qué pasa con el método mágico `__set()`?" Buena pregunta! Queremos que esto sea una envoltura de sólo lectura, así que nos saltaremos este método mágico.

## Utilizando `TranslatedObject`

¡Pongamos a trabajar esta nueva clase! Ve a `ObjectTranslator::translate()` y elimina el todo. Envuelve este `$object` en `new TranslatedObject()`:

[[[ code('0f318a39f4') ]]]

Recuerda, en nuestro método `ArticleController::show()`, estamos pasando el objeto artículo por este traductor y pasándoselo a Twig.

¡Es hora de probarlo! En tu navegador, visita una página de artículo. Vale, hasta aquí todo bien... Pero estamos en la versión inglesa, nuestra configuración regional por defecto. Recuerda que en`ObjectTranslator::translate()`, si estamos en la configuración regional por defecto, estamos devolviendo el objeto tal cual. No estamos utilizando nuestra nueva clase `TranslatedObject`.

Así que, de vuelta en el navegador, cambia a la versión francesa... Uh oh, un error: "Llamada a método indefinido `Article::title()`". Hmm, esto funcionó sin problemas con el objeto artículo real. ¿Qué ocurre aquí?

Se trata de un matiz de Twig que tendremos que tener en cuenta en nuestra envoltura. Nos ocuparemos de ello a continuación, ¡y además con una prueba unitaria!
