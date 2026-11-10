<?php
/**
 * Widget Rolling : Rolling_Widget_Transcription.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Transcription de la vidéo (une ligne, ouverte à la demande ; absente si vide).
 */
class Rolling_Widget_Transcription extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-transcription';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Transcription', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-accordion';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'transcription', array( 'label' => __( 'Transcription', 'rolling' ) ) );
		$this->texte( 'libelle', __( 'Texte du lien', 'rolling' ), __( 'Lire la transcription de la vidéo', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$texte = (string) Rolling_Contenus::valeur( 'transcription', $this->contenu() );
		if ( '' === $texte ) {
			return;
		}
		echo '<details class="transcription"><summary>' . esc_html( $this->get_settings_for_display( 'libelle' ) ) . '</summary><p>' . wp_kses( nl2br( esc_html( $texte ) ), array( 'br' => array() ) ) . '</p></details>';
	}
}
