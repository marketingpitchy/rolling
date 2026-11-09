<?php
/**
 * Lecture des données embarquées par le thème (dossier data/).
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Accès aux fichiers JSON du dossier data/.
 */
class Rolling_Donnees {

	/**
	 * Cache des fichiers déjà lus.
	 *
	 * @var array<string, mixed>
	 */
	private static $cache = array();

	/**
	 * Lit un fichier JSON du dossier data/.
	 *
	 * @param string $chemin Chemin relatif à data/, sans extension.
	 * @return array
	 */
	public static function lire( $chemin ) {
		if ( isset( self::$cache[ $chemin ] ) ) {
			return self::$cache[ $chemin ];
		}

		$fichier = ROLLING_DIR . '/data/' . $chemin . '.json';
		$donnees = array();

		if ( is_readable( $fichier ) ) {
			// Lecture d'un fichier local du thème.
			$contenu = file_get_contents( $fichier ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$donnees = json_decode( (string) $contenu, true );
			$donnees = is_array( $donnees ) ? $donnees : array();
		}

		self::$cache[ $chemin ] = $donnees;

		return $donnees;
	}

	/**
	 * Liste les fichiers JSON d'un sous-dossier de data/.
	 *
	 * @param string $dossier Sous-dossier (ex. « blocs »).
	 * @return array<string, array> Données indexées par nom de fichier.
	 */
	public static function lire_dossier( $dossier ) {
		$resultat = array();
		$fichiers = glob( ROLLING_DIR . '/data/' . $dossier . '/*.json' );

		foreach ( (array) $fichiers as $fichier ) {
			$nom              = basename( $fichier, '.json' );
			$resultat[ $nom ] = self::lire( $dossier . '/' . $nom );
		}

		ksort( $resultat );

		return $resultat;
	}

	/**
	 * Empreinte stable d'une donnée (pour savoir si la cliente l'a modifiée).
	 *
	 * @param mixed $valeur Donnée à résumer.
	 * @return string
	 */
	public static function empreinte( $valeur ) {
		return md5( (string) wp_json_encode( self::trier( self::normaliser( $valeur ) ) ) );
	}

	/**
	 * Rend deux écritures équivalentes identiques (l'éditeur réécrit null en « », 1.06 en « 1.06 »…).
	 *
	 * @param mixed $valeur Donnée.
	 * @return mixed
	 */
	private static function normaliser( $valeur ) {
		if ( is_array( $valeur ) ) {
			$resultat = array();

			foreach ( $valeur as $cle => $element ) {
				if ( null === $element || '' === $element || array() === $element ) {
					continue;
				}

				$resultat[ $cle ] = self::normaliser( $element );
			}

			return $resultat;
		}

		if ( is_object( $valeur ) ) {
			return self::normaliser( (array) $valeur );
		}

		if ( null === $valeur ) {
			return '';
		}

		if ( is_numeric( $valeur ) ) {
			return (string) ( 0 + $valeur );
		}

		return $valeur;
	}

	/**
	 * Trie récursivement les clés d'un tableau associatif.
	 *
	 * @param mixed $valeur Donnée.
	 * @return mixed
	 */
	private static function trier( $valeur ) {
		if ( ! is_array( $valeur ) ) {
			return $valeur;
		}

		$est_liste = array_keys( $valeur ) === range( 0, count( $valeur ) - 1 );

		if ( ! $est_liste ) {
			ksort( $valeur );
		}

		foreach ( $valeur as $cle => $element ) {
			$valeur[ $cle ] = self::trier( $element );
		}

		return $valeur;
	}
}
