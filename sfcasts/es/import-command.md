# Comando de importación de traducciones

Antes de abordar el comando de importación, me he dado cuenta de que hay un error en nuestro `ObjectTranslator`. Ábrelo y echa un vistazo al método `translate()`. Estamos utilizando un `WeakMap`, cuya clave es el objeto. El problema aquí es que si traduces un objeto con la configuración regional francesa y, más tarde, traduces el mismo objeto con la configuración regional española, obtendrás de vuelta la versión francesa, porque eso es lo que hay en el `WeakMap`.

Una solución sería incluir la configuración regional en la clave de la caché, pero `WeakMap`no admite claves complejas. Podríamos utilizar una estructura anidada: una matriz de `WeakMap`'s, cada una con la clave de la configuración regional. Pero para simplificar las cosas por ahora, vamos a eliminar por completo `WeakMap`. Creo que nuestro sistema de caché es lo suficientemente robusto como para que no tengamos problemas de rendimiento. Si lo hacemos, siempre podemos reintroducirlo más adelante.

Así pues, elimina la propiedad `$translatedObjects` y todas las referencias a ella que aparecen a continuación:

[[[ code('22fa9c6f10') ]]]

## El comando Importar

Bien, una vez aclarado esto, vamos a centrarnos en el comando de importación. En la carpeta `tutorial/`, copia `ObjectTranslationImportCommand.php`en el directorio `Command/` de nuestro bundle. Si no ves este archivo, puedes copiarlo desde el script que aparece a continuación:

[[[ code('e4fef51ea8') ]]]

Vamos a recorrerlo. Es bastante similar al comando exportar. En `configure()`, el primer argumento es el archivo CSV a importar, y el segundo argumento es la configuración regional para la que estamos importando.

En `execute()`, estamos cogiendo el `file` y el `locale` del`$input`, creando el objeto `$io`, y luego abriendo el archivo pasado como legible. Esta vez, creamos manualmente una barra de progreso en lugar de utilizar`progressIterate()`. Iniciamos la barra de progreso y, a continuación, utilizamos `fgetcsv` para pasar por la primera fila del archivo CSV (las cabeceras) que no queremos importar.

A continuación, hacemos un bucle sobre todas las filas del archivo CSV, expandiéndolas en variables, y luego las pasamos a este método `upsert()` aún no creado en nuestro gestor de mapeo. Todavía en este bucle, hacemos avanzar la barra de progreso y, finalmente, cerramos el archivo, terminamos la barra de progreso y mostramos un mensaje de éxito.

¡Genial! Vamos a conectarlo. En el archivo `services.php` de nuestro bundle, copia la definición del comando exportar y pégala a continuación. Corrige la sangría, renombra el id a `import_command` y cambia la clase a `ObjectTranslationImportCommand`:

[[[ code('1ae703f329') ]]]

## Implementación del método `upsert()` 

Ahora vamos con el método `upsert()`. De vuelta en nuestro comando, busca la llamada a `upsert()` y añade el método a `TranslatableMappingManager`. Establece todos los tipos de parámetros a `string` y el tipo de retorno a `void`:

[[[ code('0dc111daf8') ]]]

Si no estás familiarizado con el término "upsert", es una combinación de "update" e "insert". Significa actualizar un registro existente si existe, o insertar uno nuevo si no existe. Esto es exactamente lo que queremos hacer al importar traducciones.

En primer lugar, coge el "Gestor de objetos" de la clase de traducción del objeto utilizando`$om = $this->doctrine->getManagerForClass($this->translationClass)`:

[[[ code('8639412018') ]]]

Ahora intenta encontrar una traducción existente:`$translation = $om->getRepository($this->translationClass)->findOneBy()`. Para los criterios: `'objectType' => $type`, `'objectId' => $id`,`'locale' => $locale`, y `'field' => $field`.

[[[ code('3f630146a6') ]]]

Estas 4 propiedades identifican de forma única una traducción:

Si obtenemos una traducción de la base de datos, se trata de una actualización, si no, es una inserción.

Comprueba si esta traducción no existe con `if (!$translation)`. En este caso, tenemos que crear una nueva. Así que instanciamos un objeto traducción:`$translation = new ($this->translationClass)()`:

[[[ code('c79ef79704') ]]]

Sí, ¡puedes instanciar una clase con una variable como ésta!

Entra rápidamente en nuestra clase `Model/Translation`... genial, todas las propiedades son públicas. Así que, de vuelta a nuestro método `upsert()`, `$translation->objectType = $type`,`$translation->objectId = $id`, `$translation->locale = $locale`, y`$translation->field = $field`:

[[[ code('3f7a9c1644') ]]]

A continuación, establece el valor con `$translation->value = $value`:

[[[ code('166be118ba') ]]]

Por último, guarda la traducción con `$om->persist($translation)` y`$om->flush()`:

[[[ code('8397eb3b2b') ]]]

La llamada a persistir es necesaria para las traducciones nuevas, pero es seguro llamarla también para las existentes.

De vuelta al comando, el error de método indefinido ha desaparecido.

## Probar el comando Importar

¡Hora de probar! Si recuerdas el capítulo anterior, exportamos nuestras traducciones de objetos a este archivo `var/export.csv`. Tomé este archivo y traduje la columna `value` al español y al francés utilizando GitHub Copilot. En el directorio `tutorial/`, encontrarás [`import.es.csv`](https://raw.githubusercontent.com/SymfonyCasts/bundle-development/refs/heads/bundle/tutorial/import.es.csv) y [`import.fr.csv`](https://raw.githubusercontent.com/SymfonyCasts/bundle-development/refs/heads/bundle/tutorial/import.fr.csv) con los valores traducidos. Si no los ves, están enlazados en el script que aparece a continuación.

En el terminal, importa las traducciones al español ejecutando:

```terminal
symfony console object-translation:import tutorial/import.es.csv es
```

Genial, 15 traducciones importadas. Ahora las del francés:

```terminal
symfony console object-translation:import tutorial/import.fr.csv fr
```

Como hemos hecho cambios en la base de datos, necesitamos actualizar nuestra caché de traducciones. Por suerte, tenemos nuestro comando de calentamiento:

```terminal
symfony console object-translation:warmup
```

En el navegador, estamos en la página de inicio en francés y vemos aquí nuestros datos fijos en francés. Actualiza... y... ¡Genial! Todos los artículos están traducidos al francés. Cambiamos al español y... ¡Boom! ¡Artículos en español! Haz clic en un artículo para ver el contenido completo traducido.

Bien, ¡el código inicial del bundle para una versión 1.0 está listo! A continuación, vamos a empezar a trabajar en los metadatos necesarios para lanzar este bundle al mundo.
