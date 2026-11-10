<?php
/**
 * Widget Rolling : Rolling_Widget_Contenu.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Texte de l'article (contenu saisi dans WordPress), avec une ancre sur chaque intertitre (sommaire, chapitres).
 */
class Rolling_Widget_Contenu extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-contenu';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Texte de l’article', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-post-content';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'contenu', array( 'label' => __( 'Texte', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classe du texte', 'rolling' ), 'article-texte' );
		$this->end_controls_section();
	}

	/**
	 * Ajoute un id aux intertitres H2 qui n'en ont pas.
	 *
	 * @param string $html Contenu.
	 * @return string
	 */
	public static function ancrer( $html ) {
		return (string) preg_replace_callback(
			'#<h2(?![^>]*\sid=)([^>]*)>(.*?)</h2>#is',
			function ( $m ) {
				return '<h2 id="' . esc_attr( sanitize_title( wp_strip_all_tags( $m[2] ) ) ) . '"' . $m[1] . '>' . $m[2] . '</h2>';
			},
			$html
		);
	}

	/**
	 * Contenu de l'article affiché.
	 *
	 * @return string
	 */
	public static function contenu_article() {
		$id = get_the_ID() && 'elementor_library' !== get_post_type() ? get_the_ID() : 0;
		if ( ! $id ) {
			$dernier = get_posts(
				array(
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			$id      = $dernier ? $dernier[0] : 0;
		}

		return $id ? self::ancrer( apply_filters( 'the_content', get_post_field( 'post_content', $id ) ) ) : ''; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- filtre de WordPress.
	}

	/** Affichage. */
	protected function render() {
		echo '<div class="' . esc_attr( $this->get_settings_for_display( 'classe' ) ) . '" data-rl-article>' . self::contenu_article() . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- contenu de l'article, filtré par WordPress.
	}
}
