# Mapeo traducible

Es hora de sumergirnos en cómo marcarán los usuarios las entidades de su app para traducirlas. Para nuestra app, tenemos cuatro entidades: `Article`, `Category`,`Tag`, y `Translation`. La entidad `Translation` almacena, por supuesto, nuestras traducciones, pero las otras tres necesitan traducción. 

Por ejemplo, echa un vistazo a `Article`. Necesitamos marcar esta clase como traducible. Y aquí abajo, queremos que las propiedades `title` y `content` sean campos traducibles en esta entidad.

## Elegir un enfoque de traducción

Tenemos un par de formas de conseguirlo. Un método podría ser crear algún tipo de interfaz en nuestro bundle. Los usuarios la implementarían en las entidades que quisieran que fueran traducibles. Pero un enfoque más moderno y elegante es utilizar atributos PHP.

A mí me gusta lo moderno y elegante, así que vamos a utilizar atributos.

Serán similares a los atributos de mapeo ORM de Doctrine. Los nuestros mapearán entidades y propiedades traducibles.

## Creación del atributo `Translatable` 

En nuestro directorio `object-translation-bundle/src`, crea un nuevo directorio,`Mapping`. Aquí es donde almacenaremos nuestros atributos.

Dentro, crea una nueva clase PHP llamada `Translatable`. Este atributo se utilizará para marcar una entidad como traducible. Hazlo `final`. Para que PHP sepa que se trata de un atributo, ¡necesitamos utilizar un atributo! Sobre la clase, escribe `#[\Attribute()]`. El primer argumento es a qué tipo de elemento se puede aplicar este atributo. En nuestro caso, queremos aplicarlo a las clases, así que escribe `\Attribute::TARGET_CLASS`:

[[[ code('02ed7ee669') ]]]

Necesitamos pasar un argumento a este atributo: el `name`. Éste es el alias de cadena que almacenaremos en la base de datos en lugar del nombre de la clase. Crea un constructor con `public function __construct()`. Ahora añade un único argumento obligatorio (que también es una propiedad): `public string $name`:

[[[ code('a96d3a0e12') ]]]

## Creación del atributo `TranslatableProperty` 

También necesitamos que nuestro bundle sepa qué propiedades deben ser traducibles. Ahora bien, podríamos utilizar un argumento de matriz en el atributo `Translatable` para enumerar las propiedades... pero creo que es más limpio utilizar un atributo independiente para esto.

También en el directorio `Mapping`, crea otra clase PHP llamada`TranslatableProperty`. Esta clase estará vacía, sin argumentos - sólo se utiliza como marcador de propiedades.

Marca la clase como atributo con `#[\Attribute()]`. Esta vez, queremos aplicar este atributo a las propiedades, así que utiliza `\Attribute::TARGET_PROPERTY`:

[[[ code('2505f059c4') ]]]

## Marcar entidades como traducibles

Ahora que nuestros atributos están listos, vayamos a las entidades de nuestra aplicación y marquémoslas. Empieza con `Article`. Encima de la clase, añade el atributo`#[Translatable('article')]`:

[[[ code('93fc418149') ]]]

Ahora, identifica las propiedades que van a ser traducibles. Encima de `$title`, añade`#[TranslatableProperty]` y haz lo mismo para `$content`:

[[[ code('d78daff195') ]]]

A continuación, marca `Category` como traducible con `#[Translatable('category')]`. La propiedad `$name` será una `TranslatableProperty`:

[[[ code('4aa0bf2697') ]]]

Por último, marca `Tag` como `#[Translatable('tag')]` y de nuevo, la propiedad `$name`será la única `TranslatableProperty`:

[[[ code('88ef4d0773') ]]]

Tanto nuestro `Tag` como nuestro `Category` tienen una única propiedad traducible.`Article` tiene dos: `$content` y `$title`. ¡Qué bien!

## Crear dispositivos de traducción

Hay una última cosa que quiero hacer. Si miramos en el directorio `Story` de nuestra aplicación, encontraremos `AppStory`, donde estamos cargando los artículos para nuestros datos de fixture. Para probar esto de forma más eficaz, crearemos algunas fijaciones de traducción. De este modo, cuando cambiemos al francés en nuestro sitio, mostraremos algunos datos simulados en francés, igual que actualmente tenemos datos simulados en inglés.

Necesitamos crear una fábrica utilizando Foundry. Ejecuta:

```terminal
symfony console make:factory
```

Crear una fábrica para la entidad `Translation`: `0` en esta lista.

Si volvemos a nuestro directorio `Factory`, aquí está: `TranslationFactory`. En `defaults()`, se han detectado todos los campos y se están generando datos falsos para ellos. Vamos a anular todo esto cuando lo creemos, así que no hace falta editar nada aquí, sólo necesitamos que exista esta fábrica. 

Ahora, en `AppStory`, encuentra el primer artículo "Por qué los asteroides saben a beicon". Asigna este artículo creado a una variable con `$article1 = `:

[[[ code('4b6bcfa305') ]]]

Hacemos esto porque necesitamos obtener su ID para vincular nuestras traducciones a él.

Debajo de esta fijación, crea nuestra primera traducción con `TranslationFactory::createOne()`. Dentro, un array: `'locale' => 'fr'`, `'objectId' => $article1->getId()`,`'objectType' => 'article'`. Ahora, el primer campo que queremos traducir es el título, así que `'field' => 'title'`. Para el valor, `'value' => 'French title...'`:

[[[ code('ce5c1e900b') ]]]

No es muy creativo, pero cumple su función.

Ahora vamos a traducir el contenido. Copia todo este `TranslationFactory::createOne()`y pégalo abajo. Cambia el `field` por `'content'` y el `value` por`'French content...'`. Deja todo lo demás igual, ya que es para el mismo objeto y localización:

[[[ code('c2ba8ba5a0') ]]]

¡Basta para empezar!

En tu terminal, vuelve a cargar los accesorios con:

```terminal
symfony console foundry:load-fixtures
```

Elige `y` para confirmar la recreación de la base de datos.

Hagamos una comprobación rápida para asegurarnos de que las traducciones se han cargado:

```terminal
symfony console doctrine:query:sql 'SELECT * FROM translation'
```

(traducción es el nombre de la tabla de nuestra entidad `Translation`.)

¡Perfecto! Nuestras dos entidades de traducción están en la base de datos.

A continuación, escribiremos la lógica que carga las traducciones de la base de datos y traduce nuestras entidades `Translatable`.
