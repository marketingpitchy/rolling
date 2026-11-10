<?php
/**
 * Installation et mise à jour : variables, classes globales, médias, blocs, pages et menus.
 *
 * Principe : ce que le thème a installé et que la cliente n'a pas modifié est remis à jour.
 * Ce qu'elle a modifié est gardé ; pour un modèle, la nouvelle version arrive à côté (« V2 »).
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Plugin;
use Elementor\Modules\GlobalClasses\Global_Classes_Repository;

/**
 * Installateur du thème Rolling.
 */
class Rolling_Installateur {

	const OPTION_VERSION    = 'rolling_version_installee';
	const OPTION_VARIABLES  = 'rolling_variables';
	const OPTION_CLASSES    = 'rolling_classes';
	const OPTION_MEDIAS     = 'rolling_medias';
	const OPTION_MODELES    = 'rolling_modeles';
	const OPTION_PREMIERE   = 'rolling_premiere_installation';
	const TRANSIENT_RAPPORT = 'rolling_rapport';
	const URL_MEDIA         = 'https://media.rolling.invalid/';
	const META_VARIABLES    = '_elementor_global_variables';

	/**
	 * Lignes du rapport affiché après une installation.
	 *
	 * @var string[]
	 */
	private static $rapport = array();

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'verifier_version' ), 20 );
		add_action( 'after_switch_theme', array( __CLASS__, 'demander_installation' ) );
		add_action( 'admin_notices', array( __CLASS__, 'afficher_rapport' ) );
	}

	/**
	 * Force une installation au prochain passage dans l'administration.
	 */
	public static function demander_installation() {
		delete_option( self::OPTION_VERSION );
	}

	/**
	 * Lance l'installation si la version du thème a changé.
	 */
	public static function verifier_version() {
		if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
			return;
		}

		if ( get_option( self::OPTION_VERSION ) === ROLLING_VERSION ) {
			return;
		}

		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( Global_Classes_Repository::class ) ) {
			self::$rapport[] = __( 'Elementor 4 doit être activé pour installer les styles et les blocs Rolling.', 'rolling' );
			self::enregistrer_rapport();
			return;
		}

		if ( get_transient( 'rolling_installation_en_cours' ) ) {
			return;
		}

		set_transient( 'rolling_installation_en_cours', 1, 5 * MINUTE_IN_SECONDS );
		self::installer();
		delete_transient( 'rolling_installation_en_cours' );
	}

	/**
	 * Installation complète (idempotente).
	 */
	public static function installer() {
		self::$rapport = array();

		self::installer_variables();
		$obsoletes = self::installer_classes();
		$medias    = self::installer_medias();
		self::installer_modeles( $medias );
		self::supprimer_classes_inutiles( $obsoletes );

		$site_existant = (bool) get_option( self::OPTION_PREMIERE );

		if ( ! $site_existant ) {
			self::premiere_installation( $medias );
			update_option( self::OPTION_PREMIERE, ROLLING_VERSION, false );
		}

		self::nouveautes( $site_existant, $medias );
		flush_rewrite_rules( false );

		self::vider_caches();
		update_option( self::OPTION_VERSION, ROLLING_VERSION, false );

		/* translators: %s: numéro de version du thème. */
		array_unshift( self::$rapport, sprintf( __( 'Thème Rolling %s installé : variables, classes et blocs à jour.', 'rolling' ), ROLLING_VERSION ) );
		self::enregistrer_rapport();
	}

	// Variables.

	/**
	 * Installe ou met à jour les variables Elementor (couleurs, polices).
	 */
	private static function installer_variables() {
		$kit = Plugin::$instance->kits_manager->get_active_kit();

		if ( ! $kit ) {
			return;
		}

		$sources    = Rolling_Donnees::lire( 'variables' );
		$suivi      = get_option( self::OPTION_VARIABLES, array() );
		$record     = $kit->get_json_meta( self::META_VARIABLES );
		$record     = is_array( $record ) && ! empty( $record ) ? $record : array(
			'data'      => array(),
			'watermark' => 0,
			'version'   => 2,
		);
		$donnees    = isset( $record['data'] ) && is_array( $record['data'] ) ? $record['data'] : array();
		$maintenant = gmdate( 'Y-m-d H:i:s' );
		$ordre      = count( $donnees );
		$change     = false;
		$gardees    = 0;
		$ids_theme  = array();

		foreach ( $sources as $source ) {
			$id          = $source['id'];
			$ids_theme[] = $id;
			$attendu     = array(
				'type'  => $source['type'],
				'label' => $source['label'],
				'value' => $source['value'],
			);

			if ( ! isset( $donnees[ $id ] ) ) {
				$donnees[ $id ] = $attendu + array(
					'order'      => ++$ordre,
					'created_at' => $maintenant,
					'updated_at' => $maintenant,
				);
				$change         = true;
			} else {
				$actuel   = array_intersect_key( $donnees[ $id ], $attendu );
				$modifiee = isset( $suivi[ $id ] ) && Rolling_Donnees::empreinte( $actuel ) !== $suivi[ $id ];

				if ( $modifiee ) {
					++$gardees;
					continue;
				}

				if ( Rolling_Donnees::empreinte( $actuel ) !== Rolling_Donnees::empreinte( $attendu ) ) {
					$donnees[ $id ] = array_merge( $donnees[ $id ], $attendu, array( 'updated_at' => $maintenant ) );
					unset( $donnees[ $id ]['deleted'], $donnees[ $id ]['deleted_at'] );
					$change = true;
				}
			}

			$suivi[ $id ] = Rolling_Donnees::empreinte( $attendu );
		}

		// Variables du thème devenues inutiles : retirées si la cliente ne les a pas modifiées.
		foreach ( array_keys( $suivi ) as $id ) {
			if ( in_array( $id, $ids_theme, true ) || ! isset( $donnees[ $id ] ) ) {
				continue;
			}

			$actuel = array_intersect_key( $donnees[ $id ], array_flip( array( 'type', 'label', 'value' ) ) );

			if ( Rolling_Donnees::empreinte( $actuel ) === $suivi[ $id ] ) {
				unset( $donnees[ $id ] );
				$change = true;
			}

			unset( $suivi[ $id ] );
		}

		if ( $change ) {
			$record['data']      = $donnees;
			$record['watermark'] = (int) ( $record['watermark'] ?? 0 ) + 1;
			$record['version']   = $record['version'] ?? 2;
			$kit->update_json_meta( self::META_VARIABLES, $record );
		}

		update_option( self::OPTION_VARIABLES, $suivi, false );

		if ( $gardees ) {
			/* translators: %d: nombre de variables. */
			self::$rapport[] = sprintf( _n( '%d variable modifiée par vous a été gardée telle quelle.', '%d variables modifiées par vous ont été gardées telles quelles.', $gardees, 'rolling' ), $gardees );
		}
	}

	// Classes globales.

	/**
	 * Installe ou met à jour les classes globales du thème (sans rien supprimer).
	 *
	 * @return string[] Ids des classes du thème devenues inutiles, à supprimer après la mise à jour des modèles.
	 */
	private static function installer_classes() {
		$sources = Rolling_Donnees::lire( 'classes' );
		$classes = isset( $sources['classes'] ) ? $sources['classes'] : array();

		if ( empty( $classes ) ) {
			return array();
		}

		$depot   = Global_Classes_Repository::make();
		$actuels = $depot->all( true )->get_items()->all();
		$ordre   = $depot->get_order();
		$suivi   = get_option( self::OPTION_CLASSES, array() );
		$labels  = array();

		foreach ( $actuels as $id => $item ) {
			$labels[ strtolower( $item['label'] ) ] = $id;
		}

		$touchees  = array();
		$ajoutees  = array();
		$modifiees = array();
		$gardees   = array();
		$ids_theme = array();

		foreach ( $classes as $classe ) {
			$id          = $classe['id'];
			$ids_theme[] = $id;
			$attendu     = array(
				'id'       => $id,
				'label'    => $classe['label'],
				'type'     => 'class',
				'variants' => $classe['variants'],
			);

			if ( ! isset( $actuels[ $id ] ) ) {
				// Une classe de la cliente porte déjà ce nom : on ne l'écrase pas.
				if ( isset( $labels[ strtolower( $classe['label'] ) ] ) ) {
					$gardees[] = $classe['label'];
					continue;
				}

				$touchees[ $id ] = $attendu;
				$ajoutees[]      = $id;
				$suivi[ $id ]    = self::empreinte_classe( $attendu );
				continue;
			}

			$actuel   = $actuels[ $id ];
			$modifiee = isset( $suivi[ $id ] ) && self::empreinte_classe( $actuel ) !== $suivi[ $id ];

			if ( $modifiee ) {
				$gardees[] = $actuel['label'];
				continue;
			}

			if ( self::empreinte_classe( $actuel ) !== self::empreinte_classe( $attendu ) ) {
				$touchees[ $id ] = $attendu;
				$modifiees[]     = $id;
			}

			$suivi[ $id ] = self::empreinte_classe( $attendu );
		}

		// Classes du thème devenues inutiles : candidates à la suppression si la cliente ne les a pas modifiées.
		$obsoletes = array();

		foreach ( array_keys( $suivi ) as $id ) {
			if ( in_array( $id, $ids_theme, true ) ) {
				continue;
			}

			if ( isset( $actuels[ $id ] ) && self::empreinte_classe( $actuels[ $id ] ) === $suivi[ $id ] ) {
				$obsoletes[] = $id;
			} else {
				unset( $suivi[ $id ] );
			}
		}

		// Ordre : celui des données du thème (les premières l'emportent), après les classes de la cliente.
		// Il est appliqué même si aucune classe n'a changé : une priorité peut évoluer d'une version à l'autre.
		$presentes    = array_values(
			array_filter(
				array_merge( $ids_theme, $obsoletes ),
				fn( $id ) => isset( $actuels[ $id ] ) || in_array( $id, $ajoutees, true )
			)
		);
		$nouvel_ordre = self::ordre_classes( $ordre, $presentes, array(), $actuels );

		if ( $ajoutees || $modifiees || $nouvel_ordre !== $ordre ) {
			$depot->apply_changes(
				$touchees,
				array(
					'added'    => $ajoutees,
					'modified' => $modifiees,
					'deleted'  => array(),
					'order'    => $nouvel_ordre !== $ordre,
				),
				$nouvel_ordre
			);
		}

		// L'empreinte retenue est celle de la classe telle qu'Elementor l'a enregistrée.
		if ( $ajoutees || $modifiees ) {
			$relues = Global_Classes_Repository::make()->all( true )->get_items()->all();

			foreach ( array_merge( $ajoutees, $modifiees ) as $id ) {
				if ( isset( $relues[ $id ] ) ) {
					$suivi[ $id ] = self::empreinte_classe( $relues[ $id ] );
				}
			}
		}

		update_option( self::OPTION_CLASSES, $suivi, false );

		if ( $gardees ) {
			/* translators: %s: liste de noms de classes. */
			self::$rapport[] = sprintf( __( 'Classes modifiées par vous, gardées telles quelles : %s.', 'rolling' ), implode( ', ', $gardees ) );
		}

		return $obsoletes;
	}

	/**
	 * Supprime les classes du thème devenues inutiles, seulement si plus aucune page ni aucun modèle ne les utilise
	 * (Elementor retirerait sinon la classe de ces pages, et le thème ne touche jamais aux pages).
	 *
	 * @param string[] $obsoletes Ids candidats.
	 */
	private static function supprimer_classes_inutiles( $obsoletes ) {
		if ( empty( $obsoletes ) ) {
			return;
		}

		$relations  = new \Elementor\Modules\GlobalClasses\Global_Classes_Relations();
		$depot      = Global_Classes_Repository::make();
		$actuels    = $depot->all( true )->get_items()->all();
		$suivi      = get_option( self::OPTION_CLASSES, array() );
		$supprimees = array();
		$utilisees  = array();

		foreach ( $obsoletes as $id ) {
			if ( ! isset( $actuels[ $id ] ) ) {
				unset( $suivi[ $id ] );
				continue;
			}

			if ( $relations->get_posts_by_style( $id ) ) {
				$utilisees[] = $actuels[ $id ]['label'];
				continue;
			}

			$supprimees[] = $id;
			unset( $suivi[ $id ] );
		}

		if ( $supprimees ) {
			$ordre = array_values( array_diff( $depot->get_order(), $supprimees ) );
			$depot->apply_changes(
				array(),
				array(
					'added'    => array(),
					'modified' => array(),
					'deleted'  => $supprimees,
					'order'    => true,
				),
				$ordre
			);
		}

		update_option( self::OPTION_CLASSES, $suivi, false );

		if ( $utilisees ) {
			/* translators: %s: liste de noms de classes. */
			self::$rapport[] = sprintf( __( 'Classes que le thème n’utilise plus, gardées car vos pages s’en servent : %s.', 'rolling' ), implode( ', ', $utilisees ) );
		}
	}

	/**
	 * Ordre des classes : les classes de la cliente gardent leur place, celles du thème suivent
	 * l'ordre du thème (les plus prioritaires en premier).
	 *
	 * @param string[] $ordre      Ordre actuel.
	 * @param string[] $ids_theme  Ids des classes du thème, dans l'ordre voulu.
	 * @param string[] $supprimees Ids supprimés.
	 * @param array    $actuels    Classes existantes.
	 * @return string[]
	 */
	private static function ordre_classes( $ordre, $ids_theme, $supprimees, $actuels ) {
		$cliente = array();

		foreach ( $ordre as $id ) {
			if ( ! in_array( $id, $ids_theme, true ) && ! in_array( $id, $supprimees, true ) && isset( $actuels[ $id ] ) ) {
				$cliente[] = $id;
			}
		}

		return array_values( array_unique( array_merge( $cliente, $ids_theme ) ) );
	}

	/**
	 * Empreinte d'une classe (nom et styles).
	 *
	 * @param array $classe Classe.
	 * @return string
	 */
	private static function empreinte_classe( $classe ) {
		return Rolling_Donnees::empreinte(
			array(
				'label'    => $classe['label'] ?? '',
				'variants' => $classe['variants'] ?? array(),
			)
		);
	}

	// Médias de départ.

	/**
	 * Copie les images de départ dans la médiathèque (une seule fois chacune).
	 *
	 * @return array<string, int> Clé du média => id de la pièce jointe.
	 */
	private static function installer_medias() {
		$medias = Rolling_Donnees::lire( 'medias' );
		$suivi  = get_option( self::OPTION_MEDIAS, array() );

		if ( empty( $medias ) ) {
			return $suivi;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		foreach ( $medias as $media ) {
			$cle = $media['cle'];

			if ( isset( $suivi[ $cle ] ) && get_post( $suivi[ $cle ] ) ) {
				continue;
			}

			$fichier = ROLLING_DIR . '/assets/medias/' . $media['fichier'];

			if ( ! is_readable( $fichier ) ) {
				continue;
			}

			$temporaire = wp_tempnam( $media['fichier'] );
			copy( $fichier, $temporaire );

			$id = media_handle_sideload(
				array(
					'name'     => $media['fichier'],
					'tmp_name' => $temporaire,
				),
				0,
				$media['titre'] ?? ''
			);

			if ( is_wp_error( $id ) ) {
				wp_delete_file( $temporaire );
				continue;
			}

			update_post_meta( $id, '_wp_attachment_image_alt', $media['alt'] ?? '' );
			update_post_meta( $id, '_rolling_cle', $cle );
			$suivi[ $cle ] = $id;
		}

		update_option( self::OPTION_MEDIAS, $suivi, false );

		return $suivi;
	}

	// Blocs et modèles (Modèles > Mes modèles).

	/**
	 * Installe ou met à jour les blocs du thème dans « Mes modèles ».
	 *
	 * @param array<string, int> $medias Médias installés.
	 */
	private static function installer_modeles( $medias ) {
		$blocs  = Rolling_Donnees::lire_dossier( 'blocs' );
		$suivi  = get_option( self::OPTION_MODELES, array() );
		$source = Plugin::$instance->templates_manager->get_source( 'local' );
		$cles   = array();

		if ( ! $source ) {
			return;
		}

		foreach ( $blocs as $bloc ) {
			if ( empty( $bloc['cle'] ) || empty( $bloc['contenu'] ) ) {
				continue;
			}

			$cle     = $bloc['cle'];
			$cles[]  = $cle;
			$contenu = self::remplacer_medias( $bloc['contenu'], $medias );
			$info    = $suivi[ $cle ] ?? array();
			$post_id = isset( $info['post_id'] ) && get_post( $info['post_id'] ) ? (int) $info['post_id'] : 0;

			// Même version du bloc déjà installée : rien à faire.
			if ( $post_id && ( $info['source'] ?? '' ) === Rolling_Donnees::empreinte( $contenu ) ) {
				continue;
			}

			if ( $post_id && self::modele_modifie( $post_id, $info ) ) {
				// La cliente a modifié ce modèle : la nouvelle version arrive à côté. Une fiche garde son modèle modifié.
				$fiche = array_search( $cle, Rolling_Dynamique::FICHES, true );
				if ( false !== $fiche && ! get_theme_mod( 'rolling_modele_' . $fiche ) ) {
					set_theme_mod( 'rolling_modele_' . $fiche, $post_id );
				}
				$titre   = self::titre_libre( $bloc['titre'] );
				$post_id = self::creer_modele( $source, $bloc, $contenu, $titre );

				if ( $post_id ) {
					/* translators: %s: titre du modèle. */
					self::$rapport[] = sprintf( __( 'Vous aviez modifié un modèle : la nouvelle version s’appelle « %s ».', 'rolling' ), $titre );
				}
			} elseif ( $post_id ) {
				$document = Plugin::$instance->documents->get( $post_id, false );

				if ( $document ) {
					$document->save( array( 'elements' => $contenu ) );
				}
				if ( ! empty( $bloc['apercu'] ) ) {
					update_post_meta( $post_id, '_rolling_apercu', sanitize_key( $bloc['apercu'] ) );
				}
			} else {
				$post_id = self::creer_modele( $source, $bloc, $contenu, $bloc['titre'] );
			}

			if ( $post_id ) {
				$suivi[ $cle ] = array(
					'post_id'   => $post_id,
					'source'    => Rolling_Donnees::empreinte( $contenu ),
					'empreinte' => self::empreinte_modele( $post_id ),
				);
				// Suivi gardé après chaque modèle : une installation interrompue ne fait pas croire à des modifications.
				update_option( self::OPTION_MODELES, $suivi, false );
			}
		}

		// Blocs retirés du thème : supprimés s'ils n'ont pas été modifiés.
		foreach ( $suivi as $cle => $info ) {
			if ( in_array( $cle, $cles, true ) ) {
				continue;
			}

			if ( ! empty( $info['post_id'] ) && get_post( $info['post_id'] ) && ! self::modele_modifie( (int) $info['post_id'], $info ) ) {
				wp_delete_post( (int) $info['post_id'], true );
			}

			unset( $suivi[ $cle ] );
		}

		update_option( self::OPTION_MODELES, $suivi, false );
	}

	/**
	 * Crée un modèle dans la bibliothèque locale d'Elementor.
	 *
	 * @param object $source  Source locale d'Elementor.
	 * @param array  $bloc    Bloc du thème.
	 * @param array  $contenu Éléments Elementor.
	 * @param string $titre   Titre du modèle.
	 * @return int
	 */
	private static function creer_modele( $source, $bloc, $contenu, $titre ) {
		$post_id = $source->save_item(
			array(
				'type'          => $bloc['type'] ?? 'container',
				'title'         => $titre,
				'content'       => $contenu,
				'page_settings' => $bloc['reglages'] ?? array(),
			)
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 0;
		}

		update_post_meta( $post_id, '_rolling_cle', $bloc['cle'] );
		if ( ! empty( $bloc['apercu'] ) ) {
			update_post_meta( $post_id, '_rolling_apercu', sanitize_key( $bloc['apercu'] ) );
		}

		return (int) $post_id;
	}

	/**
	 * Indique si la cliente a modifié un modèle depuis son installation.
	 *
	 * @param int   $post_id Modèle.
	 * @param array $info    Suivi.
	 * @return bool
	 */
	private static function modele_modifie( $post_id, $info ) {
		return isset( $info['empreinte'] ) && self::empreinte_modele( $post_id ) !== $info['empreinte'];
	}

	/**
	 * Empreinte du contenu enregistré d'un modèle.
	 *
	 * @param int $post_id Modèle.
	 * @return string
	 */
	private static function empreinte_modele( $post_id ) {
		$donnees = json_decode( (string) get_post_meta( $post_id, '_elementor_data', true ), true );

		return Rolling_Donnees::empreinte( self::sans_ids( is_array( $donnees ) ? $donnees : array() ) );
	}

	/**
	 * Retire les ids d'éléments (Elementor les régénère) pour comparer deux contenus.
	 *
	 * @param array $elements Éléments.
	 * @return array
	 */
	private static function sans_ids( $elements ) {
		foreach ( $elements as $i => $element ) {
			if ( ! is_array( $element ) ) {
				continue;
			}

			unset( $elements[ $i ]['id'] );

			if ( ! empty( $element['elements'] ) ) {
				$elements[ $i ]['elements'] = self::sans_ids( $element['elements'] );
			}
		}

		return $elements;
	}

	/**
	 * Premier titre libre : « Titre V2 », « Titre V3 »…
	 *
	 * @param string $titre Titre de départ.
	 * @return string
	 */
	private static function titre_libre( $titre ) {
		for ( $n = 2; $n < 100; $n++ ) {
			$candidat = $titre . ' V' . $n;
			$existe   = get_posts(
				array(
					'post_type'      => 'elementor_library',
					'title'          => $candidat,
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);

			if ( empty( $existe ) ) {
				return $candidat;
			}
		}

		return $titre . ' V' . wp_rand( 100, 999 );
	}

	/**
	 * Remplace les images de départ (URL fictive) par les pièces jointes de la médiathèque.
	 *
	 * @param mixed              $valeur Données Elementor.
	 * @param array<string, int> $medias Médias installés.
	 * @return mixed
	 */
	private static function remplacer_medias( $valeur, $medias ) {
		if ( ! is_array( $valeur ) ) {
			return $valeur;
		}

		// Source d'image Elementor qui pointe vers une URL fictive du thème : on la relie à la pièce jointe.
		if ( isset( $valeur['$$type'], $valeur['value']['url']['value'] ) && 'image-src' === $valeur['$$type'] ) {
			$cle = self::cle_media( $valeur['value']['url']['value'] );

			if ( $cle && ! empty( $medias[ $cle ] ) ) {
				return array(
					'$$type' => 'image-src',
					'value'  => array(
						'id'  => array(
							'$$type' => 'image-attachment-id',
							'value'  => (int) $medias[ $cle ],
						),
						'url' => null,
					),
				);
			}
		}

		// Source de vidéo, de SVG ou d'arrière-plan : même principe.
		if ( isset( $valeur['$$type'], $valeur['value']['url']['value'] ) && in_array( $valeur['$$type'], array( 'svg-src', 'video-src' ), true ) ) {
			$cle = self::cle_media( $valeur['value']['url']['value'] );

			if ( $cle && ! empty( $medias[ $cle ] ) ) {
				return array(
					'$$type' => $valeur['$$type'],
					'value'  => array(
						'id'  => array(
							'$$type' => 'image-attachment-id',
							'value'  => (int) $medias[ $cle ],
						),
						'url' => null,
					),
				);
			}
		}

		foreach ( $valeur as $cle => $element ) {
			$valeur[ $cle ] = self::remplacer_medias( $element, $medias );
		}

		return $valeur;
	}

	/**
	 * Clé d'un média à partir de son URL fictive.
	 *
	 * @param mixed $url URL.
	 * @return string
	 */
	private static function cle_media( $url ) {
		if ( ! is_string( $url ) || 0 !== strpos( $url, self::URL_MEDIA ) ) {
			return '';
		}

		return pathinfo( substr( $url, strlen( self::URL_MEDIA ) ), PATHINFO_FILENAME );
	}

	// Première installation : pages, menus, réglages.

	/**
	 * Crée les pages manquantes, les menus, et pose les réglages de départ (une seule fois).
	 *
	 * @param array<string, int> $medias Médias installés.
	 */
	private static function premiere_installation( $medias ) {
		$config = Rolling_Donnees::lire( 'installation' );
		$pages  = array();

		foreach ( $config['pages'] ?? array() as $page ) {
			$pages[ $page['cle'] ] = self::page( $page );
		}

		$confidentialite = (int) get_option( 'wp_page_for_privacy_policy' );

		if ( $confidentialite && get_post( $confidentialite ) ) {
			$pages['confidentialite'] = $confidentialite;
		}

		$emplacements = get_theme_mod( 'nav_menu_locations', array() );

		foreach ( $config['menus'] ?? array() as $menu ) {
			if ( ! empty( $emplacements[ $menu['emplacement'] ] ) ) {
				continue;
			}

			$menu_id = self::menu( $menu, $pages );

			if ( $menu_id ) {
				$emplacements[ $menu['emplacement'] ] = $menu_id;
			}
		}

		set_theme_mod( 'nav_menu_locations', $emplacements );

		// Page d'accueil et page des articles, seulement si rien n'est réglé.
		if ( 'posts' === get_option( 'show_on_front' ) && ! get_option( 'page_on_front' ) && ! empty( $pages['accueil'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $pages['accueil'] );

			if ( ! empty( $pages['blog'] ) ) {
				update_option( 'page_for_posts', $pages['blog'] );
			}
		}

		// Réglages du Personnalisateur (textes du pied de page, bouton de l'en-tête).
		foreach ( $config['reglages'] ?? array() as $cle => $valeur ) {
			if ( false === get_theme_mod( $cle, false ) ) {
				set_theme_mod( $cle, $valeur );
			}
		}

		if ( ! get_theme_mod( 'rolling_cta_page' ) && ! empty( $pages['contact'] ) ) {
			set_theme_mod( 'rolling_cta_page', $pages['contact'] );
		}

		if ( ! get_theme_mod( 'custom_logo' ) && ! empty( $medias['logo-rolling'] ) ) {
			set_theme_mod( 'custom_logo', $medias['logo-rolling'] );
		}

		if ( ! get_theme_mod( 'rolling_logo_negatif' ) && ! empty( $medias['logo-rolling-negatif'] ) ) {
			set_theme_mod( 'rolling_logo_negatif', $medias['logo-rolling-negatif'] );
		}

		// Polices hébergées par le thème : Elementor ne charge plus Google Fonts.
		update_option( 'elementor_google_font', '0' );

		// Points de rupture de la charte : tablette à 980 px.
		$kit = Plugin::$instance->kits_manager->get_active_kit();

		if ( $kit && ! empty( $config['kit'] ) ) {
			$kit->update_settings( $config['kit'] );
		}
	}

	/**
	 * Ce qui arrive avec une nouvelle version, créé une seule fois (jamais recréé si la cliente l'a supprimé) :
	 * pages, termes des filtres, menus d'emplacements nouveaux, réglages, logos clients de départ.
	 *
	 * @param bool               $site_existant Site déjà installé avant cette version.
	 * @param array<string, int> $medias        Médias installés.
	 */
	private static function nouveautes( $site_existant, $medias ) {
		$config = Rolling_Donnees::lire( 'installation' );
		$faits  = get_option( 'rolling_deja_crees', null );
		$pages  = array();

		// Site déjà installé avant ce suivi : ce qui existait avant (sans « depuis ») est réputé fait, pour ne pas
		// recréer une page ou un menu que la cliente aurait supprimé.
		if ( null === $faits ) {
			$faits = array();
			if ( $site_existant ) {
				foreach ( $config['pages'] ?? array() as $page ) {
					if ( empty( $page['depuis'] ) ) {
						$faits[] = 'page:' . $page['cle'];
					}
				}
				foreach ( $config['menus'] ?? array() as $menu ) {
					if ( empty( $menu['depuis'] ) ) {
						$faits[] = 'menu:' . $menu['emplacement'];
					}
				}
			}
		}
		$faits = (array) $faits;

		foreach ( $config['pages'] ?? array() as $page ) {
			$existante = get_page_by_path( $page['slug'] );
			if ( $existante ) {
				$pages[ $page['cle'] ] = (int) $existante->ID;
			}
			if ( in_array( 'page:' . $page['cle'], $faits, true ) ) {
				continue;
			}
			$pages[ $page['cle'] ] = self::page( $page );
			$faits[]               = 'page:' . $page['cle'];
		}

		foreach ( Rolling_Contenus::taxonomies() as $taxonomie => $t ) {
			if ( in_array( 'termes:' . $taxonomie, $faits, true ) ) {
				continue;
			}
			foreach ( $t['termes'] as $slug => $terme ) {
				if ( ! term_exists( $slug, $taxonomie ) ) {
					wp_insert_term( $terme, $taxonomie, array( 'slug' => $slug ) );
				}
			}
			$faits[] = 'termes:' . $taxonomie;
		}

		$emplacements = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( $config['menus'] ?? array() as $menu ) {
			if ( in_array( 'menu:' . $menu['emplacement'], $faits, true ) ) {
				continue;
			}
			$faits[] = 'menu:' . $menu['emplacement'];
			if ( ! empty( $emplacements[ $menu['emplacement'] ] ) ) {
				continue;
			}
			$menu_id = self::menu( $menu, $pages );
			if ( $menu_id ) {
				$emplacements[ $menu['emplacement'] ] = $menu_id;
			}
		}
		set_theme_mod( 'nav_menu_locations', $emplacements );

		// Nouveaux réglages du Personnalisateur : valeur de départ posée une fois, si rien n'est réglé.
		foreach ( $config['reglages'] ?? array() as $cle => $valeur ) {
			if ( in_array( 'reglage:' . $cle, $faits, true ) ) {
				continue;
			}
			if ( false === get_theme_mod( $cle, false ) ) {
				set_theme_mod( $cle, $valeur );
			}
			$faits[] = 'reglage:' . $cle;
		}

		// Logos clients de départ (ceux de la maquette), si la liste est vide.
		if ( ! in_array( 'logos:depart', $faits, true ) ) {
			$existants = get_posts(
				array(
					'post_type'      => 'rolling_logo',
					'posts_per_page' => 1,
					'post_status'    => 'any',
					'fields'         => 'ids',
				)
			);
			if ( ! $existants ) {
				foreach ( $config['logos'] ?? array() as $rang => $logo ) {
					if ( empty( $medias[ $logo['media'] ] ) ) {
						continue;
					}
					$id = wp_insert_post(
						array(
							'post_type'   => 'rolling_logo',
							'post_status' => 'publish',
							'post_title'  => $logo['nom'],
							'menu_order'  => $rang,
						)
					);
					if ( $id && ! is_wp_error( $id ) ) {
						set_post_thumbnail( $id, $medias[ $logo['media'] ] );
						update_post_meta( $id, Rolling_Contenus::PREFIXE . 'hauteur', (string) $logo['hauteur'] );
					}
				}
			}
			$faits[] = 'logos:depart';
		}

		update_option( 'rolling_deja_crees', array_values( array_unique( $faits ) ), false );
	}

	/**
	 * Retourne la page demandée, créée vide si elle n'existe pas.
	 *
	 * @param array $page Configuration.
	 * @return int
	 */
	private static function page( $page ) {
		$existante = get_page_by_path( $page['slug'] );

		if ( $existante ) {
			return (int) $existante->ID;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page['titre'],
				'post_name'    => $page['slug'],
				'post_content' => '',
			)
		);

		if ( is_wp_error( $id ) ) {
			return 0;
		}

		if ( ! empty( $page['modele'] ) ) {
			update_post_meta( $id, '_wp_page_template', $page['modele'] );
		}

		return (int) $id;
	}

	/**
	 * Crée un menu à partir des pages.
	 *
	 * @param array $menu  Configuration.
	 * @param array $pages Pages par clé.
	 * @return int
	 */
	private static function menu( $menu, $pages ) {
		$existant = wp_get_nav_menu_object( $menu['nom'] );
		$menu_id  = $existant ? (int) $existant->term_id : wp_create_nav_menu( $menu['nom'] );

		if ( is_wp_error( $menu_id ) ) {
			return 0;
		}

		if ( $existant ) {
			return $menu_id;
		}

		self::elements_de_menu( $menu_id, $menu['pages'], $pages, 0 );

		return $menu_id;
	}

	/**
	 * Ajoute des éléments de menu : clé de page, ou { titre, lien | page, enfants }.
	 *
	 * @param int   $menu_id  Menu.
	 * @param array $elements Éléments.
	 * @param array $pages    Pages par clé.
	 * @param int   $parent   Élément parent.
	 */
	private static function elements_de_menu( $menu_id, $elements, $pages, $parent ) {
		static $position = 0;

		foreach ( $elements as $element ) {
			$element = is_array( $element ) ? $element : array( 'page' => $element );
			$args    = array(
				'menu-item-status'    => 'publish',
				'menu-item-position'  => ++$position,
				'menu-item-parent-id' => $parent,
			);

			if ( ! empty( $element['page'] ) ) {
				if ( empty( $pages[ $element['page'] ] ) ) {
					continue;
				}
				$args += array(
					'menu-item-object-id' => $pages[ $element['page'] ],
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
				);
			} else {
				$lien  = $element['lien'] ?? '#';
				$args += array(
					'menu-item-type' => 'custom',
					'menu-item-url'  => 0 === strpos( $lien, '/' ) ? home_url( $lien ) : $lien,
				);
			}

			if ( ! empty( $element['titre'] ) ) {
				$args['menu-item-title'] = $element['titre'];
			}

			$id = wp_update_nav_menu_item( $menu_id, 0, $args );

			if ( ! is_wp_error( $id ) && ! empty( $element['enfants'] ) ) {
				self::elements_de_menu( $menu_id, $element['enfants'], $pages, $id );
			}
		}
	}

	// Caches et rapport.

	/**
	 * Vide le cache d'Elementor et celui de WP Super Cache.
	 */
	public static function vider_caches() {
		if ( isset( Plugin::$instance->files_manager ) ) {
			Plugin::$instance->files_manager->clear_cache();
		}

		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}
	}

	/**
	 * Garde le rapport pour l'afficher à l'écran suivant.
	 */
	private static function enregistrer_rapport() {
		set_transient( self::TRANSIENT_RAPPORT, self::$rapport, HOUR_IN_SECONDS );
	}

	/**
	 * Affiche le rapport d'installation une fois.
	 */
	public static function afficher_rapport() {
		$rapport = get_transient( self::TRANSIENT_RAPPORT );

		if ( empty( $rapport ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		delete_transient( self::TRANSIENT_RAPPORT );

		echo '<div class="notice notice-success is-dismissible"><p>' . wp_kses_post( implode( '</p><p>', array_map( 'esc_html', $rapport ) ) ) . '</p></div>';
	}
}
