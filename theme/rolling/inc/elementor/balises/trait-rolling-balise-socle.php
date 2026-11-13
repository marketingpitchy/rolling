<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Socle.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Socle commun : groupe Rolling, liste de choix.
 */
trait Rolling_Balise_Socle {

	/**
	 * Groupe.
	 *
	 * @return string
	 */
	public function get_group() {
		return Rolling_Dynamique::GROUPE;
	}

	/**
	 * Pas de section « Avancé ».
	 */
	protected function register_advanced_section() {}

	/**
	 * Ajoute une liste de choix.
	 *
	 * @param string $cle     Clé.
	 * @param string $libelle Libellé.
	 * @param array  $options Options.
	 */
	protected function choix( $cle, $libelle, $options ) {
		$this->add_control(
			$cle,
			array(
				'label'   => $libelle,
				'type'    => Controls_Manager::SELECT,
				'options' => $options,
				'default' => (string) array_key_first( $options ),
			)
		);
	}

	/**
	 * Contenu lu par la balise.
	 *
	 * @return int
	 */
	protected function contenu() {
		$source = (string) $this->get_settings( 'source' );
		if ( in_array( $source, Rolling_Dynamique::CONTEXTES, true ) ) {
			return Rolling_Dynamique::contenu_du_contexte( $source );
		}
		$id = Rolling_Dynamique::contenu_courant();

		return 'temoignage' === $source ? Rolling_Contenus::temoignage_lie( $id ) : $id;
	}

	/**
	 * Le réglage « Lire dans » désigne un contenu précis (dernier article…) plutôt que le contenu affiché.
	 *
	 * @return bool
	 */
	protected function source_precise() {
		return in_array( (string) $this->get_settings( 'source' ), Rolling_Dynamique::CONTEXTES, true );
	}

	/**
	 * Réglage « Lire dans » : le contenu affiché, le témoignage lié, ou un contenu précis (dernier article…).
	 */
	protected function source() {
		$this->choix(
			'source',
			__( 'Lire dans', 'rolling' ),
			array(
				'courant'          => __( 'Le contenu affiché', 'rolling' ),
				'temoignage'       => __( 'Le témoignage lié (sur une fiche réalisation)', 'rolling' ),
				'dernier-article'  => __( 'Le dernier article', 'rolling' ),
				'dernier-guide'    => __( 'Le dernier guide', 'rolling' ),
				'prochain-webinar' => __( 'Le prochain webinar', 'rolling' ),
				'dernier-replay'   => __( 'Le dernier replay', 'rolling' ),
			)
		);
	}
}
