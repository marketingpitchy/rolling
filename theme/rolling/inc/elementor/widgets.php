<?php
/**
 * Widgets « Rolling » (chargés par Elementor) : listes de cartes et parties des fiches qui se répètent.
 *
 * Leur HTML porte les classes globales du thème (plaque-chiffres, liste-coches…) : la mise en forme reste dans
 * Elementor (Classes), aucune règle CSS du thème n'est accrochée à ces noms.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Socle des widgets Rolling.
 */
abstract class Rolling_Widget extends Widget_Base {

	/** Catégorie. */
	public function get_categories() {
		return array( Rolling_Dynamique::GROUPE );
	}

	/** Mots-clés. */
	public function get_keywords() {
		return array( 'rolling' );
	}

	/** Contenu qui change selon la page : jamais mis en cache. */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/** Pas d'enveloppe interne supplémentaire. */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * Contenu affiché (dans l'éditeur : le dernier contenu du type présenté par le modèle).
	 *
	 * @return int
	 */
	protected function contenu() {
		return Rolling_Dynamique::contenu_courant();
	}

	/**
	 * Ajoute un champ texte.
	 *
	 * @param string $cle     Clé.
	 * @param string $libelle Libellé.
	 * @param string $defaut  Valeur par défaut.
	 * @param string $type    Type de contrôle.
	 */
	protected function texte( $cle, $libelle, $defaut, $type = Controls_Manager::TEXT ) {
		$this->add_control(
			$cle,
			array(
				'label'   => $libelle,
				'type'    => $type,
				'default' => $defaut,
			)
		);
	}

	/**
	 * Ajoute une liste de choix.
	 *
	 * @param string $cle     Clé.
	 * @param string $libelle Libellé.
	 * @param array  $options Options.
	 * @param string $defaut  Valeur par défaut.
	 */
	protected function choix( $cle, $libelle, $options, $defaut = null ) {
		$this->add_control(
			$cle,
			array(
				'label'   => $libelle,
				'type'    => Controls_Manager::SELECT,
				'options' => $options,
				'default' => null === $defaut ? (string) array_key_first( $options ) : $defaut,
			)
		);
	}

	/**
	 * Modèles de « Mes modèles ».
	 *
	 * @param string $premier Libellé du premier choix (0).
	 * @return array
	 */
	protected static function modeles( $premier ) {
		$choix = array( '0' => $premier );
		foreach ( get_posts(
			array(
				'post_type'      => 'elementor_library',
				'posts_per_page' => 300,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		) as $modele ) {
			$choix[ (string) $modele->ID ] = get_the_title( $modele );
		}

		return $choix;
	}
}

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
		$this->choix( 'modele_vide', __( 'Liste vide : modèle à afficher', 'rolling' ), self::modeles( __( 'Rien', 'rolling' ) ), '0' );
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
		$r                = $this->get_settings_for_display();
		list( $ids, $reste ) = $this->contenus( $r );
		$variante         = 'une' === ( $r['carte_theme'] ?? '' ) ? '-une' : '';
		$modele           = (int) $r['modele'] ? (int) $r['modele'] : self::modele_du_theme( $r['contenu'], $variante );
		$modele           = $modele ? $modele : self::modele_du_theme( $r['contenu'] );
		$modele_une       = (int) $r['modele_une'];

		if ( ! $modele_une && in_array( $r['contenu'], array( 'temoignages', 'webinars' ), true ) && ! (int) $r['decalage'] ) {
			$modele_une = 'temoignages' === $r['contenu'] && $ids && ! Rolling_Contenus::valeur( 'a_la_une', $ids[0] ) ? 0 : self::modele_du_theme( $r['contenu'], '-une' );
			$modele_une = 'webinars' === $r['contenu'] ? 0 : $modele_une;
		}

		$this->afficher_filtres( $r );

		if ( ! $ids ) {
			if ( (int) $r['modele_vide'] ) {
				echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( (int) $r['modele_vide'], false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML produit par Elementor.
			}
			return;
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

		echo '<nav class="filtres-liste" aria-label="' . esc_attr__( 'Filtrer la liste', 'rolling' ) . '">';
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

/**
 * Plaque des chiffres clés d'un témoignage (chiffres en verre).
 */
class Rolling_Widget_Chiffres extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-chiffres';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Chiffres clés', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-counter';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'chiffres', array( 'label' => __( 'Chiffres clés', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classes de la plaque', 'rolling' ), 'plaque-libre' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$chiffres = Rolling_Contenus::valeur( 'chiffres', $this->contenu() );
		if ( ! $chiffres ) {
			return;
		}
		echo '<div class="' . esc_attr( $this->get_settings_for_display( 'classe' ) ) . '">';
		foreach ( $chiffres as $chiffre ) {
			echo '<div>';
			echo '<div class="chiffre-verre">' . Rolling_Dynamique::chiffres_en_verre( $chiffre['valeur'] ) . '<span class="texte-masque">' . esc_html( $chiffre['valeur'] ) . '</span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- images de wp_get_attachment_image().
			echo '<span class="libelle-chiffre">' . esc_html( $chiffre['libelle'] ) . '</span>';
			echo '</div>';
		}
		echo '</div>';
	}
}

/**
 * Liste à coches (ce que nous avons produit, notre accompagnement).
 */
class Rolling_Widget_Coches extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-coches';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Liste à coches', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-bullet-list';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'coches', array( 'label' => __( 'Liste à coches', 'rolling' ) ) );
		$this->choix(
			'champ',
			__( 'Champ', 'rolling' ),
			array(
				'accompagnement' => __( 'Notre accompagnement (témoignage)', 'rolling' ),
				'produit'        => __( 'Ce que nous avons produit (réalisation)', 'rolling' ),
			)
		);
		$this->texte( 'classe_texte', __( 'Classe du texte', 'rolling' ), 'texte' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r      = $this->get_settings_for_display();
		$lignes = Rolling_Contenus::valeur( $r['champ'], $this->contenu() );
		if ( ! $lignes ) {
			return;
		}
		$coche = Rolling_Dynamique::image_du_theme( 'coche-verre', 'thumbnail', array( 'class' => 'coche' ) );
		echo '<ul class="liste-coches">';
		foreach ( $lignes as $ligne ) {
			echo '<li class="ligne-coche">' . $coche . '<span class="' . esc_attr( $r['classe_texte'] ) . '">' . esc_html( $ligne['texte'] ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image de wp_get_attachment_image().
		}
		echo '</ul>';
	}
}

/**
 * Générique de fin d'une réalisation.
 */
class Rolling_Widget_Generique extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-generique';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Générique', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-text-align-center';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'generique', array( 'label' => __( 'Générique', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classe de la liste', 'rolling' ), 'generique-liste' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$lignes = Rolling_Contenus::valeur( 'generique', $this->contenu() );
		if ( ! $lignes ) {
			return;
		}
		echo '<dl class="' . esc_attr( $this->get_settings_for_display( 'classe' ) ) . '">';
		foreach ( $lignes as $rang => $ligne ) {
			echo '<div style="--i:' . (int) $rang . '"><dt>' . esc_html( $ligne['role'] ) . '</dt><dd>' . esc_html( $ligne['valeur'] ) . '</dd></div>';
		}
		echo '</dl>';
	}
}

