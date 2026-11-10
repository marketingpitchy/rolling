<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Image.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Image : image à la une, logo du client, portrait.
 */
class Rolling_Balise_Image extends Data_Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-image';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Image', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::IMAGE_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'image',
			__( 'Image', 'rolling' ),
			array(
				'une'      => __( 'Image (vignette et affiche)', 'rolling' ),
				'logo'     => __( 'Logo du client', 'rolling' ),
				'portrait' => __( 'Portrait', 'rolling' ),
			)
		);
		$this->source();
	}

	/**
	 * Valeur.
	 *
	 * @param array $options Options.
	 * @return array
	 */
	public function get_value( array $options = array() ) {
		$id    = $this->contenu();
		$choix = (string) $this->get_settings( 'image' );
		$image = 'une' === $choix ? (int) get_post_thumbnail_id( $id ) : (int) Rolling_Contenus::valeur( $choix, $id );

		return $image ? array(
			'id'  => $image,
			'url' => (string) wp_get_attachment_image_url( $image, 'full' ),
		) : array(
			'id'  => '',
			'url' => '',
		);
	}
}
