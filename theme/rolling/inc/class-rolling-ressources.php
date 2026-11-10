<?php
/**
 * Chargement des polices, du CSS et du JavaScript du thème.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ressources du thème.
 */
class Rolling_Ressources {

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'charger' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'precharger_polices' ), 1 );
		add_action( 'elementor/editor/after_enqueue_styles', array( __CLASS__, 'charger_editeur' ) );
		add_action( 'elementor/preview/enqueue_styles', array( __CLASS__, 'charger_editeur' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'script_editeur' ) );
		add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
		add_filter( 'hello_elementor_page_title', array( __CLASS__, 'titre_de_page' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'supports' ) );
		add_filter( 'wp_content_img_tag', array( __CLASS__, 'image_principale' ), 20 );
		add_filter( 'wp_content_img_tag', array( __CLASS__, 'tailles_automatiques' ), 25 );
	}

	/**
	 * Fonctions du thème.
	 */
	public static function supports() {
		load_child_theme_textdomain( 'rolling', ROLLING_DIR . '/languages' );
		add_theme_support(
			'custom-logo',
			array(
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
		register_nav_menus(
			array(
				'principal' => __( 'Menu principal (en-tête et pied de page)', 'rolling' ),
				'pied'      => __( 'Pied de page : informations', 'rolling' ),
				'colonnes'  => __( 'Pied de page : colonnes de liens (1er niveau = titre de colonne)', 'rolling' ),
			)
		);
	}

	/**
	 * CSS et JavaScript du site.
	 */
	public static function charger() {
		wp_enqueue_style( 'rolling', ROLLING_URL . '/assets/css/rolling.css', array(), ROLLING_VERSION );
		wp_enqueue_script(
			'rolling',
			ROLLING_URL . '/assets/js/rolling.js',
			array(),
			ROLLING_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	/**
	 * Mêmes polices et mêmes animations (gelées) dans l'éditeur.
	 */
	public static function charger_editeur() {
		wp_enqueue_style( 'rolling-editeur', ROLLING_URL . '/assets/css/rolling.css', array(), ROLLING_VERSION );
	}

	/**
	 * Script de l'éditeur : fait charger les classes du thème (voir assets/js/rolling-editeur.js).
	 */
	public static function script_editeur() {
		$classes = Rolling_Donnees::lire( 'classes' );
		$ids     = array_values( array_map( fn( $c ) => $c['id'], $classes['classes'] ?? array() ) );

		wp_enqueue_script( 'rolling-editeur', ROLLING_URL . '/assets/js/rolling-editeur.js', array(), ROLLING_VERSION, true );
		wp_add_inline_script( 'rolling-editeur', 'window.rollingEditeur = ' . wp_json_encode( array( 'classes' => $ids ) ) . ';', 'before' );
	}

	/**
	 * Image principale (LCP) : la première grande image de la page n'est pas chargée en différé
	 * et passe en priorité haute (règle SEO 6.3). Les suivantes restent en chargement différé.
	 *
	 * @param string $balise Balise <img>.
	 * @return string
	 */
	public static function image_principale( $balise ) {
		static $trouvee = false;

		if ( $trouvee || is_admin() || ! preg_match( '/\swidth="(\d+)"/', $balise, $largeur ) || (int) $largeur[1] < 800 ) {
			return $balise;
		}

		$trouvee = true;
		$balise  = preg_replace( '/\sloading="lazy"/', '', $balise );

		if ( false === strpos( $balise, 'fetchpriority=' ) ) {
			$balise = str_replace( '<img ', '<img fetchpriority="high" ', $balise );
		}

		return $balise;
	}

	/**
	 * Images chargées en différé : le navigateur télécharge la taille réellement affichée (sizes="auto").
	 * Les images d'Elementor 4 ont un srcset sans sizes : sans cela, il prend la plus grande.
	 *
	 * @param string $balise Balise <img>.
	 * @return string
	 */
	public static function tailles_automatiques( $balise ) {
		if ( is_admin() || false === strpos( $balise, ' srcset=' ) || false !== strpos( $balise, ' sizes=' ) || false === strpos( $balise, 'loading="lazy"' ) ) {
			return $balise;
		}

		return str_replace( '<img ', '<img sizes="auto, 100vw" ', $balise );
	}

	/**
	 * Précharge les deux polices principales.
	 */
	public static function precharger_polices() {
		foreach ( array( 'bricolage-grotesque-variable', 'hanken-grotesk-variable' ) as $police ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( ROLLING_URL . '/assets/fonts/' . $police . '.woff2' ) );
		}
	}

	/**
	 * Pas de titre de page automatique sur les pages construites avec Elementor : le H1 est dans la page.
	 *
	 * @param bool $afficher Afficher le titre.
	 * @return bool
	 */
	public static function titre_de_page( $afficher ) {
		if ( is_singular() && class_exists( '\Elementor\Plugin' ) ) {
			$document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );

			if ( $document && $document->is_built_with_elementor() ) {
				return false;
			}
		}

		return $afficher;
	}
}