/**
 * Compte à rebours d'un webinar (heure de Paris), puis « En direct », puis « Terminé ».
 */
class Rolling_Widget_Compte_A_Rebours extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-compte-a-rebours';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Compte à rebours du webinar', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-countdown';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'textes', array( 'label' => __( 'Textes', 'rolling' ) ) );
		$this->texte( 'avant', __( 'Avant le début', 'rolling' ), __( 'Début dans', 'rolling' ) );
		$this->texte( 'jours', __( 'Unité des jours', 'rolling' ), __( 'j', 'rolling' ) );
		$this->texte( 'direct', __( 'Pendant le webinar', 'rolling' ), __( 'En direct maintenant', 'rolling' ) );
		$this->texte( 'fin', __( 'Après la fin', 'rolling' ), __( 'Ce webinar est terminé. Le replay arrive bientôt.', 'rolling' ) );
		$this->texte( 'bouton', __( 'Texte du bouton pendant le direct', 'rolling' ), __( 'Rejoindre le direct', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r     = $this->get_settings_for_display();
		$id    = $this->contenu();
		$debut = Rolling_Webinars::debut( $id );
		if ( ! $debut ) {
			return;
		}
		$quand = wp_date( 'l j F Y \à H \h i', $debut, new DateTimeZone( Rolling_Webinars::FUSEAU ) );
		printf(
			'<div class="compte-rebours" data-debut="%1$s" data-fin="%2$s" data-bouton="%3$s"><div class="cr-avant"><p class="cr-pilule" aria-hidden="true"><span class="cr-point"></span><span class="cr-label">%4$s</span><span class="cr-temps"><span class="cr-jours"><b data-cr="j">--</b><span class="cr-unite">%5$s</span></span><span class="cr-heure"><b data-cr="h">--</b><span class="cr-deux">:</span><b data-cr="m">--</b><span class="cr-deux">:</span><b data-cr="s">--</b></span></span></p><p class="texte-masque">%6$s</p></div><p class="cr-direct"><span class="cr-rec"></span>%7$s</p><p class="cr-fin">%8$s</p></div>',
			esc_attr( (string) ( $debut * 1000 ) ),
			esc_attr( (string) ( Rolling_Webinars::fin( $id ) * 1000 ) ),
			esc_attr( $r['bouton'] ),
			esc_html( $r['avant'] ),
			esc_html( $r['jours'] ),
			/* translators: %s : date et heure du webinar. */
			esc_html( sprintf( __( 'Le webinar commence le %s, heure de Paris.', 'rolling' ), $quand ) ),
			esc_html( $r['direct'] ),
			esc_html( $r['fin'] )
		);
	}
}

