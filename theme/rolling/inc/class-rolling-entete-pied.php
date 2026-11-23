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
	 * Réseaux : clé => [ nom, icône SVG (24 × 24) ].
	 *
	 * @var array
	 */
	const RESEAUX = array(
		'linkedin'  => array( 'LinkedIn', '<path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.84-2.05 3.78-2.05C20.6 8.65 21 11.2 21 14.5V21h-4v-5.8c0-1.4 0-3.2-1.95-3.2S12.8 13.5 12.8 15.1V21H9z" fill="currentColor"/>' ),
		'youtube'   => array( 'YouTube', '<path d="M22 8.2a3 3 0 0 0-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 0 0 2 8.2 31 31 0 0 0 1.6 12a31 31 0 0 0 .4 3.8 3 3 0 0 0 2.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .4-3.8 31 31 0 0 0-.4-3.8zM10 15V9l5.2 3z" fill="currentColor"/>' ),
		'instagram' => array( 'Instagram', '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor"/>' ),
	);

	/**
	 * Réglages des réseaux avec leur valeur par défaut (affichée tant que la cliente n'a rien changé).
	 *
	 * @return array<string, string>
	 */
	public static function defauts() {
		return array(
			'rolling_suivre_titre'             => __( 'Suivez-nous.', 'rolling' ),
			'rolling_suivre_texte'             => __( 'Les coulisses, nos nouvelles vidéos et nos conseils.', 'rolling' ),
			'rolling_reseaux_sous_menu'        => __( 'Ressources', 'rolling' ),
			'rolling_reseau_libelle_linkedin'  => __( 'Rolling sur LinkedIn', 'rolling' ),
			'rolling_reseau_libelle_youtube'   => __( 'Nos vidéos sur YouTube', 'rolling' ),
			'rolling_reseau_libelle_instagram' => __( 'Rolling sur Instagram', 'rolling' ),
		);
	}

	/**
	 * Réglage avec sa valeur par défaut.
	 *
	 * @param string $cle Clé.
	 * @return string
	 */
	public static function reglage( $cle ) {
		$defauts = self::defauts();

		return (string) get_theme_mod( $cle, $defauts[ $cle ] ?? '' );
	}

	/**
	 * Réseaux dont le lien est rempli (Personnaliser > Rolling : en-tête et pied de page).
	 *
	 * @return array<string, array{nom: string, lien: string, icone: string}>
	 */
	public static function reseaux() {
		$liste = array();
		foreach ( self::RESEAUX as $cle => $reseau ) {
			$lien = (string) get_theme_mod( 'rolling_reseau_' . $cle, '' );
			if ( '' !== $lien ) {
				$liste[ $cle ] = array(
					'nom'   => $reseau[0],
					'lien'  => $lien,
					'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $reseau[1] . '</svg>',
				);
			}
		}

		return $liste;
	}

	/**
	 * Lien vers un réseau : icône + texte, ouvre un nouvel onglet.
	 *
	 * @param string $cle    Réseau.
	 * @param string $classe Classe du lien.
	 * @param string $texte  Texte visible (vide = nom du réseau).
	 * @return string HTML.
	 */
	public static function lien_reseau( $cle, $classe, $texte = '' ) {
		$reseaux = self::reseaux();
		if ( ! isset( $reseaux[ $cle ] ) ) {
			return '';
		}
		$reseau = $reseaux[ $cle ];

		return '<a class="' . esc_attr( $classe ) . '" href="' . esc_url( $reseau['lien'] ) . '" target="_blank" rel="noopener me">'
			. $reseau['icone'] . '<span>' . esc_html( '' !== $texte ? $texte : $reseau['nom'] ) . '</span>'
			. '<span class="screen-reader-text">' . esc_html__( '(nouvel onglet)', 'rolling' ) . '</span></a>';
	}

	/**
	 * Menu principal : liens des réseaux ajoutés à la fin du sous-menu choisi (par défaut « Ressources »).
	 *
	 * @param array    $elements Éléments du menu.
	 * @param stdClass $args     Réglages du menu.
	 * @return array
	 */
	public static function reseaux_dans_le_menu( $elements, $args ) {
		if ( empty( $args->theme_location ) || 'principal' !== $args->theme_location ) {
			return $elements;
		}
		$titre   = strtolower( trim( self::reglage( 'rolling_reseaux_sous_menu' ) ) );
		$reseaux = self::reseaux();
		if ( '' === $titre || ! $reseaux ) {
			return $elements;
		}
		$parent = 0;
		foreach ( $elements as $element ) {
			if ( ! (int) $element->menu_item_parent && strtolower( trim( wp_strip_all_tags( $element->title ) ) ) === $titre ) {
				$parent = (int) $element->db_id;
				break;
			}
		}
		if ( ! $parent ) {
			return $elements;
		}
		$rang = 0;
		foreach ( $reseaux as $cle => $reseau ) {
			$element                   = new stdClass();
			$element->ID               = 990000 + $rang;
			$element->db_id            = 990000 + $rang;
			$element->menu_item_parent = (string) $parent;
			$element->object_id        = 0;
			$element->object           = 'custom';
			$element->type             = 'custom';
			$element->type_label       = '';
			$element->title            = self::reglage( 'rolling_reseau_libelle_' . $cle );
			$element->url              = $reseau['lien'];
			$element->target           = '_blank';
			$element->attr_title       = '';
			$element->description      = '';
			$element->xfn              = 'noopener me';
			$element->current          = false;
			$element->classes          = array( 'menu-item', 'rl-menu__reseau', 'rl-reseau--' . $cle, 0 === $rang ? 'rl-menu__reseau--premier' : '' );
			$element->menu_order       = 9990 + $rang;
			$element->post_parent      = 0;
			$elements[]                = $element;
			++$rang;
		}

		return $elements;
	}

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'personnaliser' ) );
		add_filter( 'walker_nav_menu_start_el', array( __CLASS__, 'element_de_menu' ), 10, 4 );
		add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'reseaux_dans_le_menu' ), 10, 2 );
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
		$modeles[ self::MODELE_LANDING ]            = __( 'Landing : logo seul, sans menu', 'rolling' );
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

		if ( in_array( 'rl-menu__reseau', (array) $item->classes, true ) ) {
			$cle = str_replace( 'rl-reseau--', '', (string) current( preg_grep( '/^rl-reseau--/', (array) $item->classes ) ) );
			return self::lien_reseau( $cle, 'rl-sous-menu__reseau', $item->title );
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
					'label'   => sprintf( __( 'Lien %s (vide = rien ne s’affiche : pied de page, menu, cartes et bandeaux)', 'rolling' ), $nom ),
					'section' => 'rolling_entete_pied',
					'type'    => 'url',
				)
			);
		}

		foreach ( self::defauts() as $cle => $defaut ) {
			$wp_customize->add_setting(
				$cle,
				array(
					'default'           => $defaut,
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
		}
		$libelles = array(
			'rolling_suivre_titre'             => __( 'Pied de page : titre de la bande des réseaux', 'rolling' ),
			'rolling_suivre_texte'             => __( 'Pied de page : phrase de la bande des réseaux', 'rolling' ),
			'rolling_reseaux_sous_menu'        => __( 'Menu : liens des réseaux à la fin du sous-menu (nom de l’élément parent, vide = aucun)', 'rolling' ),
			'rolling_reseau_libelle_linkedin'  => __( 'Menu : texte du lien LinkedIn', 'rolling' ),
			'rolling_reseau_libelle_youtube'   => __( 'Menu : texte du lien YouTube', 'rolling' ),
			'rolling_reseau_libelle_instagram' => __( 'Menu : texte du lien Instagram', 'rolling' ),
		);
		foreach ( $libelles as $cle => $libelle ) {
			$wp_customize->add_control(
				$cle,
				array(
					'label'   => $libelle,
					'section' => 'rolling_entete_pied',
					'type'    => 'text',
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
