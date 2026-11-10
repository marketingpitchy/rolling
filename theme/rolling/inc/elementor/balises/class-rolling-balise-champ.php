<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Champ.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Un champ texte d'un contenu (client, chapô, citation…).
 */
class Rolling_Balise_Champ extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-champ';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Champ', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/**
	 * Champs proposés (texte et zones).
	 *
	 * @return array
	 */
	public static function options() {
		$options = array();
		foreach ( Rolling_Contenus::champs() as $type => $boites ) {
			$type_nom = Rolling_Contenus::types()[ $type ]['singulier'];
			foreach ( $boites as $champs ) {
				foreach ( $champs as $cle => $def ) {
					if ( in_array( $def[0], array( 'texte', 'zone', 'nombre' ), true ) && ! isset( $options[ $cle ] ) ) {
						$options[ $cle ] = $def[1] . ' (' . $type_nom . ')';
					}
				}
			}
		}

		return $options;
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix( 'champ', __( 'Champ', 'rolling' ), self::options() );
		foreach ( array(
			'avant' => __( 'Texte avant', 'rolling' ),
			'apres' => __( 'Texte après (ex. : « pages »)', 'rolling' ),
		) as $cle => $libelle ) {
			$this->add_control(
				$cle,
				array(
					'label'   => $libelle,
					'type'    => Controls_Manager::TEXT,
					'default' => '',
				)
			);
		}
	}

	/** Affichage. */
	public function render() {
		$cle    = (string) $this->get_settings( 'champ' );
		$valeur = (string) Rolling_Contenus::valeur( $cle, $this->contenu() );
		$def    = Rolling_Contenus::champ( get_post_type( $this->contenu() ), $cle );

		if ( '' === $valeur ) {
			return;
		}

		echo esc_html( (string) $this->get_settings( 'avant' ) );
		if ( $def && 'zone' === $def[0] ) {
			echo wp_kses( nl2br( esc_html( $valeur ) ), array( 'br' => array() ) );
		} else {
			echo esc_html( $valeur );
		}
		echo esc_html( (string) $this->get_settings( 'apres' ) );
	}
}
