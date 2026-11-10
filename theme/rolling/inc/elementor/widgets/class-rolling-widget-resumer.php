<?php
/**
 * Widget Rolling : Rolling_Widget_Resumer.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * « Résumer avec » : ChatGPT, Claude, Perplexity, Le Chat, avec l'adresse de l'article.
 */
class Rolling_Widget_Resumer extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-resumer';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Résumer avec une IA', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-ai';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'resumer', array( 'label' => __( 'Résumer avec', 'rolling' ) ) );
		/* translators: %s : adresse de l'article (laisser tel quel). */
		$this->texte( 'demande', __( 'Demande envoyée (%s = adresse)', 'rolling' ), __( 'Résume cet article en 5 points : %s', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$demande = rawurlencode( sprintf( (string) $this->get_settings_for_display( 'demande' ), (string) get_permalink() ) );
		$outils  = array(
			'ChatGPT'    => 'https://chatgpt.com/?q=',
			'Claude'     => 'https://claude.ai/new?q=',
			'Perplexity' => 'https://www.perplexity.ai/search?q=',
			'Le Chat'    => 'https://chat.mistral.ai/chat?q=',
		);
		echo '<ul class="rangee resumer">';
		foreach ( $outils as $nom => $base ) {
			echo '<li><a class="chip-neutre" href="' . esc_url( $base . $demande ) . '" target="_blank" rel="noopener">' . esc_html( $nom ) . '<span class="texte-masque"> ' . esc_html__( '(nouvel onglet)', 'rolling' ) . '</span></a></li>';
		}
		echo '</ul>';
	}
}
