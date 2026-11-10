<?php
/**
 * SEO technique du thème. Les métas (title, description, canonical, Open Graph) restent à l'outil SEO
 * choisi par la cliente, pour éviter les doublons. Le thème ajoute : robots d'IA, « noindex » page par page
 * (case ACF « noindex »), archives auteurs / catégories / étiquettes / dates retirées du site et du sitemap.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * SEO du thème.
 */
class Rolling_Seo {

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_filter( 'robots_txt', array( __CLASS__, 'robots_ia' ), 20, 2 );

		// Noindex page par page : balise du cœur de WordPress, ou de Rank Math / Yoast s'ils la remplacent.
		add_filter( 'wp_robots', array( __CLASS__, 'robots_wp' ), 20 );
		add_filter( 'rank_math/frontend/robots', array( __CLASS__, 'robots_extension' ), 20 );
		add_filter( 'wpseo_robots_array', array( __CLASS__, 'robots_extension' ), 20 );

		// Sitemap de WordPress : ni auteurs, ni catégories, ni étiquettes, ni pages en noindex.
		add_filter( 'wp_sitemaps_add_provider', array( __CLASS__, 'sans_auteurs' ), 10, 2 );
		add_filter( 'wp_sitemaps_taxonomies', array( __CLASS__, 'sans_taxonomies' ) );
		add_filter( 'wp_sitemaps_posts_query_args', array( __CLASS__, 'sans_noindex' ) );
		add_filter( 'rank_math/sitemap/entry', array( __CLASS__, 'entree_rank_math' ), 20, 3 );

		// Archives inutiles : redirigées (301).
		add_action( 'template_redirect', array( __CLASS__, 'archives' ), 1 );
	}

	/**
	 * Nom du champ ACF (case « Ne pas indexer cette page »).
	 */
	const CHAMP_NOINDEX = 'noindex';

	/**
	 * Le contenu demande-t-il à ne pas être indexé ? Accepte un champ ACF « Vrai / Faux » (1) ou « Case à cocher »
	 * (liste d'au moins une valeur cochée).
	 *
	 * @param int $id Contenu.
	 * @return bool
	 */
	public static function est_noindex( $id ) {
		if ( ! $id ) {
			return false;
		}

		$valeur = get_post_meta( $id, self::CHAMP_NOINDEX, true );
		if ( is_array( $valeur ) ) {
			return array() !== array_filter( $valeur );
		}

		return in_array( strtolower( trim( (string) $valeur ) ), array( '1', 'true', 'oui', 'yes', 'on', 'noindex' ), true );
	}

	/**
	 * Contenu affiché (page, article, fiche, page du blog), sinon 0.
	 *
	 * @return int
	 */
	private static function contenu_affiche() {
		if ( is_singular() ) {
			return (int) get_queried_object_id();
		}
		if ( is_home() && get_option( 'page_for_posts' ) ) {
			return (int) get_option( 'page_for_posts' );
		}

		return 0;
	}

	/**
	 * Balise robots de WordPress : noindex seulement pour la page cochée.
	 *
	 * @param array $robots Directives.
	 * @return array
	 */
	public static function robots_wp( $robots ) {
		if ( self::est_noindex( self::contenu_affiche() ) ) {
			unset( $robots['index'], $robots['max-image-preview'] );
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}

		return $robots;
	}

	/**
	 * Balise robots de Rank Math ou de Yoast : même règle.
	 *
	 * @param array $robots Directives (index => noindex…).
	 * @return array
	 */
	public static function robots_extension( $robots ) {
		if ( is_array( $robots ) && self::est_noindex( self::contenu_affiche() ) ) {
			$robots['index'] = 'noindex';
		}

		return $robots;
	}

	/**
	 * Sitemap : pas de liste des auteurs.
	 *
	 * @param WP_Sitemaps_Provider $fournisseur Fournisseur.
	 * @param string               $nom         Nom.
	 * @return WP_Sitemaps_Provider|false
	 */
	public static function sans_auteurs( $fournisseur, $nom ) {
		return 'users' === $nom ? false : $fournisseur;
	}

	/**
	 * Sitemap : pas de catégories ni d'étiquettes.
	 *
	 * @param array $taxonomies Taxonomies.
	 * @return array
	 */
	public static function sans_taxonomies( $taxonomies ) {
		unset( $taxonomies['category'], $taxonomies['post_tag'] );

		return $taxonomies;
	}

	/**
	 * Sitemap : pas de contenus en noindex.
	 *
	 * @param array $args Requête.
	 * @return array
	 */
	public static function sans_noindex( $args ) {
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			array(
				'key'     => self::CHAMP_NOINDEX,
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => self::CHAMP_NOINDEX,
				'value'   => array( '', '0', 'a:0:{}' ),
				'compare' => 'IN',
			),
		);

		return $args;
	}

	/**
	 * Sitemap de Rank Math : retire les contenus en noindex.
	 *
	 * @param array|false $entree Entrée.
	 * @param string      $type   Type d'entrée.
	 * @param object      $objet  Contenu.
	 * @return array|false
	 */
	public static function entree_rank_math( $entree, $type, $objet ) {
		if ( 'post' === $type && $objet instanceof WP_Post && self::est_noindex( $objet->ID ) ) {
			return false;
		}

		return $entree;
	}

	/**
	 * Archives des auteurs, des étiquettes et des dates : vers le blog. Catégorie : vers le blog filtré.
	 */
	public static function archives() {
		if ( is_admin() || is_feed() || ! ( is_author() || is_category() || is_tag() || is_date() ) ) {
			return;
		}

		$blog = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );
		if ( is_category() ) {
			$blog = add_query_arg( 'categorie', get_queried_object()->slug, $blog );
		}

		wp_safe_redirect( $blog, 301, 'Rolling' );
		exit;
	}

	/**
	 * Ajoute au robots.txt les règles des robots d'IA (data/robots.json).
	 *
	 * @param string $sortie Contenu du robots.txt.
	 * @param bool   $ouvert Site ouvert aux moteurs.
	 * @return string
	 */
	public static function robots_ia( $sortie, $ouvert ) {
		if ( ! $ouvert ) {
			return $sortie;
		}

		$regles = Rolling_Donnees::lire( 'robots' );
		$lignes = array( '', '# Robots d’IA : recherche et citation autorisées, entraînement refusé (thème Rolling).' );

		foreach ( $regles['autorises'] ?? array() as $robot ) {
			$lignes[] = 'User-agent: ' . $robot;
			$lignes[] = 'Allow: /';
			$lignes[] = '';
		}

		foreach ( $regles['refuses'] ?? array() as $robot ) {
			$lignes[] = 'User-agent: ' . $robot;
			$lignes[] = 'Disallow: /';
			$lignes[] = '';
		}

		return rtrim( $sortie ) . "\n" . implode( "\n", $lignes );
	}
}
