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
		return Rolling_Dynamique::contenu_courant();
	}
}
