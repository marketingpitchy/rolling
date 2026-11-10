<?php
/**
 * Balises dynamiques « Rolling » (chargées par Elementor).
 *
 * Chaque balise lit le contenu courant (la réalisation, le témoignage… affiché). Dans l'éditeur, elle lit le dernier
 * contenu du type présenté par le modèle, pour mettre en forme avec de vrais textes.
 *
 * Les contrôles « Avant / Après / Repli » d'Elementor sont retirés : sans valeur par défaut, Elementor 4 refuse
 * la balise dans ses éléments atomiques.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Socle commun : groupe Rolling, liste de choix.
 */
trait Rolling_Balise_Socle {

	/**
	 * Groupe.
	 *
	 * @return string
	 */
	public function get_group() {
		return Rolling_Dynamique::GROUPE;
	}

	/**
	 * Pas de section « Avancé ».
	 */
	protected function register_advanced_section() {}

	/**
	 * Ajoute une liste de choix.
	 *
	 * @param string $cle     Clé.
	 * @param string $libelle Libellé.
	 * @param array  $options Options.
	 */
	protected function choix( $cle, $libelle, $options ) {
		$this->add_control(
			$cle,
			array(
				'label'   => $libelle,
				'type'    => Controls_Manager::SELECT,
				'options' => $options,
				'default' => (string) array_key_first( $options ),
			)
		);
	}

	/**
	 * Contenu lu par la balise.
	 *
	 * @return int
	 */
	protected function contenu() {
		return Rolling_Dynamique::contenu_courant();
	}
}

/**
 * Titre d'une fiche, la fin en couleur (mise en italique : dégradé de la section).
 */
class Rolling_Balise_Titre extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-titre';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Titre (fin en couleur)', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'forme',
			__( 'Forme', 'rolling' ),
			array(
				'client' => __( 'Client : titre', 'rolling' ),
				'titre'  => __( 'Titre seul', 'rolling' ),
			)
		);
	}

	/** Affichage. */
	public function render() {
		$id      = $this->contenu();
		$titre   = esc_html( get_the_title( $id ) );
		$couleur = trim( (string) Rolling_Contenus::valeur( 'titre_couleur', $id ) );

		if ( '' !== $couleur && false !== mb_strpos( $titre, esc_html( $couleur ) ) ) {
			$titre = str_replace( esc_html( $couleur ), '<em>' . esc_html( $couleur ) . '</em>', $titre );
		}

		$client = (string) Rolling_Contenus::valeur( 'client', $id );
		if ( 'client' === $this->get_settings( 'forme' ) && '' !== $client ) {
			$titre = esc_html( $client ) . '&nbsp;: ' . $titre;
		}

		echo wp_kses( $titre, array( 'em' => array() ) );
	}
}

/**
 * Un champ texte d'un contenu (client, chapô, citation…).
 */
class Rolling_Balise_Champ extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-champ';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Champ', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/**
	 * Champs proposés (texte et zones).
	 *
	 * @return array
	 */
	public static function options() {
		$options = array();
		foreach ( Rolling_Contenus::champs() as $type => $boites ) {
			$type_nom = Rolling_Contenus::types()[ $type ]['singulier'];
			foreach ( $boites as $champs ) {
				foreach ( $champs as $cle => $def ) {
					if ( in_array( $def[0], array( 'texte', 'zone', 'nombre' ), true ) && ! isset( $options[ $cle ] ) ) {
						$options[ $cle ] = $def[1] . ' (' . $type_nom . ')';
					}
				}
			}
		}

		return $options;
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix( 'champ', __( 'Champ', 'rolling' ), self::options() );
		foreach ( array(
			'avant' => __( 'Texte avant', 'rolling' ),
			'apres' => __( 'Texte après (ex. : « pages »)', 'rolling' ),
		) as $cle => $libelle ) {
			$this->add_control(
				$cle,
				array(
					'label'   => $libelle,
					'type'    => Controls_Manager::TEXT,
					'default' => '',
				)
			);
		}
	}

	/** Affichage. */
	public function render() {
		$cle    = (string) $this->get_settings( 'champ' );
		$valeur = (string) Rolling_Contenus::valeur( $cle, $this->contenu() );
		$def    = Rolling_Contenus::champ( get_post_type( $this->contenu() ), $cle );

		if ( '' === $valeur ) {
			return;
		}

		echo esc_html( (string) $this->get_settings( 'avant' ) );
		if ( $def && 'zone' === $def[0] ) {
			echo wp_kses( nl2br( esc_html( $valeur ) ), array( 'br' => array() ) );
		} else {
			echo esc_html( $valeur );
		}
		echo esc_html( (string) $this->get_settings( 'apres' ) );
	}
}

