<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Article.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Article de blog : titre, date, catégorie, extrait, temps de lecture.
 */
class Rolling_Balise_Article extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-article';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Article ou page', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'info',
			__( 'Information', 'rolling' ),
			array(
				'titre'     => __( 'Titre', 'rolling' ),
				'publie'    => __( 'Publié le…', 'rolling' ),
				'date'      => __( 'Date', 'rolling' ),
				'categorie' => __( 'Catégorie', 'rolling' ),
				'extrait'   => __( 'Extrait', 'rolling' ),
				'lecture'   => __( 'Temps de lecture (6 minutes)', 'rolling' ),
				'lecture_c' => __( 'Temps de lecture court (6 min de lecture)', 'rolling' ),
			)
		);
	}

	/**
	 * Minutes de lecture (200 mots par minute).
	 *
	 * @param int $id Contenu.
	 * @return int
	 */
	public static function minutes( $id ) {
		$mots = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', $id ) ) );

		return max( 1, (int) round( $mots / 200 ) );
	}

	/** Affichage. */
	public function render() {
		$id = get_the_ID() && 'elementor_library' !== get_post_type() ? get_the_ID() : $this->contenu();
		if ( 'elementor_library' === get_post_type( $id ) ) {
			$dernier = get_posts(
				array(
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			$id      = $dernier ? $dernier[0] : $id;
		}

		switch ( $this->get_settings( 'info' ) ) {
			case 'publie':
				/* translators: %s : date de publication. */
				echo esc_html( sprintf( __( 'Publié le %s', 'rolling' ), get_the_date( 'j F Y', $id ) ) );
				break;
			case 'date':
				echo esc_html( get_the_date( 'j F Y', $id ) );
				break;
			case 'categorie':
				$categories = get_the_category( $id );
				echo esc_html( $categories ? $categories[0]->name : '' );
				break;
			case 'extrait':
				echo esc_html( get_the_excerpt( $id ) );
				break;
			case 'lecture':
				/* translators: %d : nombre de minutes. */
				echo esc_html( sprintf( _n( '%d minute', '%d minutes', self::minutes( $id ), 'rolling' ), self::minutes( $id ) ) );
				break;
			case 'lecture_c':
				/* translators: %d : nombre de minutes. */
				echo esc_html( sprintf( __( '%d min de lecture', 'rolling' ), self::minutes( $id ) ) );
				break;
			default:
				echo esc_html( get_the_title( $id ) );
		}
	}
}
