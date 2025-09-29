# Obtener el ID de objeto con Doctrine

Actualmente, nuestros datos de traducción al francés se muestran correctamente en la página. Sin embargo, nuestro método actual de obtención del ID mediante`$object->getId()` es un poco inestable. Supone que el usuario siempre tiene un método `getId()`en sus entidades, lo que no siempre es el caso. Podemos hacerlo mejor... ¡y Doctrine nos cubre las espaldas!

## Obtener el gestor de objetos

En nuestro método `ObjectTranslator::translationsFor()`, encima de obtener las traducciones, haz algo de espacio. En primer lugar, obtén el gestor de objetos de Doctrine:`$om = $this->doctrine->getManagerForClass()`. Esto necesita un nombre de clase, así que utiliza la clase del objeto pasado: `$object::class`.

Cuando utilices el ORM, habrás utilizado algo llamado gestor de entidades. Esto es lo que tenemos aquí: el gestor de objetos es una interfaz más general para él. De nuevo, utilizar estas interfaces abstractas hace que nuestro bundle sea más flexible.

`getManagerForClass()` puede devolver null, así que compruébalo`if (!$om) { throw new \LogicException(sprintf('No object manager found for class "%s".', $object::class)); }`

## Obtener el ID del Gestor de Objetos

A continuación, `$id = $om->getClassMetadata($object::class)`. Esto devuelve un objeto especial que lo sabe todo sobre el mapeo de Doctrine para esta clase. `->getIdentifierValues()`
es lo que queremos. Pasa la instancia `$object`. Esto recupera el ID del objeto pasado, independientemente de cómo esté implementado.

`dd($id)` para ver de qué se trata. En el navegador, actualiza. Hmm... Es una matriz de valores... Doctrine admite ID compuestos, lo que básicamente significa múltiples campos de ID para una entidad. Se trata de una función bastante avanzada, y nuestro bundle, al menos inicialmente, no la admite.

Aunque tu entidad sólo tenga un campo ID, Doctrine lo devuelve como una matriz. Elimina el `dd()` y añade una comprobación: `if (count($id) > 1)`. Dentro,`throw new \LogicException(sprintf('Class "%s" must have a single identifier to be translatable', $object::class))`.

Coge el primer elemento del array con `$id = reset($id)`. `dd()` que... y refresca el navegador. Perfecto, ¡un solo valor!

Elimina el `dd()` y abajo en nuestra matriz `findBy()`, sustituye `$object->getId()` por`$id`. Pruébalo en el navegador... ¡y funcionó!

Nuestro `ObjectTranslator` funciona, y es bastante sólido. A continuación, ¡creemos un filtro Twig para él!
