<?php
/**
 * Contenus gérés dans l'administration : réalisations, témoignages, logos clients, guides et webinars.
 *
 * Les champs sont ceux du thème (pas d'extension nécessaire). Une seule description par champ sert à la fois
 * au formulaire d'administration, à l'enregistrement et aux balises dynamiques d'Elementor.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Types de contenus et champs.
 */
class Rolling_Contenus {

	const PREFIXE = '_rolling_';

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'enregistrer' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'boites' ) );
		add_action( 'save_post', array( __CLASS__, 'sauver' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts_admin' ) );
		add_filter( 'enter_title_here', array( __CLASS__, 'aide_titre' ), 10, 2 );
		add_action( 'template_redirect', array( __CLASS__, 'temoignage_sans_recit' ) );
		add_filter( 'wp_sitemaps_posts_query_args', array( __CLASS__, 'sitemap_sans_citations_seules' ), 10, 2 );
		foreach ( array_keys( self::types() ) as $type ) {
			add_filter( 'manage_' . $type . '_posts_columns', array( __CLASS__, 'colonnes' ) );
			add_action( 'manage_' . $type . '_posts_custom_column', array( __CLASS__, 'colonne' ), 10, 2 );
		}
	}

	/**
	 * Témoignage sans récit (citation seule) : pas de page, redirection vers sa réalisation, sinon la page Témoignages.
	 */
	public static function temoignage_sans_recit() {
		if ( ! is_singular( 'rolling_temoignage' ) || self::a_recit( (int) get_queried_object_id() ) ) {
			return;
		}

		$videos = self::realisations_liees( (int) get_queried_object_id() );
		$page   = get_page_by_path( 'temoignages' );
		$cible  = $videos ? get_permalink( (int) $videos[0] ) : ( $page ? get_permalink( $page ) : home_url( '/' ) );

		wp_safe_redirect( $cible, 301, 'Rolling' );
		exit;
	}

	/**
	 * Témoignages sans récit (citation seule : pas de page, redirigés vers la réalisation).
	 *
	 * @return int[]
	 */
	public static function citations_seules() {
		return array_values(
			array_filter(
				get_posts(
					array(
						'post_type'      => 'rolling_temoignage',
						'posts_per_page' => 500,
						'fields'         => 'ids',
						'no_found_rows'  => true,
					)
				),
				function ( $id ) {
					return ! self::a_recit( $id );
				}
			)
		);
	}

	/**
	 * Sitemap : seulement les témoignages qui ont une page (récit rempli).
	 *
	 * @param array  $args Requête.
	 * @param string $type Type de contenu.
	 * @return array
	 */
	public static function sitemap_sans_citations_seules( $args, $type ) {
		if ( 'rolling_temoignage' !== $type ) {
			return $args;
		}

		$seules = self::citations_seules();
		if ( $seules ) {
			$args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), array_values( $seules ) ); // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
		}

		return $args;
	}

	/**
	 * Types de contenus.
	 *
	 * @return array
	 */
	public static function types() {
		return array(
			'rolling_realisation' => array(
				'pluriel'   => __( 'Réalisations', 'rolling' ),
				'singulier' => __( 'Réalisation', 'rolling' ),
				'feminin'   => true,
				'slug'      => 'realisations',
				'public'    => true,
				'icone'     => 'dashicons-video-alt3',
				'position'  => 21,
				'supports'  => array( 'title', 'thumbnail', 'revisions' ),
				'titre'     => __( 'Titre de la réalisation (ex. : 13 jours de formation, une seule vidéo.)', 'rolling' ),
			),
			'rolling_temoignage'  => array(
				'pluriel'   => __( 'Témoignages', 'rolling' ),
				'singulier' => __( 'Témoignage', 'rolling' ),
				'feminin'   => false,
				'slug'      => 'temoignages',
				'public'    => true,
				'icone'     => 'dashicons-format-quote',
				'position'  => 22,
				'supports'  => array( 'title', 'thumbnail', 'revisions' ),
				'titre'     => __( 'Titre du cas (ex. : l’expertise vidéo qui manquait à l’équipe SIRH.)', 'rolling' ),
			),
			'rolling_logo'        => array(
				'pluriel'   => __( 'Logos clients', 'rolling' ),
				'singulier' => __( 'Logo client', 'rolling' ),
				'feminin'   => false,
				'slug'      => '',
				'public'    => false,
				'icone'     => 'dashicons-awards',
				'position'  => 23,
				'supports'  => array( 'title', 'thumbnail', 'page-attributes' ),
				'titre'     => __( 'Nom du client (lu par Google et les lecteurs d’écran)', 'rolling' ),
			),
			'rolling_guide'       => array(
				'pluriel'   => __( 'Guides', 'rolling' ),
				'singulier' => __( 'Guide', 'rolling' ),
				'feminin'   => false,
				'slug'      => '',
				'public'    => false,
				'icone'     => 'dashicons-media-document',
				'position'  => 24,
				'supports'  => array( 'title', 'thumbnail' ),
				'titre'     => __( 'Titre du guide', 'rolling' ),
			),
			'rolling_webinar'     => array(
				'pluriel'   => __( 'Webinars', 'rolling' ),
				'singulier' => __( 'Webinar', 'rolling' ),
				'feminin'   => false,
				'slug'      => '',
				'public'    => false,
				'icone'     => 'dashicons-microphone',
				'position'  => 25,
				'supports'  => array( 'title', 'thumbnail' ),
				'titre'     => __( 'Titre du webinar', 'rolling' ),
			),
		);
	}

	/**
	 * Taxonomies (filtres des listes).
	 *
	 * @return array
	 */
	public static function taxonomies() {
		return array(
			'rolling_metier' => array(
				'nom'    => __( 'Métiers', 'rolling' ),
				'un'     => __( 'Métier', 'rolling' ),
				'types'  => array( 'rolling_realisation', 'rolling_temoignage' ),
				'termes' => array(
					'com-interne-rh-formation' => 'Com interne · RH · Formation',
					'marketing-communication'  => 'Marketing & communication',
				),
			),
			'rolling_format' => array(
				'nom'    => __( 'Formats vidéo', 'rolling' ),
				'un'     => __( 'Format vidéo', 'rolling' ),
				'types'  => array( 'rolling_realisation' ),
				'termes' => array(
					'motion-design'      => 'Motion design',
					'tournage'           => 'Tournage',
					'interview'          => 'Interview',
					'demo-produit'       => 'Démo produit',
					'evenement'          => 'Événement',
					'format-9-16'        => 'Format 9:16',
					'avatar-et-voix-off' => 'Avatar et voix off',
				),
			),
			'rolling_theme'  => array(
				'nom'    => __( 'Thèmes', 'rolling' ),
				'un'     => __( 'Thème', 'rolling' ),
				'types'  => array( 'rolling_guide', 'rolling_webinar' ),
				'termes' => array(
					'communication-interne' => 'Communication interne',
					'rh-formation'          => 'RH & formation',
					'marketing-video'       => 'Marketing vidéo',
					'ia-video'              => 'IA & vidéo',
				),
			),
		);
	}

	/**
	 * Champs de chaque type, rangés par boîte.
	 *
	 * Types : texte, zone (plusieurs lignes), lien, nombre, date, heure, image, case, lignes (liste répétable), choix, relation.
	 *
	 * @return array
	 */
	public static function champs() {
		$tonalite = array(
			'orange' => __( 'Orange (com interne, RH, formation)', 'rolling' ),
			'bleu'   => __( 'Bleu (marketing, IA)', 'rolling' ),
		);

		return array(
			'rolling_realisation' => array(
				__( 'La carte et le haut de la fiche', 'rolling' ) => array(
					'client'        => array( 'texte', __( 'Client', 'rolling' ), __( 'Ex. : Fnac Darty. Affiché au-dessus du titre.', 'rolling' ) ),
					'titre_couleur' => array( 'texte', __( 'Fin du titre en couleur', 'rolling' ), __( 'Recopiez la fin du titre à mettre en dégradé. Ex. : une seule vidéo.', 'rolling' ) ),
					'chapo'         => array( 'zone', __( 'Chapô (facultatif)', 'rolling' ), __( 'Une ou deux phrases sous le titre de la fiche.', 'rolling' ) ),
					'avant'         => array( 'texte', __( 'Contenu de départ', 'rolling' ), __( 'Ex. : Programme du séminaire.', 'rolling' ) ),
					'apres'         => array( 'texte', __( 'Vidéo produite', 'rolling' ), __( 'Ex. : aftermovie de 2 min.', 'rolling' ) ),
					'tonalite'      => array( 'choix', __( 'Couleur de l’étiquette', 'rolling' ), '', $tonalite ),
					'duree'         => array( 'texte', __( 'Durée de la vidéo', 'rolling' ), __( 'Ex. : 2:04', 'rolling' ) ),
				),
				__( 'La vidéo', 'rolling' ) => array(
					'video'         => array( 'lien', __( 'Lien de la vidéo', 'rolling' ), __( 'Lien YouTube ou lien d’un fichier de la médiathèque. L’image à la une sert d’affiche ; sans elle, la miniature YouTube ou la première image du fichier.', 'rolling' ) ),
					'transcription' => array( 'zone', __( 'Transcription (facultatif)', 'rolling' ), __( 'Laissez vide : la ligne « Lire la transcription » n’apparaît pas.', 'rolling' ) ),
				),
				__( 'Le récit', 'rolling' ) => array(
					'besoin'   => array( 'zone', __( 'Le besoin', 'rolling' ), '' ),
					'produit'  => array( 'lignes', __( 'Ce que nous avons produit', 'rolling' ), __( 'Une ligne par point (liste à coches).', 'rolling' ), array( 'texte' => __( 'Point', 'rolling' ) ) ),
					'resultat' => array( 'zone', __( 'Le résultat', 'rolling' ), '' ),
				),
				__( 'Le témoignage du client (facultatif)', 'rolling' ) => array(
					'temoignage' => array( 'relation', __( 'Témoignage lié', 'rolling' ), __( 'Cochez le témoignage de ce client : sa citation s’affiche sur cette fiche, et sa carte mène à cette vidéo. Vous pouvez aussi faire le lien depuis le témoignage (« Réalisations liées »).', 'rolling' ), 'rolling_temoignage' ),
				),
				__( 'Le générique (facultatif)', 'rolling' ) => array(
					'generique' => array(
						'lignes',
						__( 'Lignes du générique', 'rolling' ),
						__( 'Ex. : Client — Fnac Darty ; Direction artistique — Thomas. Laissez vide : pas de générique.', 'rolling' ),
						array(
							'role'   => __( 'Intitulé', 'rolling' ),
							'valeur' => __( 'Valeur', 'rolling' ),
						),
					),
				),
			),
			'rolling_temoignage'  => array(
				__( 'La citation (toujours)', 'rolling' ) => array(
					'client'   => array( 'texte', __( 'Client', 'rolling' ), __( 'Ex. : Groupement Mousquetaires.', 'rolling' ) ),
					'logo'     => array( 'image', __( 'Logo du client', 'rolling' ), '' ),
					'citation' => array( 'zone', __( 'Citation, mot pour mot', 'rolling' ), __( 'Deux phrases, avec un résultat précis si possible.', 'rolling' ) ),
					'auteur'   => array( 'texte', __( 'Prénom Nom', 'rolling' ), '' ),
					'poste'    => array( 'texte', __( 'Poste', 'rolling' ), '' ),
					'portrait' => array( 'image', __( 'Portrait (carré)', 'rolling' ), '' ),
					'videos'   => array( 'relation', __( 'Réalisations liées', 'rolling' ), __( 'La carte « Voir la vidéo » mène à la première cochée, et la citation s’affiche sur sa fiche. Aucune : pas de lien vidéo.', 'rolling' ), 'rolling_realisation' ),
					'a_la_une' => array( 'case', __( 'À la une sur la page Témoignages', 'rolling' ), __( 'Un seul témoignage à la une : le plus récent coché.', 'rolling' ) ),
				),
				__( 'Le récit (facultatif) : rempli, il crée une page « cas client »', 'rolling' ) => array(
					'titre_couleur'  => array( 'texte', __( 'Fin du titre en couleur', 'rolling' ), __( 'Recopiez la fin du titre à mettre en dégradé.', 'rolling' ) ),
					'chapo'          => array( 'zone', __( 'Chapô', 'rolling' ), __( 'En deux phrases : qui ils sont, ce que nous avons fait ensemble.', 'rolling' ) ),
					'secteur'        => array( 'texte', __( 'Secteur', 'rolling' ), '' ),
					'equipes'        => array( 'texte', __( 'Équipes', 'rolling' ), '' ),
					'formats'        => array( 'texte', __( 'Formats', 'rolling' ), '' ),
					'duree'          => array( 'texte', __( 'Durée', 'rolling' ), __( 'Ex. : 3 mois. Un champ vide n’est pas affiché.', 'rolling' ) ),
					'chiffres'       => array(
						'lignes',
						__( 'Chiffres clés', 'rolling' ),
						__( 'Valeur, ex. « 87 % », « 2 500+ », « 4,2/5 », « 35:00 » ou « <3 mois » : les chiffres, +, %, k et M en verre, les autres signes et les mots courts dans le même dégradé. La phrase va dans le libellé. Aucun chiffre : la plaque n’apparaît pas.', 'rolling' ),
						array(
							'valeur'  => __( 'Valeur', 'rolling' ),
							'libelle' => __( 'Libellé', 'rolling' ),
						),
					),
					'contexte'       => array( 'zone', __( 'Le contexte', 'rolling' ), __( 'Contexte, besoin, accompagnement ou chiffres remplis : la page « cas client » existe et la carte mène à « Lire le cas client ».', 'rolling' ) ),
					'besoin'         => array( 'zone', __( 'Leur besoin', 'rolling' ), '' ),
					'accompagnement' => array( 'lignes', __( 'Notre accompagnement', 'rolling' ), __( 'Une ligne par étape (liste à coches).', 'rolling' ), array( 'texte' => __( 'Étape', 'rolling' ) ) ),
				),
			),
			'rolling_logo'        => array(
				__( 'Le logo', 'rolling' ) => array(
					'hauteur' => array( 'nombre', __( 'Hauteur d’affichage (px)', 'rolling' ), __( 'Pour équilibrer les logos entre eux. Par défaut : 32.', 'rolling' ) ),
				),
			),
			'rolling_guide'       => array(
				__( 'Le guide', 'rolling' ) => array(
					'resume'   => array( 'zone', __( 'Résumé', 'rolling' ), __( 'Une phrase sous le titre.', 'rolling' ) ),
					'type_doc' => array( 'texte', __( 'Type', 'rolling' ), __( 'Ex. : Guide PDF, Check-list, Modèle.', 'rolling' ) ),
					'pages'    => array( 'nombre', __( 'Nombre de pages', 'rolling' ), '' ),
					'lien'     => array( 'lien', __( 'Lien de la page HubSpot', 'rolling' ), __( 'Le bouton « Télécharger le guide » ouvre ce lien.', 'rolling' ) ),
				),
			),
			'rolling_webinar'     => array(
				__( 'Le webinar', 'rolling' )       => array(
					'resume' => array( 'zone', __( 'Résumé', 'rolling' ), __( 'Une ou deux phrases sous le titre.', 'rolling' ) ),
					'date'   => array( 'date', __( 'Date', 'rolling' ), '' ),
					'heure'  => array( 'heure', __( 'Heure de début (heure de Paris)', 'rolling' ), '' ),
					'duree'  => array( 'nombre', __( 'Durée (minutes)', 'rolling' ), __( 'Par défaut : 45. À la fin, le webinar quitte la page Webinars.', 'rolling' ) ),
				),
				__( 'Les intervenants', 'rolling' ) => array(
					'intervenants' => array(
						'lignes',
						__( 'Intervenants', 'rolling' ),
						'',
						array(
							'nom'   => __( 'Prénom', 'rolling' ),
							'poste' => __( 'Poste', 'rolling' ),
						),
					),
				),
				__( 'Les liens', 'rolling' )        => array(
					'inscription' => array( 'lien', __( 'Lien d’inscription (Livestorm ou HubSpot)', 'rolling' ), '' ),
					'replay'      => array( 'lien', __( 'Lien du replay (page HubSpot)', 'rolling' ), __( 'Une fois le webinar passé, il apparaît sur la page Replays dès que ce lien est rempli.', 'rolling' ) ),
				),
			),
		);
	}

	/**
	 * Description d'un champ.
	 *
	 * @param string $type Type de contenu.
	 * @param string $cle  Clé du champ.
	 * @return array|null
	 */
	public static function champ( $type, $cle ) {
		foreach ( self::champs()[ $type ] ?? array() as $boite ) {
			if ( isset( $boite[ $cle ] ) ) {
				return $boite[ $cle ];
			}
		}

		return null;
	}

	/**
	 * Le témoignage a-t-il un récit (page « cas client ») ? Contexte, besoin, accompagnement ou chiffres remplis.
	 *
	 * @param int $id Témoignage.
	 * @return bool
	 */
	public static function a_recit( $id ) {
		foreach ( array( 'contexte', 'besoin', 'accompagnement', 'chiffres' ) as $cle ) {
			$valeur = self::valeur( $cle, $id );
			if ( '' !== $valeur && array() !== $valeur && null !== $valeur ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Témoignage qui cite cette réalisation dans « Réalisations liées » (le plus récent), ou 0.
	 *
	 * @param int $realisation Réalisation.
	 * @return int
	 */
	public static function temoignage_lie( $realisation ) {
		$propre = array_values( array_filter( (array) self::valeur( 'temoignage', $realisation ), 'get_post' ) );
		if ( $propre ) {
			return (int) $propre[0];
		}

		static $liens = null;
		if ( null === $liens ) {
			$liens = array();
			foreach ( get_posts(
				array(
					'post_type'      => 'rolling_temoignage',
					'posts_per_page' => 200,
					'fields'         => 'ids',
					'no_found_rows'  => true,
				)
			) as $temoignage ) {
				foreach ( (array) self::valeur( 'videos', $temoignage ) as $video ) {
					if ( ! isset( $liens[ (int) $video ] ) ) {
						$liens[ (int) $video ] = (int) $temoignage;
					}
				}
			}
		}

		return $liens[ (int) $realisation ] ?? 0;
	}

	/**
	 * Réalisations liées à un témoignage : cochées dans le témoignage, puis celles qui le citent (« Témoignage lié »).
	 *
	 * @param int $temoignage Témoignage.
	 * @return int[]
	 */
	public static function realisations_liees( $temoignage ) {
		static $inverse = null;
		if ( null === $inverse ) {
			$inverse = array();
			foreach ( get_posts(
				array(
					'post_type'      => 'rolling_realisation',
					'posts_per_page' => 300,
					'fields'         => 'ids',
					'no_found_rows'  => true,
				)
			) as $realisation ) {
				foreach ( (array) self::valeur( 'temoignage', $realisation ) as $cible ) {
					$inverse[ (int) $cible ][] = (int) $realisation;
				}
			}
		}

		$ids = array_merge( array_map( 'intval', (array) self::valeur( 'videos', $temoignage ) ), $inverse[ (int) $temoignage ] ?? array() );

		return array_values( array_unique( array_filter( $ids, 'get_post' ) ) );
	}


	/**
	 * Valeur d'un champ (lignes : tableau de lignes).
	 *
	 * @param string   $cle     Clé du champ.
	 * @param int|null $post_id Contenu (par défaut, le contenu courant).
	 * @return mixed
	 */
	public static function valeur( $cle, $post_id = null ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		$valeur  = get_post_meta( $post_id, self::PREFIXE . $cle, true );
		$def     = self::champ( get_post_type( $post_id ), $cle );

		if ( $def && in_array( $def[0], array( 'lignes', 'relation' ), true ) ) {
			return is_array( $valeur ) ? $valeur : array();
		}

		return $valeur;
	}

	/**
	 * Enregistre types, taxonomies et champs.
	 */
	public static function enregistrer() {
		foreach ( self::types() as $type => $t ) {
			register_post_type(
				$type,
				array(
					'labels'              => self::libelles( $t ),
					'public'              => $t['public'],
					'publicly_queryable'  => $t['public'],
					'exclude_from_search' => ! $t['public'],
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_rest'        => true,
					'menu_icon'           => $t['icone'],
					'menu_position'       => $t['position'],
					'supports'            => $t['supports'],
					'has_archive'         => false,
					'rewrite'             => $t['slug'] ? array(
						'slug'       => $t['slug'],
						'with_front' => false,
					) : false,
					'hierarchical'        => false,
				)
			);
		}

		foreach ( self::taxonomies() as $tax => $t ) {
			register_taxonomy(
				$tax,
				$t['types'],
				array(
					'labels'            => array(
						'name'          => $t['nom'],
						'singular_name' => $t['un'],
						'menu_name'     => $t['nom'],
						'add_new_item'  => __( 'Ajouter', 'rolling' ),
					),
					'public'            => false,
					'show_ui'           => true,
					'show_in_rest'      => true,
					'show_admin_column' => true,
					'hierarchical'      => true,
					'rewrite'           => false,
				)
			);
		}

		foreach ( self::champs() as $type => $boites ) {
			foreach ( $boites as $champs ) {
				foreach ( $champs as $cle => $def ) {
					$liste = in_array( $def[0], array( 'lignes', 'relation' ), true );
					register_post_meta(
						$type,
						self::PREFIXE . $cle,
						array(
							'single'            => true,
							'type'              => $liste ? 'array' : 'string',
							'show_in_rest'      => $liste ? array(
								'schema' => array(
									'type'  => 'array',
									'items' => array( 'type' => array( 'object', 'integer' ) ),
								),
							) : true,
							'auth_callback'     => function () {
								return current_user_can( 'edit_posts' );
							},
							'sanitize_callback' => function ( $valeur ) use ( $def ) {
								return self::nettoyer( $def, $valeur );
							},
						)
					);
				}
			}
		}
	}

	/**
	 * Libellés d'un type de contenu.
	 *
	 * @param array $t Type.
	 * @return array
	 */
	private static function libelles( $t ) {
		return array(
			'name'               => $t['pluriel'],
			'singular_name'      => $t['singulier'],
			'menu_name'          => $t['pluriel'],
			'all_items'          => $t['pluriel'],
			/* translators: %s : nom du contenu au singulier. */
			'add_new_item'       => sprintf( $t['feminin'] ? __( 'Ajouter une %s', 'rolling' ) : __( 'Ajouter un %s', 'rolling' ), mb_strtolower( $t['singulier'] ) ),
			'add_new'            => __( 'Ajouter', 'rolling' ),
			/* translators: %s : nom du contenu au singulier. */
			'edit_item'          => sprintf( __( 'Modifier : %s', 'rolling' ), mb_strtolower( $t['singulier'] ) ),
			'search_items'       => __( 'Rechercher', 'rolling' ),
			'not_found'          => __( 'Aucun contenu pour le moment.', 'rolling' ),
			'not_found_in_trash' => __( 'Rien dans la corbeille.', 'rolling' ),
			'featured_image'     => __( 'Image (vignette et affiche)', 'rolling' ),
			'set_featured_image' => __( 'Choisir l’image', 'rolling' ),
		);
	}

	/**
	 * Nettoie une valeur selon son type.
	 *
	 * @param array $def    Champ.
	 * @param mixed $valeur Valeur reçue.
	 * @return mixed
	 */
	public static function nettoyer( $def, $valeur ) {
		switch ( $def[0] ) {
			case 'zone':
				return sanitize_textarea_field( (string) $valeur );
			case 'lien':
				return esc_url_raw( (string) $valeur );
			case 'nombre':
				return '' === $valeur || null === $valeur ? '' : (string) absint( $valeur );
			case 'image':
				return $valeur ? (string) absint( $valeur ) : '';
			case 'case':
				return $valeur ? '1' : '';
			case 'date':
				return preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $valeur ) ? (string) $valeur : '';
			case 'heure':
				return preg_match( '/^\d{2}:\d{2}$/', (string) $valeur ) ? (string) $valeur : '';
			case 'choix':
				return isset( $def[3][ $valeur ] ) ? (string) $valeur : '';
			case 'relation':
				return array_values( array_filter( array_map( 'absint', (array) $valeur ) ) );
			case 'lignes':
				$lignes = array();
				foreach ( (array) $valeur as $ligne ) {
					$propre = array();
					foreach ( array_keys( $def[3] ) as $colonne ) {
						$propre[ $colonne ] = sanitize_text_field( $ligne[ $colonne ] ?? '' );
					}
					if ( implode( '', $propre ) !== '' ) {
						$lignes[] = $propre;
					}
				}
				return $lignes;
			default:
				return sanitize_text_field( (string) $valeur );
		}
	}

	/**
	 * Boîtes de champs dans l'écran de modification.
	 */
	public static function boites() {
		foreach ( self::champs() as $type => $boites ) {
			$rang = 0;
			foreach ( $boites as $titre => $champs ) {
				add_meta_box(
					'rolling-' . $type . '-' . ( ++$rang ),
					$titre,
					array( __CLASS__, 'afficher_boite' ),
					$type,
					'normal',
					'high',
					array( 'champs' => $champs )
				);
			}
		}
	}

	/**
	 * Affiche une boîte de champs.
	 *
	 * @param WP_Post $post  Contenu.
	 * @param array   $boite Boîte (args.champs).
	 */
	public static function afficher_boite( $post, $boite ) {
		wp_nonce_field( 'rolling_champs', 'rolling_champs_nonce' );
		echo '<div class="rolling-champs">';
		foreach ( $boite['args']['champs'] as $cle => $def ) {
			self::afficher_champ( $post, $cle, $def );
		}
		echo '</div>';
	}

	/**
	 * Affiche un champ.
	 *
	 * @param WP_Post $post Contenu.
	 * @param string  $cle  Clé.
	 * @param array   $def  Champ.
	 */
	private static function afficher_champ( $post, $cle, $def ) {
		$nom                           = 'rolling[' . $cle . ']';
		$id                            = 'rolling-' . $cle;
		$valeur                        = self::valeur( $cle, $post->ID );
		list( $type, $libelle, $aide ) = $def;

		echo '<div class="rolling-champ rolling-champ-' . esc_attr( $type ) . '">';
		if ( 'case' !== $type ) {
			echo '<label class="rolling-libelle" for="' . esc_attr( $id ) . '">' . esc_html( $libelle ) . '</label>';
		}

		switch ( $type ) {
			case 'zone':
				echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $nom ) . '" rows="4" class="large-text">' . esc_textarea( $valeur ) . '</textarea>';
				break;
			case 'lien':
				echo '<input type="url" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nom ) . '" value="' . esc_attr( $valeur ) . '" class="large-text" placeholder="https://">';
				break;
			case 'nombre':
				echo '<input type="number" min="0" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nom ) . '" value="' . esc_attr( $valeur ) . '" class="small-text">';
				break;
			case 'date':
			case 'heure':
				echo '<input type="' . ( 'date' === $type ? 'date' : 'time' ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nom ) . '" value="' . esc_attr( $valeur ) . '">';
				break;
			case 'case':
				echo '<label for="' . esc_attr( $id ) . '"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nom ) . '" value="1"' . checked( $valeur, '1', false ) . '> ' . esc_html( $libelle ) . '</label>';
				break;
			case 'choix':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $nom ) . '">';
				foreach ( $def[3] as $option => $texte ) {
					echo '<option value="' . esc_attr( $option ) . '"' . selected( $valeur, $option, false ) . '>' . esc_html( $texte ) . '</option>';
				}
				echo '</select>';
				break;
			case 'image':
				$apercu = $valeur ? wp_get_attachment_image( (int) $valeur, 'thumbnail' ) : '';
				echo '<div class="rolling-image" data-rolling-image>';
				echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nom ) . '" value="' . esc_attr( $valeur ) . '">';
				echo '<span class="rolling-apercu">' . wp_kses_post( $apercu ) . '</span>';
				echo '<button type="button" class="button" data-choisir>' . esc_html__( 'Choisir une image', 'rolling' ) . '</button> ';
				echo '<button type="button" class="button-link" data-retirer' . ( $valeur ? '' : ' hidden' ) . '>' . esc_html__( 'Retirer', 'rolling' ) . '</button>';
				echo '</div>';
				break;
			case 'relation':
				$choix = get_posts(
					array(
						'post_type'      => $def[3],
						'posts_per_page' => 200,
						'orderby'        => 'title',
						'order'          => 'ASC',
						'post_status'    => array( 'publish', 'draft' ),
					)
				);
				echo '<div class="rolling-relation" id="' . esc_attr( $id ) . '">';
				if ( ! $choix ) {
					echo '<p class="description">' . esc_html__( 'Aucune réalisation pour le moment.', 'rolling' ) . '</p>';
				}
				foreach ( $choix as $c ) {
					echo '<label><input type="checkbox" name="' . esc_attr( $nom ) . '[]" value="' . esc_attr( $c->ID ) . '"' . checked( in_array( $c->ID, $valeur, true ), true, false ) . '> ' . esc_html( get_the_title( $c ) ) . '</label>';
				}
				echo '</div>';
				break;
			case 'lignes':
				$colonnes = $def[3];
				$lignes   = $valeur ? $valeur : array( array() );
				echo '<div class="rolling-lignes" data-rolling-lignes data-nom="' . esc_attr( $nom ) . '">';
				echo '<ol class="rolling-liste">';
				foreach ( $lignes as $i => $ligne ) {
					echo '<li class="rolling-ligne">';
					foreach ( $colonnes as $colonne => $titre ) {
						echo '<input type="text" name="' . esc_attr( $nom . '[' . $i . '][' . $colonne . ']' ) . '" value="' . esc_attr( $ligne[ $colonne ] ?? '' ) . '" placeholder="' . esc_attr( $titre ) . '" aria-label="' . esc_attr( $titre ) . '" data-colonne="' . esc_attr( $colonne ) . '">';
					}
					echo '<button type="button" class="button-link rolling-monter" data-monter aria-label="' . esc_attr__( 'Monter', 'rolling' ) . '">↑</button>';
					echo '<button type="button" class="button-link rolling-retirer" data-retirer-ligne aria-label="' . esc_attr__( 'Retirer cette ligne', 'rolling' ) . '">×</button>';
					echo '</li>';
				}
				echo '</ol>';
				echo '<button type="button" class="button" data-ajouter>' . esc_html__( 'Ajouter une ligne', 'rolling' ) . '</button>';
				echo '</div>';
				break;
			default:
				echo '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nom ) . '" value="' . esc_attr( $valeur ) . '" class="large-text">';
		}

		if ( $aide ) {
			echo '<p class="description">' . esc_html( $aide ) . '</p>';
		}
		echo '</div>';
	}

	/**
	 * Enregistre les champs.
	 *
	 * @param int     $post_id Contenu.
	 * @param WP_Post $post    Contenu.
	 */
	public static function sauver( $post_id, $post ) {
		if ( ! isset( self::champs()[ $post->post_type ] ) || wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}
		if ( ! isset( $_POST['rolling_champs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rolling_champs_nonce'] ) ), 'rolling_champs' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Chaque valeur est nettoyée champ par champ par self::nettoyer().
		$recu = isset( $_POST['rolling'] ) && is_array( $_POST['rolling'] ) ? wp_unslash( $_POST['rolling'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( self::champs()[ $post->post_type ] as $champs ) {
			foreach ( $champs as $cle => $def ) {
				$valeur = self::nettoyer( $def, $recu[ $cle ] ?? '' );
				if ( '' === $valeur || array() === $valeur ) {
					delete_post_meta( $post_id, self::PREFIXE . $cle );
				} else {
					update_post_meta( $post_id, self::PREFIXE . $cle, $valeur );
				}
			}
		}
	}

	/**
	 * Script et style des formulaires (choix d'image, lignes répétables).
	 *
	 * @param string $ecran Écran courant.
	 */
	public static function scripts_admin( $ecran ) {
		if ( ! in_array( $ecran, array( 'post.php', 'post-new.php' ), true ) || ! isset( self::champs()[ get_post_type() ] ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'rolling-admin', ROLLING_URL . '/assets/js/rolling-admin.js', array(), ROLLING_VERSION, true );
		wp_enqueue_style( 'rolling-admin', ROLLING_URL . '/assets/css/rolling-admin.css', array(), ROLLING_VERSION );
	}

	/**
	 * Texte d'aide dans le champ titre.
	 *
	 * @param string  $texte Texte.
	 * @param WP_Post $post  Contenu.
	 * @return string
	 */
	public static function aide_titre( $texte, $post ) {
		$types = self::types();

		return isset( $types[ $post->post_type ] ) ? $types[ $post->post_type ]['titre'] : $texte;
	}

	/**
	 * Colonnes utiles dans les listes de l'administration.
	 *
	 * @param array $colonnes Colonnes.
	 * @return array
	 */
	public static function colonnes( $colonnes ) {
		$type  = get_current_screen() ? get_current_screen()->post_type : '';
		$ajout = array(
			'rolling_webinar'     => array( 'rolling_quand' => __( 'Date', 'rolling' ) ),
			'rolling_temoignage'  => array( 'rolling_client' => __( 'Client', 'rolling' ) ),
			'rolling_realisation' => array( 'rolling_client' => __( 'Client', 'rolling' ) ),
		);
		if ( isset( $ajout[ $type ] ) ) {
			$colonnes = array_slice( $colonnes, 0, 2, true ) + $ajout[ $type ] + array_slice( $colonnes, 2, null, true );
		}

		return $colonnes;
	}

	/**
	 * Contenu d'une colonne.
	 *
	 * @param string $colonne Colonne.
	 * @param int    $post_id Contenu.
	 */
	public static function colonne( $colonne, $post_id ) {
		if ( 'rolling_client' === $colonne ) {
			echo esc_html( self::valeur( 'client', $post_id ) );
		}
		if ( 'rolling_quand' === $colonne ) {
			$debut = Rolling_Webinars::debut( $post_id );
			echo $debut ? esc_html( wp_date( 'j F Y · H\hi', $debut ) ) . '<br><em>' . esc_html( Rolling_Webinars::etat_libelle( $post_id ) ) . '</em>' : '—';
		}
	}
}
