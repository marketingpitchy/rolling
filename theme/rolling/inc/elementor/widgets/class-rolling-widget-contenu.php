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
		/* translators: %s : titre de la vidéo. */
		$this->texte( 'libelle_video', __( 'Bouton des vidéos (%s = titre)', 'rolling' ), __( 'Lire la vidéo : %s', 'rolling' ) );
		$this->texte( 'libelle_tableau', __( 'Nom des tableaux sans légende (lecteurs d’écran)', 'rolling' ), __( 'Tableau', 'rolling' ) );
		$this->end_controls_section();
	}

	/**
	 * Compositions Rolling du texte : coches en verre (liste « Liste à coches »), chiffres en verre (« Chiffre clé »),
	 * vidéos YouTube chargées au clic, tableaux accessibles au clavier quand ils défilent.
	 *
	 * @param string $html           Texte de l'article.
	 * @param string $libelle_video  Nom du bouton lecture (%s = titre).
	 * @param string $libelle_tableau Nom d'un tableau sans légende.
	 * @return string
	 */
	public static function enrichir( $html, $libelle_video, $libelle_tableau ) { // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- propriétés de DOMDocument.
		if ( ! preg_match( '/is-style-coches|rl-chiffre-valeur|is-provider-youtube|wp-block-table/', $html ) || ! class_exists( 'DOMDocument' ) ) {
			return $html;
		}

		$dom      = new DOMDocument();
		$anciens  = libxml_use_internal_errors( true );
		$charge   = $dom->loadHTML( '<?xml encoding="utf-8"?><div id="rl-racine">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_clear_errors();
		libxml_use_internal_errors( $anciens );
		if ( ! $charge ) {
			return $html;
		}
		$xpath = new DOMXPath( $dom );
		$classe = function ( $nom ) {
			return "contains(concat(' ', normalize-space(@class), ' '), ' " . $nom . " ')";
		};
		$fragment = function ( $code ) use ( $dom ) {
			$f = $dom->createDocumentFragment();
			$f->appendXML( $code );
			return $f;
		};

		// Listes à coches.
		$coche = Rolling_Dynamique::image_du_theme(
			'coche-verre',
			'thumbnail',
			array(
				'class'  => 'coche',
				'width'  => 26,
				'height' => 26,
			)
		);
		foreach ( $xpath->query( '//ul[' . $classe( 'is-style-coches' ) . ']' ) as $liste ) {
			$liste->setAttribute( 'class', trim( $liste->getAttribute( 'class' ) . ' liste-coches' ) );
			foreach ( $xpath->query( './li', $liste ) as $ligne ) {
				$texte = $dom->createElement( 'span' );
				while ( $ligne->firstChild ) {
					$texte->appendChild( $ligne->firstChild );
				}
				$ligne->setAttribute( 'class', trim( $ligne->getAttribute( 'class' ) . ' ligne-coche' ) );
				if ( $coche ) {
					$ligne->appendChild( $fragment( self::xhtml( $coche ) ) );
				}
				$ligne->appendChild( $texte );
			}
		}

		// Chiffres clés en verre.
		foreach ( $xpath->query( '//p[' . $classe( 'rl-chiffre-valeur' ) . ']' ) as $valeur ) {
			$texte  = trim( $valeur->textContent );
			$images = Rolling_Dynamique::chiffres_en_verre( $texte );
			if ( '' === $texte || '' === $images ) {
				continue;
			}
			while ( $valeur->firstChild ) {
				$valeur->removeChild( $valeur->firstChild );
			}
			$valeur->appendChild( $fragment( '<span class="chiffre-verre" aria-hidden="true">' . self::xhtml( $images ) . '</span><span class="texte-masque">' . esc_html( $texte ) . '</span>' ) );
		}

		// Vidéos YouTube : affiche + bouton, la vidéo se charge au clic (rolling.js).
		foreach ( $xpath->query( '//figure[' . $classe( 'is-provider-youtube' ) . ']//iframe' ) as $iframe ) {
			if ( ! preg_match( '#(?:embed/|v=|youtu\.be/|shorts/)([\w-]{11})#', $iframe->getAttribute( 'src' ), $m ) ) {
				continue;
			}
			$titre  = $iframe->getAttribute( 'title' );
			$bouton = Rolling_Dynamique::image_du_theme( 'bouton-lecture', 'thumbnail' );
			$cadre  = '<div class="cadre-photo video-fiche" data-rl-video="' . esc_url( 'https://www.youtube.com/watch?v=' . $m[1] ) . '">'
				. '<img class="image-couvrante" src="' . esc_url( 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg' ) . '" alt="" width="480" height="360" loading="lazy" />'
				. '<button type="button" class="lecture-centre" aria-label="' . esc_attr( sprintf( $libelle_video, $titre ) ) . '">' . self::xhtml( $bouton ) . '</button></div>';
			$iframe->parentNode->replaceChild( $fragment( $cadre ), $iframe );
		}

		// Tableaux : la zone qui défile se prend au clavier.
		foreach ( $xpath->query( '//figure[' . $classe( 'wp-block-table' ) . ']' ) as $tableau ) {
			$legende = $xpath->query( './figcaption', $tableau );
			$tableau->setAttribute( 'tabindex', '0' );
			$tableau->setAttribute( 'role', 'region' );
			$tableau->setAttribute( 'aria-label', $legende->length ? trim( $legende->item( 0 )->textContent ) : $libelle_tableau );
		}

		$racine = $dom->getElementById( 'rl-racine' );
		$sortie = '';
		foreach ( $racine->childNodes as $noeud ) {
			$sortie .= $dom->saveHTML( $noeud );
		}

		return $sortie;
	} // phpcs:enable

	/**
	 * HTML d'une image en XML valide (pour DOMDocumentFragment::appendXML).
	 *
	 * @param string $html Image.
	 * @return string
	 */
	private static function xhtml( $html ) {
		return (string) preg_replace( '#<img([^>]*?)\s*/?>#', '<img$1 />', str_replace( '&', '&amp;', html_entity_decode( $html, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
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

	/**
	 * Le texte de l'article est traité par Elementor comme son widget « Contenu de l'article » : pas de remise à zéro
	 * des marges des figures (tableaux, vidéos, images) dans le texte.
	 */
	protected function add_render_attributes() {
		parent::add_render_attributes();
		$this->add_render_attribute( '_wrapper', 'class', 'elementor-widget-theme-post-content' );
	}

	/** Affichage. */
	protected function render() {
		$r = $this->get_settings_for_display();
		echo '<div class="' . esc_attr( $r['classe'] ) . '" data-rl-article>' . self::enrichir( self::contenu_article(), (string) $r['libelle_video'], (string) $r['libelle_tableau'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- contenu de l'article, filtré par WordPress.
	}
}
