<?php
/**
 * Widget Rolling : Rolling_Widget_Liste.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Liste de cartes : réalisations, témoignages, articles, guides, webinars, replays.
 *
 * Chaque carte est un modèle de « Mes modèles », modifiable dans Elementor (balises Rolling).
 */
class Rolling_Widget_Liste extends Rolling_Widget {

	/**
	 * Contenus : clé => [ libellé, clé du modèle de carte du thème ].
	 *
	 * @var array
	 */
	const CONTENUS = array(
		'realisations' => array( 'Réalisations', 'c1-carte-realisation' ),
		'autres'       => array( 'Autres réalisations (sur une fiche réalisation)', 'c1-carte-realisation' ),
		'liees'        => array( 'Vidéos du témoignage (sur une fiche témoignage)', 'c1-carte-realisation' ),
		'liees_suite'  => array( 'Autres vidéos du témoignage (sans la première)', 'c1-carte-realisation' ),
		'temoignages'  => array( 'Témoignages', 'c2-carte-temoignage' ),
		'articles'     => array( 'Articles du blog', 'c4-carte-article' ),
		'lire_aussi'   => array( 'À lire aussi (sur un article)', 'c4-carte-article' ),
		'guides'       => array( 'Guides', 'c5-carte-guide' ),
		'webinars'     => array( 'Webinars à venir', 'c6-carte-webinar' ),
		'replays'      => array( 'Replays', 'c8-carte-replay' ),
	);

