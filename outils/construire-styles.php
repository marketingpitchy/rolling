<?php
/**
 * Construction des styles du thème (à lancer sur la copie de test, jamais chez la cliente) :
 *   wp eval-file construire-styles.php <dossier source> <dossier data du thème>
 *
 * 1. Variables : ids stables (e-gv-…) calculés depuis le libellé, valeurs au format Elementor.
 * 2. Classes : le CSS de la source est converti par l'outil officiel d'Elementor
 *    (ability « elementor/manage-classes »), puis relu au format natif et renommé avec des ids stables (g-…).
 *    Les déclarations « brut » (animations, que le convertisseur retire) sont ajoutées au CSS personnalisé.
 *
 * @package Rolling
 */

// phpcs:disable WordPress.WP.AlternativeFunctions, WordPress.PHP.DiscouragedPHPFunctions

use Elementor\Plugin;
use Elementor\Modules\GlobalClasses\Global_Classes_Repository;

wp_set_current_user( 1 );

$source = $args[0] ?? '/tmp/rolling/source';
$sortie = $args[1] ?? '/tmp/rolling/data';

// Les classes prioritaires (modificateurs) passent avant les classes de base.
$prioritaires = array( 'logos-defilants', 'conteneur-etroit', 'label-violet', 'titre-eteint', 'bouton-petit', 'point-bleu', 'point-orange', 'grand-bloc', 'carte-chiffre', 'carte-question', 'portrait-petit', 'image-couvrante' );

/**
 * Id stable d'une variable ou d'une classe.
 *
 * @param string $prefixe Préfixe Elementor.
 * @param string $label   Libellé.
 * @return string
 */
function rolling_id( $prefixe, $label ) {
	return $prefixe . substr( md5( 'rolling:' . $label ), 0, 7 );
}

/* 1. Variables ------------------------------------------------------------ */

$variables = json_decode( file_get_contents( $source . '/variables.json' ), true );
$sortie_variables = array();

foreach ( $variables as $variable ) {
	$valeur = 'global-color-variable' === $variable['type']
		? array( '$$type' => 'color', 'value' => $variable['value'] )
		: array( '$$type' => 'string', 'value' => $variable['value'] );

	$sortie_variables[] = array(
		'id'          => rolling_id( 'e-gv-', $variable['label'] ),
		'label'       => $variable['label'],
		'type'        => $variable['type'],
		'value'       => $valeur,
		'description' => $variable['description'],
	);
}

// Sur la copie de test : on repart des seules variables du thème.
$kit    = Plugin::$instance->kits_manager->get_active_kit();
$record = array( 'data' => array(), 'watermark' => 0, 'version' => 2 );
$ordre  = 0;

foreach ( $sortie_variables as $v ) {
	$record['data'][ $v['id'] ] = array(
		'type'       => $v['type'],
		'label'      => $v['label'],
		'value'      => $v['value'],
		'order'      => ++$ordre,
		'created_at' => gmdate( 'Y-m-d H:i:s' ),
		'updated_at' => gmdate( 'Y-m-d H:i:s' ),
	);
}

$ancien = $kit->get_json_meta( '_elementor_global_variables' );
$record['watermark'] = (int) ( $ancien['watermark'] ?? 0 ) + 1;
$kit->update_json_meta( '_elementor_global_variables', $record );
delete_option( 'rolling_variables' );

