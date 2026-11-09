<?php
/**
 * Construction des blocs (copie de test seulement) :
 *   wp eval-file construire-blocs.php <dossier des sources de blocs> <dossier data/blocs du thème>
 *
 * Chaque bloc est composé par l'outil officiel d'Elementor (« elementor/build-composition ») dans une page
 * temporaire, puis son contenu natif (_elementor_data) est exporté pour le thème. Les classes et variables
 * du thème doivent être installées avant (le thème actif les installe).
 *
 * @package Rolling
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.GlobalVariablesOverride.Prohibited -- Script de construction lancé en ligne de commande (wp eval-file) sur la copie de test, jamais sur le site.

// phpcs:disable WordPress.WP.AlternativeFunctions

wp_set_current_user( 1 );


/**
 * Ajoute du CSS au CSS personnalisé du style local d'éléments (repérés par leur nom dans le Navigateur).
 *
 * @param array $elements Éléments.
 * @param array $brut     Nom => CSS (modifié : les noms traités sont retirés).
 * @return array
 */
function rolling_poser_brut( $elements, &$brut ) {
	foreach ( $elements as &$element ) {
		$nom = $element['editor_settings']['title'] ?? '';

		if ( '' !== $nom && ! empty( $brut[ $nom ] ) ) {
			$css     = $brut[ $nom ];
			$classes = $element['settings']['classes']['value'] ?? array();
			$local   = null;

			foreach ( $classes as $id ) {
				if ( isset( $element['styles'][ $id ] ) ) {
					$local = $id;
				}
			}

			if ( null === $local ) {
				$local                          = 'e-' . $element['id'] . '-' . substr( md5( $nom ), 0, 4 );
				$element['styles'][ $local ]    = array(
					'id'       => $local,
					'label'    => 'local',
					'type'     => 'class',
					'variants' => array(),
				);
				$element['settings']['classes'] = array(
					'$$type' => 'classes',
					'value'  => array_merge( $classes, array( $local ) ),
				);
			}

			$pose = false;

			foreach ( $element['styles'][ $local ]['variants'] as &$variant ) {
				if ( 'desktop' === ( $variant['meta']['breakpoint'] ?? '' ) && null === ( $variant['meta']['state'] ?? null ) ) {
					$existant              = isset( $variant['custom_css']['raw'] ) ? base64_decode( $variant['custom_css']['raw'] ) : ''; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
					$variant['custom_css'] = array( 'raw' => base64_encode( trim( $existant . ' ' . $css ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
					$pose                  = true;
				}
			}
			unset( $variant );

			if ( ! $pose ) {
				$element['styles'][ $local ]['variants'][] = array(
					'meta'       => array(
						'breakpoint' => 'desktop',
						'state'      => null,
					),
					'props'      => (object) array(),
					'custom_css' => array( 'raw' => base64_encode( $css ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
				);
			}

			unset( $brut[ $nom ] );
		}

		if ( ! empty( $element['elements'] ) ) {
			$element['elements'] = rolling_poser_brut( $element['elements'], $brut );
		}
	}
	unset( $element );

	return $elements;
}

/**
 * Range les classes globales de chaque élément de la moins prioritaire à la plus prioritaire.
 *
 * Sur le site, entre deux classes, c'est l'ordre global qui décide (la première de la liste l'emporte).
 * Quand la cliente insère un bloc avec « Conserver les styles du modèle », case décochée, Elementor recopie
 * les classes dans l'ordre de l'élément et la dernière l'emporte : les deux ordres doivent donc coïncider.
 *
 * @param array $elements Éléments.
 * @param array $rang     Id de classe => rang dans l'ordre global (0 = la plus prioritaire).
 * @return array
 */
function rolling_ordonner_classes( $elements, $rang ) {
	foreach ( $elements as &$element ) {
		$ids = $element['settings']['classes']['value'] ?? null;

		if ( is_array( $ids ) ) {
			$globales = array_values( array_filter( $ids, fn( $id ) => isset( $rang[ $id ] ) ) );
			$autres   = array_values( array_filter( $ids, fn( $id ) => ! isset( $rang[ $id ] ) ) );
			usort( $globales, fn( $a, $b ) => $rang[ $b ] <=> $rang[ $a ] );
			$element['settings']['classes']['value'] = array_merge( $globales, $autres );
		}

		if ( ! empty( $element['elements'] ) ) {
			$element['elements'] = rolling_ordonner_classes( $element['elements'], $rang );
		}
	}
	unset( $element );

	return $elements;
}

$source = $args[0] ?? '/tmp/rolling/source/blocs';
$sortie = $args[1] ?? '/tmp/rolling/data/blocs';
$filtre = $args[2] ?? '';
$outil  = wp_get_ability( 'elementor/build-composition' );
$total  = 0;
$rang   = array_flip( array_column( json_decode( file_get_contents( dirname( $sortie ) . '/classes.json' ), true )['classes'], 'id' ) );

foreach ( glob( $source . '/*.json' ) as $fichier ) {
	$bloc = json_decode( file_get_contents( $fichier ), true );

	if ( $filtre && false === strpos( $bloc['cle'], $filtre ) ) {
		continue;
	}

	$page = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'draft',
			'post_title'  => 'Construction ' . $bloc['cle'],
		)
	);
	update_post_meta( $page, '_elementor_edit_mode', 'builder' );
	update_post_meta( $page, '_elementor_template_type', 'wp-page' );

	$resultat = $outil->execute( array_merge( array( 'post_id' => $page ), $bloc['composition'] ) );

	if ( is_wp_error( $resultat ) || empty( $resultat['success'] ) ) {
		echo 'ERREUR ' . $bloc['cle'] . ' : ' . wp_json_encode( is_wp_error( $resultat ) ? $resultat->get_error_message() : $resultat, JSON_UNESCAPED_UNICODE ) . "\n";
		wp_delete_post( $page, true );
		continue;
	}

	// CSS à poser dans le CSS personnalisé : règles imbriquées (source) + déclarations refusées (avertissements).
	$brut = $bloc['brut'] ?? array();

	foreach ( $resultat['warnings'] ?? array() as $avertissement ) {
		if ( preg_match( '/^\[(.+?)\] These CSS properties or values are not supported and were not saved: (.+)$/s', $avertissement, $m ) ) {
			$brut[ $m[1] ] = trim( $m[2] . ' ' . ( $brut[ $m[1] ] ?? '' ) );
		} else {
			echo 'Avertissement ' . $bloc['cle'] . ' : ' . $avertissement . "\n";
		}
	}

	$document = \Elementor\Plugin::$instance->documents->get( $page, false );
	$contenu  = rolling_ordonner_classes( $document ? $document->get_elements_data() : array(), $rang );
	// Le CSS personnalisé des styles locaux n'est rendu qu'avec une licence Pro : on le refuse.
	foreach ( $brut as $nom => $css ) {
		echo 'À METTRE DANS UNE CLASSE (' . $bloc['cle'] . ' / ' . $nom . ') : ' . $css . "\n";
	}

	file_put_contents(
		$sortie . '/' . $bloc['cle'] . '.json',
		wp_json_encode(
			array(
				'cle'     => $bloc['cle'],
				'titre'   => $bloc['titre'],
				'type'    => $bloc['type'],
				'contenu' => $contenu,
			),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		) . "\n"
	);

	wp_delete_post( $page, true );
	++$total;
	echo 'Bloc ' . $bloc['cle'] . " construit.\n";
}

echo $total . " bloc(s).\n";
