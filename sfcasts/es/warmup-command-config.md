# Configuración del comando de calentamiento

Ya tenemos nuestra clase de comando de calentamiento creada, ahora necesitamos configurarla como servicio. En tus aplicaciones, esto se haría automáticamente gracias al atributo `AsCommand`, pero en los bundles, como en cualquier otro servicio, tenemos que configurarlo manualmente.

Abre el archivo `services.php` de nuestro bundle. Añádelo como nuevo servicio con`->set('.symfonycasts.object_translator.warmup_command')`. Clase:`ObjectTranslationWarmupCommand`:

[[[ code('7fe24e3822') ]]]

Añade algunos `args()`: El primero es el servicio traductor de objetos, así que escribe`service()` y copia y pega el ID de ese servicio. El siguiente es el gestor de mapeo`service()`. Copia y pega también el ID de ese servicio. El siguiente `service()` es el conmutador de configuraciones regionales, es el mismo servicio que usamos antes, así que copia y pega eso.

El último argumento son las configuraciones regionales activadas, escribe `param('kernel.enabled_locales')`:

[[[ code('11725b5843') ]]]

Por último, marca este servicio como comando de consola con `->tag('console.command')`:

[[[ code('47c2b3f47e') ]]]

Vamos a probar este comando en el terminal. Ejecuta:

```terminal
symfony console object-translation:warmup -v
```

El `-v` nos dará un seguimiento de cualquier error que encontremos.

Y... tenemos un error. Bueno, en realidad una advertencia: "Clave de matriz indefinida 0". Fíjate en la primera línea de la traza de la pila, nos está señalando la línea 23 de `TranslatableMappingManager`.

## Arreglo de la clave de matriz indefinida

Comprueba esa clase y encuentra la línea.

Es sutil pero, estamos intentando utilizar el operador null safe en un array. No nos protege de las claves indefinidas. Para solucionarlo, ponlo entre corchetes y añade `?? null`:

[[[ code('4098c0527f') ]]]

Prueba de nuevo el comando:

```terminal-silent
symfony console object-translation:warmup -v
```

¡Genial! ¡Ha funcionado!

Pero en realidad hay un error aún más sutil que puede aparecer aquí...

Borra la caché sin calentamiento:

```terminal
symfony console cache:clear --no-warmup
```

Ejecuta de nuevo el comando de calentamiento:

```terminal-silent
symfony console object-translation:warmup -v
```

Hmm... "La clase Proxies__CG__\App\Entity\Category no es traducible" ¿Qué pasa con este nombre de clase?

## Contabilización de los proxies de Doctrine

Lo que ocurre es que Doctrine genera una clase proxy que extiende tu clase entidad entre bastidores. En este caso, `App\Entity\Category`. Esta clase proxy es la que permite a Doctrine hacer su magia.

En `TranslatableMappingManager`, el objeto pasado es a veces uno de estos proxies. Y esa clase proxy no tiene el atributo `Translatable`, ése es el problema.

Recuerda que la clase proxy extiende nuestra clase entidad. Podemos utilizar la reflexión para obtener la clase padre si detectamos que es un proxy.

Después de crear `ReflectionClass`, escribe `if ($class->implementsInterface(Proxy::class))`. Importa la clase de `Doctrine\Persistence`:

[[[ code('9524938cb2') ]]]

Todos los proxies generados tienen esta interfaz.

Dentro, escribe `$class = $class->getParentClass()` para obtener la verdadera clase entidad:

[[[ code('ad9a795d34') ]]]

Esto debería solucionar el problema, pero confírmalo ejecutando de nuevo el borrado de caché sin calentamiento:

```terminal-silent
symfony console cache:clear --no-warmup
```

Ejecuta de nuevo el comando de calentamiento:

```terminal-silent
symfony console object-translation:warmup -v
```

¡Bien! Esta vez no hay errores, y nuestra barra de progreso muestra 12 entidades procesadas. ¡Es un resultado con estilo!

## Forzar el recálculo de la caché

Ahora nos queda una última cosa por hacer. Actualmente, cuando calentamos la caché, si una traducción ya está en la caché, no se recalcula. Actualmente, este comando sólo es realmente útil para calentar una caché vacía.

Necesitamos forzar el recálculo de las traducciones, aunque ya estén en caché.

¡Los Contratos de Caché de Symfony nos cubren las espaldas!

En `ObjectTranslator::translationsFor()`, profundiza en el método de caché `get()`. En `CacheInterface`. ¿Ves este parámetro `float $beta`? Echa un vistazo a su docblock: "Un flotador que, a medida que crece, controla la probabilidad de activar la expiración anticipada.`0` la desactiva, `INF` fuerza la expiración inmediata"

Esta función ayuda con las estampidas de caché, si un montón de elementos caducan al mismo tiempo, hay una probabilidad de que algunos caduquen antes para ayudar a repartir la carga.

Para nuestro propósito, podemos pasar infinito para forzar la caducidad inmediata.

De nuevo en `ObjectTranslator::translationsFor()`, añade un nuevo parámetro: `bool $forceRefresh = false`:

[[[ code('3e2767b0ea') ]]]

Ahora, para el tercer argumento de `cache->get()`, pasa `$forceRefresh ? \INF : null`:

[[[ code('fd35867cd6') ]]]

Si es verdadero, usa infinito como `$beta` para forzar la caducidad, si no, pasa `null` para usar el comportamiento por defecto.

Arriba en `translate()`, PhpStorm se queja de que necesitamos pasar este nuevo parámetro. Añade un tercer argumento al método: `array $options = []`:

[[[ code('bb7e60d761') ]]]

Podríamos haber utilizado un parámetro específico para esto, pero utilizar una matriz de opciones facilitará añadir más opciones en el futuro.

Amplía la llamada a `translationsFor()` y para el tercer argumento: `$options['force_refresh'] ?? false`:

[[[ code('a6ca52bb21') ]]]

Por último, de vuelta en nuestro comando de calentamiento, en el bucle más interno donde estamos llamando a`translate()`, añade un tercer argumento: `['force_refresh' => true]`:

[[[ code('e593fe7327') ]]]

En el terminal, vuelve a ejecutar el comando de calentamiento:

```terminal-silent
symfony console object-translation:warmup -v
```

No hay errores, ¡eso es buena señal!

Si quieres comprobar que la actualización se produce realmente, puedes cambiar algunas traducciones en la base de datos y, a continuación, ejecutar el comando. Deberías ver reflejados estos cambios al actualizar la página - y ninguna nueva consulta a la base de datos.

A continuación, añadiremos dos comandos más que necesitamos para la versión 1.0
