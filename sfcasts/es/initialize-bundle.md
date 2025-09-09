# Inicializar el bundle

¡Hola amigos! Bienvenidos al curso Reusable Symfony Bundle. Soy Kevin, y seré tu navegador de bundle en este viaje. He creado unos cuantos bundles en mis tiempos, así que estoy muy emocionado de compartir mis conocimientos contigo. 

No vamos a crear simplemente un bundle de demostración para este curso, vamos a crear un bundle real, utilizable y, con suerte, útil. ¿Y la guinda del pastel? Lo publicaremos como código abierto en GitHub y lo añadiremos a Packagist. Si encuentras útil este bundle, podrás incorporarlo a tus propios proyectos, ¡e incluso puede que ayudes a su futuro desarrollo!

## ¿Qué es un bundle?

¿Qué es exactamente un bundle? Es un trozo de código reutilizable que puede incluirse fácilmente en cualquier proyecto Symfony. Pueden compartirse de forma privada, en todos tus proyectos, o ponerse a disposición de la comunidad Symfony en general. Cualquier cosa que puedas añadir a una aplicación Symfony se puede bundlear: controladores, servicios, entidades, formularios, ¡incluso plantillas Twig!

Imagina tu aplicación como una estación espacial: los bundles son como módulos adicionales que puedes conectar para añadir nuevas funciones.

## Nuestra aplicación

Para seguir adelante, asegúrate de descargar el código del curso desde esta página. Consulta el README para configurar y poner en marcha la aplicación. Una vez que hayas completado el último paso y ejecutes el servidor web, deberías aterrizar en una página como la que se muestra aquí.

Bienvenido a la Barra Espacial---

Un momento, esto es una aplicación Symfony en toda regla, ¿pensaba que estábamos creando un bundle? Cuando se crea un bundle por primera vez, me parece útil empezar a desarrollarlo dentro de una aplicación Symfony existente, que requiera la funcionalidad que proporcionará tu nuevo bundle. Esto hace que la fase inicial de desarrollo sea mucho más fácil, y puedes probar la funcionalidad de tu bundle en un escenario del mundo real, de inmediato.

Así que... la Barra Espacial. 

Se trata de una plataforma de blogs de noticias espaciales. La página de inicio enumera los artículos disponibles y al hacer clic en uno, te lleva a la página del artículo.

Si seguiste nuestro [Curso de Traducciones](https://symfonycasts.com/screencast/translations), puede que reconozcas esto. Lo retomamos donde lo dejó ese curso. Este conmutador de idiomas alterna entre el inglés, que es nuestro idioma por defecto, el francés y el español.

Hemos utilizado el componente de traducción de Symfony para traducir los elementos estáticos de nuestro sitio, como la cabecera y el pie de página. Pero los propios artículos se almacenan como entidades en nuestra base de datos, y el componente Traducción no permite traducirlos.

Necesitamos una solución diferente para estas traducciones...

Ya existen algunos bundles que son capaces de hacer esto, pero yo quería enfocarlo desde otro punto de vista, con una perspectiva ligeramente diferente.

Así que eso es lo que hará nuestro bundle: ¡traducir entidades Doctrine!

## Crear la carpeta del bundle

¡Vamos a empezar! 

Ve al código de este proyecto en tu IDE - yo estoy utilizando [PhpStorm](https://www.jetbrains.com/phpstorm/). En la raíz del proyecto, crea una nueva carpeta para nuestro bundle. Llámala`object-translation-bundle`. Podríamos llamarla entidad-traducción-bundle, pero prefiero utilizar el término más abstracto, objeto. Recuerda, las entidades son objetos. Para empezar, sólo soportaremos el ORM de Doctrine, pero en el futuro podríamos añadir soporte para otros mapeadores de objetos, como MongoDB.

Dentro de este directorio, crea tres subcarpetas: `src`, `tests`, y `config`.

## Bundle `composer.json`

Como este bundle acabará siendo un paquete independiente, necesita un archivo `composer.json`. En lugar de crearlo manualmente, ¡existe un práctico comando asistente!

En tu terminal, en la raíz de nuestro proyecto, desplázate a nuestra nueva carpeta bundle:

```terminal
cd object-translation-bundle
```

Ejecuta:

```terminal
symfony composer init
```

Nos pide el nombre del paquete. Este nombre por defecto es casi correcto, pero quiero utilizar el nombre del proveedor `symfonycasts` (por defecto es el nombre de mi ordenador). Así que introduce `symfonycasts/object-translation-bundle`. ¿Descripción? `Translate your entities!`
¿Autor? Parece que de alguna manera encuentra mi nombre y mi dirección de correo electrónico, así que lo acepto. ¿Estabilidad mínima? No tenemos que preocuparnos por eso ahora, así que introdúcelo. ¿Tipo de paquete? Este es importante, y veremos por qué más adelante. Introduce `symfony-bundle`. ¿Licencia? Al final lo publicaremos bajo la licencia MIT, así que introduce `MIT`.

Ahora te pregunta por las dependencias. No te preocupes por eso ahora, así que introduce `n`para saltártelo. Lo mismo para las dependencias de desarrollo.

Ahora, carga automática. Ya está adivinando un espacio de nombres para nosotros basándose en el nombre de nuestro paquete,`Symfonycasts\ObjectTranslationBundle`. ¡Qué bien! Acéptalo.

Por último, tenemos que confirmar la generación. Esto tiene buena pinta, así que pulsa intro.

`composer.json` ¡generado!

Vuelve a la raíz de nuestro proyecto ejecutando:

```terminal
cd ..
```

Vuelve a nuestro IDE - tenemos algunas cosas nuevas dentro de nuestra carpeta bundle. Por alguna razón, se ha creado este directorio vacío `composer` - bórralo.

Al igual que nuestra aplicación, nuestro bundle tiene ahora un directorio `vendor`. Para evitar que se confirme, en nuestro directorio bundle, crea un archivo `.gitignore` y añade `vendor/`:

[[[ code('89d20ebfdb') ]]]

¡Echemos un vistazo a nuestro archivo `composer.json` recién horneado!

Este es un punto de partida sólido. El único ajuste que haré es poner en mayúsculas la "C" en "SymfonyCasts":

[[[ code('c9a3826eb4') ]]]

A continuación, vamos a añadir algo de código: empezaremos con la importantísima clase Bundle.
