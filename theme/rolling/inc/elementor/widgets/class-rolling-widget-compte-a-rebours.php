<?php
/**
 * Widget Rolling : Rolling_Widget_Compte_A_Rebours.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Compte à rebours d'un webinar (heure de Paris), puis « En direct », puis « Terminé ».
 */
class Rolling_Widget_Compte_A_Rebours extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-compte-a-rebours';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Compte à rebours du webinar', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-countdown';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'textes', array( 'label' => __( 'Textes', 'rolling' ) ) );
		$this->texte( 'avant', __( 'Avant le début', 'rolling' ), __( 'Début dans', 'rolling' ) );
		$this->texte( 'jours', __( 'Unité des jours', 'rolling' ), __( 'j', 'rolling' ) );
		$this->texte( 'direct', __( 'Pendant le webinar', 'rolling' ), __( 'En direct maintenant', 'rolling' ) );
		$this->texte( 'fin', __( 'Après la fin', 'rolling' ), __( 'Ce webinar est terminé. Le replay arrive bientôt.', 'rolling' ) );
		$this->texte( 'bouton', __( 'Texte du bouton pendant le direct', 'rolling' ), __( 'Rejoindre le direct', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r     = $this->get_settings_for_display();
		$id    = $this->contenu();
		$debut = Rolling_Webinars::debut( $id );
		if ( ! $debut ) {
			return;
		}
		$quand = wp_date( 'l j F Y \à H \h i', $debut, new DateTimeZone( Rolling_Webinars::FUSEAU ) );
		printf(
			'<div class="compte-rebours" data-debut="%1$s" data-fin="%2$s" data-bouton="%3$s"><div class="cr-avant"><p class="cr-pilule" aria-hidden="true"><span class="cr-point"></span><span class="cr-label">%4$s</span><span class="cr-temps"><span class="cr-jours"><b data-cr="j">--</b><span class="cr-unite">%5$s</span></span><span class="cr-heure"><b data-cr="h">--</b><span class="cr-deux">:</span><b data-cr="m">--</b><span class="cr-deux">:</span><b data-cr="s">--</b></span></span></p><p class="texte-masque">%6$s</p></div><p class="cr-direct"><span class="cr-rec"></span>%7$s</p><p class="cr-fin">%8$s</p></div>',
			esc_attr( (string) ( $debut * 1000 ) ),
			esc_attr( (string) ( Rolling_Webinars::fin( $id ) * 1000 ) ),
			esc_attr( $r['bouton'] ),
			esc_html( $r['avant'] ),
			esc_html( $r['jours'] ),
			/* translators: %s : date et heure du webinar. */
			esc_html( sprintf( __( 'Le webinar commence le %s, heure de Paris.', 'rolling' ), $quand ) ),
			esc_html( $r['direct'] ),
			esc_html( $r['fin'] )
		);
	}
}
