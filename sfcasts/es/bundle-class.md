# La clase bundle

Tenemos un hogar para nuestro bundle dentro de nuestra aplicación. Y hemos configurado su archivo`composer.json` para que esté marcado como paquete para Composer. Ahora necesita una "clase bundle" para que Symfony pueda cargarlo como un bundle.

Esta clase es el corazón de tu bundle, el punto de entrada principal.

En el directorio `src/`, crea una nueva clase PHP... llámala `ObjectTranslationBundle`. Pero espera, tenemos un pequeño contratiempo. El "campo namespace" no está siendo rellenado por PhpStorm como suele ser habitual. PhpStorm aún no conoce el espacio de nombres de nuestro bundle.

Sal de este diálogo por ahora. En el archivo `composer.json` de nuestro bundle, establecemos que el espacio de nombres de nuestro directorio `src/` sea `SymfonyCasts\ObjectTranslationBundle\`.

Copia el espacio de nombres. Aquí tenemos que ayudar un poco a PhpStorm. 

## Actualizar tu espacio de nombres en PHPStorm

Navega hasta la configuración de tu PhpStorm y busca la sección "Directorios". Esta es la estructura actual de nuestro proyecto. Abre `object-translation-bundle` y haz clic en el directorio `src`. Arriba, márcalo como "Fuentes". Ahora PhpStorm sabe que es un directorio "fuentes", y lo ha añadido aquí a la derecha.

Haz clic en el pequeño icono del lápiz para establecer el espacio de nombres, o "prefijo del paquete" para esta fuente. Pega, elimina las barras inclinadas adicionales y haz clic en "Aceptar".

Pulsa "Aplicar" y "Aceptar" para guardar esta actualización, y cierra el archivo `composer.json`.

## Crear tu clase

De nuevo, crea una nueva clase PHP en el directorio `src/` de nuestro bundle. ¡Genial! ¡El espacio de nombres ya está rellenado! Llámala `ObjectTranslationBundle`.

Esta es nuestra clase bundle. Primero, márcala como `final` - no queremos que nadie extienda esto. Déjala vacía por ahora y haz que extienda `AbstractBundle`desde el componente `HttpKernel`:

[[[ code('97b7abb6b9') ]]]

## Clase bundle mejorada

Si alguna vez has construido bundles en el pasado, concretamente antes de Symfony 6, puede que recuerdes que las clases bundle solían extender `Bundle`, no `AbstractBundle`.

En Symfony 6, se ha mejorado la forma de construir bundles. La nueva clase `AbstractBundle`proporciona un enfoque más ágil para el desarrollo de bundles. Ahora puedes alojar casi toda la configuración de tu bundle y la lógica de extensión directamente dentro de tu clase "bundle". La forma antigua requería varias clases adicionales. Consulta esta [entrada de blog](https://symfony.com/blog/new-in-symfony-6-1-simpler-bundle-extension-and-configuration) para obtener más información.

A continuación, ¡vamos a "instalar" este bundle en nuestra aplicación!