/**
 * Vidéo d'une réalisation : affiche + bouton lecture ; la vidéo ne se charge qu'au clic.
 */
class Rolling_Widget_Video extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-video';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Vidéo de la réalisation', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-youtube';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'video', array( 'label' => __( 'Vidéo', 'rolling' ) ) );
		/* translators: %s : titre de la vidéo. */
		$this->texte( 'libelle', __( 'Bouton lecture, pour lecteur d’écran (%s = titre)', 'rolling' ), __( 'Lire la vidéo : %s', 'rolling' ) );
		$this->texte( 'classe', __( 'Classes du cadre', 'rolling' ), 'cadre-photo video-fiche' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r     = $this->get_settings_for_display();
		$id    = $this->contenu();
		$video = (string) Rolling_Contenus::valeur( 'video', $id );
		if ( ! $video && ! has_post_thumbnail( $id ) ) {
			return;
		}
		$formats = get_the_terms( $id, 'rolling_format' );
		$format  = $formats && ! is_wp_error( $formats ) ? $formats[0]->name : '';
		$duree   = (string) Rolling_Contenus::valeur( 'duree', $id );
		$titre   = get_the_title( $id );

		echo '<div class="' . esc_attr( $r['classe'] ) . '"' . ( $video ? ' data-rl-video="' . esc_url( $video ) . '"' : '' ) . '>';
		echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de WordPress.
			$id,
			'large',
			array(
				'class'         => 'image-couvrante',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'alt'           => '',
			)
		);
		if ( $format ) {
			echo '<span class="bandeau-video">' . esc_html( $format ) . '</span>';
		}
		if ( $duree ) {
			echo '<span class="duree">' . esc_html( $duree ) . '</span>';
		}
		if ( $video ) {
			echo '<button type="button" class="lecture-centre" aria-label="' . esc_attr( sprintf( $r['libelle'], $titre ) ) . '">' . Rolling_Dynamique::image_du_theme( 'bouton-lecture', 'thumbnail' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image de wp_get_attachment_image().
		}
		echo '</div>';
	}
}

