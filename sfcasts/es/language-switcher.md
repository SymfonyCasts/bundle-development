# Crear un conmutador de idiomas

Ya tenemos el enrutamiento localizado, pero sólo podemos cambiar de idioma actualizando manualmente la URL. Eso no es suficiente Aquí arriba, junto a nuestro formulario de búsqueda, necesitamos un enlace a esta página, pero uno para cada uno de los idiomas admitidos. Pero... ¿cómo podemos generar un enlace a una ruta para una localización diferente?

Echa un vistazo al perfil web de esta petición y observa un montón de atributos de petición subrayados. La mayoría son internos de Symfony, pero este`_locale` es importante. Cuando se utilizan rutas localizadas, Symfony establece la configuración regional actual de la petición. Lo bueno es que puedes utilizarlo como parámetro de ruta para generar URLs a diferentes configuraciones regionales.

Por ejemplo, para generar una URL para `app_homepage`, si pasas `_locale: 'fr'`como parámetro de ruta, la URL generada será `/fr`, independientemente de la configuración regional actual.

¡Al widget!

## Construir el conmutador de idiomas

En el directorio `tutorial/`, abre `language_switcher.html.twig`, selecciona todo y cópialo. Ahora, en `templates/base.html.twig`, busca la etiqueta `<form>`, y justo encima, pégala:

[[[ code('34df3b8f22') ]]]

Vuelve a nuestra aplicación y actualízala, ¡y ya está! Ahora mismo es sólo un stub, pero puedes ver cómo debería funcionar. Este botón mostrará la configuración regional actual y, cuando hagas clic en él, aparecerá un menú desplegable con enlaces a las demás configuraciones regionales.

¡Vamos a conectar esto!

De vuelta en `base.html.twig`, busca la etiqueta de anclaje con el texto "Idioma:". Este es el botón. Para este texto codificado, `en`, queremos la configuración regional actual, ya sabemos cómo hacerlo, `{{ app.locale }}`:

[[[ code('bd98a807a4') ]]]

Abajo en `ul`, que es el menú desplegable, antes de la etiqueta `li`, añade`{% for locale in app.enabled_locales %}`. ¡Te dije que configurar `enabled_locales`sería muy útil! Debajo de `li`, añade `{% endfor %}` y haz una sangría en las tripas.

Queremos excluirla, así que añade una condición antes de la etiqueta `li`:`{% if locale != app.locale %}`. Añade `{% endif %}` debajo, y sangrías:

[[[ code('ac5ac9c06c') ]]]

Para el texto del enlace dentro de `li`, utiliza `{{ locale }}`. Para el `href`, podríamos enviarles a la página de inicio de esa localidad... ¡pero podemos hacerlo mejor! Quiero que permanezcan en la misma página, sólo que con una configuración regional diferente.

Mira esto:`{{ path(app.current_route, app.current_route_parameters|merge({_locale: locale})) }}`.`app.current_route` nos da el nombre de la ruta de la página actual, y`app.current_route_parameters` nos da los parámetros de la ruta de la página actual. Luego los fusionamos con un nuevo parámetro, `_locale: locale`. Esto generará una URL para la misma página, pero con una configuración regional diferente:

[[[ code('9aa3e219f4') ]]]

## Probando el Cambiador de idioma

¡El momento de la verdad! Volvamos a nuestra aplicación y actualicemos. Estamos en la página de inicio en francés y nuestro widget muestra "Language: FR". Hasta aquí, ¡todo bien! Pulsa el botón, y aquí están nuestras otras localizaciones, pero con "FR" excluido. Haz clic en "ES" - Español. Boom, ¡estamos en la página de inicio en español! Cambia a "EN", sí, estamos en la página de inicio en inglés sin prefijo.

Haz clic en un artículo y cambia a "FR". ¡Estupendo! Estamos en la versión francesa del mismo artículo. Nuestro widget funciona a la perfección: se acabaron las llamadas nocturnas al servicio de atención al cliente para que les explique cómo cambiar la URL manualmente, ¡y en alemán! ¡Das ist ja verrückt!

## Eliminar la barra diagonal final

Una pequeña queja mía es que cuando estás en una página de inicio con prefijo local, como `/fr`, hay una barra al final: `/fr/`. No hay ningún problema técnico, simplemente no me gusta. Y es muy fácil de quitar.

Abre `config/routes.yaml`, y bajo la clave `controllers`, añade la opción`trailing_slash_on_root: false`:

[[[ code('89f6686477') ]]]

¡Ya está!

Volvemos a nuestra aplicación... actualizamos... ¡y la barra diagonal final ha desaparecido! ¡Ya puedo dormir tranquilo esta noche!

Vale, ¡basta de trabajo previo! A continuación, ¡vamos a traducir el contenido!
