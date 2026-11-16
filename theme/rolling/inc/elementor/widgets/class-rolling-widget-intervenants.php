<?php
/**
 * Widget Rolling : Rolling_Widget_Intervenants.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Intervenants d'un webinar : pastilles d'initiales, puis prénom et poste.
 */
class Rolling_Widget_Intervenants extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-intervenants';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Intervenants du webinar', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-person';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'intervenants', array( 'label' => __( 'Intervenants', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classes du bloc', 'rolling' ), 'intervenants-webinar' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r         = $this->get_settings_for_display();
		$personnes = array_filter( (array) Rolling_Contenus::valeur( 'intervenants', $this->contenu() ), 'is_array' );
		if ( ! $personnes ) {
			return;
		}
		$initiales = '';
		$lignes    = array();
		foreach ( $personnes as $personne ) {
			$nom = trim( (string) ( $personne['nom'] ?? '' ) );
			if ( '' === $nom ) {
				continue;
			}
			$initiales .= '<i>' . esc_html( mb_strtoupper( mb_substr( $nom, 0, 1 ) ) ) . '</i>';
			$lignes[]   = '<strong>' . esc_html( $nom ) . '</strong>' . ( ! empty( $personne['poste'] ) ? ', ' . esc_html( $personne['poste'] ) : '' );
		}
		if ( ! $lignes ) {
			return;
		}
		echo '<div class="' . esc_attr( $r['classe'] ) . '"><span class="avatars-initiales" aria-hidden="true">' . $initiales . '</span><p class="texte-petit">' . implode( '<br>', $lignes ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus.
	}
}
