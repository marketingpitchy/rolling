<?php
/**
 * Widget Rolling : Rolling_Widget_Partage.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Partage de l'article (LinkedIn, Facebook, X, WhatsApp, e-mail) et copie du lien (Instagram).
 */
class Rolling_Widget_Partage extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-partage';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Partage de l’article', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-share';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'partage', array( 'label' => __( 'Partage', 'rolling' ) ) );
		$this->texte( 'copier', __( 'Bouton « Copier le lien »', 'rolling' ), __( 'Copier le lien de l’article (pour Instagram ou ailleurs)', 'rolling' ) );
		$this->texte( 'copie', __( 'Message une fois copié', 'rolling' ), __( 'Lien copié', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r     = $this->get_settings_for_display();
		$url   = rawurlencode( (string) get_permalink() );
		$titre = rawurlencode( (string) get_the_title() );
		$icone = array(
			'LinkedIn' => '<path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.84-2.05 3.78-2.05C20.6 8.65 21 11.2 21 14.5V21h-4v-5.8c0-1.4 0-3.2-1.95-3.2S12.8 13.5 12.8 15.1V21H9z" fill="currentColor"/>',
			'Facebook' => '<path d="M14 8h3V4h-3c-2.8 0-4.5 1.7-4.5 4.6V11H7v4h2.5v7h4v-7h3l.5-4h-3.5V8.8c0-.6.3-.8 1-.8z" fill="currentColor"/>',
			'X'        => '<path d="M17.5 3h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.4L5 21H1.9l7.3-8.3L1.5 3h6.4l4.4 5.9zm-1.1 16.2h1.7L7.7 4.7H5.9z" fill="currentColor"/>',
			'WhatsApp' => '<path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .1-3.3-.8-2.8-1.1-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2.1 1-2.4c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.3 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.5.2.5.3.1.2.1.7-.1 1.3z" fill="currentColor"/>',
			'E-mail'   => '<path d="M3 5h18v14H3z" fill="none" stroke="currentColor" stroke-width="2"/><path d="M3 6l9 7 9-7" fill="none" stroke="currentColor" stroke-width="2"/>',
		);
		$liens = array(
			'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
			'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
			'X'        => 'https://x.com/intent/post?url=' . $url . '&text=' . $titre,
			'WhatsApp' => 'https://wa.me/?text=' . $titre . '%20' . $url,
			'E-mail'   => 'mailto:?subject=' . $titre . '&body=' . $url,
		);

		echo '<ul class="partage">';
		foreach ( $liens as $nom => $lien ) {
			/* translators: %s : réseau (LinkedIn…). */
			$libelle = 'E-mail' === $nom ? __( 'Envoyer par e-mail', 'rolling' ) : sprintf( __( 'Partager sur %s (nouvel onglet)', 'rolling' ), $nom );
			echo '<li><a class="partage-bouton" href="' . esc_url( $lien ) . '"' . ( 'E-mail' === $nom ? '' : ' target="_blank" rel="noopener"' ) . ' aria-label="' . esc_attr( $libelle ) . '" title="' . esc_attr( $libelle ) . '"><svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $icone[ $nom ] . '</svg></a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icône SVG fixe.
		}
		echo '<li><button type="button" class="partage-bouton" data-rl-copier="' . esc_attr( $r['copie'] ) . '" aria-label="' . esc_attr( $r['copier'] ) . '" title="' . esc_attr( $r['copier'] ) . '"><svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="8" y="8" width="12" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M16 8V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3" fill="none" stroke="currentColor" stroke-width="2"/></svg></button></li>';
		echo '</ul>';
	}
}
