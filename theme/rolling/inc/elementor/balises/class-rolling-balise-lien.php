<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Lien.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Lien : fiche, vidéo, page HubSpot du guide, inscription, replay.
 */
class Rolling_Balise_Lien extends Data_Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-lien';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Lien', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::URL_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'lien',
			__( 'Lien', 'rolling' ),
			array(
				'fiche'       => __( 'La fiche (réalisation, témoignage, article)', 'rolling' ),
				'realisation' => __( 'La réalisation liée (sur un témoignage)', 'rolling' ),
				'video'       => __( 'La vidéo', 'rolling' ),
				'lien'        => __( 'Page HubSpot du guide', 'rolling' ),
				'inscription' => __( 'Inscription au webinar', 'rolling' ),
				'replay'      => __( 'Replay du webinar', 'rolling' ),
			)
		);
	}

	/**
	 * Valeur.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	public function get_value( array $options = array() ) {
		$id    = $this->contenu();
		$choix = (string) $this->get_settings( 'lien' );

		if ( 'realisation' === $choix ) {
			$videos = array_values( array_filter( (array) Rolling_Contenus::valeur( 'videos', $id ), 'get_post' ) );
			return $videos ? (string) get_permalink( (int) $videos[0] ) : '';
		}

		return 'fiche' === $choix ? (string) get_permalink( $id ) : (string) Rolling_Contenus::valeur( $choix, $id );
	}
}
