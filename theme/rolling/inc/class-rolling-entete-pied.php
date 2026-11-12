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
		add_filter( 'walker_nav_menu_start_el', array( __CLASS__, 'element_de_menu' ), 10, 4 );
		add_filter( 'theme_page_templates', array( __CLASS__, 'modeles_de_page' ) );

		// Les modèles « Elementor pleine largeur » et « Canevas » n'ont pas de zone <main> : on l'ajoute (accessibilité, lien d'évitement).
		foreach ( array( 'header-footer', 'canvas' ) as $modele ) {
			add_action( 'elementor/page_templates/' . $modele . '/before_content', array( __CLASS__, 'ouvrir_main' ) );
			add_action( 'elementor/page_templates/' . $modele . '/after_content', array( __CLASS__, 'fermer_main' ) );
		}
	}

	/**
	 * Ouvre la zone principale de la page.
	 */
	public static function ouvrir_main() {
		echo '<main id="content" class="site-main">';
	}

	/**
	 * Ferme la zone principale de la page.
	 */
	public static function fermer_main() {
		echo '</main>';
	}

	/**
	 * Déclare le modèle de page « Landing ».
	 *
	 * @param array $modeles Modèles.
	 * @return array
	 */
	public static function modeles_de_page( $modeles ) {
		$modeles[ self::MODELE_LANDING ] = __( 'Landing : logo seul, sans menu', 'rolling' );
		$modeles[ Rolling_Dynamique::MODELE_TEXTE ] = __( 'Page de texte : mentions légales, CGV, confidentialité', 'rolling' );

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
	 * Menu principal : un élément qui a des sous-éléments devient un bouton qui ouvre son sous-menu ;
	 * les sous-éléments affichent leur description (champ « Description » du menu).
	 *
	 * @param string   $html  HTML de l'élément.
	 * @param WP_Post  $item  Élément.
	 * @param int      $depth Profondeur.
	 * @param stdClass $args  Réglages du menu.
	 * @return string
	 */
	public static function element_de_menu( $html, $item, $depth, $args ) {
		// Pied de page : le premier niveau du menu « colonnes » est le titre de chaque colonne.
		if ( ! empty( $args->theme_location ) && 'colonnes' === $args->theme_location && 0 === $depth ) {
			return '<p class="rl-pied__titre">' . esc_html( $item->title ) . '</p>';
		}

		if ( empty( $args->theme_location ) || 'principal' !== $args->theme_location ) {
			return $html;
		}

		if ( 0 === $depth && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
			return '<button type="button" class="rl-menu__ouvrir" aria-expanded="false" aria-controls="rl-sous-' . (int) $item->ID . '">' . esc_html( $item->title ) . ' <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6"/></svg></button>';
		}

		if ( $depth > 0 && trim( (string) $item->description ) ) {
			return str_replace( '</a>', '<span class="rl-sous-menu__aide">' . esc_html( $item->description ) . '</span></a>', $html );
		}

		return $html;
	}

	/**
	 * Avis clients du pied de page (remplis dans le Personnalisateur), dans l'ordre.
	 *
	 * @return array[]
	 */
	public static function avis() {
		$avis = array();
		for ( $i = 1; $i <= 4; $i++ ) {
			$note  = trim( (string) get_theme_mod( 'rolling_avis_' . $i . '_note', '' ) );
			$image = (int) get_theme_mod( 'rolling_avis_' . $i . '_image' );
			if ( '' === $note && ! $image ) {
				continue;
			}
			$avis[] = array(
				'note'  => $note,
				'image' => $image,
				'texte' => (string) get_theme_mod( 'rolling_avis_' . $i . '_texte', '' ),
				'lien'  => (string) get_theme_mod( 'rolling_avis_' . $i . '_lien', '' ),
			);
		}

		return $avis;
	}

	/**
	 * Étoiles d'une note sur 5 (remplies au prorata), avec la note lue par les lecteurs d'écran.
	 *
	 * @param string $note Note (« 4,9 »).
	 * @return string
	 */
	public static function etoiles( $note ) {
		$valeur = min( 5, max( 0, (float) str_replace( ',', '.', $note ) ) );
		$pct    = (int) round( $valeur / 5 * 100 );
		$id     = 'rl-et-' . wp_unique_id();
		$forme  = '';
		for ( $i = 0; $i < 5; $i++ ) {
			$forme .= '<path transform="translate(' . ( $i * 20 ) . ' 0)" d="M9 1.5l2.3 4.8 5.2.7-3.8 3.6.9 5.2L9 13.3l-4.6 2.5.9-5.2L1.5 7l5.2-.7z"/>';
		}

		/* translators: %s : note sur 5. */
		return '<span class="rl-etoiles" role="img" aria-label="' . esc_attr( sprintf( __( 'Note : %s sur 5', 'rolling' ), $note ) ) . '"><svg width="98" height="18" viewBox="0 0 98 18" aria-hidden="true" focusable="false"><defs><linearGradient id="' . esc_attr( $id ) . '"><stop offset="' . $pct . '%" stop-color="#FFB547"/><stop offset="' . $pct . '%" stop-color="rgba(255,255,255,0.22)"/></linearGradient></defs><g fill="url(#' . esc_attr( $id ) . ')">' . $forme . '</g></svg></span>';
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
			'rolling_pied_appel'              => array( __( 'Pied de page : phrase de l’encart « Réserver un échange »', 'rolling' ), 'text' ),
			'rolling_badge_1'                 => array( __( 'Pied de page : badge 1 (cadenas)', 'rolling' ), 'text' ),
			'rolling_badge_2'                 => array( __( 'Pied de page : badge 2 (globe)', 'rolling' ), 'text' ),
			'rolling_badge_3'                 => array( __( 'Pied de page : badge 3 (bouclier)', 'rolling' ), 'text' ),
			'rolling_pied_marque'             => array( __( 'Pied de page : mention de la marque (après la présentation)', 'rolling' ), 'text' ),
			'rolling_pied_titre_navigation'   => array( __( 'Pied de page sans colonnes : titre du menu principal', 'rolling' ), 'text' ),
			'rolling_pied_titre_informations' => array( __( 'Pied de page sans colonnes : titre des informations', 'rolling' ), 'text' ),
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

		foreach ( array(
			'linkedin'  => 'LinkedIn',
			'youtube'   => 'YouTube',
			'instagram' => 'Instagram',
		) as $reseau => $nom ) {
			$wp_customize->add_setting( 'rolling_reseau_' . $reseau, array( 'sanitize_callback' => 'esc_url_raw' ) );
			$wp_customize->add_control(
				'rolling_reseau_' . $reseau,
				array(
					/* translators: %s : réseau social. */
					'label'   => sprintf( __( 'Pied de page : lien %s (vide = pas d’icône)', 'rolling' ), $nom ),
					'section' => 'rolling_entete_pied',
					'type'    => 'url',
				)
			);
		}

		$wp_customize->add_section(
			'rolling_avis',
			array(
				'title'       => __( 'Rolling : avis clients (pied de page)', 'rolling' ),
				'description' => __( 'Jusqu’à 4 avis : image (badge ou capture), note sur 5 (ex. 4,9), texte (ex. 37 avis Google) et lien. Un avis sans note ni image n’est pas affiché ; sans aucun avis, la zone n’apparaît pas.', 'rolling' ),
				'priority'    => 32,
			)
		);
		for ( $i = 1; $i <= 4; $i++ ) {
			$wp_customize->add_setting( 'rolling_avis_' . $i . '_image', array( 'sanitize_callback' => 'absint' ) );
			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'rolling_avis_' . $i . '_image',
					array(
						/* translators: %d : numéro de l'avis. */
						'label'     => sprintf( __( 'Avis %d : image', 'rolling' ), $i ),
						'section'   => 'rolling_avis',
						'mime_type' => 'image',
					)
				)
			);
			foreach ( array(
				'note'  => array( __( 'note sur 5', 'rolling' ), 'text', 'sanitize_text_field' ),
				'texte' => array( __( 'texte', 'rolling' ), 'text', 'sanitize_text_field' ),
				'lien'  => array( __( 'lien', 'rolling' ), 'url', 'esc_url_raw' ),
			) as $champ => $reglage ) {
				$wp_customize->add_setting( 'rolling_avis_' . $i . '_' . $champ, array( 'sanitize_callback' => $reglage[2] ) );
				$wp_customize->add_control(
					'rolling_avis_' . $i . '_' . $champ,
					array(
						/* translators: 1 : numéro de l'avis, 2 : nom du champ. */
						'label'   => sprintf( __( 'Avis %1$d : %2$s', 'rolling' ), $i, $reglage[0] ),
						'section' => 'rolling_avis',
						'type'    => $reglage[1],
					)
				);
			}
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
