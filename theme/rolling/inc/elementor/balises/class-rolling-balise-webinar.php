<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Webinar.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Webinar : date complète, jour, mois, heure, intervenants.
 */
class Rolling_Balise_Webinar extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-webinar';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Webinar : date et intervenants', 'rolling' );
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
				'complete'     => __( 'Jeudi 3 décembre 2026 · 11 h 00 · 45 min · en ligne', 'rolling' ),
				'jour'         => __( 'Jour (3)', 'rolling' ),
				'mois'         => __( 'Mois (Déc.)', 'rolling' ),
				'heure'        => __( 'Heure (11 h 00)', 'rolling' ),
				'date'         => __( 'Date (3 décembre 2026)', 'rolling' ),
				'diffuse'      => __( 'Diffusé le 3 décembre 2026', 'rolling' ),
				'duree'        => __( 'Durée (45 min · en ligne)', 'rolling' ),
				'intervenants' => __( 'Intervenants (Thomas, Directeur Artistique, et Sophie, Chef de Projet)', 'rolling' ),
			)
		);
	}

	/** Affichage. */
	public function render() {
		$id     = $this->contenu();
		$debut  = Rolling_Webinars::debut( $id );
		$duree  = (int) Rolling_Contenus::valeur( 'duree', $id );
		$duree  = $duree ? $duree : 45;
		$fuseau = new DateTimeZone( Rolling_Webinars::FUSEAU );
		/* translators: %d : durée en minutes. */
		$texte_duree = sprintf( __( '%d min · en ligne', 'rolling' ), $duree );

		switch ( $this->get_settings( 'info' ) ) {
			case 'jour':
				echo esc_html( $debut ? wp_date( 'j', $debut, $fuseau ) : '' );
				break;
			case 'mois':
				echo esc_html( $debut ? self::mois_court( (int) wp_date( 'n', $debut, $fuseau ) ) : '' );
				break;
			case 'heure':
				echo esc_html( $debut ? str_replace( ':', ' h ', wp_date( 'H:i', $debut, $fuseau ) ) : '' );
				break;
			case 'date':
				echo esc_html( $debut ? wp_date( 'j F Y', $debut, $fuseau ) : '' );
				break;
			case 'duree':
				echo esc_html( $texte_duree );
				break;
			case 'diffuse':
				/* translators: %s : date de diffusion. */
				echo esc_html( $debut ? sprintf( __( 'Diffusé le %s', 'rolling' ), wp_date( 'j F Y', $debut, $fuseau ) ) : '' );
				break;
			case 'intervenants':
				$noms = array();
				foreach ( Rolling_Contenus::valeur( 'intervenants', $id ) as $personne ) {
					$noms[] = '<strong>' . esc_html( $personne['nom'] ) . '</strong>' . ( $personne['poste'] ? ', ' . esc_html( $personne['poste'] ) : '' );
				}
				echo wp_kses( wp_sprintf( '%l', $noms ), array( 'strong' => array() ) );
				break;
			default:
				echo esc_html( $debut ? ucfirst( wp_date( 'l j F Y', $debut, $fuseau ) ) . ' · ' . str_replace( ':', ' h ', wp_date( 'H:i', $debut, $fuseau ) ) . ' · ' . $texte_duree : '' );
		}
	}

	/**
	 * Mois abrégé à la française.
	 *
	 * @param int $mois 1 à 12.
	 * @return string
	 */
	private static function mois_court( $mois ) {
		$noms = array( '', 'Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.' );

		return $noms[ $mois ] ?? '';
	}
}
