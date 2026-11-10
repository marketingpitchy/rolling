<?php
/**
 * Thème enfant Rolling : chargement des modules.
 *
 * Aucun texte affiché sur le site n'est écrit ici : les contenus viennent d'Elementor,
 * des menus et du Personnalisateur.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

define( 'ROLLING_VERSION', wp_get_theme( get_stylesheet() )->get( 'Version' ) );
define( 'ROLLING_DIR', get_stylesheet_directory() );
define( 'ROLLING_URL', get_stylesheet_directory_uri() );

require_once ROLLING_DIR . '/inc/class-rolling-donnees.php';
require_once ROLLING_DIR . '/inc/class-rolling-installateur.php';
require_once ROLLING_DIR . '/inc/class-rolling-ressources.php';
require_once ROLLING_DIR . '/inc/class-rolling-entete-pied.php';
require_once ROLLING_DIR . '/inc/class-rolling-guide.php';
require_once ROLLING_DIR . '/inc/class-rolling-seo.php';
require_once ROLLING_DIR . '/inc/class-rolling-contenus.php';
require_once ROLLING_DIR . '/inc/class-rolling-webinars.php';
require_once ROLLING_DIR . '/inc/class-rolling-dynamique.php';

Rolling_Installateur::init();
Rolling_Ressources::init();
Rolling_Entete_Pied::init();
Rolling_Guide::init();
Rolling_Seo::init();
Rolling_Contenus::init();
Rolling_Dynamique::init();