	/** Nom. */
	public function get_name() {
		return 'rolling-liste';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Liste de cartes', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'liste', array( 'label' => __( 'Liste', 'rolling' ) ) );
		$contenus = array();
		foreach ( self::CONTENUS as $cle => $c ) {
			$contenus[ $cle ] = $c[0];
		}
		$this->choix( 'contenu', __( 'Contenu', 'rolling' ), $contenus );
		$this->add_control(
			'nombre',
			array(
				'label'   => __( 'Nombre de cartes', 'rolling' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 9,
				'min'     => 1,
				'max'     => 60,
			)
		);
		$this->add_control(
			'decalage',
			array(
				'label'       => __( 'Ignorer les premiers', 'rolling' ),
				'description' => __( 'Ex. : 1 pour une liste « Les suivants » sous « Le prochain webinar ».', 'rolling' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
			)
		);
		$this->texte( 'titre', __( 'Titre au-dessus (affiché seulement s’il y a des cartes)', 'rolling' ), '' );
		$this->texte( 'titre_classe', __( 'Classes du titre', 'rolling' ), 'label label-violet' );
		$this->texte( 'classe', __( 'Classe de la grille', 'rolling' ), 'grille-3' );
		$this->choix(
			'carte_theme',
			__( 'Carte du thème', 'rolling' ),
			array(
				'normale' => __( 'Normale', 'rolling' ),
				'une'     => __( 'À la une (grande carte)', 'rolling' ),
				'bulle'   => __( 'Bulle de citation (témoignages)', 'rolling' ),
			)
		);
		$this->choix( 'modele', __( 'Modèle de carte', 'rolling' ), self::modeles( __( 'Carte du thème', 'rolling' ) ), '0' );
		$this->choix( 'modele_une', __( 'Modèle de la première carte', 'rolling' ), self::modeles( __( 'Comme les autres', 'rolling' ) ), '0' );
		$this->add_control(
			'une_auto',
			array(
				'label'       => __( 'Témoignages : le témoignage « à la une » en grand', 'rolling' ),
				'description' => __( 'Désactivez pour des cartes toutes identiques (ex. 3 témoignages sur la landing).', 'rolling' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);
		$this->add_control(
			'avec_citation',
			array(
				'label'       => __( 'Témoignages : seulement ceux qui ont une citation', 'rolling' ),
				'description' => __( 'Ex. : la landing, où chaque carte doit montrer une citation.', 'rolling' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
			)
		);
		$this->add_control(
			'citations_courtes',
			array(
				'label'       => __( 'Témoignages : les citations les plus courtes', 'rolling' ),
				'description' => __( 'Ex. : les bulles autour du showreel de la page Contact.', 'rolling' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
			)
		);
		$this->add_control(
			'extraits',
			array(
				'label'       => __( 'Mur de montage : un extrait vidéo toutes les … réalisations', 'rolling' ),
				'description' => __( 'Réalisations > Extraits vidéo. 0 : aucun extrait.', 'rolling' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'min'         => 0,
			)
		);
		$this->texte( 'texte_extrait', __( 'Mur de montage : pastille des extraits', 'rolling' ), __( 'Studio Rolling', 'rolling' ) );
		$this->add_control(
			'premiere_une',
			array(
				'label'       => __( 'Première carte à la une (carte du thème)', 'rolling' ),
				'description' => __( 'Ex. : le dernier article en grand sur la page Blog.', 'rolling' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
			)
		);
		$this->add_control(
			'filtres',
			array(
				'label'   => __( 'Filtres au-dessus de la liste', 'rolling' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''               => __( 'Aucun', 'rolling' ),
					'rolling_metier' => __( 'Métiers', 'rolling' ),
					'rolling_format' => __( 'Formats vidéo', 'rolling' ),
					'both'           => __( 'Métiers et formats vidéo', 'rolling' ),
					'usage'          => __( 'Métiers et cas d’usage', 'rolling' ),
					'trois'          => __( 'Métiers, cas d’usage et formats vidéo', 'rolling' ),
					'rolling_usage'  => __( 'Cas d’usage', 'rolling' ),
					'rolling_theme'  => __( 'Thèmes', 'rolling' ),
					'category'       => __( 'Catégories du blog', 'rolling' ),
				),
				'default' => '',
			)
		);
		$this->texte( 'tous', __( 'Filtres : premier bouton', 'rolling' ), __( 'Tout voir', 'rolling' ) );
		$this->texte( 'nom_metier', __( 'Filtres sur deux lignes : nom de la ligne des métiers', 'rolling' ), __( 'Métier', 'rolling' ) );
		$this->texte( 'nom_format', __( 'Filtres sur deux lignes : nom de la ligne des formats', 'rolling' ), __( 'Format', 'rolling' ) );
		$this->texte( 'texte_aucun', __( 'Filtres sans résultat : message', 'rolling' ), __( 'Aucune réalisation pour ces filtres.', 'rolling' ) );
		$this->texte( 'texte_tout_voir', __( 'Filtres sans résultat : bouton', 'rolling' ), __( 'Tout voir', 'rolling' ) );
		$this->texte( 'nom_usage', __( 'Filtres sur deux lignes : nom de la ligne des cas d’usage', 'rolling' ), __( 'Cas d’usage', 'rolling' ) );
		$this->add_control(
			'plus',
			array(
				'label'   => __( 'Bouton « Voir plus »', 'rolling' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);
		$this->texte( 'plus_texte', __( 'Texte du bouton', 'rolling' ), __( 'Voir plus', 'rolling' ) );
		$this->choix( 'modele_vide', __( 'Liste vide : modèle à afficher', 'rolling' ), self::modeles( __( 'Celui du thème (webinars : « Pas de webinar programmé »), sinon rien', 'rolling' ) ), '0' );
		$this->end_controls_section();
	}

	/**
	 * Modèle de carte du thème pour un contenu.
	 *
	 * @param string $contenu Contenu.
	 * @param string $suffixe Suffixe de clé (« -une »).
	 * @return int
	 */
	public static function modele_du_theme( $contenu, $suffixe = '' ) {
		$suivi = get_option( Rolling_Installateur::OPTION_MODELES, array() );
		$cle   = ( self::CONTENUS[ $contenu ][1] ?? '' ) . $suffixe;
		$cles  = array(
			'c2-carte-temoignage-une'   => 'c3-carte-temoignage-une',
			'c6-carte-webinar-une'      => 'c7-carte-webinar-une',
			'c4-carte-article-une'      => 'c9-carte-article-une',
			'c6-carte-webinar-vide'     => 'c10-aucun-webinar',
			'c2-carte-temoignage-bulle' => 'c12-carte-bulle',
		);
		$cle   = $cles[ $cle ] ?? $cle;

		return (int) ( $suivi[ $cle ]['post_id'] ?? 0 );
	}

	/**
	 * Taxonomies d'un réglage « Filtres » (une ou plusieurs lignes).
	 *
	 * @param string $choix Réglage.
	 * @return string[]
	 */
	private static function taxonomies_filtres( $choix ) {
		$groupes = array(
			'both'  => array( 'rolling_metier', 'rolling_format' ),
			'usage' => array( 'rolling_metier', 'rolling_usage' ),
			'trois' => array( 'rolling_metier', 'rolling_usage', 'rolling_format' ),
		);

		return $groupes[ $choix ] ?? array_filter( array( $choix ) );
	}

	/**
	 * Filtre demandé dans l'adresse (?metier=…, ?format=…, ?usage=…, ?theme=…, ?categorie=…).
	 *
	 * @param string $taxonomie Taxonomie.
	 * @return string
	 */
	private static function filtre_demande( $taxonomie ) {
		$parametres = array(
			'rolling_metier' => 'metier',
			'rolling_format' => 'format',
			'rolling_usage'  => 'usage',
			'rolling_theme'  => 'theme',
			'category'       => 'categorie',
		);
		// Lecture seule d'un filtre de liste public : pas de jeton nécessaire.
		return isset( $_GET[ $parametres[ $taxonomie ] ] ) ? sanitize_title( wp_unslash( $_GET[ $parametres[ $taxonomie ] ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Contenus à afficher.
	 *
	 * @param array $r Réglages.
	 * @return array [ ids, il en reste ]
	 */
	private function contenus( $r ) {
		$nombre   = max( 1, (int) $r['nombre'] );
		$decalage = max( 0, (int) $r['decalage'] );
		$page     = isset( $_GET['voir'] ) ? max( 1, absint( $_GET['voir'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$total    = $nombre * $page;
		$courant  = $this->contenu();

		if ( in_array( $r['contenu'], array( 'webinars', 'replays' ), true ) ) {
			$ids = Rolling_Webinars::liste( 'webinars' === $r['contenu'] ? 'a_venir' : 'replay' );
			return array( array_slice( $ids, $decalage, $total ), count( $ids ) > $decalage + $total );
		}

		if ( in_array( $r['contenu'], array( 'liees', 'liees_suite' ), true ) ) {
			$ids = Rolling_Contenus::realisations_liees( $courant );
			$ids = 'liees_suite' === $r['contenu'] ? array_slice( $ids, 1 ) : $ids;
			return array( array_slice( $ids, $decalage, $total ), false );
		}

		$types = array(
			'realisations' => 'rolling_realisation',
			'autres'       => 'rolling_realisation',
			'temoignages'  => 'rolling_temoignage',
			'articles'     => 'post',
			'lire_aussi'   => 'post',
			'guides'       => 'rolling_guide',
		);
		$args  = array(
			'post_type'           => $types[ $r['contenu'] ] ?? 'post',
			'posts_per_page'      => $total + 1,
			'offset'              => $decalage,
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( in_array( $r['contenu'], array( 'autres', 'lire_aussi' ), true ) && $courant ) {
			$args['post__not_in'] = array( $courant );
		}
		if ( 'lire_aussi' === $r['contenu'] && $courant ) {
			$categories = wp_get_post_categories( $courant );
			if ( $categories ) {
				$args['category__in'] = $categories;
			}
		}

		$filtres = self::taxonomies_filtres( $r['filtres'] );
		foreach ( $filtres as $taxonomie ) {
			$terme = self::filtre_demande( $taxonomie );
			if ( $terme ) {
				$args['tax_query'][] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					'taxonomy' => $taxonomie,
					'field'    => 'slug',
					'terms'    => $terme,
				);
			}
		}

		if ( 'temoignages' === $r['contenu'] && ( 'yes' === ( $r['avec_citation'] ?? '' ) || 'yes' === ( $r['citations_courtes'] ?? '' ) ) ) {
			$args['meta_query'][] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'key'     => Rolling_Contenus::PREFIXE . 'citation',
				'value'   => '',
				'compare' => '!=',
			);
		}

		if ( 'temoignages' === $r['contenu'] && 'yes' === ( $r['citations_courtes'] ?? '' ) ) {
			// Les citations les plus courtes (ex. bulles autour du showreel) : pas de « à la une », pas de suite.
			$args['posts_per_page'] = 60;
			$args['offset']         = 0;
			$ids                    = get_posts( $args );
			usort(
				$ids,
				function ( $a, $b ) {
					return mb_strlen( (string) Rolling_Contenus::valeur( 'citation', $a ) ) <=> mb_strlen( (string) Rolling_Contenus::valeur( 'citation', $b ) );
				}
			);
			return array( array_slice( $ids, 0, $total ), false );
		}

		$ids = get_posts( $args );

		// Témoignages : celui « à la une » en premier.
		if ( 'temoignages' === $r['contenu'] && ! $decalage ) {
			$une = get_posts(
				array(
					'post_type'      => 'rolling_temoignage',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_key'       => Rolling_Contenus::PREFIXE . 'a_la_une', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				)
			);
			if ( $une ) {
				$ids = array_merge( $une, array_values( array_diff( $ids, $une ) ) );
			}
		}

		return array( array_slice( $ids, 0, $total ), count( $ids ) > $total );
	}

	/** Affichage. */
	protected function render() {
		$r                   = $this->get_settings_for_display();
		list( $ids, $reste ) = $this->contenus( $r );
		$variante            = in_array( $r['carte_theme'] ?? '', array( 'une', 'bulle' ), true ) ? '-' . $r['carte_theme'] : '';
		$modele              = (int) $r['modele'] ? (int) $r['modele'] : self::modele_du_theme( $r['contenu'], $variante );
		$modele              = $modele ? $modele : self::modele_du_theme( $r['contenu'] );
		$modele_une          = (int) $r['modele_une'];

		if ( ! $modele_une && in_array( $r['contenu'], array( 'temoignages', 'webinars' ), true ) && ! (int) $r['decalage'] && 'yes' === ( $r['une_auto'] ?? 'yes' ) ) {
			$modele_une = 'temoignages' === $r['contenu'] && $ids && ! Rolling_Contenus::valeur( 'a_la_une', $ids[0] ) ? 0 : self::modele_du_theme( $r['contenu'], '-une' );
			$modele_une = 'webinars' === $r['contenu'] ? 0 : $modele_une;
		}
		if ( ! $modele_une && 'yes' === ( $r['premiere_une'] ?? '' ) ) {
			$modele_une = self::modele_du_theme( $r['contenu'], '-une' );
		}

		$this->afficher_filtres( $r );

		if ( ! $ids ) {
			$vide = (int) $r['modele_vide'] ? (int) $r['modele_vide'] : ( 'webinars' === $r['contenu'] && ! (int) $r['decalage'] ? self::modele_du_theme( 'webinars', '-vide' ) : 0 );
			if ( $vide ) {
				echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $vide, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML produit par Elementor.
			} elseif ( array_filter( array_map( array( __CLASS__, 'filtre_demande' ), self::taxonomies_filtres( $r['filtres'] ) ) ) ) {
				// Des filtres sont choisis mais aucun contenu ne correspond : le dire, et proposer de tout revoir.
				$tout = remove_query_arg( array( 'metier', 'format', 'usage', 'theme', 'categorie', 'voir' ) );
				echo '<div class="carte-verre carte-vide"><p class="titre-xs">' . esc_html( $r['texte_aucun'] ?? '' ) . '</p><a class="bouton-verre bouton-petit" href="' . esc_url( $tout ) . '">' . esc_html( $r['texte_tout_voir'] ?? '' ) . '</a></div>';
			}
			return;
		}

		if ( '' !== trim( (string) ( $r['titre'] ?? '' ) ) ) {
			echo '<h2 class="' . esc_attr( $r['titre_classe'] ) . '">' . esc_html( $r['titre'] ) . '</h2>';
		}

		if ( ! $modele ) {
			return;
		}

		global $post;
		$avant = $post;
		// Mur de montage (classe « mur-montage ») : chaque carte dans une case, la première en grand, les 9:16 en hauteur.
		$classes = preg_split( '/\s+/', (string) $r['classe'] );
		$mur     = in_array( 'mur-montage', $classes, true );
		// Mur maçonné des témoignages (« mur-temoins ») : chaque carte dans une case à la taille de sa citation.
		$macon    = in_array( 'mur-temoins', $classes, true );
		$extraits = $mur ? self::extraits( $r, count( $ids ) ) : array();
		echo '<div class="rl-liste ' . esc_attr( $r['classe'] ) . '">';
		foreach ( $ids as $rang => $id ) {
			$post = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- boucle de cartes, restauré après.
			setup_postdata( $post );
			$carte = 0 === $rang && $modele_une ? $modele_une : $modele;
			if ( $mur ) {
				$case = 0 === $rang ? ' mur-large' : ( self::vertical( $id ) ? ' mur-haute' : '' );
				echo '<div class="mur-case' . esc_attr( $case ) . '">';
			}
			if ( $macon ) {
				echo '<div class="macon-case ' . esc_attr( 0 === $rang && $modele_une ? 'macon-une' : self::longueur_citation( $id ) ) . '">';
			}
			// Styles déjà chargés dans l'en-tête (Rolling_Dynamique::styles_des_modeles) : pas de feuille dans la grille.
			echo Rolling_Dynamique::affiches_video( \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $carte, false ), get_the_title( $id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML produit par Elementor.
			if ( $mur || $macon ) {
				echo '</div>';
			}
			$pas = (int) ( $r['extraits'] ?? 0 );
			if ( $extraits && $pas > 0 && 0 === ( $rang + 1 ) % $pas ) {
				self::afficher_extrait( array_shift( $extraits ), (string) ( $r['texte_extrait'] ?? '' ) );
			}
		}
		echo '</div>';
		$post = $avant; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- restauration.
		if ( $avant ) {
			setup_postdata( $avant );
		} else {
			wp_reset_postdata();
		}

		if ( $reste && 'yes' === $r['plus'] ) {
			$page = isset( $_GET['voir'] ) ? max( 1, absint( $_GET['voir'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<p class="liste-plus"><a class="bouton-verre bouton-petit" href="' . esc_url( add_query_arg( 'voir', $page + 1 ) ) . '">' . esc_html( $r['plus_texte'] ) . '</a></p>';
		}
	}

	/**
	 * Taille d'une carte témoignage selon sa citation : courte, moyenne ou longue.
	 *
	 * @param int $id Témoignage.
	 * @return string
	 */
	private static function longueur_citation( $id ) {
		$n = mb_strlen( trim( wp_strip_all_tags( (string) Rolling_Contenus::valeur( 'citation', $id ) ) ) );
		if ( ! $n ) {
			return 'macon-moyen';
		}

		return $n <= 70 ? 'macon-court' : ( $n >= 170 ? 'macon-long' : 'macon-moyen' );
	}

	/**
	 * Extraits vidéo (Réalisations > Extraits vidéo) glissés dans le mur de montage, pour la page affichée.
	 *
	 * @param array $r      Réglages.
	 * @param int   $cartes Nombre de réalisations affichées.
	 * @return int[]
	 */
	private static function extraits( $r, $cartes ) {
		$pas = (int) ( $r['extraits'] ?? 0 );
		if ( $pas < 1 || $cartes < $pas || ! post_type_exists( 'rolling_extrait' ) ) {
			return array();
		}
		$places = intdiv( $cartes, $pas );
		$page   = isset( $_GET['voir'] ) ? max( 1, absint( $_GET['voir'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return get_posts(
			array(
				'post_type'      => 'rolling_extrait',
				'posts_per_page' => $places,
				'offset'         => ( $page - 1 ) * $places,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_key'       => Rolling_Contenus::PREFIXE . 'video', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_compare'   => '!=',
				'meta_value'     => '', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
	}

	/**
	 * Un extrait vidéo dans le mur : vidéo muette en boucle (lancée par rolling.js quand elle est à l'écran), sans titre
	 * ni lien ; image fixe avec « réduire les animations ».
	 *
	 * @param int    $id       Extrait.
	 * @param string $pastille Texte de la pastille.
	 */
	private static function afficher_extrait( $id, $pastille ) {
		$video  = (string) Rolling_Contenus::valeur( 'video', $id );
		$format = (string) Rolling_Contenus::valeur( 'format', $id );
		$texte  = (string) Rolling_Contenus::valeur( 'description', $id );
		$image  = has_post_thumbnail( $id ) ? get_the_post_thumbnail_url( $id, 'medium_large' ) : '';
		echo '<div class="mur-case extrait-case' . ( '9-16' === $format ? ' mur-haute' : '' ) . '"><div class="extrait-cadre">';
		echo '<div class="extrait-video"' . ( '' !== $texte ? ' role="img" aria-label="' . esc_attr( $texte ) . '"' : '' ) . '>';
		echo '<video src="' . esc_url( $video . ( $image ? '' : '#t=0.5' ) ) . '"' . ( $image ? ' poster="' . esc_url( $image ) . '"' : '' ) . ' muted loop playsinline preload="' . ( $image ? 'none' : 'metadata' ) . '" aria-hidden="true" tabindex="-1"></video>';
		if ( '' !== trim( $pastille ) ) {
			echo '<span class="extrait-pastille" aria-hidden="true">' . esc_html( $pastille ) . '</span>';
		}
		echo '</div></div></div>';
	}

	/**
	 * Réalisation au format vertical (format vidéo dont l'identifiant contient « 9-16 », ex. « Format 9:16 »).
	 *
	 * @param int $id Réalisation.
	 * @return bool
	 */
	private static function vertical( $id ) {
		$formats = get_the_terms( $id, 'rolling_format' );
		foreach ( is_array( $formats ) ? $formats : array() as $format ) {
			if ( false !== strpos( $format->slug, '9-16' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Boutons de filtre.
	 *
	 * @param array $r Réglages.
	 */
	private function afficher_filtres( $r ) {
		$taxonomies = self::taxonomies_filtres( $r['filtres'] );
		if ( ! $taxonomies ) {
			return;
		}

		$parametres = array(
			'rolling_metier' => 'metier',
			'rolling_format' => 'format',
			'rolling_usage'  => 'usage',
			'rolling_theme'  => 'theme',
			'category'       => 'categorie',
		);
		$base       = remove_query_arg( array_merge( array_values( $parametres ), array( 'voir' ) ) );

		/* translators: %s : contenu de la liste (Réalisations, Guides…). */
		echo '<nav class="filtres-liste" aria-label="' . esc_attr( sprintf( __( 'Filtrer : %s', 'rolling' ), self::CONTENUS[ $r['contenu'] ][0] ?? '' ) ) . '">';
		foreach ( $taxonomies as $rang => $taxonomie ) {
			$actif     = self::filtre_demande( $taxonomie );
			$termes    = get_terms(
				array(
					'taxonomy'   => $taxonomie,
					'hide_empty' => true,
				)
			);
			$plusieurs = count( $taxonomies ) > 1;
			if ( $plusieurs && ( is_wp_error( $termes ) || ! $termes ) ) {
				continue; // Ligne sans terme utilisé (ex. aucun cas d'usage coché) : rien à filtrer.
			}
			$ici = $plusieurs ? remove_query_arg( array( $parametres[ $taxonomie ], 'voir' ) ) : $base;
			echo '<ul class="rangee">';
			if ( $plusieurs ) {
				// Deux lignes : chacune porte son nom et son propre « Tous » (il ne retire que son filtre).
				$noms = array(
					'rolling_format' => $r['nom_format'],
					'rolling_usage'  => $r['nom_usage'] ?? '',
				);
				$nom  = $noms[ $taxonomie ] ?? $r['nom_metier'];
				if ( '' !== trim( (string) $nom ) ) {
					echo '<li class="nom-filtre">' . esc_html( $nom ) . '</li>';
				}
				echo '<li><a class="chip-filtre" href="' . esc_url( $ici ) . '"' . ( '' === $actif ? ' aria-current="page"' : '' ) . '>' . esc_html( $r['tous'] ) . '</a></li>';
			} elseif ( 0 === $rang ) {
				$aucun = ! array_filter( array_map( array( __CLASS__, 'filtre_demande' ), $taxonomies ) );
				echo '<li><a class="chip-filtre" href="' . esc_url( $base ) . '"' . ( $aucun ? ' aria-current="page"' : '' ) . '>' . esc_html( $r['tous'] ) . '</a></li>';
			}
			foreach ( is_wp_error( $termes ) ? array() : $termes as $terme ) {
				$lien = add_query_arg( $parametres[ $taxonomie ], $terme->slug, $plusieurs ? $ici : $base );
				echo '<li><a class="chip-filtre" href="' . esc_url( $lien ) . '"' . ( $actif === $terme->slug ? ' aria-current="page"' : '' ) . '>' . esc_html( $terme->name ) . '</a></li>';
			}
			echo '</ul>';
		}
		echo '</nav>';
	}
}
