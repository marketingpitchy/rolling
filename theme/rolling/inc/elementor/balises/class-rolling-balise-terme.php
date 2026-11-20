<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Terme.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Premier terme d'un filtre (métier, format, thème, catégorie).
 */
class Rolling_Balise_Terme extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-terme';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Métier, format ou thème', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'taxonomie',
			__( 'Liste', 'rolling' ),
			array(
				'rolling_metier' => __( 'Métier', 'rolling' ),
				'rolling_format' => __( 'Format vidéo', 'rolling' ),
				'rolling_usage'  => __( 'Cas d’usage', 'rolling' ),
				'rolling_theme'  => __( 'Thème (guides, webinars)', 'rolling' ),
				'category'       => __( 'Catégorie d’article', 'rolling' ),
			)
		);
	}

	/** Affichage. */
	public function render() {
		$taxonomie = (string) $this->get_settings( 'taxonomie' );
		$termes    = get_the_terms( $this->contenu(), $taxonomie );
		if ( ! $termes || is_wp_error( $termes ) ) {
			return;
		}
		if ( 'rolling_format' === $taxonomie && count( $termes ) > 1 ) {
			// Plusieurs formats cochés : celui de la vidéo principale passe devant (ex. « 9:16 » avant « 1:1 »).
			$videos    = Rolling_Contenus::videos( $this->contenu() );
			$principal = $videos ? $videos[0]['format'] : '';
			usort(
				$termes,
				function ( $a, $b ) use ( $principal ) {
					return (int) ( Rolling_Widget_Video::est_rapport( $b ) === $principal ) - (int) ( Rolling_Widget_Video::est_rapport( $a ) === $principal );
				}
			);
		}
		echo esc_html( $termes[0]->name );
	}
}
