<?php

/**
 * Plugin Name: Wof
 * Description: Wordpress Object Framework Plugin to help developing custom post types or custom taxonomies.
 * Author: Emmanuel BLANCHARD et Oz WP team S2
 * Version: 1.0.0
 *
 * Ce fichier porte le nom du plugin (du répertoire du plugin)
 * => C'est le point d'entrée du plugin
 */

use Wof\Plugin;

require __DIR__ .'/autoload.php';

// Chargement du fichier de langue lors du chargement du plugin
function wof_init() {
    $plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages'; /* Relative to WP_PLUGIN_DIR */
    load_plugin_textdomain( 'wof', false, $plugin_rel_path );
}
add_action('plugins_loaded', 'wof_init');

// $plugin = new Plugin();
// $plugin->register();

