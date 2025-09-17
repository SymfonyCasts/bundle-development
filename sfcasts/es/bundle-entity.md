# Entidades en un bundle

Nuestro bundle necesita una forma de almacenar las traducciones de los objetos. Como estamos traduciendo entidades Doctrine desde la base de datos, sabemos que se utiliza una base de datos. Así que la mejor forma de almacenar estas traducciones es también en la base de datos.

Éste es el plan: vamos a crear una única tabla para todas las traducciones de los distintos objetos Doctrine de tu aplicación. Para conseguirlo, vamos a emplear un patrón conocido como Valor de Atributo de Entidad o, abreviado, modelo EAV.

En este patrón, cada fila tendrá un `object_type`, que es básicamente un nombre que representa el tipo de entidad, como `article` o `category`. Podríamos utilizar aquí el nombre de la clase, pero almacenar nombres de clases en la base de datos puede acarrear problemas si refactorizas o renombras una clase. Así que, al utilizar estos nombres de alias, hacemos que el sistema sea resistente a la refactorización.

La siguiente columna es `object_id`. Será el identificador único del objeto, representado por el `object_type` anterior. También vamos a almacenar el `locale` (que indica la configuración regional de la traducción) y el`field`. El `field` será la propiedad de la entidad que se está traduciendo y, por último, el `value`, el valor traducido en esa configuración regional.

Podrías pensar que este patrón es ineficaz, y en cierto modo tendrías razón, pero podemos utilizarlo para traducir fácilmente docenas de tipos de entidad sin tener que crear una tabla nueva para cada entidad. La contrapartida es el rendimiento, ya que esta tabla puede crecer bastante, y probablemente lo hará. Sin embargo, con la indexación y el almacenamiento en caché adecuados, la contrapartida de rendimiento puede mitigarse en su mayor parte.

## Crear el modelo

Hay un truco para que un bundle proporcione una entidad, así que vamos a ello.

En el directorio `src` del bundle, crea un nuevo directorio. Puede que pienses que debería llamarse `Entity`, como en tus aplicaciones, pero dale el nombre de `Model`. Esta es una práctica habitual en los bundles. En el futuro, es posible que ofrezcamos soporte para otras capas de abstracción de Doctrine, como MongoDB, que utiliza el término `Document` en lugar de `Entity`. `Model` es un término más genérico que abarca `Entity`, `Document` y otros.

Dentro de este directorio, crea una nueva clase llamada `Translation`. Aquí está el truco: haz que esta clase sea `abstract`: 

[[[ code('fc2e5d8a8f') ]]]

Esto va a ser un `MappedSuperclass`en términos de Doctrine. No queremos que nuestro bundle proporcione la entidad real. El usuario del bundle creará su propia entidad `Translation` que extienda a ésta. Sólo necesitarán un campo ID. El resto de los campos se heredarán de esta clase. Esto ofrece flexibilidad para que los usuarios definan su propia estrategia de ID, nombre de tabla, índices, nombre de campo o anulaciones de tipo, incluso añadiendo campos adicionales si es necesario.

## Configurar los campos

Ahora los campos. Primero: `public string $objectType`, éste será el alias de la clase objeto de la que hemos hablado antes. A continuación: `public string $objectId`. 
Estamos utilizando `string` en lugar de `int` para `objectId` para poder acomodar los distintos tipos de ID que se pueden utilizar en una entidad Doctrine. Ya sea un número entero, un UUID o cualquier otra cosa, siempre que pueda convertirse en una cadena, estamos listos. Ahora: `public string $locale`. Estos tres campos juntos nos permitirán consultar todas las traducciones de campos de un objeto determinado, en una configuración regional determinada.

Por último, `public string $field`, el nombre de la propiedad de la entidad que se está traduciendo, y `public string $value`, el valor traducido en la configuración regional de esta fila.

[[[ code('da0c8132fa') ]]]

Ya está

A continuación, al igual que con los servicios, tenemos que informar a Symfony y Doctrine sobre esta clase
