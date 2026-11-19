<?php
/**
 * Widget Rolling : Rolling_Widget_Newsletter.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Formulaire d'inscription à la newsletter : le code HubSpot est collé une seule fois dans Apparence > Personnaliser >
 * « Rolling : newsletter », et repris partout où ce widget est posé (blog, pages ressources, articles).
 */
class Rolling_Widget_Newsletter extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-newsletter';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Formulaire de la newsletter', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-mail';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'newsletter', array( 'label' => __( 'Newsletter', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classes du formulaire', 'rolling' ), 'zone-newsletter' );
		$this->end_controls_section();
	}

	/**
	 * Code du formulaire collé dans le Personnalisateur.
	 *
	 * @return string
	 */
	public static function code() {
		return trim( (string) get_theme_mod( 'rolling_newsletter_code', '' ) );
	}

	/** Affichage. */
	protected function render() {
		$r    = $this->get_settings_for_display();
		$code = self::code();
		if ( '' === $code ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
				echo '<p class="texte-petit">' . esc_html__( 'Collez le code du formulaire HubSpot dans Apparence > Personnaliser > Rolling : newsletter. Rien ne s’affiche sur le site tant qu’il est vide.', 'rolling' ) . '</p>';
			}
			return;
		}
		echo '<div class="' . esc_attr( $r['classe'] ) . '">' . $code . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- code HubSpot collé par un administrateur (unfiltered_html).
	}
}
