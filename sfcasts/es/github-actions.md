# CI con acciones de GitHub

¡Por fin ha llegado el momento de compartir nuestro bundle con el mundo!

Antes, sin embargo, añadiremos la configuración necesaria para que las Acciones de GitHub ejecuten nuestras pruebas, análisis estáticos y verifiquen nuestras normas de codificación.

En la raíz de nuestro bundle, añade el siguiente directorio y subdirectorio: `.github/workflows`. Copia el archivo `ci.yml` del directorio `tutorial` en `workflows`:

[[[ code('24cd941137') ]]]

O cópialo del script de abajo si no lo encuentras.

## Comprender el flujo de trabajo de las acciones de GitHub

Hay mucho que desentrañar aquí, ¡así que vamos a ello! La sección `on` define los eventos que activarán nuestro flujo de trabajo. Queremos que se ejecute cuando se envíe código o se cree o modifique una petición de extracción. Además, esta programación cron garantiza que el flujo de trabajo se ejecute dos veces al mes, los días 1 y 16. Esto es útil para garantizar que sigue funcionando con las últimas dependencias.

A continuación, configuramos `jobs`. El primero, `tests`, es el que ejecuta nuestro conjunto de pruebas en varias versiones de PHP y Symfony. Lo hace utilizando la estrategia `matrix` para crear combinaciones de trabajos. Lo tenemos ejecutándose en PHP `8.2`, `8.3`, y `8.4`, y en las versiones de Symfony `6.4`, `7.3`, y `7.4`. Se creará un trabajo para cada permutación de estos valores. Este `include` añade un caso especial personalizado a la matriz, que se ejecuta en nuestra versión de PHP menos compatible, `8.2` y con la opción `prefer-lowest` Composer.

A continuación, el `steps` para este trabajo. En primer lugar, utilizaremos la acción `checkout` que extrae el código.

Esta acción `setup-php`, lo has adivinado, instala PHP. Observa que este `with: version:` está configurado en una variable `matrix.php`. Estas variables son la forma de personalizar el trabajo en función de los valores de la matriz. También estamos pasando `tools: flex` - esto instala Symfony Flex globalmente.

La acción `composer-install` instala las dependencias de nuestro bundle. Para `dependency-versions`, estamos pasando la variable `matrix.deps`. Si volvemos a mirar nuestra matriz, veremos que siempre está configurada como `highest`, excepto en nuestro caso especial, en el que es `lowest`. Estamos pasando la variable de entorno `SYMFONY_REQUIRE` como `matrix.symfony` para especificar qué versión de Symfony instalar.

Finalmente, el último paso ejecuta nuestro conjunto de pruebas con `vendor/bin/phpunit`.

## Trabajos PHPStan y PHP-CS-Fixer

Nuestra siguiente tarea es `static-analysis`, que ejecuta PHPStan. Este trabajo es mucho más sencillo, ya que no tiene matriz. Es mejor ejecutar el análisis estático en la última versión de PHP, 8.4 en nuestro caso, para detectar la mayoría de los problemas.

Para la tarea `php-cs-fixer`, la ejecutamos en nuestra versión de PHP menos compatible, la 8.2, para asegurarnos de que no sugiere correcciones que no son compatibles con esa versión. También he añadido los indicadores `--dry-run` y `--diff` al comando para que no cambie realmente ningún archivo, pero muestre una diferencia de lo que hay que cambiar en la salida de la acción.

## Crear el repositorio

Ahora, ¡a crear el repositorio de GitHub! Dirígete a GitHub y crea un nuevo repositorio haciendo clic en el icono más de la parte superior derecha y seleccionando `New repository`.

Para el propietario, voy a elegir la organización SymfonyCasts, pero puedes seleccionar tu cuenta personal si quieres seguir adelante. Para la descripción, simplemente la copiaré de nuestro archivo `composer.json`. ¿Visibilidad? Voy a hacerlo público porque lo publicaré en Packagist.

No cambies nada más aquí, no queremos ningún código generado por GitHub.

¡Pulsa `Create repository`! Ahora estamos en la página del repositorio vacío.

## Confirmación inicial

Localmente, necesitamos inicializar un nuevo repositorio git para nuestro bundle. Nuestro bundle vive dentro de una aplicación Symfony, que es su propio repositorio git. Un repositorio git dentro de otro repositorio git puede ser molesto de manejar, así que, vamos a sacarlo.

En tu terminal, navega a un directorio fuera de tu aplicación Symfony y ejecuta:

```terminal-silent
cp -R path/to/your/symfony/project/object-translation-bundle ./
```

Esto copia recursivamente el directorio bundle a tu ubicación actual. Navega hasta el nuevo directorio `object-translation-bundle` y ejecuta:

```terminal
git init --initial-branch=1.x
```

Por defecto, la rama inicial se llama `main`, el uso de `1.x` ayuda a distinguir entre las diferentes ramas de versiones principales más adelante. La versión 2 de nuestro bundle se desarrollaría en la rama `2.x`.

Ahora ejecuta:

```terminal
git add .
```

Para escenificar todos los archivos. En nuestra página vacía del repositorio de GitHub, copia el comando para añadir el origen remoto: `git remote add origin...` y pégalo en el terminal.

A continuación, confirma los archivos con:

```terminal
git commit -m "Initial commit"
```

Ahora, copia el comando git push de GitHub, pégalo en tu terminal, cambiando`main` por `1.x`, y ejecútalo:

```terminal
git push -u origin 1.x
```

De vuelta en GitHub, actualiza la página... ¡Bien! ¡Aquí está el código de nuestro bundle! Parece que nuestro README se ha mostrado correctamente, e incluso tenemos esta ingeniosa tabla de contenidos.

También se ha detectado nuestra licencia.

Si hacemos clic en la pestaña `Actions`, podemos ver que nuestro flujo de trabajo se ha ejecutado, ¡y parece que con éxito! Haz clic en ella para ver los trabajos individuales.

Estos trabajos que empiezan por `PHP` son las distintas permutaciones creadas a partir de nuestra matriz. Si saltamos a una, podemos ver cada paso. Si ampliamos el paso Prueba, veremos la salida de PHPUnit.

También podemos ver la salida de nuestros trabajos PHPStan y PHP-CS-Fixer.

Ahora que nuestro bundle está alojado en GitHub, podemos publicarlo en Packagist: ¡Eso a continuación!
