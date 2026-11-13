<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Titre.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Titre d'une fiche, la fin en couleur (mise en italique : dégradé de la section).
 */
class Rolling_Balise_Titre extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-titre';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Titre (fin en couleur)', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'forme',
			__( 'Forme', 'rolling' ),
			array(
				'client' => __( 'Client : titre', 'rolling' ),
				'titre'  => __( 'Titre seul', 'rolling' ),
			)
		);
		$this->source();
	}

	/** Affichage. */
	public function render() {
		$id      = $this->contenu();
		$titre   = esc_html( get_the_title( $id ) );
		$couleur = trim( (string) Rolling_Contenus::valeur( 'titre_couleur', $id ) );

		if ( '' !== $couleur && false !== mb_strpos( $titre, esc_html( $couleur ) ) ) {
			$titre = str_replace( esc_html( $couleur ), '<em>' . esc_html( $couleur ) . '</em>', $titre );
		}

		$client = (string) Rolling_Contenus::valeur( 'client', $id );
		if ( 'client' === $this->get_settings( 'forme' ) && '' !== $client ) {
			$titre = esc_html( $client ) . '&nbsp;: ' . $titre;
		}

		echo wp_kses( $titre, array( 'em' => array() ) );
	}
}
