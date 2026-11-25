<?php
/**
 * Éditeur des articles : compositions Rolling (bouton « + » > Compositions > Rolling), styles de blocs
 * (Liste à coches, Étapes numérotées, Tableau comparatif) et aperçu au style du site.
 * Le rendu sur le site vient de la classe Elementor « article-texte » (modèle d'article).
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compositions des articles.
 */
class Rolling_Compositions {

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'enregistrer' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'apercu' ) );
		add_action( 'init', array( __CLASS__, 'giphy' ) );
	}

	/**
	 * Liens Giphy dans le bloc « Intégrer » (page du GIF, adresse d'intégration, image) : WordPress ne sait pas les
	 * afficher (« Désolé, ce contenu ne peut pas être embarqué »). Le thème les affiche en image du GIF, sans appel à
	 * Giphy depuis le serveur ; sur le site, le widget « Texte de l'article » en fait ensuite la carte GIF légère.
	 */
	public static function giphy() {
		wp_embed_register_handler(
			'rolling-giphy',
			'#^https?://(?:(?:www\.)?giphy\.com/(?:embed|gifs|stickers|clips)/|(?:media\d*|i)\.giphy\.com/)\S+#i',
			array( __CLASS__, 'giphy_html' )
		);
	}

	/**
	 * Identifiant Giphy et titre lisible tirés d'une adresse (embed, page, média, image).
	 *
	 * @param string $adresse Adresse.
	 * @return array [ id, titre ] ou tableau vide.
	 */
	public static function giphy_infos( $adresse ) {
		$adresse = trim( (string) $adresse );
		if ( preg_match( '#giphy\.com/embed/([A-Za-z0-9]+)#', $adresse, $m )
			|| preg_match( '#(?:media\d*|i)\.giphy\.com/(?:media/(?:v1\.[^/]+/)?)?([A-Za-z0-9]+)(?:/|\.gif|\.webp|\.mp4)#', $adresse, $m ) ) {
			return array( $m[1], '' );
		}
		if ( preg_match( '#giphy\.com/(?:gifs|stickers|clips)/(?:[\w-]+/)?(?:([\w-]+)-)?([A-Za-z0-9]+)/?(?:[?\#].*)?$#', $adresse, $m ) ) {
			return array( $m[2], ucfirst( str_replace( '-', ' ', (string) $m[1] ) ) );
		}
		return array();
	}
	/**
	 * Image du GIF pour un lien Giphy.
	 *
	 * @param array  $correspondances Correspondances de l'expression.
	 * @param array  $attributs       Attributs de l'intégration.
	 * @param string $adresse         Adresse collée.
	 * @return string
	 */
	public static function giphy_html( $correspondances, $attributs, $adresse ) {
		$infos = self::giphy_infos( $adresse );
		if ( ! $infos ) {
			return '';
		}
		return '<img class="rl-giphy" src="' . esc_url( 'https://media.giphy.com/media/' . rawurlencode( $infos[0] ) . '/giphy.gif' ) . '" alt="' . esc_attr( $infos[1] ) . '" loading="lazy" />';
	}

	/**
	 * Styles de blocs et compositions.
	 */
	public static function enregistrer() {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		register_block_style(
			'core/list',
			array(
				'name'  => 'coches',
				'label' => __( 'Liste à coches', 'rolling' ),
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'  => 'etapes',
				'label' => __( 'Étapes numérotées', 'rolling' ),
			)
		);
		register_block_style(
			'core/table',
			array(
				'name'  => 'comparatif',
				'label' => __( 'Comparatif', 'rolling' ),
			)
		);

		register_block_pattern_category( 'rolling', array( 'label' => 'Rolling' ) );

		foreach ( Rolling_Donnees::lire( 'compositions' ) as $composition ) {
			$fichier = ROLLING_DIR . '/data/compositions/' . sanitize_file_name( $composition['cle'] ) . '.html';
			if ( ! is_readable( $fichier ) ) {
				continue;
			}
			// Lecture d'un fichier local du thème.
			$contenu = (string) file_get_contents( $fichier ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			register_block_pattern(
				'rolling/' . $composition['cle'],
				array(
					'title'       => 'Rolling : ' . $composition['titre'],
					'description' => $composition['description'],
					'categories'  => array( 'rolling' ),
					'postTypes'   => array( 'post' ),
					'content'     => self::remplir( $contenu ),
				)
			);
		}
	}

	/**
	 * Remplace {{media:clé|taille}} (adresse de l'image installée par le thème), {{media-id:clé}} (son id : WordPress
	 * ajoute alors dimensions et srcset) et {{page:chemin}} (page du site).
	 *
	 * @param string $contenu Composition.
	 * @return string
	 */
	private static function remplir( $contenu ) {
		$medias = (array) get_option( Rolling_Installateur::OPTION_MEDIAS, array() );

		return (string) preg_replace_callback(
			'/\{\{(media-id|media|page):([\w-]+)(?:\|([\w-]+))?\}\}/',
			function ( $m ) use ( $medias ) {
				$id = empty( $medias[ $m[2] ] ) ? 0 : (int) $medias[ $m[2] ];
				if ( 'media-id' === $m[1] ) {
					return (string) $id;
				}
				if ( 'media' === $m[1] ) {
					$url = $id ? wp_get_attachment_image_url( $id, empty( $m[3] ) ? 'medium' : $m[3] ) : '';
					return esc_url( $url ? $url : ROLLING_URL . '/assets/medias/' . $m[2] . '.jpg' );
				}
				$page = get_page_by_path( $m[2] );
				return esc_url( $page ? get_permalink( $page ) : home_url( '/' . $m[2] . '/' ) );
			},
			$contenu
		);
	}

	/**
	 * Aperçu dans l'éditeur WordPress : polices et styles du texte d'article (feuille générée depuis la classe).
	 */
	public static function apercu() {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/rolling-article-editeur.css' );
	}
}