/**
 * Article de blog : titre, date, catégorie, extrait, temps de lecture.
 */
class Rolling_Balise_Article extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-article';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Article ou page', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'info',
			__( 'Information', 'rolling' ),
			array(
				'titre'     => __( 'Titre', 'rolling' ),
				'publie'    => __( 'Publié le…', 'rolling' ),
				'date'      => __( 'Date', 'rolling' ),
				'categorie' => __( 'Catégorie', 'rolling' ),
				'extrait'   => __( 'Extrait', 'rolling' ),
				'lecture'   => __( 'Temps de lecture (6 minutes)', 'rolling' ),
				'lecture_c' => __( 'Temps de lecture court (6 min de lecture)', 'rolling' ),
			)
		);
	}

	/**
	 * Minutes de lecture (200 mots par minute).
	 *
	 * @param int $id Contenu.
	 * @return int
	 */
	public static function minutes( $id ) {
		$mots = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', $id ) ) );

		return max( 1, (int) round( $mots / 200 ) );
	}

	/** Affichage. */
	public function render() {
		$id = get_the_ID() && 'elementor_library' !== get_post_type() ? get_the_ID() : $this->contenu();
		if ( 'elementor_library' === get_post_type( $id ) ) {
			$dernier = get_posts(
				array(
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			$id      = $dernier ? $dernier[0] : $id;
		}

		switch ( $this->get_settings( 'info' ) ) {
			case 'publie':
				/* translators: %s : date de publication. */
				echo esc_html( sprintf( __( 'Publié le %s', 'rolling' ), get_the_date( 'j F Y', $id ) ) );
				break;
			case 'date':
				echo esc_html( get_the_date( 'j F Y', $id ) );
				break;
			case 'categorie':
				$categories = get_the_category( $id );
				echo esc_html( $categories ? $categories[0]->name : '' );
				break;
			case 'extrait':
				echo esc_html( get_the_excerpt( $id ) );
				break;
			case 'lecture':
				/* translators: %d : nombre de minutes. */
				echo esc_html( sprintf( _n( '%d minute', '%d minutes', self::minutes( $id ), 'rolling' ), self::minutes( $id ) ) );
				break;
			case 'lecture_c':
				/* translators: %d : nombre de minutes. */
				echo esc_html( sprintf( __( '%d min de lecture', 'rolling' ), self::minutes( $id ) ) );
				break;
			default:
				echo esc_html( get_the_title( $id ) );
		}
	}
}

/**
 * Premier terme d'un filtre (métier, format, thème, catégorie).
 */
class Rolling_Balise_Terme extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-terme';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Métier, format ou thème', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'taxonomie',
			__( 'Liste', 'rolling' ),
			array(
				'rolling_metier' => __( 'Métier', 'rolling' ),
				'rolling_format' => __( 'Format vidéo', 'rolling' ),
				'rolling_theme'  => __( 'Thème (guides, webinars)', 'rolling' ),
				'category'       => __( 'Catégorie d’article', 'rolling' ),
			)
		);
	}

	/** Affichage. */
	public function render() {
		$termes = get_the_terms( $this->contenu(), (string) $this->get_settings( 'taxonomie' ) );
		echo esc_html( $termes && ! is_wp_error( $termes ) ? $termes[0]->name : '' );
	}
}

/**
 * Webinar : date complète, jour, mois, heure, intervenants.
 */
