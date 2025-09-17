# Extender la entidad de nuestro bundle

Nuestro bundle tiene una superclase abstracta mapeada `Translation` y está registrada en Doctrine. Es hora de crear la entidad real `Translation` en nuestra aplicación.

En tu terminal, ejecuta:

```terminal
symfony console make:entity
```

Para el nombre de la clase, utiliza `Translation`. Todas las propiedades están definidas en la clase abstracta `Translation` del bundle, así que pulsa enter para terminar.

## La entidad de traducción real

En tu editor, abre nuestra nueva entidad en `src/Entity/Translation.php`:

[[[ code('30a8a8eaab') ]]]

Muy bien, ¡allá vamos! Se nos ha añadido el ID, que es todo lo que necesitamos. Ahora vamos a extender la clase abstracta `Translation` del bundle. Como tiene el mismo nombre, necesitamos importarla con un alias.

En la parte superior, escribe `use Translation` y elige el de nuestro bundle. Después,`as BaseTranslation`. Extiéndelo con `extends BaseTranslation`.

Genial, ¡ya hemos terminado!

Eventualmente, proporcionaremos una receta para que los usuarios finales no tengan que hacer este paso.

## Hacer una migración

¿Nueva entidad? ¡Nueva migración!

Vuelve al terminal y ejecuta:

```terminal
symfony console make:migration
```

Echa un vistazo. Abre el nuevo archivo de migración en el directorio `migrations/`.

Comprueba el método `up()`. Está creando la tabla `translation` con la`id`, pero también todas las columnas de la superclase mapeada de nuestro bundle. ¡Perfecto!

Añade una descripción: `Add Translation entity`:

[[[ code('b28a12aa12') ]]]

Es hora de ejecutarlo. En tu terminal, ejecuta:

```terminal
symfony console doctrine:migrations:migrate
```

Elige `yes`, y... ¡boom! ¡Base de datos migrada!

Vale, ya tenemos la entidad en nuestra aplicación, pero para que nuestro bundle pueda realizar consultas en su nombre, el bundle necesita conocerla. Un trabajo perfecto para la configuración del bundle: ¡Eso a continuación!
