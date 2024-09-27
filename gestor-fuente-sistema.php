<?php
/**
 * Plugin Name: Gestor de Fuente System Sans-serif
 * Description: Permite activar o desactivar la fuente "System Sans-serif" modificando el archivo theme.json del tema activo.
 * Version: 1.0
 * Author: A. Cambronero Blogpocket.com
 * License: GPL2
 */

// Agregar un elemento de menú en el panel de administración
add_action('admin_menu', 'gfss_agregar_menu');

function gfss_agregar_menu() {
    add_options_page(
        'Gestor de Fuente System Sans-serif', // Título de la página
        'Fuente System Sans-serif',           // Título del menú
        'manage_options',                     // Capacidad requerida
        'gfss-configuracion',                 // Slug del menú
        'gfss_pagina_configuracion'           // Función que muestra el contenido
    );
}

// Mostrar la página de configuración
function gfss_pagina_configuracion() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        return;
    }

    // Mensaje de estado
    $mensaje = '';

    // Manejar acciones de formulario
    if (isset($_POST['gfss_accion'])) {
        check_admin_referer('gfss_nonce_action', 'gfss_nonce_field');

        if ($_POST['gfss_accion'] == 'activar') {
            $resultado = gfss_modificar_theme_json('activar');
            if ($resultado) {
                $mensaje = 'La fuente "System Sans-serif" ha sido activada.';
            } else {
                $mensaje = 'Error al activar la fuente.';
            }
        } elseif ($_POST['gfss_accion'] == 'desactivar') {
            $resultado = gfss_modificar_theme_json('desactivar');
            if ($resultado) {
                $mensaje = 'La fuente "System Sans-serif" ha sido desactivada.';
            } else {
                $mensaje = 'Error al desactivar la fuente.';
            }
        }
    }

    // Verificar si la fuente está actualmente activa
    $fuente_activa = gfss_verificar_fuente_activa();

    ?>
    <div class="wrap">
        <h1>Gestor de Fuente System Sans-serif</h1>
        <?php if ($mensaje): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html($mensaje); ?></p>
            </div>
        <?php endif; ?>
        <p>Utiliza los botones a continuación para activar o desactivar la fuente "System Sans-serif" en tu tema activo.</p>
        <form method="post">
            <?php wp_nonce_field('gfss_nonce_action', 'gfss_nonce_field'); ?>
            <?php if (!$fuente_activa): ?>
                <input type="hidden" name="gfss_accion" value="activar">
                <?php submit_button('Activar Fuente'); ?>
            <?php else: ?>
                <input type="hidden" name="gfss_accion" value="desactivar">
                <?php submit_button('Desactivar Fuente', 'delete'); ?>
            <?php endif; ?>
        </form>
    </div>
    <?php
}

// Función para verificar si la fuente está activa
function gfss_verificar_fuente_activa() {
    $theme_json_data = gfss_obtener_datos_theme_json();
    if ($theme_json_data === null) {
        return false;
    }

    if (isset($theme_json_data['settings']['typography']['fontFamilies'])) {
        foreach ($theme_json_data['settings']['typography']['fontFamilies'] as $fuente) {
            if (isset($fuente['slug']) && $fuente['slug'] === 'system-sans-serif') {
                return true;
            }
        }
    }
    return false;
}

// Función para obtener y decodificar el contenido de theme.json
function gfss_obtener_datos_theme_json() {
    $theme_json_path = gfss_obtener_ruta_theme_json();

    if (!file_exists($theme_json_path)) {
        return null;
    }

    $theme_json_content = file_get_contents($theme_json_path);
    $theme_json_data = json_decode($theme_json_content, true);

    if ($theme_json_data === null) {
        return null;
    }

    return $theme_json_data;
}

// Función para obtener la ruta del archivo theme.json del tema activo
function gfss_obtener_ruta_theme_json() {
    $theme_dir = get_stylesheet_directory();
    return $theme_dir . '/theme.json';
}

// Función para modificar el archivo theme.json
function gfss_modificar_theme_json($accion) {
    $theme_json_path = gfss_obtener_ruta_theme_json();

    if (!file_exists($theme_json_path)) {
        // Si no existe, podríamos crearlo, pero por simplicidad, retornamos false
        return false;
    }

    $theme_json_data = gfss_obtener_datos_theme_json();

    if ($theme_json_data === null) {
        return false;
    }

    $nueva_fuente = array(
        'fontFamily' => '-apple-system, BlinkMacSystemFont, "avenir next", avenir, "segoe ui", "helvetica neue", helvetica, Cantarell, Ubuntu, roboto, noto, arial, sans-serif',
        'name'       => 'System Sans-serif',
        'slug'       => 'system-sans-serif',
    );

    // Verificar si el apartado de fuentes existe
    if (!isset($theme_json_data['settings']['typography']['fontFamilies'])) {
        $theme_json_data['settings']['typography']['fontFamilies'] = array();
    }

    if ($accion == 'activar') {
        // Agregar la fuente si no existe
        $fuente_existente = false;
        foreach ($theme_json_data['settings']['typography']['fontFamilies'] as $fuente) {
            if (isset($fuente['slug']) && $fuente['slug'] === 'system-sans-serif') {
                $fuente_existente = true;
                break;
            }
        }

        if (!$fuente_existente) {
            $theme_json_data['settings']['typography']['fontFamilies'][] = $nueva_fuente;
        }
    } elseif ($accion == 'desactivar') {
        // Eliminar la fuente si existe
        $nuevas_fuentes = array();
        foreach ($theme_json_data['settings']['typography']['fontFamilies'] as $fuente) {
            if (!(isset($fuente['slug']) && $fuente['slug'] === 'system-sans-serif')) {
                $nuevas_fuentes[] = $fuente;
            }
        }
        $theme_json_data['settings']['typography']['fontFamilies'] = $nuevas_fuentes;
    }

    // Codificar el JSON nuevamente
    $nuevo_contenido = json_encode($theme_json_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Hacer una copia de seguridad del archivo original
    $backup_created = gfss_crear_copia_respaldo($theme_json_path);

    if (!$backup_created) {
        return false;
    }

    // Guardar el nuevo contenido en theme.json
    $resultado = file_put_contents($theme_json_path, $nuevo_contenido);

    return $resultado !== false;
}

// Función para crear una copia de seguridad del archivo theme.json
function gfss_crear_copia_respaldo($theme_json_path) {
    $backup_path = $theme_json_path . '.backup';
    if (!file_exists($backup_path)) {
        return copy($theme_json_path, $backup_path);
    }
    return true;
}