class Rolling_Balise_Webinar extends Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-webinar';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Webinar : date et intervenants', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::TEXT_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'info',
			__( 'Information', 'rolling' ),
			array(
				'complete'     => __( 'Jeudi 3 décembre 2026 · 11 h 00 · 45 min · en ligne', 'rolling' ),
				'jour'         => __( 'Jour (3)', 'rolling' ),
				'mois'         => __( 'Mois (Déc.)', 'rolling' ),
				'heure'        => __( 'Heure (11 h 00)', 'rolling' ),
				'date'         => __( 'Date (3 décembre 2026)', 'rolling' ),
				'diffuse'      => __( 'Diffusé le 3 décembre 2026', 'rolling' ),
				'duree'        => __( 'Durée (45 min · en ligne)', 'rolling' ),
				'intervenants' => __( 'Intervenants (Thomas, Directeur Artistique, et Sophie, Chef de Projet)', 'rolling' ),
			)
		);
	}

	/** Affichage. */
	public function render() {
		$id    = $this->contenu();
		$debut = Rolling_Webinars::debut( $id );
		$duree = (int) Rolling_Contenus::valeur( 'duree', $id );
		$duree = $duree ? $duree : 45;
		$fuseau = new DateTimeZone( Rolling_Webinars::FUSEAU );
		/* translators: %d : durée en minutes. */
		$texte_duree = sprintf( __( '%d min · en ligne', 'rolling' ), $duree );

		switch ( $this->get_settings( 'info' ) ) {
			case 'jour':
				echo esc_html( $debut ? wp_date( 'j', $debut, $fuseau ) : '' );
				break;
			case 'mois':
				echo esc_html( $debut ? self::mois_court( (int) wp_date( 'n', $debut, $fuseau ) ) : '' );
				break;
			case 'heure':
				echo esc_html( $debut ? str_replace( ':', ' h ', wp_date( 'H:i', $debut, $fuseau ) ) : '' );
				break;
			case 'date':
				echo esc_html( $debut ? wp_date( 'j F Y', $debut, $fuseau ) : '' );
				break;
			case 'duree':
				echo esc_html( $texte_duree );
				break;
			case 'diffuse':
				/* translators: %s : date de diffusion. */
				echo esc_html( $debut ? sprintf( __( 'Diffusé le %s', 'rolling' ), wp_date( 'j F Y', $debut, $fuseau ) ) : '' );
				break;
			case 'intervenants':
				$noms = array();
				foreach ( Rolling_Contenus::valeur( 'intervenants', $id ) as $personne ) {
					$noms[] = '<strong>' . esc_html( $personne['nom'] ) . '</strong>' . ( $personne['poste'] ? ', ' . esc_html( $personne['poste'] ) : '' );
				}
				echo wp_kses( wp_sprintf( '%l', $noms ), array( 'strong' => array() ) );
				break;
			default:
				echo esc_html( $debut ? ucfirst( wp_date( 'l j F Y', $debut, $fuseau ) ) . ' · ' . str_replace( ':', ' h ', wp_date( 'H:i', $debut, $fuseau ) ) . ' · ' . $texte_duree : '' );
		}
	}

	/**
	 * Mois abrégé à la française.
	 *
	 * @param int $mois 1 à 12.
	 * @return string
	 */
	private static function mois_court( $mois ) {
		$noms = array( '', 'Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.' );

		return $noms[ $mois ] ?? '';
	}
}

/**
 * Image : image à la une, logo du client, portrait.
 */
class Rolling_Balise_Image extends Data_Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-image';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Image', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::IMAGE_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'image',
			__( 'Image', 'rolling' ),
			array(
				'une'      => __( 'Image (vignette et affiche)', 'rolling' ),
				'logo'     => __( 'Logo du client', 'rolling' ),
				'portrait' => __( 'Portrait', 'rolling' ),
			)
		);
	}

	/**
	 * Valeur.
	 *
	 * @param array $options Options.
	 * @return array
	 */
	public function get_value( array $options = array() ) {
		$id    = $this->contenu();
		$choix = (string) $this->get_settings( 'image' );
		$image = 'une' === $choix ? (int) get_post_thumbnail_id( $id ) : (int) Rolling_Contenus::valeur( $choix, $id );

		return $image ? array(
			'id'  => $image,
			'url' => (string) wp_get_attachment_image_url( $image, 'full' ),
		) : array(
			'id'  => '',
			'url' => '',
		);
	}
}

/**
 * Lien : fiche, vidéo, page HubSpot du guide, inscription, replay.
 */
class Rolling_Balise_Lien extends Data_Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-lien';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Lien', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::URL_CATEGORY );
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix(
			'lien',
			__( 'Lien', 'rolling' ),
			array(
				'fiche'       => __( 'La fiche (réalisation, témoignage, article)', 'rolling' ),
				'video'       => __( 'La vidéo', 'rolling' ),
				'lien'        => __( 'Page HubSpot du guide', 'rolling' ),
				'inscription' => __( 'Inscription au webinar', 'rolling' ),
				'replay'      => __( 'Replay du webinar', 'rolling' ),
			)
		);
	}

	/**
	 * Valeur.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	public function get_value( array $options = array() ) {
		$id    = $this->contenu();
		$choix = (string) $this->get_settings( 'lien' );

		return 'fiche' === $choix ? (string) get_permalink( $id ) : (string) Rolling_Contenus::valeur( $choix, $id );
	}
}
