# Optimización del rendimiento 2: Almacenamiento en caché

Tenemos algo de memoización en memoria para reducir las llamadas a la base de datos, pero sólo mientras dura una petición. Seguimos viendo 4 consultas en la página de inicio en francés. 1 para obtener los artículos y 3 para obtener las traducciones (1 por artículo). Como es probable que estas traducciones no cambien tan a menudo, implementemos además una estrategia de almacenamiento en caché más persistente.

Sumérgete en el código del servicio `ObjectTranslator`. Aquí abajo, en el método`translationsFor()`, este `findBy()` es el que realiza las consultas. ¡Esto es lo que queremos almacenar en caché!

Doctrine ofrece algo de caché de resultados, pero puede resultar complejo y no es tan flexible como el componente Symfony Cache. Así que vamos a utilizarlo.

## Inyectar el componente Symfony Cache

Lo primero es lo primero, necesitamos inyectar el `CacheInterface` en el constructor:`private CacheInterface $cache`. Asegúrate de importar el de `Symfony\Contracts\Cache`.

Es posible que ya hayas utilizado alguna de las interfaces de caché de PSR. Symfony las soporta, pero también proporciona sus propios contratos de caché. Para nuestro caso de uso, creo que es superior.

## Utilizar la caché en `translationsFor()`

Ahora, abajo en el método `translationsFor()`, justo después de calcular el`$id`, escribe `return $this->cache->get()`. El primer argumento es la clave de caché, que debe ser única para lo que estamos almacenando en caché. Entre comillas dobles, utiliza la interpolación de cadenas para crear una clave como ésta: `object_translation.{$locale}.{$type}.{$id}`.

El segundo argumento es lo divertido: es una llamada a `function()...`.

El funcionamiento es bastante ingenioso. Cuando llamas a `get()`, primero comprueba si la clave existe en la caché. Si no es así, ejecuta la llamada y almacena el resultado. Ahora, en las siguientes llamadas con la misma clave, ¡devuelve el valor almacenado en caché y se salta por completo la llamada! De alguna manera, ¡consigues el conjunto de la caché y lo obtienes todo en la llamada!

Para las tripas de esta función, selecciona el resto del método `translationsFor()`, incluido nuestro código de normalización, y córtalo. Pégalo dentro.

PhpStorm se está quejando porque estas variables ya no están en el ámbito. Haz que estén disponibles en la función añadiendo `use ($locale, $type, $id)` después de`function()`.

## Cableado del Servicio de Caché

Ahora necesitamos cablear este nuevo argumento constructor, así que utilicemos nuestro truco para obtener el ID del servicio. En tu terminal, ejecuta:

```terminal
symfony console debug:autowiring CacheInterface
```

Bien! `cache.app` es lo que queremos.

De vuelta a nuestro código, abre el archivo `services.php` de nuestro bundle. Justo aquí, debajo de`service('doctrine')`, añade: `service('cache.app')`.

¡Es hora de probarlo! De vuelta en tu navegador, estamos en la página principal francesa. Actualiza la página. Sigues viendo cuatro peticiones, lo cual es de esperar, ya que esta petición debería estar calculando la caché. Ahora, actualiza de nuevo. ¡BAM! Sólo hay una consulta ¡Eso es caché!

En la barra de herramientas de depuración web, puedes hacer clic en el icono de la caché para obtener detalles sobre la actividad de la caché para esta petición. 3 llamadas y 3 aciertos. Aquí abajo, podemos ver las claves de caché que se utilizaron.

## Implementar etiquetas de caché

¡Hora de la bonificación! Los Contratos de Caché de Symfony tienen una función muy interesante: el etiquetado. Esto te permite agrupar elementos en caché e invalidarlos juntos. Creo que nuestro bundle debería soportarlo

Volviendo a `ObjectTranslator::translationsFor()`, esta llamada a la caché acepta un argumento:`ItemInterface $item`. Este objeto nos da la oportunidad de configurar cosas sobre este elemento específico de la caché, ¡como añadir etiquetas!

Una cosa sobre el etiquetado de la caché, es que no todos los adaptadores de caché lo soportan. Por ejemplo, tu `cache.app` por defecto no lo hace. Comprueba si lo admite añadiendo`if ($this->cache instanceof TagAwareCacheInterface)`. Asegúrate de importar el de `Symfony\Contracts\Cache`.

¡Ahora ya podemos añadir etiquetas! Dentro del if, escribe `$item->tag()`. Esto toma un array. ¿Qué etiquetas serían útiles? ¿Qué tal `object-translation` y `object-translation-{$type}`.

Ahora los usuarios pueden invalidar todas las traducciones de objetos... o sólo un tipo concreto.

Para ver cómo funcionaría, salta a tu terminal y ejecuta:

```terminal
symfony console cache:pool:invalidate-tags object-translation
```

Esto dice que se ha hecho correctamente, pero como ya he dicho, `cache.app` no admite el etiquetado.

A continuación, vamos a añadir alguna configuración de bundle para que los usuarios puedan elegir un grupo de caché... y otras opciones.
