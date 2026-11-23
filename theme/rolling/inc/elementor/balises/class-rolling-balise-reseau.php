<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Reseau.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Lien vers un réseau de Rolling (LinkedIn, YouTube, Instagram), rempli une seule fois dans
 * Apparence > Personnaliser > Rolling : en-tête et pied de page. À poser sur un bouton ou un conteneur ;
 * avec la classe repère « si-reseau--youtube » (etc.), l'élément disparaît tant que le lien est vide.
 */
class Rolling_Balise_Reseau extends Data_Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-reseau';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Réseau de Rolling (LinkedIn, YouTube…)', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::URL_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix( 'reseau', __( 'Réseau', 'rolling' ), wp_list_pluck( Rolling_Entete_Pied::RESEAUX, 0 ) );
	}

	/**
	 * Valeur.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	public function get_value( array $options = array() ) {
		$reseau = (string) $this->get_settings( 'reseau' );

		return isset( Rolling_Entete_Pied::RESEAUX[ $reseau ] ) ? (string) get_theme_mod( 'rolling_reseau_' . $reseau, '' ) : '';
	}
}