file_put_contents( $sortie . '/variables.json', wp_json_encode( $sortie_variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" );
echo 'Variables : ' . count( $sortie_variables ) . "\n";

/* 2. Classes -------------------------------------------------------------- */

$definition = json_decode( file_get_contents( $source . '/classes.json' ), true );
$depot      = Global_Classes_Repository::make();

/**
 * Supprime toutes les classes globales (copie de test seulement).
 *
 * @param Global_Classes_Repository $depot Dépôt.
 */
function rolling_vider_classes( $depot ) {
	$ids = array_keys( $depot->all( true )->get_items()->all() );

	if ( $ids ) {
		$depot->apply_changes( array(), array( 'added' => array(), 'modified' => array(), 'deleted' => $ids, 'order' => true ), array() );
	}
}

rolling_vider_classes( $depot );
delete_option( 'rolling_classes' );

$liste = array();

foreach ( $definition['groupes'] as $groupe ) {
	foreach ( $groupe['classes'] as $classe ) {
		$classe['groupe'] = $groupe['nom'];
		$liste[]          = $classe;
	}
}

$ability = wp_get_ability( 'elementor/manage-classes' );
$erreurs = 0;

foreach ( array_chunk( $liste, 40 ) as $lot ) {
	$operations = array();

	foreach ( $lot as $classe ) {
		$operations[] = array(
			'action' => 'create',
			'label'  => $classe['label'],
			'css'    => $classe['css'],
		);
	}

	$resultat = $ability->execute( array( 'operations' => $operations ) );

	if ( is_wp_error( $resultat ) ) {
		echo 'ERREUR : ' . $resultat->get_error_message() . "\n";
		exit( 1 );
	}

	foreach ( $resultat['results'] ?? array() as $r ) {
		if ( 'ok' !== ( $r['status'] ?? '' ) ) {
			++$erreurs;
			echo 'Classe en erreur : ' . wp_json_encode( $r, JSON_UNESCAPED_UNICODE ) . "\n";
		}
	}

	foreach ( $resultat['errors'] ?? array() as $r ) {
		++$erreurs;
		echo 'Erreur : ' . wp_json_encode( $r, JSON_UNESCAPED_UNICODE ) . "\n";
	}
}

$converties = array();

foreach ( Global_Classes_Repository::make()->all( true )->get_items()->all() as $item ) {
	$converties[ $item['label'] ] = $item;
}

$sortie_classes = array();

foreach ( $liste as $classe ) {
	if ( ! isset( $converties[ $classe['label'] ] ) ) {
		echo 'Absente après conversion : ' . $classe['label'] . "\n";
		++$erreurs;
		continue;
	}

	$variants = $converties[ $classe['label'] ]['variants'];

	if ( ! empty( $classe['brut'] ) ) {
		$trouve = false;

		foreach ( $variants as &$variant ) {
			if ( 'desktop' === ( $variant['meta']['breakpoint'] ?? '' ) && null === ( $variant['meta']['state'] ?? null ) ) {
				$existant              = isset( $variant['custom_css']['raw'] ) ? base64_decode( $variant['custom_css']['raw'] ) : '';
				$variant['custom_css'] = array( 'raw' => base64_encode( trim( $existant . ' ' . $classe['brut'] ) ) );
				$trouve                = true;
			}
		}
		unset( $variant );

		if ( ! $trouve ) {
			$variants[] = array(
				'meta'       => array( 'breakpoint' => 'desktop', 'state' => null ),
				'props'      => array(),
				'custom_css' => array( 'raw' => base64_encode( $classe['brut'] ) ),
			);
		}
	}

	$sortie_classes[] = array(
		'id'          => rolling_id( 'g-', $classe['label'] ),
		'label'       => $classe['label'],
		'groupe'      => $classe['groupe'],
		'description' => $classe['description'],
		'variants'    => $variants,
	);
}

usort(
	$sortie_classes,
	function ( $a, $b ) use ( $prioritaires ) {
		return (int) ! in_array( $a['label'], $prioritaires, true ) <=> (int) ! in_array( $b['label'], $prioritaires, true );
	}
);

rolling_vider_classes( Global_Classes_Repository::make() );

$groupes = array_map(
	function ( $g ) {
		return $g['nom'];
	},
	$definition['groupes']
);

file_put_contents(
	$sortie . '/classes.json',
	wp_json_encode( array( 'groupes' => $groupes, 'classes' => $sortie_classes ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n"
);

echo 'Classes : ' . count( $sortie_classes ) . ', erreurs : ' . $erreurs . "\n";
