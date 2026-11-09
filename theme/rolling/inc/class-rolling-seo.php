<?php
/**
 * SEO technique du thème. Les métas (title, description, canonical, Open Graph, sitemap) restent à l'outil SEO
 * choisi par la cliente, pour éviter les doublons ; le thème n'ajoute que ce qu'il est seul à savoir faire.
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
