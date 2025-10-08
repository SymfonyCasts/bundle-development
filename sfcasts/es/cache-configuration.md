# Configuración de la caché

¡Nuestras traducciones de objetos se están almacenando en caché con éxito! Ahora quiero permitir que el usuario configure su grupo de caché y el tiempo que desea almacenar en caché las traducciones. Para ello, vamos a utilizar a nuestro viejo amigo, la configuración de bundle.

## Un ArrayNode

Abre nuestro `ObjectTranslationBundle` y busca el método `configure()`. Permitiremos dos opciones para la caché: la reserva de caché a utilizar, y el tiempo de vida o ttl - cuánto tiempo queremos que la caché sea válida. Podemos agrupar estas dos opciones.

Debajo del `->stringNode()`'s `->end()`, añade un `->arrayNode()` y llámalo `cache`. Ciérralo con un `->end()`, y añade un espacio. Añade una descripción con `->info('Cache settings for object translations.')`.

Quiero que el usuario pueda desactivar todo este nodo para evitar por completo el almacenamiento en caché. Hay un atajo para esto: `->canBeDisabled()`. Esto añade automáticamente una opción booleana `enabled` y por defecto `true`. 

***TIP
También hay un método `canBeEnabled()` si quieres que el valor por defecto sea`false`.
***

## Añadir hijos

Ahora añade las dos subopciones: Escribe `->children()` y ciérralo con un`->end()`.

El primer hijo será `->stringNode('pool')->end()`. Dentro,`->info('The cache pool to use for storing object translations.')`. Sé que todas las aplicaciones Symfony tienen un grupo `cache.app`, así que úsalo como valor por defecto con `->defaultValue('cache.app')`.

A continuación, añade un `->integerNode('ttl')->end()` y dentro,`->info('The time-to-live for cached translations, in seconds. null for no expiration')`. Aunque se trata de un nodo entero, a menos que lo desautoricemos explícitamente, se puede utilizar `null`. Por defecto, no quiero caducidad, así que `->defaultNull()`.

## Depurar la configuración

Comprueba que todo parece correcto saltando a tu terminal y ejecutando:

```terminal
symfony console config:dump-reference symfonycasts_object_translation
```

¡Bonita y bien documentada configuración! Comprueba la opción `enabled` añadida automáticamente por `canBeDisabled()`.

Hay otro comando que muestra la configuración actual (y todos los valores por defecto) de un bundle. Ejecuta:

```terminal
symfony console debug:config symfonycasts_object_translation
```

Genial, esta es la configuración que cargará nuestro bundle.

## Opcional `CacheInterface`

Ahora preparemos nuestro código para la nueva configuración. Abre `ObjectTranslator.php`. Como ahora la caché es opcional, necesitamos permitir null para `CacheInterface`. Primero, copia esta propiedad y pégala encima del constructor.

En el constructor, elimina `private`, lo queremos como un argumento normal. Hazlo anulable anteponiendo a la sugerencia de tipo `?` y dale un valor por defecto de `null`.

A continuación, añade un nuevo argumento de propiedad para el ttl: `private ?int $cacheTtl = null`.

Podríamos añadir algunas comprobaciones para que sólo se utilice la caché si se ha inyectado un adaptador de caché... pero... hay una forma más limpia de hacerlo. El patrón de objeto nulo.

Dentro del constructor, escribe `$this->cache = $cache ?? new NullAdapter()`. ¡Genial, ahora no tenemos que cambiar ningún código que utilice la caché!

Ahora a utilizar el valor `cacheTtl`. Abajo en `translationsFor()`, dentro de la llamada, ya hemos inyectado el `ItemInterface` - esto es en lo que fijamos la caducidad.

Añade una comprobación para ver si el ttl está establecido: `if ($this->cacheTtl)`. Dentro:`$item->expiresAfter($this->cacheTtl)`. ¡Listo!

## Utilizar la configuración

Por último, tenemos que ajustar la definición de nuestro servicio para que utilice nuestra nueva configuración.

Abre el `services.php` de nuestro bundle y elimina el argumento `service('cache.app')`. Ahora es opcional y se configurará en función de la configuración del usuario.

Ahora, ve a `ObjectTranslationBundle::loadExtension()`. Para volver a comprobar el aspecto de nuestro `$config`, `dd`... y... de nuevo en el navegador, actualiza.

Perfecto, aquí está nuestra matriz de caché, las dos opciones, más la activada.

De vuelta en `loadExtension()`, elimina el `dd`. Como vamos a utilizar esta definición de servicio varias veces, crearemos una variable para ella. Copia el `$builder->getDefinition(...)`, y arriba, escribe`$objectTranslatorDef =` y... pega.

Abajo, refactoriza para llamar a `->setArgument()` en nuestra nueva variable. Establecer el `translation_class` siempre es necesario, pero sólo establecemos la caché si está activada.

Escribe `if ($config['cache']['enabled'])`. Dentro, configuraremos el pool de caché y los argumentos ttl. Primero, `$objectTranslatorDef->setArgument()`. Encuentra el índice de argumentos saltando rápidamente al constructor `ObjectTranslator`, y cuenta los argumentos, 0, 1, 2, 3, 4. ¡Ya está!

Utiliza `4` como primer argumento, y para el segundo, no podemos utilizar simplemente la cadena`pool` sin procesar: tiene que ser una referencia de servicio. Así que escribe`new Reference()`, asegúrate de importarlo del espacio de nombres DependencyInjection. Dentro, pasa `$config['cache']['pool']`. 

Para el ttl, podemos utilizar el entero en bruto, así que`$objectTranslatorDef->setArgument(5, $config['cache']['ttl'])`.

¡Creo que ya estamos listos! Primero, asegurémonos de que la caché de nuestra aplicación está limpia. En tu terminal, ejecuta:

```terminal
symfony console cache:clear
```

En el navegador, actualiza... 4 consultas, esto debería estar calculando y configurando la caché. Actualiza de nuevo... Se ha reducido a 1 consulta.

Vale, en realidad no ha cambiado mucho... ¡así que vamos a configurarlo con una reserva personalizada!

## Grupo de caché personalizado

En `cache.yaml` de nuestra aplicación, descomenta la sección `pools`. Nombra nuestro pool`object_translation.cache`. Por defecto, se basará en nuestra reserva`cache.app`. Pero vamos a activar el etiquetado añadiendo `tags: true`.

Ahora, en `symfonycasts_object_translation.yaml`, configura nuestro pool personalizado poniendo: `cache: pool:`. ¿Cómo lo llamamos aquí? `object_translation.cache`
cópialo y pégalo.

De vuelta en el navegador, actualiza... 4 consultas, esto debería estar utilizando el nuevo pool. Actualiza de nuevo... Se ha reducido a 1 consulta. Perfecto

## Invalidación de etiquetas de caché (de verdad)

Ahora deberíamos poder ver realmente cómo funciona la invalidación de etiquetas de caché. Ve a tu terminal y ejecuta:

```terminal
symfony console cache:pool:invalidate-tags object-translation
```

Vuelve al navegador y actualiza... 4 consultas. ¡Eso significa que los elementos etiquetados de la caché se invalidaron y tuvieron que calcularse de nuevo!

Bien, nuestro `ObjectTranslator` se ha convertido en un monstruo. A continuación, ¡refactoricemos esta bestia!
