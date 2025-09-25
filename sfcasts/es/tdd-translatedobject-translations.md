# TDD `TranslatedObject` Traducciones

La lógica actual de nuestro `TranslatedObject` es sólida. En efecto, pasa todas las llamadas a métodos y accesos a propiedades al objeto subyacente. Y... ¡tenemos las pruebas que lo demuestran!

Anteriormente utilizamos TDD para solucionar el problema de las llamadas a métodos Twig. Ahora, ¡utilicémoslo para crear una función!

Nuestro `TranslatedObject` necesita gestionar traducciones de propiedades. Antes de llamar al método o propiedad del objeto subyacente, debe comprobar si existe un valor traducido. Si existe, debe devolver ese valor traducido.

¡Manos a la obra!

## Empieza con pruebas verdes

Primero, antes de hacer nada, confirma que nuestro conjunto de pruebas es todo verde. En tu terminal, ejecuta:

```terminal
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Verde, ¡genial! Empezar una nueva función con TDD es una tontería si tus pruebas ya están fallando.

## Nueva prueba

De vuelta en `TranslatedObjectTest`, añade una nueva prueba con:`public function testCanTranslateProperties()`:

[[[ code('bbc3cef623') ]]]

Escribimos esta prueba con la lógica que queremos ver. Para ello, copia la fase de configuración de la prueba anterior y pégala aquí.

Ahora, para el segundo argumento del constructor `TranslatedObject`, éste será un array de propiedades traducidas. A continuación traduciremos todas las propiedades de nuestro`ObjectForTranslationStub`. Dentro de la matriz, escribe`'prop1' => 'translated1', 'prop2' => 'translated2', 'prop3' => 'translated3',`:

[[[ code('c2a46fe2d5') ]]]

Puedes ver que PhpStorm está marcando todo esto como gris, ya que el constructor aún no acepta este parámetro.

Creo que esto queda bastante bien. Cuando pasas un array de valores traducidos, con clave por nombre de propiedad, al acceder a estas propiedades, deberíamos obtener de vuelta los valores traducidos.

Para las afirmaciones, copia éstas de la primera prueba y pégalas aquí. Ahora, cambia todos los valores esperados de `value` a `translated`. `translated1`,`translated2`, y `translated3`:

[[[ code('19e77faf43') ]]]

Creo que todos sabemos que esto no va a funcionar pero... ¡dejemos que las pruebas nos lo digan!

En tu terminal, vuelve a ejecutar las pruebas:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

¡Fallo! Pero totalmente esperado. Fallo al afirmar que "valor1" es "traducido1" en la línea 36.

De vuelta a la prueba, en la línea 36 es donde accedemos a la propiedad `prop1`. Así que, ¡añadamos la lógica para que esta prueba pase!

Esta prueba... impulsa... nuestro desarrollo, ¿lo pillas?

## Inyectar traducciones

En `TranslatedObject`, añade una nueva propiedad al constructor:`private array $_translations,` - recuerda que el prefijo `_` es una convención que utilizamos porque se trata de un mixin.

[[[ code('7eb1bf0ede') ]]]

Arriba, añade un bloque doc `@param` para este nuevo parámetro, de tipo: `array`. Seamos inteligentes y especifiquemos los tipos de clave y valor de esta matriz. Dentro de los corchetes angulares, escribe `string,string`. El primer `string` es el tipo de clave, el nombre de la propiedad, y el segundo `string` es el tipo de valor, el valor traducido. Por último, escribe `$_translations` para terminar este doc block.

## Traducir el acceso a propiedades

Recuerda que nuestra prueba falla al acceder a una propiedad. Así que, abajo en el método`__get()`, antes de devolver la propiedad interna, escribe`$this->_translations[$name] ??`:

[[[ code('adf0845149') ]]]

Esto comprobará si existe un valor traducido para este nombre de propiedad. Si existe, lo devolverá. Si no, volverá a devolver la propiedad interna del objeto.

Bien, vuelve al terminal y ejecuta de nuevo las pruebas:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Sigue fallando... pero fíjate bien: ahora falla porque "traducido2" y "valor2" no coinciden, y en la línea 39.

Vuelve a la prueba. En la línea 39 es donde llamamos al método `prop2()`. Que haya llegado tan lejos significa que nuestra lógica de acceso a la propiedad traducida de la línea 36 ¡funciona! ¡Genial!

## Traducir llamadas a métodos

Ahora vamos a manejar las llamadas a métodos. En `TranslatedObject::__call()`, en la parte superior, añade `if (isset($this->_translations[$name]))`. Dentro,`return $this->_translations[$name];`.

[[[ code('cbedb20383') ]]]

Esto comprueba si existe un valor traducido para este nombre exacto de método. Si existe, devuelve ese valor.

¡Ya sabes lo que tienes que hacer! De vuelta al terminal, ejecuta de nuevo las pruebas:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Ahora falla en la línea 40 - "traducido3" y "valor3". Comprueba esta línea en nuestra prueba. Ahh... el método getter... Tenemos que tenerlo en cuenta, pero más o menos al revés de lo que hicimos con el problema de la llamada al método Twig. Tenemos que comprobar si el nombre del método existe como una propiedad traducida sin el prefijo `get`. ¡Intrincado!

## Traducir métodos Getter

Vuelve a comprobarlo en `TranslatedObject::__call()`. Este método se está haciendo un poco largo, así que vamos a refactorizarlo y añadir nuestra nueva lógica en un método privado. A continuación, escribe `private function translatedValue(string $name): ?string`:

[[[ code('fc2b1670c6') ]]]

Esto aceptará el nombre del método y devolverá el valor traducido como una cadena, o null si no existe.

Vuelve a `__call()`, corta la declaración `if (isset(...))` y pégala en nuestro nuevo método privado:

[[[ code('bc4fde836a') ]]]

Esto comprueba si el nombre exacto del método existe como propiedad traducida.

A continuación, escribe `if (!str_starts_with($name, 'get'))`. Esto comprueba si el nombre del método no es un getter. No hay nada que hacer en este caso, así que, `return null`:

[[[ code('bc4fde836a') ]]]

A continuación, escribe `$property = lcfirst(substr($name, 3))`. `substr` corta los 3 primeros caracteres del nombre -que sabemos que es `get`. `lcfirst` escribe el primer carácter en minúsculas, dejándonos el nombre de la propiedad:

[[[ code('c3ef62cce1') ]]]

Por último, `return $this->_translations[$property] ?? null`. Devuelve el valor traducido de esta propiedad si existe; si no, devuelve `null`:

[[[ code('86de53e9f7') ]]]

De vuelta a `__call()`, comprueba si existe un valor traducido con`if ($translatedValue = $this->translatedValue($name))`. Dentro,`return $translatedValue`:

[[[ code('e79b5dfa4a') ]]]

Ejecuta las pruebas, ¡ejecuta!

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Hmm, tenemos algunos errores. Desplázate un poco hacia arriba para ver el resumen. Las dos primeras pruebas dieron error, pero nuestra tercera prueba, la de las propiedades traducidas, está pasando. Son las pruebas originales las que tienen problemas. ¡Por eso era importante ejecutar las pruebas antes de iniciar esta función! Sabemos con seguridad que hicimos algo que rompió la funcionalidad existente.

## Arreglar la funcionalidad existente

Comprueba el error "Demasiados pocos argumentos para... TranslatedObject::__construct()"

Ahh, hemos añadido un nuevo parámetro obligatorio al constructor de esta clase. Las dos primeras pruebas no están pasando la matriz `$_translations`.

En nuestra clase de prueba, desplázate hasta las dos primeras pruebas. PhpStorm incluso nos advierte de esto. En ambas pruebas, pasa un array vacío como segundo argumento:

[[[ code('ef7e8b3d95') ]]]

¿Ya hemos terminado? Averígualo volviendo a ejecutar nuestras pruebas:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

¡Woo! ¡Todas las pruebas son correctas! ¡Nueva función añadida con éxito!

A continuación, daremos un paso al lado y veremos cómo marcaremos las entidades de nuestra aplicación para su traducción.
