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
			)
		);
		$this->choix( 'modele', __( 'Modèle de carte', 'rolling' ), self::modeles( __( 'Carte du thème', 'rolling' ) ), '0' );
		$this->choix( 'modele_une', __( 'Modèle de la première carte', 'rolling' ), self::modeles( __( 'Comme les autres', 'rolling' ) ), '0' );
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
					'rolling_theme'  => __( 'Thèmes', 'rolling' ),
					'category'       => __( 'Catégories du blog', 'rolling' ),
				),
				'default' => '',
			)
		);
		$this->texte( 'tous', __( 'Filtres : premier bouton', 'rolling' ), __( 'Tout voir', 'rolling' ) );
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
			'c2-carte-temoignage-une' => 'c3-carte-temoignage-une',
			'c6-carte-webinar-une'    => 'c7-carte-webinar-une',
			'c4-carte-article-une'    => 'c9-carte-article-une',
			'c6-carte-webinar-vide'   => 'c10-aucun-webinar',
		);
		$cle   = $cles[ $cle ] ?? $cle;

		return (int) ( $suivi[ $cle ]['post_id'] ?? 0 );
	}

	/**
	 * Filtre demandé dans l'adresse (?metier=…, ?format=…, ?theme=…, ?categorie=…).
	 *
	 * @param string $taxonomie Taxonomie.
	 * @return string
	 */
	private static function filtre_demande( $taxonomie ) {
		$parametres = array(
			'rolling_metier' => 'metier',
			'rolling_format' => 'format',
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

		if ( 'liees' === $r['contenu'] ) {
			$ids = array_values( array_filter( (array) Rolling_Contenus::valeur( 'videos', $courant ), 'get_post' ) );
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

		$filtres = 'both' === $r['filtres'] ? array( 'rolling_metier', 'rolling_format' ) : array_filter( array( $r['filtres'] ) );
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
		$variante            = 'une' === ( $r['carte_theme'] ?? '' ) ? '-une' : '';
		$modele              = (int) $r['modele'] ? (int) $r['modele'] : self::modele_du_theme( $r['contenu'], $variante );
		$modele              = $modele ? $modele : self::modele_du_theme( $r['contenu'] );
		$modele_une          = (int) $r['modele_une'];

		if ( ! $modele_une && in_array( $r['contenu'], array( 'temoignages', 'webinars' ), true ) && ! (int) $r['decalage'] ) {
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
		echo '<div class="rl-liste ' . esc_attr( $r['classe'] ) . '">';
		foreach ( $ids as $rang => $id ) {
			$post = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- boucle de cartes, restauré après.
			setup_postdata( $post );
			$carte = 0 === $rang && $modele_une ? $modele_une : $modele;
			// Styles déjà chargés dans l'en-tête (Rolling_Dynamique::styles_des_modeles) : pas de feuille dans la grille.
			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $carte, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML produit par Elementor.
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
	 * Boutons de filtre.
	 *
	 * @param array $r Réglages.
	 */
	private function afficher_filtres( $r ) {
		$taxonomies = 'both' === $r['filtres'] ? array( 'rolling_metier', 'rolling_format' ) : array_filter( array( $r['filtres'] ) );
		if ( ! $taxonomies ) {
			return;
		}

		$parametres = array(
			'rolling_metier' => 'metier',
			'rolling_format' => 'format',
			'rolling_theme'  => 'theme',
			'category'       => 'categorie',
		);
		$base       = remove_query_arg( array_merge( array_values( $parametres ), array( 'voir' ) ) );

		/* translators: %s : contenu de la liste (Réalisations, Guides…). */
		echo '<nav class="filtres-liste" aria-label="' . esc_attr( sprintf( __( 'Filtrer : %s', 'rolling' ), self::CONTENUS[ $r['contenu'] ][0] ?? '' ) ) . '">';
		foreach ( $taxonomies as $rang => $taxonomie ) {
			$actif  = self::filtre_demande( $taxonomie );
			$termes = get_terms(
				array(
					'taxonomy'   => $taxonomie,
					'hide_empty' => true,
				)
			);
			echo '<ul class="rangee">';
			if ( 0 === $rang ) {
				$aucun = ! array_filter( array_map( array( __CLASS__, 'filtre_demande' ), $taxonomies ) );
				echo '<li><a class="chip-filtre" href="' . esc_url( $base ) . '"' . ( $aucun ? ' aria-current="page"' : '' ) . '>' . esc_html( $r['tous'] ) . '</a></li>';
			}
			foreach ( is_wp_error( $termes ) ? array() : $termes as $terme ) {
				$lien = add_query_arg( $parametres[ $taxonomie ], $terme->slug, $base );
				echo '<li><a class="chip-filtre" href="' . esc_url( $lien ) . '"' . ( $actif === $terme->slug ? ' aria-current="page"' : '' ) . '>' . esc_html( $terme->name ) . '</a></li>';
			}
			echo '</ul>';
		}
		echo '</nav>';
	}
}
