# Comando Exportar traducciones

Muy bien, ya casi hemos terminado la codificación para la versión 1.0. Pero nos queda una cosa por hacer: gestionar las traducciones. Con el tiempo, me gustaría que este bundle incluyera quizás un `FormType` que permita a los usuarios integrar la gestión de las traducciones en su interfaz de administrador. Pero por ahora, vamos a crear dos comandos de consola: uno para exportar los campos traducibles en la configuración regional por defecto, inglés en nuestro caso, a un archivo CSV. A continuación, el usuario llevará este archivo a un servicio de traducción que traduzca los valores a las configuraciones regionales admitidas. Luego, tendremos el comando Importar para volver a aspirar estos archivos traducidos.

## El comando Exportar

Lo primero es lo primero, vamos a construir ese comando de exportación. Para agilizar las cosas, ya lo he creado. En el directorio `tutorial`, copia`ObjectTranslationExportCommand.php` en el directorio `src/Command`del bundle. Si no lo ves en tu directorio `tutorial`, no te preocupes. Puedes copiarlo desde el script que aparece a continuación.

Ahora, vamos a diseccionar un poco este comando. Debería resultarte familiar. Nombre del comando: `object-translation:export`, descripción: `Exports object translations to a CSV`.

Para el constructor, sólo estamos inyectando nuestro `TranslatableMappingManager` - eso es todo lo que necesitaremos.

El método `configure()` es donde configuramos los argumentos y las opciones. Este comando tomará un argumento: la ruta del archivo al que exportar el CSV. El usuario puede pasar una ruta absoluta o relativa.

En `execute()`, creamos el `$io`, cogemos el argumento `$file`, y luego emitimos un título. A continuación, `fopen` el archivo para escribir. Como será un CSV, utilizaremos `fputcsv()` para escribir las cabeceras de las columnas: tipo, ID, campo y valor.

Al igual que con nuestro comando de calentamiento, iteramos progresivamente sobre todos nuestros objetos traducibles. Toma el tipo y el ID de cada objeto y, a continuación, itera sobre este método del gestor de mapeo`translatableValuesFor()`, que crearemos en breve. Este método itera sobre los nombres y valores de los campos traducibles del objeto traducible.

Dentro, `fputcsv()` una fila con el tipo, ID, campo y valor.

Por último, debajo de los dos bucles, `fclose()` el archivo y mostramos un mensaje de éxito.

## Cableado del comando

Ahora tenemos que cablear este comando. Dirígete al archivo `services.php` de nuestro bundle. Añade `->set('.symfonycasts.object_translator.export_command, ObjectTranslationExportCommand::class)`. Para `args()`, sólo un servicio: copia y pega esto del comando anterior.

Por último, `->tag('console.command')`.

## Crear un nuevo método

En el `ObjectTranslationExportCommand`, este método `translatableValuesFor()`de nuestro servicio gestor de mapas no existe. Créalo. Utiliza`object` como tipo de argumento y `iterable` como tipo de retorno.

Utilizaremos la reflexión para obtener estas propiedades, así que primero, obtén la clase de reflexión con `$class = new \ReflectionClass($object);`. Después, haz un bucle sobre las propiedades con `foreach ($class->getProperties() as $property)`.

¿Cómo sabemos qué propiedades son traducibles? ¿Recuerdas el atributo`TranslatableProperty` que creamos antes? Aún no lo hemos utilizado, ¡pero ha llegado su momento! En las entidades de nuestra app, este atributo marca las propiedades traducibles.

Primero, excluye las propiedades que no tienen este atributo.`if (!$property->getAttributes(TranslatableProperty::class))`, `continue`.

Ahora sabemos que la propiedad es traducible, así que,`yield $property->getName() => $property->getValue($object)`. Aunque la propiedad sea privada o esté protegida, al utilizar la reflexión de esta forma, podemos obtener el valor.

De vuelta al comando... ¡genial! ¡Se acabó el aviso!

¡Es hora de probarlo! En el terminal, Ejecuta:

```terminal
symfony console object-translation:export var/export.csv
```

¡Bien! La barra de progreso muestra que se han exportado 12 objetos y se han guardado en el directorio `var` de nuestro proyecto como `export.csv`.

De vuelta a tu IDE, busca ese archivo y ábrelo

Y... ¡boom! ¡Aquí lo tienes! La primera fila son nuestras cabeceras de columna, y cada fila de abajo es la versión exportada en inglés de los campos traducibles de nuestro objeto traducible. Tiene un aspecto algo desagradable debido a todos los saltos de línea en los campos de contenido de nuestro artículo, pero ten por seguro que se trata de un CSV válido.

A continuación, crearemos el comando de importación para devolver la versión traducida de este archivo a nuestro proyecto.
