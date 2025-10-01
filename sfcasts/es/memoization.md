# Optimización del rendimiento 1: Memoización

Nuestro filtro Twig funciona como esperábamos, y lo estamos utilizando aquí en nuestro índice de artículos. Ahora mismo, estamos sobreescribiendo la variable artículo con su versión traducida al inicio del bucle. Creo que hay otra forma válida de utilizar este filtro: sólo cuando lo necesitemos.

## Utilizar el filtro sólo cuando sea necesario

En primer lugar, elimina la modificación de `set article`. Luego, aquí abajo, donde realmente necesitamos la versión traducida - para el título y el contenido. Cambia `article.title`por `article|translate_object.title` y haz lo mismo para el contenido:`article|translate_object.content`.

Vuelve a nuestro navegador: estamos en la página de inicio en francés. Antes de actualizar, observa el recuento de consultas en la barra de herramientas de depuración web. 4, una consulta para obtener todos los artículos, y luego una consulta por artículo para obtener las traducciones. Ahora actualiza la página. Vale, todo sigue funcionando... pero mira el número de consultas: ¡7! Una consulta para todos los artículos, y ahora dos consultas por artículo. Los valores traducidos se están cargando dos veces, aunque sean idénticos.

Arreglemos esto con una técnica llamada memoización. Es una palabra elegante, y me gusta mucho decirla, pero sólo significa almacenar datos en memoria para evitar el procesamiento redundante.

## WeakMap

Implementaremos esto en nuestro `ObjectTranslator`, así que ábrelo. Podríamos simplemente añadir una propiedad array para almacenar objetos traducidos por ID o algo así, pero esto podría provocar fugas de memoria. En procesos de larga duración, este array podría crecer indefinidamente.

En su lugar, utilizaremos un objeto especial del núcleo de PHP llamado `WeakMap`. Añade una nueva propiedad,`private \WeakMap $translatedObjects`.

En PHP, cuando se crea y se pasa un objeto, en realidad es una referencia a ese objeto. Mientras algo esté utilizando ese objeto, permanece en memoria. Cuando ya nada lo utiliza, PHP lo limpia automáticamente. Si utilizáramos una matriz estándar para almacenar objetos traducidos, esa referencia nunca se eliminaría. Un `WeakMap`es diferente, guarda el objeto pero no impide que se limpie: es una referencia débil. ¡Esto es perfecto para nuestro caso de uso!

Para utilizarlo, primero tenemos que instanciarlo, así que en el constructor,`$this->translatedObjects = new \WeakMap()`.

Abajo, en el método `translate()`, esta llamada a `translationFor()` es cara, está haciendo una consulta a la base de datos. Así que vamos a guardar todo este `TranslatedObject` en nuestro `WeakMap`. Justo después de `return`, escribe `$this->translatedObjects` - ¿con qué clave? El `$object` original ! Ahora, utiliza el operador de asignación de coalescencia nula `??=` y luego crea el `TranslatedObject`. Esto comprueba si el objeto traducido ya está en el mapa débil (para el objeto original). Si es así, lo devuelve. En caso contrario, crea un nuevo `TranslatedObject`, lo almacena en el mapa débil y lo devuelve. Si el objeto original es limpiado por PHP, se eliminará automáticamente del mapa débil.

¡Veámoslo en acción! De vuelta en tu navegador, vemos las 7 consultas. Ahora actualiza... sigue funcionando... y mira el recuento de consultas: ¡vuelve a ser 4! ¡La memoización funciona!

A continuación, vamos a continuar este proceso de optimización del rendimiento con el almacenamiento en caché sin memoria.
