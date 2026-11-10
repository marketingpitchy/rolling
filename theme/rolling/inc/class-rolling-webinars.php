<?php
/**
 * Webinars : à venir, en direct, puis replay, selon la date et l'heure saisies (heure de Paris).
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * État des webinars.
 */
class Rolling_Webinars {

	const FUSEAU = 'Europe/Paris';

	/**
	 * Début du webinar (horodatage), ou 0 si la date manque.
	 *
	 * @param int $post_id Webinar.
	 * @return int
	 */
	public static function debut( $post_id ) {
		$date  = Rolling_Contenus::valeur( 'date', $post_id );
		$heure = Rolling_Contenus::valeur( 'heure', $post_id );
		if ( ! $date ) {
			return 0;
		}
		$moment = date_create_immutable( $date . ' ' . ( $heure ? $heure : '00:00' ), new DateTimeZone( self::FUSEAU ) );

		return $moment ? $moment->getTimestamp() : 0;
	}

	/**
	 * Fin du webinar (horodatage).
	 *
	 * @param int $post_id Webinar.
	 * @return int
	 */
	public static function fin( $post_id ) {
		$duree = (int) Rolling_Contenus::valeur( 'duree', $post_id );

		return self::debut( $post_id ) + ( $duree ? $duree : 45 ) * MINUTE_IN_SECONDS;
	}

	/**
	 * État : a_venir, en_direct, replay (passé avec lien) ou termine (passé sans lien).
	 *
	 * @param int $post_id Webinar.
	 * @return string
	 */
	public static function etat( $post_id ) {
		$maintenant = time();
		if ( $maintenant < self::debut( $post_id ) ) {
			return 'a_venir';
		}
		if ( $maintenant < self::fin( $post_id ) ) {
			return 'en_direct';
		}

		return Rolling_Contenus::valeur( 'replay', $post_id ) ? 'replay' : 'termine';
	}

	/**
	 * Libellé de l'état pour l'administration.
	 *
	 * @param int $post_id Webinar.
	 * @return string
	 */
	public static function etat_libelle( $post_id ) {
		$libelles = array(
			'a_venir'   => __( 'À venir : page Webinars', 'rolling' ),
			'en_direct' => __( 'En direct', 'rolling' ),
			'replay'    => __( 'Passé : page Replays', 'rolling' ),
			'termine'   => __( 'Passé : ajoutez le lien du replay pour l’afficher sur la page Replays', 'rolling' ),
		);

		return $libelles[ self::etat( $post_id ) ];
	}

	/**
	 * Webinars d'une liste : « a_venir » (en direct compris) ou « replay », triés.
	 *
	 * @param string $liste  a_venir ou replay.
	 * @param int    $nombre Nombre maximum.
	 * @return int[]
	 */
	public static function liste( $liste, $nombre = 50 ) {
		$ids   = get_posts(
			array(
				'post_type'      => 'rolling_webinar',
				'posts_per_page' => 200,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		$garde = array();
		foreach ( $ids as $id ) {
			$etat = self::etat( $id );
			if ( ( 'a_venir' === $liste && in_array( $etat, array( 'a_venir', 'en_direct' ), true ) ) || ( 'replay' === $liste && 'replay' === $etat ) ) {
				$garde[ $id ] = self::debut( $id );
			}
		}
		'replay' === $liste ? arsort( $garde ) : asort( $garde );

		return array_slice( array_keys( $garde ), 0, $nombre );
	}
}
