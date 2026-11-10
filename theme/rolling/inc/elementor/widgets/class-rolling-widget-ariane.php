<?php
/**
 * Widget Rolling : Rolling_Widget_Ariane.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Fil d'Ariane : Accueil › page de la liste › titre (sans données structurées : votre extension SEO s'en charge).
 */
class Rolling_Widget_Ariane extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-ariane';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Fil d’Ariane', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'ariane', array( 'label' => __( 'Fil d’Ariane', 'rolling' ) ) );
		$this->texte( 'accueil', __( 'Premier lien', 'rolling' ), __( 'Accueil', 'rolling' ) );
		$this->texte( 'libelle', __( 'Nom pour lecteur d’écran', 'rolling' ), __( 'Fil d’Ariane', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r      = $this->get_settings_for_display();
		$id     = $this->contenu();
		$type   = get_post_type( $id );
		$slugs  = array(
			'rolling_realisation' => 'realisations',
			'rolling_temoignage'  => 'temoignages',
		);
		$parent = 0;
		if ( isset( $slugs[ $type ] ) ) {
			$page   = get_page_by_path( $slugs[ $type ] );
			$parent = $page ? $page->ID : 0;
		} elseif ( 'post' === $type ) {
			$parent = (int) get_option( 'page_for_posts' );
		}

		echo '<nav class="ariane" aria-label="' . esc_attr( $r['libelle'] ) . '"><ol>';
		echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $r['accueil'] ) . '</a></li>';
		if ( $parent ) {
			echo '<li><a href="' . esc_url( get_permalink( $parent ) ) . '">' . esc_html( get_the_title( $parent ) ) . '</a></li>';
		}
		$titre = 'rolling_realisation' === $type || 'rolling_temoignage' === $type ? (string) Rolling_Contenus::valeur( 'client', $id ) : '';
		echo '<li aria-current="page">' . esc_html( $titre ? $titre : get_the_title( $id ) ) . '</li>';
		echo '</ol></nav>';
	}
}
