# Comportamiento del Getter TDD

Tenemos nuestra primera prueba para `TranslatedObject`, que es superadora. Cuando escribimos esa prueba, no utilizamos realmente el Desarrollo Dirigido por Pruebas, porque la lógica ya existía, sólo la confirmamos con una prueba.

El verdadero TDD es cuando escribes una prueba para algo que aún no existe, de la forma que quieres que funcione, ves que falla y luego escribes el código para que pase. Así que, ¡hagámoslo ahora!

## Otra prueba

Queremos que `TranslatedObject::__call()` compruebe primero si hay un getter disponible. Al llamar a `title()`, si este método no existe en el objeto interno, intenta llamar a `getTitle()`.

En `TranslatedObjectTest`, debajo de nuestra primera prueba, crea una nueva`public function testCallUsesGetterIfAvailable()`:

[[[ code('6f0252b83c') ]]]

Dentro, nuestra configuración será la misma, así que copia el `$object =` de la prueba anterior y pégalo aquí:

[[[ code('2c2dfd35a6') ]]]

Ahora la aserción. En nuestro objeto stub, tenemos una propiedad `prop3` con un getter: `getProp3()`. Por lo tanto, si llamamos al método `prop3()` en nuestro wrapper (sin el `get`) queremos reenviar esta llamada a `getProp3()` en el objeto interno.

`$this->assertSame('value3', $object->prop3())` - ¡Ya está!

[[[ code('f26a67c726') ]]]

En tu terminal, ejecuta las pruebas con:

```terminal
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

Esta prueba falla - ¡pero eso es lo que esperamos! Esto coincide básicamente con el error que vemos en Twig. Ahora tenemos que escribir el código para que pase.

## Implementar la lógica

En `TranslatedObject::__call()`, al principio del método, pon`$method = $name`. Ahora, añade una comprobación: `if (!method_exists($this->_inner, $name))`. Dentro, escribe `$method = 'get'.ucfirst($name)` - esto pone en mayúscula la primera letra del nombre y antepone `get`:

[[[ code('f9667acaa5') ]]]

Abajo, en `return`, cambia `$name` por `$method`. ¡Ya está!

[[[ code('cf1378fce9') ]]]

## Verificar el comportamiento

De nuevo en tu terminal, ejecuta de nuevo las pruebas:

```terminal-silent
symfony php vendor/bin/phpunit object-translation-bundle/tests
```

¡Verde! TDDS - ¡Éxito en el Desarrollo Orientado a Pruebas!

Observa que nuestras dos pruebas han sido superadas, lo que es importante porque significa que no hemos roto ninguna funcionalidad existente.

Ahora, la verdadera prueba: veamos si esto soluciona nuestro problema con Twig.

En tu navegador, en la página del artículo que tiene el error, actualiza... ¡y perfecto! El error ha desaparecido. El título y el contenido se extraen correctamente del objeto subyacente.

Probablemente haya más casos extremos que debamos tener en cuenta, pero éste es un buen comienzo. Ahora que tenemos esta prueba, si encontramos un caso extremo, podemos añadir una prueba para él, ver si falla, y luego implementar la lógica para que pase. ¡Bien de TDD!

A continuación, volvamos a utilizar TDD para desarrollar una función: ¡las traducciones de objetos reales!
