<?php
/**
 * En-tête et pied de page du thème : réglages dans Apparence > Personnaliser.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * En-tête et pied de page.
 */
class Rolling_Entete_Pied {

	const MODELE_LANDING = 'modeles/landing.php';

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'personnaliser' ) );
		add_filter( 'theme_page_templates', array( __CLASS__, 'modeles_de_page' ) );
	}

	/**
	 * Déclare le modèle de page « Landing ».
	 *
	 * @param array $modeles Modèles.
	 * @return array
	 */
	public static function modeles_de_page( $modeles ) {
		$modeles[ self::MODELE_LANDING ] = __( 'Landing : logo seul, sans menu', 'rolling' );

		return $modeles;
	}

	/**
	 * La page affichée est-elle une landing (logo seul) ?
	 *
	 * @return bool
	 */
	public static function est_landing() {
		return is_page_template( self::MODELE_LANDING );
	}

	/**
	 * Valeur d'un réglage texte du Personnalisateur.
	 *
	 * @param string $cle Clé.
	 * @return string
	 */
	public static function texte( $cle ) {
		$valeur = (string) get_theme_mod( $cle, '' );

		return str_replace( '{annee}', gmdate( 'Y' ), $valeur );
	}

	/**
	 * Image d'un logo (logo du site ou logo pour fond sombre).
	 *
	 * @param bool $negatif Logo pour fond sombre.
	 * @return string HTML.
	 */
	public static function logo( $negatif = false ) {
		$id = (int) get_theme_mod( $negatif ? 'rolling_logo_negatif' : 'custom_logo' );

		if ( ! $id && $negatif ) {
			$id = (int) get_theme_mod( 'custom_logo' );
		}

		if ( ! $id ) {
			return esc_html( get_bloginfo( 'name' ) );
		}

		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );

		return wp_get_attachment_image(
			$id,
			'full',
			false,
			array(
				'alt'     => $alt ? $alt : get_bloginfo( 'name' ),
				'loading' => 'eager',
			)
		);
	}

	/**
	 * Lien du bouton de l'en-tête.
	 *
	 * @return string
	 */
	public static function lien_bouton() {
		$page = (int) get_theme_mod( 'rolling_cta_page' );

		return $page ? (string) get_permalink( $page ) : home_url( '/' );
	}

	/**
	 * Réglages du Personnalisateur.
	 *
	 * @param WP_Customize_Manager $wp_customize Gestionnaire.
	 */
	public static function personnaliser( $wp_customize ) {
		$wp_customize->add_section(
			'rolling_entete_pied',
			array(
				'title'    => __( 'Rolling : en-tête et pied de page', 'rolling' ),
				'priority' => 30,
			)
		);

		$textes = array(
			'rolling_cta_texte'               => array( __( 'Texte du bouton de l’en-tête', 'rolling' ), 'text' ),
			'rolling_libelle_menu_ouvrir'     => array( __( 'Bouton du menu mobile, pour lecteur d’écran (ouvrir)', 'rolling' ), 'text' ),
			'rolling_libelle_menu_fermer'     => array( __( 'Bouton du menu mobile, pour lecteur d’écran (fermer)', 'rolling' ), 'text' ),
			'rolling_pied_presentation'       => array( __( 'Pied de page : présentation', 'rolling' ), 'textarea' ),
			'rolling_pied_marque'             => array( __( 'Pied de page : mention de la marque', 'rolling' ), 'text' ),
			'rolling_pied_titre_navigation'   => array( __( 'Pied de page : titre de la colonne du menu principal', 'rolling' ), 'text' ),
			'rolling_pied_titre_informations' => array( __( 'Pied de page : titre de la colonne des informations', 'rolling' ), 'text' ),
			'rolling_pied_copyright'          => array( __( 'Pied de page : droits ({annee} = année en cours)', 'rolling' ), 'text' ),
			'rolling_pied_mention'            => array( __( 'Pied de page : mention à droite', 'rolling' ), 'text' ),
		);

		foreach ( $textes as $cle => $reglage ) {
			$wp_customize->add_setting(
				$cle,
				array(
					'sanitize_callback' => 'textarea' === $reglage[1] ? 'sanitize_textarea_field' : 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				$cle,
				array(
					'label'   => $reglage[0],
					'section' => 'rolling_entete_pied',
					'type'    => $reglage[1],
				)
			);
		}

		$wp_customize->add_setting( 'rolling_cta_page', array( 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			'rolling_cta_page',
			array(
				'label'   => __( 'Page du bouton de l’en-tête', 'rolling' ),
				'section' => 'rolling_entete_pied',
				'type'    => 'dropdown-pages',
			)
		);

		$wp_customize->add_setting( 'rolling_logo_negatif', array( 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'rolling_logo_negatif',
				array(
					'label'     => __( 'Logo pour fond sombre (pied de page)', 'rolling' ),
					'section'   => 'rolling_entete_pied',
					'mime_type' => 'image',
				)
			)
		);
	}
}
