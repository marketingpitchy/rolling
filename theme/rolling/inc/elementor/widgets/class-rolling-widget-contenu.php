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
		$this->texte( 'gif_pastille', __( 'GIF : pastille sur l’image', 'rolling' ), __( 'GIF', 'rolling' ) );
		$this->texte( 'gif_credit', __( 'GIF : crédit sous l’image', 'rolling' ), __( 'via GIPHY', 'rolling' ) );
		$this->texte( 'gif_pause', __( 'GIF : bouton pause (lecteurs d’écran)', 'rolling' ), __( 'Mettre le GIF en pause', 'rolling' ) );
		$this->texte( 'gif_lecture', __( 'GIF : bouton lecture (lecteurs d’écran)', 'rolling' ), __( 'Lire le GIF', 'rolling' ) );
		/* translators: %s : description du GIF. */
		$this->texte( 'gif_nom', __( 'GIF : texte lu (%s = légende ou titre Giphy)', 'rolling' ), __( 'GIF : %s', 'rolling' ) );
		$this->add_control(
			'encart',
			array(
				'label'       => __( 'Encart au milieu des articles', 'rolling' ),
				'description' => __( 'À partir de 4 chapitres (intertitres « Titre 2 »), le prochain webinar, sinon le dernier guide, s’affiche avant le 3e chapitre. Modèle : Mes modèles « Rolling · Encart au milieu des articles ».', 'rolling' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Image d'affiche d'une vidéo Vimeo (oEmbed de Vimeo, gardée une semaine).
	 *
	 * @param string $src Adresse du lecteur Vimeo.
	 * @return string
	 */
	public static function image_vimeo( $src ) {
		$cle   = 'rl_vimeo_' . md5( $src );
		$image = get_transient( $cle );
		if ( false === $image ) {
			$image = '';
			if ( preg_match( '#vimeo\.com/(?:video/)?(\d+)(?:[/?](?:h=)?([0-9a-f]{6,}))?#', $src, $m ) ) {
				$page    = 'https://vimeo.com/' . $m[1] . ( ! empty( $m[2] ) ? '/' . $m[2] : '' );
				$donnees = _wp_oembed_get_object()->get_data( $page, array( 'width' => 1280 ) );
				if ( $donnees && ! empty( $donnees->thumbnail_url ) ) {
					$image = preg_replace( '#_\d+x\d+#', '_1280x720', (string) $donnees->thumbnail_url );
				}
			}
			set_transient( $cle, $image, WEEK_IN_SECONDS );
		}
		return (string) $image;
	}

	/**
	 * Compositions Rolling du texte : coches en verre (liste « Liste à coches »), chiffres en verre (« Chiffre clé »),
	 * vidéos YouTube chargées au clic, tableaux accessibles au clavier quand ils défilent.
	 *
	 * @param string $html           Texte de l'article.
	 * @param string $libelle_video  Nom du bouton lecture (%s = titre).
	 * @param string $libelle_tableau Nom d'un tableau sans légende.
	 * @param array  $gif            Textes des GIF (pastille, credit, pause, lecture, nom).
	 * @return string
	 */
	public static function enrichir( $html, $libelle_video, $libelle_tableau, $gif = array() ) { // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- propriétés de DOMDocument.
		if ( ! preg_match( '/is-style-coches|rl-chiffre-valeur|is-provider-youtube|is-provider-vimeo|wp-block-table|giphy\.com|rl-auteur/', $html ) || ! class_exists( 'DOMDocument' ) ) {
			return $html;
		}

		$dom     = new DOMDocument();
		$anciens = libxml_use_internal_errors( true );
		$charge  = $dom->loadHTML( '<?xml encoding="utf-8"?><div id="rl-racine">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_clear_errors();
		libxml_use_internal_errors( $anciens );
		if ( ! $charge ) {
			return $html;
		}
		$xpath    = new DOMXPath( $dom );
		$classe   = function ( $nom ) {
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

		// Vidéos Vimeo : affiche (image de Vimeo) + bouton ; au clic, lecteur sans titre, sans photo du compte ni logo (rolling.js).
		foreach ( $xpath->query( '//figure[' . $classe( 'is-provider-vimeo' ) . ']//iframe' ) as $iframe ) {
			$src = $iframe->getAttribute( 'src' );
			if ( ! preg_match( '#vimeo\.com/(?:video/)?(\d+)#', $src ) ) {
				continue;
			}
			$titre  = $iframe->getAttribute( 'title' );
			$image  = self::image_vimeo( $src );
			$bouton = Rolling_Dynamique::image_du_theme( 'bouton-lecture', 'thumbnail' );
			$cadre  = '<div class="cadre-photo video-fiche" data-rl-video="' . esc_url( $src ) . '">'
				. ( $image ? '<img class="image-couvrante" src="' . esc_url( $image ) . '" alt="" width="640" height="360" loading="lazy" />' : '' )
				. '<button type="button" class="lecture-centre" aria-label="' . esc_attr( sprintf( $libelle_video, $titre ) ) . '">' . self::xhtml( $bouton ) . '</button></div>';
			$iframe->parentNode->replaceChild( $fragment( $cadre ), $iframe );
		}

		// GIF Giphy (code d'intégration, lien ou image) : vidéo légère de Giphy en boucle, dans une carte, avec pause.
		self::gifs( $dom, $xpath, $fragment, $gif );

		// Carte « Écrit par » : le lien LinkedIn d'exemple (sans profil) est retiré.
		foreach ( $xpath->query( '//*[' . $classe( 'rl-auteur' ) . ']//a' ) as $lien ) {
			if ( preg_match( '#^https?://(www\.)?linkedin\.com/in/?$#i', trim( $lien->getAttribute( 'href' ) ) ) ) {
				$lien->parentNode->removeChild( $lien );
			}
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
	 * Identifiant Giphy et titre lisible tirés d'une adresse (embed, page, média, image).
	 *
	 * @param string $adresse Adresse.
	 * @return array [ id, titre ] ou tableau vide.
	 */
	public static function giphy( $adresse ) {
		$adresse = trim( (string) $adresse );
		if ( preg_match( '#giphy\.com/embed/([A-Za-z0-9]+)#', $adresse, $m )
			|| preg_match( '#(?:media\d*|i)\.giphy\.com/(?:media/(?:v1\.[^/]+/)?)?([A-Za-z0-9]+)(?:/|\.gif|\.webp|\.mp4)#', $adresse, $m ) ) {
			return array( $m[1], '' );
		}
		if ( preg_match( '#giphy\.com/(?:gifs|stickers|clips)/(?:[\w-]+/)?(?:([\w-]+)-)?([A-Za-z0-9]+)/?(?:[?\#].*)?$#', $adresse, $m ) ) {
			return array( $m[2], ucfirst( str_replace( '-', ' ', (string) $m[1] ) ) );
		}
		return array();
	}

	/**
	 * Remplace chaque GIF Giphy du texte par la carte GIF du thème (vidéo mp4 de Giphy, image fixe en attendant,
	 * pastille, bouton pause, légende et crédit). Formes reconnues : iframe du code d'intégration (avec son cadre
	 * et sa ligne « via GIPHY »), lien ou adresse seule (paragraphe, bloc Intégrer), image Giphy (bloc Image).
	 * Composition « Rolling : GIF » : groupe .rl-gif-bloc, sa ligne .rl-gif-texte devient la légende.
	 *
	 * @param DOMDocument $dom      Document.
	 * @param DOMXPath    $xpath    Requêtes.
	 * @param callable    $fragment Fabrique de fragments.
	 * @param array       $textes   Textes (pastille, credit, pause, lecture, nom).
	 */
	private static function gifs( $dom, $xpath, $fragment, $textes ) { // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- propriétés de DOMDocument.
		$textes  = wp_parse_args(
			array_filter( $textes, 'strlen' ),
			array(
				'pastille' => 'GIF',
				'credit'   => 'via GIPHY',
				'pause'    => 'Mettre le GIF en pause',
				'lecture'  => 'Lire le GIF',
				'nom'      => 'GIF : %s',
			)
		);
		$classe  = function ( $nom ) {
			return "contains(concat(' ', normalize-space(@class), ' '), ' " . $nom . " ')";
		};
		$trouves = array();
		$noeuds  = $xpath->query( '//iframe[contains(@src, "giphy.com/embed/")] | //img[contains(@src, "giphy.com/")] | //a[contains(@href, "giphy.com/")] | //div[' . $classe( 'wp-block-embed__wrapper' ) . '][contains(., "giphy.com/")] | //p[contains(., "giphy.com/")][not(*)]' );
		foreach ( $noeuds as $noeud ) {
			$adresse = $noeud->getAttribute( 'src' ) ? $noeud->getAttribute( 'src' ) : ( $noeud->getAttribute( 'href' ) ? $noeud->getAttribute( 'href' ) : trim( $noeud->textContent ) );
			$infos   = self::giphy( $adresse );
			if ( ! $infos ) {
				continue;
			}
			// Unité à remplacer : le groupe de la composition, sinon la figure, le cadre du code Giphy ou le paragraphe.
			$unite = $noeud;
			$bloc  = $xpath->query( 'ancestor::*[' . $classe( 'rl-gif-bloc' ) . '][1]', $noeud )->item( 0 );
			if ( $bloc ) {
				$unite = $bloc;
			} else {
				$parent = $xpath->query( 'ancestor::figure[1] | ancestor::p[1]', $noeud )->item( 0 );
				if ( $parent ) {
					$unite = $parent;
				} elseif ( 'iframe' === $noeud->nodeName && 'div' === $noeud->parentNode->nodeName && 1 === $xpath->query( './*', $noeud->parentNode )->length ) {
					$unite = $noeud->parentNode;
				}
			}
			// Ligne « via GIPHY » du code d'intégration : elle donne le titre, puis elle part.
			if ( 'a' === $noeud->nodeName && ! $bloc && $unite->previousSibling ) {
				$avant = $unite->previousSibling;
				while ( $avant && XML_TEXT_NODE === $avant->nodeType && '' === trim( $avant->textContent ) ) {
					$avant = $avant->previousSibling;
				}
				if ( $avant && isset( $trouves[ spl_object_id( $avant ) ] ) ) {
					if ( $infos[1] && '' === $trouves[ spl_object_id( $avant ) ]['titre'] ) {
						$trouves[ spl_object_id( $avant ) ]['titre'] = $infos[1];
					}
					$trouves[ spl_object_id( $unite ) ] = array( 'supprimer' => $unite );
					continue;
				}
			}
			// Un lien Giphy au milieu d'une phrase reste un lien : seulement un lien seul sur sa ligne.
			if ( 'a' === $noeud->nodeName && ! $bloc && 'p' === $unite->nodeName && trim( $unite->textContent ) !== trim( $noeud->textContent ) ) {
				continue;
			}
			$cle = spl_object_id( $unite );
			if ( isset( $trouves[ $cle ] ) ) {
				if ( $infos[1] && empty( $trouves[ $cle ]['titre'] ) ) {
					$trouves[ $cle ]['titre'] = $infos[1];
				}
				continue;
			}
			// Format : largeur / hauteur de l'iframe ou de l'image, sinon cadre Giphy en %, sinon 4:3 (corrigé par rolling.js).
			// Dimensions en pixels seulement (« 100% » du code Giphy récent n'est pas une taille).
			$largeur = ctype_digit( trim( $noeud->getAttribute( 'width' ) ) ) ? (int) $noeud->getAttribute( 'width' ) : 0;
			$hauteur = ctype_digit( trim( $noeud->getAttribute( 'height' ) ) ) ? (int) $noeud->getAttribute( 'height' ) : 0;
			if ( ( ! $largeur || ! $hauteur ) && 'div' === $noeud->parentNode->nodeName && preg_match( '/padding-bottom:\s*([\d.]+)%/', $noeud->parentNode->getAttribute( 'style' ), $p ) && (float) $p[1] > 0 ) {
				$largeur = 1000;
				$hauteur = (int) round( 10 * (float) $p[1] );
			}
			$legende = '';
			if ( $bloc ) {
				$ligne   = $xpath->query( './/*[' . $classe( 'rl-gif-texte' ) . ']', $bloc )->item( 0 );
				$legende = $ligne ? trim( $ligne->textContent ) : '';
			} elseif ( 'figure' === $unite->nodeName ) {
				$ligne   = $xpath->query( './figcaption', $unite )->item( 0 );
				$legende = $ligne ? trim( $ligne->textContent ) : '';
			}
			$alt             = 'img' === $noeud->nodeName ? trim( $noeud->getAttribute( 'alt' ) ) : '';
			$trouves[ $cle ] = array(
				'unite'   => $unite,
				'id'      => $infos[0],
				'titre'   => $alt ? $alt : $infos[1],
				'legende' => $legende,
				'largeur' => $largeur > 0 && $hauteur > 0 ? $largeur : 4,
				'hauteur' => $largeur > 0 && $hauteur > 0 ? $hauteur : 3,
			);
		}

		foreach ( $trouves as $gif ) {
			if ( isset( $gif['supprimer'] ) ) {
				if ( $gif['supprimer']->parentNode ) {
					$gif['supprimer']->parentNode->removeChild( $gif['supprimer'] );
				}
				continue;
			}
			if ( ! $gif['unite']->parentNode ) {
				continue;
			}
			$base  = 'https://media.giphy.com/media/' . rawurlencode( $gif['id'] ) . '/';
			$nom   = $gif['legende'] ? $gif['legende'] : ( $gif['titre'] ? $gif['titre'] : $textes['pastille'] );
			$carte = '<figure class="rl-gif" data-rl-gif="" style="--rl-ratio: ' . round( $gif['largeur'] / $gif['hauteur'], 4 ) . '">'
				. '<div class="rl-gif-cadre" style="aspect-ratio: ' . (int) $gif['largeur'] . ' / ' . (int) $gif['hauteur'] . '">'
				. '<video class="rl-gif-video" src="' . esc_url( $base . 'giphy.mp4' ) . '" poster="' . esc_url( $base . 'giphy_s.gif' ) . '" muted="muted" loop="loop" playsinline="playsinline" preload="none" aria-label="' . esc_attr( sprintf( $textes['nom'], $nom ) ) . '"></video>'
				. '<span class="rl-gif-pastille" aria-hidden="true">' . esc_html( $textes['pastille'] ) . '</span>'
				. '<button type="button" class="rl-gif-bouton" aria-pressed="false" aria-label="' . esc_attr( $textes['pause'] ) . '" data-rl-pause="' . esc_attr( $textes['pause'] ) . '" data-rl-lecture="' . esc_attr( $textes['lecture'] ) . '"></button>'
				. '</div>'
				. '<figcaption class="rl-gif-legende">' . ( $gif['legende'] ? '<span>' . esc_html( $gif['legende'] ) . '</span>' : '' ) . '<span class="rl-gif-credit">' . esc_html( $textes['credit'] ) . '</span></figcaption>'
				. '</figure>';
			$gif['unite']->parentNode->replaceChild( $fragment( $carte ), $gif['unite'] );
		}
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

	/**
	 * Encart au milieu d'un article d'au moins 4 chapitres : le modèle « Rolling · Encart au milieu des articles »
	 * (prochain webinar, sinon dernier guide) avant le 3e intertitre. Rien si aucun des deux n'est publié.
	 *
	 * @param string $texte Texte de l'article.
	 * @return string
	 */
	private static function encart_milieu( $texte ) {
		if ( 'post' !== get_post_type( Rolling_Dynamique::contenu_courant() ) || preg_match_all( '/<h2[\s>]/i', $texte, $titres, PREG_OFFSET_CAPTURE ) < 4 ) {
			return $texte;
		}
		$modele = Rolling_Dynamique::modele_fiche( 'encart' );
		$encart = $modele ? \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $modele, false ) : '';
		if ( false === strpos( $encart, 'encart-milieu' ) ) {
			return $texte;
		}
		$position = $titres[0][2][1];

		return substr( $texte, 0, $position ) . '<div class="rl-encart-milieu">' . $encart . '</div>' . substr( $texte, $position );
	}

	/** Affichage. */
	protected function render() {
		$r   = $this->get_settings_for_display();
		$gif = array();
		foreach ( array( 'pastille', 'credit', 'pause', 'lecture', 'nom' ) as $cle ) {
			$gif[ $cle ] = (string) ( $r[ 'gif_' . $cle ] ?? '' );
		}
		$texte = self::enrichir( self::contenu_article(), (string) $r['libelle_video'], (string) $r['libelle_tableau'], $gif );
		if ( 'yes' === ( $r['encart'] ?? 'yes' ) ) {
			$texte = self::encart_milieu( $texte );
		}
		echo '<div class="' . esc_attr( $r['classe'] ) . '" data-rl-article>' . $texte . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- contenu de l'article, filtré par WordPress.
	}
}
