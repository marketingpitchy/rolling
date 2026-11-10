<?php
/**
 * Balise dynamique Rolling : Rolling_Balise_Partage.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as Balises;

/**
 * Lien de partage (réseaux, e-mail) ou « Résumer avec » (IA) de la page affichée. À poser sur un bouton, une image
 * ou un conteneur : le lien se calcule pour chaque article, le bouton peut donc entrer dans un composant.
 */
class Rolling_Balise_Partage extends Data_Tag {
	use Rolling_Balise_Socle;

	/** Nom. */
	public function get_name() {
		return 'rolling-partage';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Partager / Résumer avec une IA', 'rolling' );
	}

	/** Catégories. */
	public function get_categories() {
		return array( Balises::URL_CATEGORY );
	}

	/**
	 * Adresses : %1$s = adresse de la page, %2$s = titre, %3$s = demande faite à l'IA (encodés).
	 *
	 * @return array<string, array{0: string, 1: string}> Clé => libellé, modèle d'adresse.
	 */
	public static function destinations() {
		return array(
			'linkedin'   => array( __( 'Partager sur LinkedIn', 'rolling' ), 'https://www.linkedin.com/sharing/share-offsite/?url=%1$s' ),
			'facebook'   => array( __( 'Partager sur Facebook', 'rolling' ), 'https://www.facebook.com/sharer/sharer.php?u=%1$s' ),
			'x'          => array( __( 'Partager sur X', 'rolling' ), 'https://x.com/intent/post?url=%1$s&text=%2$s' ),
			'whatsapp'   => array( __( 'Partager sur WhatsApp', 'rolling' ), 'https://wa.me/?text=%2$s%%20%1$s' ),
			'email'      => array( __( 'Envoyer par e-mail', 'rolling' ), 'mailto:?subject=%2$s&body=%1$s' ),
			'chatgpt'    => array( __( 'Résumer avec ChatGPT', 'rolling' ), 'https://chatgpt.com/?q=%3$s' ),
			'claude'     => array( __( 'Résumer avec Claude', 'rolling' ), 'https://claude.ai/new?q=%3$s' ),
			'perplexity' => array( __( 'Résumer avec Perplexity', 'rolling' ), 'https://www.perplexity.ai/search?q=%3$s' ),
			'lechat'     => array( __( 'Résumer avec Le Chat (Mistral)', 'rolling' ), 'https://chat.mistral.ai/chat?q=%3$s' ),
			'page'       => array( __( 'Adresse de la page', 'rolling' ), '%4$s' ),
		);
	}

	/** Réglages. */
	protected function register_controls() {
		$this->choix( 'vers', __( 'Lien', 'rolling' ), wp_list_pluck( self::destinations(), 0 ) );
		$this->add_control(
			'demande',
			array(
				/* translators: %s : reste tel quel, remplacé par l'adresse de l'article. */
				'label'       => __( 'Demande faite à l’IA (%s = adresse de l’article)', 'rolling' ),
				'type'        => Controls_Manager::TEXT,
				/* translators: %s : reste tel quel, remplacé par l'adresse de l'article. */
				'default'     => __( 'Résume cet article en 5 points : %s', 'rolling' ),
				'description' => __( 'Utilisée seulement pour les liens « Résumer avec ».', 'rolling' ),
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
		$destinations = self::destinations();
		$vers         = (string) $this->get_settings( 'vers' );
		if ( ! isset( $destinations[ $vers ] ) ) {
			return '';
		}

		$id      = is_singular() ? (int) get_queried_object_id() : (int) $this->contenu();
		$adresse = (string) get_permalink( $id );
		$demande = (string) $this->get_settings( 'demande' );
		$demande = false !== strpos( $demande, '%s' ) ? str_replace( '%s', $adresse, $demande ) : trim( $demande . ' ' . $adresse );

		return sprintf(
			$destinations[ $vers ][1],
			rawurlencode( $adresse ),
			rawurlencode( html_entity_decode( (string) get_the_title( $id ), ENT_QUOTES, 'UTF-8' ) ),
			rawurlencode( $demande ),
			$adresse
		);
	}
}