/**
 * Transcription de la vidéo (une ligne, ouverte à la demande ; absente si vide).
 */
class Rolling_Widget_Transcription extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-transcription';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Transcription', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-accordion';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'transcription', array( 'label' => __( 'Transcription', 'rolling' ) ) );
		$this->texte( 'libelle', __( 'Texte du lien', 'rolling' ), __( 'Lire la transcription de la vidéo', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$texte = (string) Rolling_Contenus::valeur( 'transcription', $this->contenu() );
		if ( '' === $texte ) {
			return;
		}
		echo '<details class="transcription"><summary>' . esc_html( $this->get_settings_for_display( 'libelle' ) ) . '</summary><p>' . wp_kses( nl2br( esc_html( $texte ) ), array( 'br' => array() ) ) . '</p></details>';
	}
}

/**
 * Logos clients (liste « Logos clients ») : une modification de la liste met à jour toutes les bandes.
 */
class Rolling_Widget_Logos extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-logos';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Logos clients', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-logo';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'logos', array( 'label' => __( 'Logos', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classes de la liste', 'rolling' ), 'logos-defilants grille-6', Controls_Manager::TEXT );
		$this->add_control(
			'aide',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Retirez « logos-defilants » pour une grille fixe. Les logos se gèrent dans Logos clients (ordre : attribut « Ordre »).', 'rolling' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$logos = get_posts(
			array(
				'post_type'      => 'rolling_logo',
				'posts_per_page' => 40,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
			)
		);
		if ( ! $logos ) {
			return;
		}
		echo '<div class="masque-bords"><div class="rl-logos ' . esc_attr( $this->get_settings_for_display( 'classe' ) ) . '">';
		foreach ( $logos as $logo ) {
			$hauteur = (int) Rolling_Contenus::valeur( 'hauteur', $logo->ID );
			echo '<div class="carte-verre tuile-logo">' . get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de WordPress.
				$logo->ID,
				'medium',
				array(
					'alt'     => get_the_title( $logo ),
					'loading' => 'lazy',
					'style'   => 'height:' . ( $hauteur ? $hauteur : 32 ) . 'px',
				)
			) . '</div>';
		}
		echo '</div></div>';
	}
}

/**
 * Fil d'Ariane : Accueil › page de la liste › titre (sans données structurées : votre extension SEO s'en charge).
 */
class Rolling_Widget_Ariane extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-ariane';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Fil d’Ariane', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'ariane', array( 'label' => __( 'Fil d’Ariane', 'rolling' ) ) );
		$this->texte( 'accueil', __( 'Premier lien', 'rolling' ), __( 'Accueil', 'rolling' ) );
		$this->texte( 'libelle', __( 'Nom pour lecteur d’écran', 'rolling' ), __( 'Fil d’Ariane', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r      = $this->get_settings_for_display();
		$id     = $this->contenu();
		$type   = get_post_type( $id );
		$slugs  = array(
			'rolling_realisation' => 'realisations',
			'rolling_temoignage'  => 'temoignages',
		);
		$parent = 0;
		if ( isset( $slugs[ $type ] ) ) {
			$page   = get_page_by_path( $slugs[ $type ] );
			$parent = $page ? $page->ID : 0;
		} elseif ( 'post' === $type ) {
			$parent = (int) get_option( 'page_for_posts' );
		}

		echo '<nav class="ariane" aria-label="' . esc_attr( $r['libelle'] ) . '"><ol>';
		echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $r['accueil'] ) . '</a></li>';
		if ( $parent ) {
			echo '<li><a href="' . esc_url( get_permalink( $parent ) ) . '">' . esc_html( get_the_title( $parent ) ) . '</a></li>';
		}
		$titre = 'rolling_realisation' === $type || 'rolling_temoignage' === $type ? (string) Rolling_Contenus::valeur( 'client', $id ) : '';
		echo '<li aria-current="page">' . esc_html( $titre ? $titre : get_the_title( $id ) ) . '</li>';
		echo '</ol></nav>';
	}
}
