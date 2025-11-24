# Dependencias del bundle

Por el momento, nuestro bundle está anidado dentro de una aplicación Symfony completa. Depende de las dependencias de la aplicación sin definir ninguna propia. Así que ya es hora de que especifiquemos algunas dependencias para nuestro bundle.

Lo primero es lo primero, en tu terminal, navega a nuestro directorio bundle con:

```terminal
cd object-translation-bundle
```

## Añadir dependencias

Ahora, añade nuestra primera dependencia:

```terminal
symfony composer require symfony/framework-bundle
```

Algunos creadores de bundles prefieren incluir los componentes subyacentes sin exigir directamente el `framework-bundle`. Y aunque es un enfoque perfectamente válido, yo prefiero exigir el `framework-bundle` porque simplifica las cosas. Este bundle no puede funcionar sin `framework-bundle`. Para mí, eso lo convierte en una dependencia directa.

Siguiente:

```terminal
symfony composer require symfony/translation
```

Necesitamos esto porque necesitamos los servicios que proporciona el componente de traducción, como el `LocaleAwareInterface`. A continuación, ejecuta:

```terminal
symfony composer require doctrine/doctrine-bundle doctrine/orm
```

Genial, estas son las dependencias mínimas que necesita nuestro bundle para funcionar.

## Añadir dependencias de desarrollo

Ahora es el momento de añadir algunas dependencias de desarrollo. Ejecuta: :

```terminal
symfony composer require --dev phpunit/phpunit:"^9.6"
```

El símbolo `^` indica que nos parece bien cualquier versión superior (o igual) a la 9.6, pero inferior a la 10.0.

Sé que hay versiones más recientes de `phpunit`, pero no sé muy bien cómo funciona el seguimiento de las versiones obsoletas. Así que, de momento, me quedo con la 9.6.

Hablando de seguimiento de las desaprobaciones, añade también:

```terminal
symfony composer require --dev symfony/phpunit-bridge
```

Este paquete nos ayuda a gestionar las advertencias de desaprobación cuando ejecutamos nuestras pruebas.

## Configurar PHPUnit

Ya estamos casi listos para ejecutar nuestras pruebas bundle de forma autónoma, pero antes necesitamos un archivo de configuración PHPUnit.

En el directorio `tutorials/`, copia `phpunit.xml.dist` en nuestro directorio`object-translation-bundle/`. Si no lo ves, no te preocupes, puedes copiarlo desde el script que aparece a continuación:

[[[ code('046df39238') ]]]

Si tienes curiosidad por el sufijo `.dist`, es una convención que indica que este archivo es una plantilla. Puedes copiarlo en `phpunit.xml` y modificarlo como necesites sin afectar a la plantilla original. `phpunit.xml` tiene prioridad sobre `phpunit.xml.dist` si ambos están presentes.

Descomprimamos este archivo de configuración. Es claramente XML, el nodo raíz `phpunit` tiene varios atributos: algunas cosas del espacio de nombres y del esquema de las que no necesitas preocuparte demasiado, excepto saber que añade soporte de autocompletado, lo cual está bien. El atributo `bootstrap` indica a PHPUnit qué archivo debe cargar antes de ejecutar las pruebas. En nuestro caso, es el archivo de carga automática de Composer. `colors` activa/desactiva la salida en color en el terminal. `failOnRisky` y `failOnWarning`configuran el comportamiento de PHPUnit cuando encuentra pruebas arriesgadas o advertencias. Son buenos valores por defecto.

El nodo `php` es donde configuramos los ajustes y variables de entorno de `php.ini`. La variable de entorno `SYMFONY_DEPRECATIONS_HELPER` es particularmente importante. Indica al puente PHPUnit de Symfony cómo manejar las advertencias de depreciación. Este valor es un gran valor por defecto para el desarrollo de paquetes de terceros, ya que sólo provoca un fallo si nuestro bundle causa directamente una depreciación. No podemos hacer mucho con las depreciaciones causadas por los paquetes de los que depende nuestro bundle, así que las ignoramos.

El nodo `testsuites` define nuestros conjuntos de pruebas. Aquí tenemos un único conjunto para el directorio `tests`. Finalmente, cuando ejecutamos informes de cobertura de código, en el nodo `coverage` es donde especificamos los directorios fuente para el análisis de cobertura.

Por último, en el nodo `listeners`, registramos el receptor de pruebas proporcionado por el puente PHPUnit de Symfony.

¡Es hora de ejecutar el conjunto de pruebas de nuestro bundle! En el terminal, ejecuta:

```terminal
symfony php vendor/bin/phpunit
```

¡Genial! ¡Todas nuestras pruebas pasan!

Ahora, si recuerdas, sólo tenemos pruebas unitarias, que prueban clases individuales de forma aislada. A continuación, añadiremos una prueba de integración para verificar que nuestro bundle funciona correctamente dentro de una aplicación Symfony.
