# gestionar-fuentes-sistema
Plugin de WordPress que agrega una interfaz de administración con dos botones para activar y desactivar la fuente "System Sans-serif" modificando directamente el archivo theme.json del tema activo.
# Activar el plugin
Ve al panel de administración de WordPress.
Navega a Plugins y activa Gestor de Fuente System Sans-serif.
# Usar la interfaz de administración
En el panel de administración, ve a Ajustes > Fuente System Sans-serif.
Verás la interfaz con un botón para activar o desactivar la fuente, dependiendo de su estado actual.
Al hacer clic en el botón, se modificará el archivo theme.json del tema activo para agregar o eliminar la fuente.
Se mostrará un mensaje indicando si la operación fue exitosa.
# Consideraciones importantes
## Permisos de archivo
El servidor debe tener permisos de escritura en el directorio del tema y en el archivo theme.json para que el plugin pueda modificarlo.
Si el plugin no puede escribir en el archivo, mostrará un mensaje de error.
## Seguridad
Se utiliza check_admin_referer y wp_nonce_field para proteger el formulario contra ataques CSRF.
Solo los usuarios con la capacidad manage_options (administradores) pueden acceder y utilizar esta interfaz.
## Copia de seguridad
El plugin crea una copia de seguridad del archivo theme.json llamada theme.json.backup antes de realizar cualquier modificación.
Si necesitas restaurar el archivo original, puedes reemplazar el theme.json actual por la copia de seguridad.
## Actualizaciones del tema
Si actualizas el tema, es probable que el archivo theme.json sea sobrescrito y pierdas los cambios realizados.
Después de actualizar el tema, puedes volver a usar el plugin para activar la fuente nuevamente.
## Compatibilidad con temas hijos
Si usas un tema hijo, el plugin modificará el theme.json del tema hijo.
Si el tema hijo no tiene un theme.json, es posible que necesites crearlo manualmente o considerar modificar el theme.json del tema padre.
## Formato JSON
Al escribir el archivo theme.json, se utiliza JSON_UNESCAPED_UNICODE para mantener los caracteres Unicode sin escapar, lo que es útil para caracteres especiales en los nombres de fuentes.
# Últimas recomendaciones
- Respalda tu sitio: Antes de usar este plugin, es recomendable hacer una copia de seguridad completa de tu sitio, incluyendo los archivos y la base de datos.
- Pruebas en un entorno de desarrollo: Si es posible, prueba este plugin en un entorno de desarrollo o staging antes de usarlo en tu sitio en producción.
- Revisar el archivo theme.json: Después de activar o desactivar la fuente, puedes revisar el archivo theme.json para verificar que los cambios se hayan aplicado correctamente.
- Manejo de errores: El plugin incluye mensajes básicos de error, pero puedes ampliarlos para manejar situaciones específicas según tus necesidades.
