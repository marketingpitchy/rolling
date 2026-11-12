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

		// Yoast SEO : mêmes exclusions dans son plan du site, et la vidéo des réalisations dans son schéma.
		add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', array( __CLASS__, 'exclus_yoast' ) );
		add_filter( 'wpseo_sitemap_exclude_taxonomy', array( __CLASS__, 'taxonomie_exclue_yoast' ), 10, 2 );
		add_filter( 'wpseo_sitemap_exclude_author', '__return_empty_array' );
		add_filter( 'wpseo_schema_graph', array( __CLASS__, 'video_yoast' ), 20, 2 );

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
	 * Plan du site de Yoast : contenus en noindex et témoignages sans page.
	 *
	 * @param int[] $ids Contenus déjà exclus.
	 * @return int[]
	 */
	public static function exclus_yoast( $ids ) {
		$coches = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => 'publish',
				'posts_per_page' => 1000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- Liste d'ids pour le plan du site.
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_key'       => self::CHAMP_NOINDEX, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
		$coches = array_filter( $coches, array( __CLASS__, 'est_noindex' ) );

		return array_values( array_unique( array_map( 'intval', array_merge( (array) $ids, $coches, Rolling_Contenus::citations_seules() ) ) ) );
	}

	/**
	 * Plan du site de Yoast : ni catégories ni étiquettes (leurs pages sont redirigées vers le blog).
	 *
	 * @param bool   $exclue   Déjà exclue.
	 * @param string $taxonomie Taxonomie.
	 * @return bool
	 */
	public static function taxonomie_exclue_yoast( $exclue, $taxonomie ) {
		return in_array( $taxonomie, array( 'category', 'post_tag' ), true ) ? true : $exclue;
	}

	/**
	 * Schéma de Yoast : ajoute la vidéo (VideoObject) sur la fiche d'une réalisation qui a une vidéo et une affiche.
	 *
	 * @param array  $graphe   Pièces du schéma.
	 * @param object $contexte Contexte de Yoast.
	 * @return array
	 */
	public static function video_yoast( $graphe, $contexte ) {
		if ( ! is_singular( 'rolling_realisation' ) || ! is_array( $graphe ) ) {
			return $graphe;
		}

		$id      = (int) get_queried_object_id();
		$lien    = trim( (string) Rolling_Contenus::valeur( 'video', $id ) );
		$affiche = get_the_post_thumbnail_url( $id, 'large' );
		if ( '' === $lien || ! $affiche ) {
			return $graphe;
		}

		$page  = isset( $contexte->canonical ) && $contexte->canonical ? $contexte->canonical : get_permalink( $id );
		$texte = trim( wp_strip_all_tags( (string) Rolling_Contenus::valeur( 'chapo', $id ) ) );
		$video = array(
			'@type'            => 'VideoObject',
			'@id'              => $page . '#video',
			'name'             => wp_strip_all_tags( get_the_title( $id ) ),
			'description'      => '' !== $texte ? $texte : wp_strip_all_tags( get_the_title( $id ) ),
			'thumbnailUrl'     => $affiche,
			'uploadDate'       => get_the_date( 'c', $id ),
			'mainEntityOfPage' => array( '@id' => $page ),
			'inLanguage'       => get_bloginfo( 'language' ),
		);

		if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})~', $lien, $m ) ) {
			$video['embedUrl'] = 'https://www.youtube.com/embed/' . $m[1];
		} else {
			$video['contentUrl'] = esc_url_raw( $lien );
		}

		if ( preg_match( '/^(?:(\d+):)?(\d{1,2}):(\d{2})$/', trim( (string) Rolling_Contenus::valeur( 'duree', $id ) ), $d ) ) {
			$video['duration'] = 'PT' . ( $d[1] ? (int) $d[1] . 'H' : '' ) . (int) $d[2] . 'M' . (int) $d[3] . 'S';
		}

		$graphe[] = $video;

		return $graphe;
	}

	/**
	 * Archives des auteurs, des étiquettes et des dates : vers le blog. Catégorie : vers le blog filtré.
	 */
	public static function archives() {
		if ( is_admin() || is_feed() || ! ( is_author() || is_category() || is_tag() || is_date() ) ) {
			return;
		}

		$blog = Rolling_Dynamique::page_blog() ? get_permalink( Rolling_Dynamique::page_blog() ) : home_url( '/' );
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
