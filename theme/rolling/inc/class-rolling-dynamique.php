<?php
/**
 * Contenu dynamique dans Elementor : balises « Rolling », widgets de liste, parties facultatives, fiches.
 *
 * Les fiches (réalisation, témoignage, article, 404) affichent un modèle de « Mes modèles », modifiable dans
 * Elementor : le thème n'a pas besoin du Theme Builder. Le modèle utilisé se choisit dans Apparence > Personnaliser.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contenu dynamique.
 */
class Rolling_Dynamique {

	const GROUPE = 'rolling';

	/**
	 * Modèle de la fiche affichée (0 hors fiche).
	 *
	 * @var int
	 */
	private static $fiche = 0;

	/**
	 * Fiches : type de contenu => clé du modèle installé par le thème.
	 *
	 * @var array<string, string>
	 */
	const FICHES = array(
		'rolling_realisation' => 'f1-fiche-realisation',
		'rolling_temoignage'  => 'f2-fiche-temoignage',
		'post'                => 'f3-fiche-article',
		'404'                 => 'f4-page-404',
		'texte'               => 'f5-page-texte',
	);

	/**
	 * Modèle de page « Page de texte » (mentions légales, CGV, confidentialité) : texte écrit dans l'éditeur WordPress,
	 * mis en page par le modèle Elementor « Rolling · Page de texte ».
	 */
	const MODELE_TEXTE = 'modeles/texte.php';

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'elementor/dynamic_tags/register', array( __CLASS__, 'balises' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'categorie' ) );
		add_filter( 'template_include', array( __CLASS__, 'gabarit_fiche' ), 20 );
		add_action( 'elementor/frontend/after_enqueue_styles', array( __CLASS__, 'styles_des_modeles' ) );
		add_filter( 'elementor/global_classes/extract_class_ids_from_post', array( __CLASS__, 'classes_des_widgets' ), 10, 2 );
		add_filter( 'elementor/document/related_posts', array( __CLASS__, 'documents_lies' ), 10, 2 );
		add_action( 'customize_register', array( __CLASS__, 'personnaliser' ) );
		foreach ( array( 'e-flexbox', 'e-div-block', 'widget' ) as $type ) {
			add_filter( 'elementor/frontend/' . $type . '/should_render', array( __CLASS__, 'partie_facultative' ), 10, 2 );
		}
		add_action( 'elementor/frontend/before_render', array( __CLASS__, 'contexte_debut' ) );
		add_action( 'elementor/preview/enqueue_styles', array( __CLASS__, 'apercu_contextes' ), 20 );
		add_action( 'elementor/frontend/after_render', array( __CLASS__, 'contexte_fin' ) );
	}

	/**
	 * Pile des contextes en cours (id du contenu, 0 si la rubrique est vide).
	 *
	 * @var int[]
	 */
	private static $contextes = array();

	/**
	 * Contextes reconnus : classe repère « contexte-… » posée sur un conteneur. Le temps de ce conteneur, les balises
	 * et les repères « si-… » lisent ce contenu au lieu de la page (ex. le bloc Ressources de l'accueil).
	 */
	const CONTEXTES = array( 'une', 'dernier-article', 'dernier-guide', 'prochain-webinar', 'dernier-replay' );

	/**
	 * Nom du contexte porté par un élément (classe « contexte-… »), sinon chaîne vide.
	 *
	 * @param \Elementor\Element_Base $element Élément.
	 * @return string
	 */
	private static function contexte_de( $element ) {
		static $libelles = null;

		if ( null === $libelles ) {
			$libelles = array();
			$depot    = '\\Elementor\\Modules\\GlobalClasses\\Global_Classes_Repository';
			if ( class_exists( $depot ) ) {
				foreach ( $depot::make()->all()->get_items()->all() as $id => $classe ) {
					$label = (string) ( $classe['label'] ?? '' );
					if ( 0 === strpos( $label, 'contexte-' ) && in_array( substr( $label, 9 ), self::CONTEXTES, true ) ) {
						$libelles[ $id ] = substr( $label, 9 );
					}
				}
			}
		}
		if ( ! $libelles ) {
			return '';
		}

		$reglages = $element->get_settings();
		$classes  = $reglages['classes']['value'] ?? ( $reglages['classes'] ?? array() );
		foreach ( is_array( $classes ) ? $classes : array() as $classe ) {
			if ( isset( $libelles[ $classe ] ) ) {
				return $libelles[ $classe ];
			}
		}

		return '';
	}

	/**
	 * Entrée dans un conteneur « contexte-… » : empile le contenu choisi.
	 *
	 * @param \Elementor\Element_Base $element Élément.
	 */
	public static function contexte_debut( $element ) {
		$nom = self::contexte_de( $element );
		if ( '' !== $nom ) {
			self::$contextes[] = self::contenu_du_contexte( $nom );
		}
	}

	/**
	 * Sortie du conteneur : dépile.
	 *
	 * @param \Elementor\Element_Base $element Élément.
	 */
	public static function contexte_fin( $element ) {
		if ( '' !== self::contexte_de( $element ) ) {
			array_pop( self::$contextes );
		}
	}

	/**
	 * Aperçu de l'éditeur : l'éditeur affiche toutes les parties facultatives. Dans les conteneurs « contexte-… », seul
	 * l'état réel reste visible (contenu publié, ou « Bientôt » si la rubrique est vide), comme sur le site.
	 */
	public static function apercu_contextes() {
		$css = '';
		foreach ( self::CONTEXTES as $nom ) {
			$id = self::contenu_du_contexte( $nom );
			$c  = 'html.elementor-html .contexte-' . $nom;
			if ( $id ) {
				$type = str_replace( 'rolling_', '', 'post' === get_post_type( $id ) ? 'article' : (string) get_post_type( $id ) );
				$css .= $c . ' .sans-contexte{display:none!important}';
				foreach ( array_diff( array( 'article', 'guide', 'webinar' ), array( $type ) ) as $autre ) {
					$css .= $c . ' .si-type--' . $autre . '{display:none!important}';
				}
			} else {
				$css .= $c . ' .si-contexte,' . $c . ' [class*="si-type--"]{display:none!important}'
					. $c . '.carte-rubrique{background:rgba(255,255,255,.45)!important;box-shadow:inset 0 0 0 1px rgba(15,15,18,.06)!important}'
					. $c . '.carte-rubrique::before{display:none!important}'
					. $c . '.carte-rubrique .picto-rubrique{opacity:.65}';
			}
		}
		wp_add_inline_style( 'rolling-editeur', $css );
	}

	/**
	 * Un conteneur « contexte-… » est-il en cours d'affichage ?
	 *
	 * @return bool
	 */
	public static function en_contexte() {
		return (bool) self::$contextes;
	}

	/**
	 * Contenu d'un contexte : « une » = prochain webinar, sinon dernier guide, sinon dernier article.
	 *
	 * @param string $nom Contexte.
	 * @return int 0 si rien n'est publié.
	 */
	public static function contenu_du_contexte( $nom ) {
		$dernier = function ( $type ) {
			$ids = get_posts(
				array(
					'post_type'      => $type,
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'no_found_rows'  => true,
				)
			);
			return $ids ? (int) $ids[0] : 0;
		};

		switch ( $nom ) {
			case 'dernier-article':
				return $dernier( 'post' );
			case 'dernier-guide':
				return $dernier( 'rolling_guide' );
			case 'prochain-webinar':
				$ids = Rolling_Webinars::liste( 'a_venir', 1 );
				return $ids ? (int) $ids[0] : 0;
			case 'dernier-replay':
				$ids = Rolling_Webinars::liste( 'replay', 1 );
				return $ids ? (int) $ids[0] : 0;
			case 'une':
				foreach ( array( 'prochain-webinar', 'dernier-guide', 'dernier-article' ) as $suivant ) {
					$id = self::contenu_du_contexte( $suivant );
					if ( $id ) {
						return $id;
					}
				}
		}

		return 0;
	}

	/**
	 * Enregistre les balises dynamiques du thème.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $gestionnaire Gestionnaire.
	 */
	public static function balises( $gestionnaire ) {
		require_once ROLLING_DIR . '/inc/elementor/balises/trait-rolling-balise-socle.php';
		foreach ( glob( ROLLING_DIR . '/inc/elementor/balises/class-*.php' ) as $fichier ) {
			require_once $fichier;
		}
		$gestionnaire->register_group( self::GROUPE, array( 'title' => 'Rolling' ) );
		foreach ( array( 'Rolling_Balise_Titre', 'Rolling_Balise_Champ', 'Rolling_Balise_Article', 'Rolling_Balise_Terme', 'Rolling_Balise_Webinar', 'Rolling_Balise_Image', 'Rolling_Balise_Lien', 'Rolling_Balise_Partage' ) as $classe ) {
			$gestionnaire->register( new $classe() );
		}
	}

	/**
	 * Enregistre les widgets du thème.
	 *
	 * @param \Elementor\Widgets_Manager $gestionnaire Gestionnaire.
	 */
	public static function widgets( $gestionnaire ) {
		require_once ROLLING_DIR . '/inc/elementor/widgets/class-rolling-widget.php';
		foreach ( glob( ROLLING_DIR . '/inc/elementor/widgets/class-rolling-widget-*.php' ) as $fichier ) {
			require_once $fichier;
		}
		foreach ( array( 'Rolling_Widget_Liste', 'Rolling_Widget_Chiffres', 'Rolling_Widget_Coches', 'Rolling_Widget_Generique', 'Rolling_Widget_Compte_A_Rebours', 'Rolling_Widget_Video', 'Rolling_Widget_Transcription', 'Rolling_Widget_Logos', 'Rolling_Widget_Ariane', 'Rolling_Widget_Contenu', 'Rolling_Widget_Sommaire', 'Rolling_Widget_Partage', 'Rolling_Widget_Resumer' ) as $classe ) {
			$gestionnaire->register( new $classe() );
		}
	}

	/**
	 * Catégorie « Rolling » du panneau des éléments.
	 *
	 * @param \Elementor\Elements_Manager $gestionnaire Gestionnaire.
	 */
	public static function categorie( $gestionnaire ) {
		$gestionnaire->add_category(
			self::GROUPE,
			array(
				'title' => 'Rolling',
				'icon'  => 'eicon-video-camera',
			)
		);
	}

	/**
	 * Contenu dont les balises lisent les champs.
	 *
	 * Dans l'éditeur, le contenu courant est le modèle lui-même : on prend alors le dernier contenu du type que
	 * le modèle présente, pour voir de vrais textes pendant la mise en forme.
	 *
	 * @return int
	 */
	public static function contenu_courant() {
		if ( self::$contextes ) {
			return (int) end( self::$contextes );
		}

		$id = (int) get_the_ID();

		if ( $id && 'elementor_library' === get_post_type( $id ) ) {
			$type    = (string) get_post_meta( $id, '_rolling_apercu', true );
			$requete = array(
				'post_type'      => $type,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			);
			// Témoignage : celui « à la une » d'abord, souvent le plus complet.
			$apercu = 'rolling_temoignage' === $type ? get_posts(
				$requete + array(
					'meta_key'   => Rolling_Contenus::PREFIXE . 'a_la_une', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value' => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				)
			) : array();
			$apercu = $apercu ? $apercu : ( $type ? get_posts( $requete ) : array() );

			return $apercu ? (int) $apercu[0] : $id;
		}

		return $id;
	}

	/**
	 * Le contenu affiché est-il un aperçu dans l'éditeur ?
	 *
	 * @return bool
	 */
	public static function dans_editeur() {
		$plugin = class_exists( '\Elementor\Plugin' ) ? \Elementor\Plugin::$instance : null;

		return $plugin && ( $plugin->editor->is_edit_mode() || $plugin->preview->is_preview_mode() );
	}

	/**
	 * Masque une partie facultative (classe repère « si-champ ») quand le champ est vide.
	 *
	 * Ex. : un bloc qui porte la classe « si-citation » n'est pas affiché si le témoignage n'a pas de citation.
	 * Dans l'éditeur, tout reste visible pour pouvoir le mettre en forme.
	 *
	 * @param bool                    $afficher Afficher.
	 * @param \Elementor\Element_Base $element  Élément.
	 * @return bool
	 */
	public static function partie_facultative( $afficher, $element ) {
		if ( ! $afficher || self::dans_editeur() ) {
			return $afficher;
		}

		$reperes = self::reperes();
		if ( ! $reperes ) {
			return $afficher;
		}

		$reglages = $element->get_settings();
		$classes  = $reglages['classes']['value'] ?? ( $reglages['classes'] ?? array() );
		if ( ! is_array( $classes ) ) {
			return $afficher;
		}

		foreach ( $classes as $classe ) {
			if ( isset( $reperes[ $classe ] ) && ! self::condition_remplie( $reperes[ $classe ] ) ) {
				return false;
			}
		}

		return $afficher;
	}

	/**
	 * Classes repères installées : id de la classe => champ.
	 *
	 * @return array<string, string>
	 */
	private static function reperes() {
		static $reperes = null;

		if ( null === $reperes ) {
			$reperes = array();
			$depot   = '\\Elementor\\Modules\\GlobalClasses\\Global_Classes_Repository';
			if ( class_exists( $depot ) ) {
				foreach ( $depot::make()->all()->get_items()->all() as $id => $classe ) {
					$label = (string) ( $classe['label'] ?? '' );
					if ( 0 === strpos( $label, 'si-' ) || 0 === strpos( $label, 'sans-' ) ) {
						$reperes[ $id ] = $label;
					}
				}
			}
		}

		return $reperes;
	}

	/**
	 * Condition d'une classe repère : « si-champ » (rempli), « sans-champ » (vide),
	 * « si-champ--valeur » (égal) ou « sans-champ--valeur » (différent).
	 *
	 * @param string $repere Libellé de la classe.
	 * @return bool
	 */
	private static function condition_remplie( $repere ) {
		$sans     = 0 === strpos( $repere, 'sans-' );
		$reste    = substr( $repere, $sans ? 5 : 3 );
		$morceaux = explode( '--', $reste, 2 );

		if ( isset( $morceaux[1] ) && 'type' === $morceaux[0] ) {
			// « si-type--webinar » : type du contenu courant (article, guide, webinar, realisation, temoignage).
			$type = (string) get_post_type( self::contenu_courant() );
			$egal = str_replace( 'rolling_', '', 'post' === $type ? 'article' : $type ) === $morceaux[1];
			return $sans ? ! $egal : $egal;
		}
		if ( isset( $morceaux[1] ) ) {
			$egal = (string) Rolling_Contenus::valeur( str_replace( '-', '_', $morceaux[0] ), self::contenu_courant() ) === $morceaux[1];
			return $sans ? ! $egal : $egal;
		}

		$vide = self::champ_vide( $morceaux[0] );

		return $sans ? $vide : ! $vide;
	}

	/**
	 * Un champ du contenu courant est-il vide ?
	 *
	 * @param string $cle Clé du champ (tirets acceptés).
	 * @return bool
	 */
	private static function champ_vide( $cle ) {
		$cle = str_replace( '-', '_', $cle );
		$id  = self::contenu_courant();

		if ( 'contexte' === $cle ) {
			// « si-contexte » : le conteneur « contexte-… » a trouvé un contenu publié.
			return ! $id;
		}
		if ( 'image' === $cle ) {
			return ! has_post_thumbnail( $id );
		}
		if ( taxonomy_exists( 'rolling_' . $cle ) ) {
			// Filtre (métier, format, thème) : vide si aucun terme coché.
			return ! has_term( '', 'rolling_' . $cle, $id );
		}
		if ( 'videos' === $cle && 'rolling_temoignage' === get_post_type( $id ) ) {
			return ! Rolling_Contenus::realisations_liees( $id );
		}
		if ( 'video_liee' === $cle ) {
			// Témoignage : la première réalisation liée a une vidéo.
			$liees = 'rolling_temoignage' === get_post_type( $id ) ? Rolling_Contenus::realisations_liees( $id ) : array();
			return ! $liees || ! Rolling_Contenus::valeur( 'video', $liees[0] );
		}
		if ( 'autres_videos' === $cle ) {
			// Témoignage : plus d'une réalisation liée (la première est en grand en haut de la fiche).
			return count( Rolling_Contenus::realisations_liees( $id ) ) < 2;
		}
		if ( 'recit' === $cle ) {
			return ! Rolling_Contenus::a_recit( $id );
		}
		if ( 'temoignage_lie' === $cle ) {
			return ! Rolling_Contenus::temoignage_lie( $id );
		}
		if ( 'temoignage_recit' === $cle ) {
			// Le témoignage lié a un récit (sa fiche existe ; sinon elle renvoie vers la réalisation).
			$temoignage = Rolling_Contenus::temoignage_lie( $id );
			return ! $temoignage || ! Rolling_Contenus::a_recit( $temoignage );
		}
		if ( 0 === strpos( $cle, 'temoignage_' ) ) {
			// Champ du témoignage lié à la réalisation affichée (« si-temoignage-portrait »…).
			$temoignage = Rolling_Contenus::temoignage_lie( $id );
			$valeur     = $temoignage ? Rolling_Contenus::valeur( substr( $cle, 11 ), $temoignage ) : '';
			return '' === $valeur || array() === $valeur || null === $valeur;
		}
		if ( 'mise_a_jour' === $cle ) {
			// Vide tant que l'article n'a pas été modifié un autre jour que celui de sa publication.
			return get_the_modified_date( 'Y-m-d', $id ) <= get_the_date( 'Y-m-d', $id );
		}

		$valeur = Rolling_Contenus::valeur( $cle, $id );

		return '' === $valeur || array() === $valeur || null === $valeur;
	}

	/**
	 * Page du blog : la « page des articles » de WordPress si elle est réglée, sinon la page « blog » (page Elementor
	 * qui affiche la liste des articles avec le widget « Rolling · Liste »).
	 *
	 * @return int
	 */
	public static function page_blog() {
		$id = (int) get_option( 'page_for_posts' );
		if ( ! $id ) {
			$page = get_page_by_path( 'blog' );
			$id   = $page ? (int) $page->ID : 0;
		}

		return $id;
	}

	/**
	 * Modèle Elementor utilisé pour une fiche (réglage, sinon le modèle installé par le thème).
	 *
	 * @param string $type Type de contenu, ou « 404 ».
	 * @return int
	 */
	public static function modele_fiche( $type ) {
		if ( ! isset( self::FICHES[ $type ] ) ) {
			return 0;
		}

		$choisi = (int) get_theme_mod( 'rolling_modele_' . $type );
		if ( $choisi && 'elementor_library' === get_post_type( $choisi ) && 'publish' === get_post_status( $choisi ) ) {
			return $choisi;
		}

		$suivi = get_option( Rolling_Installateur::OPTION_MODELES, array() );
		$id    = (int) ( $suivi[ self::FICHES[ $type ] ]['post_id'] ?? 0 );

		return $id && get_post( $id ) ? $id : 0;
	}

	/**
	 * Fiche : gabarit du thème qui affiche le modèle Elementor, sauf si le Theme Builder de Pro en prévoit un.
	 *
	 * @param string $gabarit Gabarit choisi par WordPress.
	 * @return string
	 */
	public static function gabarit_fiche( $gabarit ) {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return $gabarit;
		}

		if ( is_404() ) {
			$type = '404';
		} elseif ( is_page() && is_page_template( self::MODELE_TEXTE ) ) {
			$type = 'texte';
		} elseif ( is_singular( array_keys( self::FICHES ) ) ) {
			$type = get_post_type();
			// Un article construit avec Elementor garde sa propre mise en page.
			if ( 'post' === $type && \Elementor\Plugin::$instance->documents->get( get_the_ID() )?->is_built_with_elementor() ) {
				return $gabarit;
			}
		} else {
			return $gabarit;
		}

		if ( self::theme_builder_prevu( '404' === $type ? 'single' : 'single' ) || ! self::modele_fiche( $type ) ) {
			return $gabarit;
		}

		self::$fiche = self::modele_fiche( $type );
		// La fiche n'est pas une page Elementor : on demande à Elementor de charger ses styles et scripts.
		add_action( 'wp_enqueue_scripts', array( \Elementor\Plugin::$instance->frontend, 'enqueue_styles' ), \Elementor\Frontend::ENQUEUED_STYLES_PRIORITY );
		add_action( 'wp_enqueue_scripts', array( \Elementor\Plugin::$instance->frontend, 'enqueue_scripts' ) );

		return ROLLING_DIR . '/modeles/fiche.php';
	}

	/**
	 * Styles des modèles affichés dans la page : modèle de la fiche et modèles de cartes des listes.
	 *
	 * Elementor ne prépare que les styles des documents qu'il affiche lui-même ; on lui signale les autres.
	 */
	public static function styles_des_modeles() {
		$documents = array_filter( array( is_singular() ? (int) get_the_ID() : 0, self::$fiche ) );
		$modeles   = self::$fiche ? array( self::$fiche ) : array();

		foreach ( $documents as $document ) {
			$modeles = array_merge( $modeles, self::modeles_des_listes( $document ) );
		}
		foreach ( $modeles as $modele ) {
			$modeles = array_merge( $modeles, self::modeles_des_listes( $modele ) );
		}

		foreach ( array_unique( array_filter( $modeles ) ) as $modele ) {
			do_action( 'elementor/post/render', $modele ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound, WordPress.NamingConventions.ValidHookName.UseUnderscores -- crochet d'Elementor.
			\Elementor\Core\Files\CSS\Post::create( $modele )->enqueue();
		}
	}

	/**
	 * Modèles de cartes affichés par un document : rattachés à lui, leurs classes vont dans la même feuille de
	 * style que la page (sinon l'ordre des classes entre plusieurs feuilles n'est plus respecté).
	 *
	 * @param array $lies    Documents liés.
	 * @param int   $post_id Document.
	 * @return array
	 */
	public static function documents_lies( $lies, $post_id ) {
		return array_merge( (array) $lies, self::modeles_des_listes( (int) $post_id ) );
	}

	/**
	 * Classes globales écrites par les widgets Rolling (leur HTML), à charger avec le document.
	 *
	 * Elementor ne charge que les classes portées par ses éléments ; les widgets écrivent les leurs dans le HTML.
	 *
	 * @param array $ids     Classes déjà trouvées.
	 * @param int   $post_id Document.
	 * @return array
	 */
	public static function classes_des_widgets( $ids, $post_id ) {
		$donnees = (string) get_post_meta( $post_id, '_elementor_data', true );
		if ( false === strpos( $donnees, '"widgetType":"rolling-' ) ) {
			return $ids;
		}

		$labels = array( 'filtres-liste', 'chip-filtre', 'rangee', 'liste-plus', 'bouton-verre', 'bouton-petit', 'plaque-libre', 'chiffre-verre', 'libelle-chiffre', 'texte-masque', 'liste-coches', 'ligne-coche', 'coche', 'texte', 'texte-petit', 'generique-liste', 'compte-rebours', 'cr-pilule', 'cr-point', 'cr-label', 'cr-temps', 'cr-direct', 'cr-fin', 'cadre-photo', 'video-fiche', 'image-couvrante', 'bandeau-video', 'duree', 'lecture-centre', 'transcription', 'masque-bords', 'logos-defilants', 'grille-6', 'carte-verre', 'tuile-logo', 'ariane', 'article-texte', 'sommaire', 'partage', 'partage-bouton', 'chip-neutre', 'resumer', 'label', 'label-violet' );
		if ( preg_match_all( '/"(?:titre_)?classe(?:_texte)?":"([^"]+)"/', $donnees, $trouves ) ) {
			foreach ( $trouves[1] as $liste ) {
				$labels = array_merge( $labels, preg_split( '/\s+/', $liste ) );
			}
		}

		$depot = '\\Elementor\\Modules\\GlobalClasses\\Global_Classes_Repository';
		if ( ! class_exists( $depot ) ) {
			return $ids;
		}
		$par_label = array_flip( $depot::make()->all_labels() );
		foreach ( array_unique( $labels ) as $label ) {
			if ( isset( $par_label[ $label ] ) ) {
				$ids[] = $par_label[ $label ];
			}
		}

		return $ids;
	}

	/**
	 * Modèles de cartes utilisés par les listes d'un document.
	 *
	 * @param int $document Document Elementor.
	 * @return int[]
	 */
	private static function modeles_des_listes( $document ) {
		$donnees = (string) get_post_meta( $document, '_elementor_data', true );
		if ( false === strpos( $donnees, 'rolling-liste' ) ) {
			return array();
		}

		$ids = array();
		foreach ( (array) get_option( Rolling_Installateur::OPTION_MODELES, array() ) as $cle => $info ) {
			if ( 0 === strpos( (string) $cle, 'c' ) && ! empty( $info['post_id'] ) ) {
				$ids[] = (int) $info['post_id'];
			}
		}
		if ( preg_match_all( '/"modele(?:_une|_vide)?":"?(\d+)/', $donnees, $trouves ) ) {
			$ids = array_merge( $ids, array_map( 'intval', $trouves[1] ) );
		}

		return $ids;
	}

	/**
	 * Le Theme Builder d'Elementor Pro a-t-il un modèle pour cet emplacement ?
	 *
	 * @param string $emplacement Emplacement.
	 * @return bool
	 */
	private static function theme_builder_prevu( $emplacement ) {
		if ( ! class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
			return false;
		}
		$conditions = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager();

		return (bool) $conditions->get_documents_for_location( $emplacement );
	}

	/**
	 * Affiche le modèle de la fiche courante.
	 */
	public static function afficher_fiche() {
		$modele = self::$fiche ? self::$fiche : self::modele_fiche( is_404() ? '404' : get_post_type() );

		if ( $modele ) {
			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $modele, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML produit par Elementor.
		}
	}

	/**
	 * Réglages : modèle de chaque fiche.
	 *
	 * @param WP_Customize_Manager $wp_customize Gestionnaire.
	 */
	public static function personnaliser( $wp_customize ) {
		$wp_customize->add_section(
			'rolling_modeles',
			array(
				'title'       => __( 'Rolling : modèles des fiches', 'rolling' ),
				'description' => __( 'Modèle de « Mes modèles » utilisé pour chaque fiche. Laissez « Modèle du thème » pour suivre les mises à jour du thème.', 'rolling' ),
				'priority'    => 31,
			)
		);

		$choix   = array( 0 => __( 'Modèle du thème', 'rolling' ) );
		$modeles = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'posts_per_page' => 300,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		foreach ( $modeles as $modele ) {
			$choix[ $modele->ID ] = get_the_title( $modele );
		}

		$libelles = array(
			'rolling_realisation' => __( 'Fiche réalisation', 'rolling' ),
			'rolling_temoignage'  => __( 'Fiche témoignage', 'rolling' ),
			'post'                => __( 'Article de blog', 'rolling' ),
			'404'                 => __( 'Page 404', 'rolling' ),
			'texte'               => __( 'Page de texte (mentions légales, CGV, confidentialité)', 'rolling' ),
		);
		foreach ( $libelles as $type => $libelle ) {
			$wp_customize->add_setting( 'rolling_modele_' . $type, array( 'sanitize_callback' => 'absint' ) );
			$wp_customize->add_control(
				'rolling_modele_' . $type,
				array(
					'label'   => $libelle,
					'section' => 'rolling_modeles',
					'type'    => 'select',
					'choices' => $choix,
				)
			);
		}
	}

	/**
	 * Chiffres en verre : images de la médiathèque pour chaque caractère (0-9, +, %, k, M).
	 *
	 * @param string $texte Valeur saisie (« 87 % », « 2 500+ »).
	 * @return string HTML (images décoratives, espaces conservés).
	 */
	public static function chiffres_en_verre( $texte ) {
		$medias = (array) get_option( Rolling_Installateur::OPTION_MEDIAS, array() );
		$noms   = array(
			'+' => 'plus',
			'%' => 'pourcent',
			'k' => 'k',
			'K' => 'k',
			'M' => 'M',
		);
		$html   = '';
		$reste  = '';
		// Caractères sans image en verre (virgule, « / », « : », « < », lettres…) : écrits en texte, dans le dégradé des
		// chiffres. Les lettres seules (« mois », « min ») sont plus petites, comme une unité.
		$texte_libre = function () use ( &$reste, &$html ) {
			if ( '' === $reste ) {
				return;
			}
			$unite = (bool) preg_match( '/^\p{L}+$/u', $reste );
			$html .= '<span class="chiffre-texte' . ( $unite ? ' chiffre-unite' : '' ) . '" aria-hidden="true">' . esc_html( $reste ) . '</span>';
			$reste = '';
		};

		foreach ( preg_split( '//u', $texte, -1, PREG_SPLIT_NO_EMPTY ) as $caractere ) {
			$cle = ctype_digit( $caractere ) ? $caractere : ( $noms[ $caractere ] ?? '' );
			if ( '' !== $cle && ! empty( $medias[ 'chiffre-' . $cle ] ) ) {
				$texte_libre();
				$html .= wp_get_attachment_image(
					(int) $medias[ 'chiffre-' . $cle ],
					'medium',
					false,
					array(
						'alt'     => '',
						'loading' => 'lazy',
						'class'   => 'chiffre-image',
					)
				);
			} elseif ( preg_match( '/[\s\x{00A0}\x{202F}]/u', $caractere ) ) {
				$texte_libre();
				$html .= '<span class="espace-chiffre" aria-hidden="true"></span>';
			} else {
				$reste .= $caractere;
			}
		}
		$texte_libre();

		return $html;
	}

	/**
	 * Image d'un média installé par le thème (coche, bouton lecture…).
	 *
	 * @param string $cle   Clé du média.
	 * @param string $taille Taille.
	 * @param array  $attr  Attributs.
	 * @return string
	 */
	public static function image_du_theme( $cle, $taille = 'thumbnail', $attr = array() ) {
		$medias = (array) get_option( Rolling_Installateur::OPTION_MEDIAS, array() );

		return empty( $medias[ $cle ] ) ? '' : wp_get_attachment_image( (int) $medias[ $cle ], $taille, false, $attr + array( 'alt' => '' ) );
	}
}
