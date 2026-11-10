<?php
/**
 * Widget Rolling : Rolling_Widget.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Socle des widgets Rolling.
 */
abstract class Rolling_Widget extends Widget_Base {

	/** Catégorie. */
	public function get_categories() {
		return array( Rolling_Dynamique::GROUPE );
	}

	/** Mots-clés. */
	public function get_keywords() {
		return array( 'rolling' );
	}

	/** Contenu qui change selon la page : jamais mis en cache. */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/** Pas d'enveloppe interne supplémentaire. */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * Contenu affiché (dans l'éditeur : le dernier contenu du type présenté par le modèle).
	 *
	 * @return int
	 */
	protected function contenu() {
		return Rolling_Dynamique::contenu_courant();
	}

	/**
	 * Ajoute un champ texte.
	 *
	 * @param string $cle     Clé.
	 * @param string $libelle Libellé.
	 * @param string $defaut  Valeur par défaut.
	 * @param string $type    Type de contrôle.
	 */
	protected function texte( $cle, $libelle, $defaut, $type = Controls_Manager::TEXT ) {
		$this->add_control(
			$cle,
			array(
				'label'   => $libelle,
				'type'    => $type,
				'default' => $defaut,
			)
		);
	}

	/**
	 * Ajoute une liste de choix.
	 *
	 * @param string $cle     Clé.
	 * @param string $libelle Libellé.
	 * @param array  $options Options.
	 * @param string $defaut  Valeur par défaut.
	 */
	protected function choix( $cle, $libelle, $options, $defaut = null ) {
		$this->add_control(
			$cle,
			array(
				'label'   => $libelle,
				'type'    => Controls_Manager::SELECT,
				'options' => $options,
				'default' => null === $defaut ? (string) array_key_first( $options ) : $defaut,
			)
		);
	}

	/**
	 * Modèles de « Mes modèles ».
	 *
	 * @param string $premier Libellé du premier choix (0).
	 * @return array
	 */
	protected static function modeles( $premier ) {
		$choix = array( '0' => $premier );
		foreach ( get_posts(
			array(
				'post_type'      => 'elementor_library',
				'posts_per_page' => 300,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		) as $modele ) {
			$choix[ (string) $modele->ID ] = get_the_title( $modele );
		}

		return $choix;
	}
}
