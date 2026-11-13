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

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.GlobalVariablesOverride.Prohibited -- Script de construction lancé en ligne de commande (wp eval-file) sur la copie de test, jamais sur le site.

// phpcs:disable WordPress.WP.AlternativeFunctions, WordPress.PHP.DiscouragedPHPFunctions

use Elementor\Plugin;

wp_set_current_user( 1 );

$source = $args[0] ?? '/tmp/rolling/source';
$sortie = $args[1] ?? '/tmp/rolling/data';

// Les classes prioritaires (modificateurs) passent avant les classes de base ; les « très prioritaires » modifient
// elles-mêmes une classe prioritaire (ex. entete-page modifie section-entete) et passent avant tout.
$tres_prioritaires = array( 'titre-point-bleu', 'titre-point-encre', 'titre-point', 'entete-page', 'appel-centre', 'titre-encart', 'carte-vide', 'titre-formule', 'titre-carte', 'offres-groupe', 'offres-entete', 'offres-resume-sombre', 'montage-4', 'clip-demande', 'clip-mauve', 'clip-final', 'offres-tableau', 'carte-ajout', 'montage', 'case-oui', 'offres-mobile', 'offres-masquer', 'zone-calendrier', 'contact-grille', 'agence-photo', 'citation-fondateurs', 'fondateurs-photo', 'conviction', 'agence-confiance', 'carte-douleur', 'comparatif', 'comparatif-entete', 'plaque-3', 'landing-action', 'zone-calendrier-court' );
$prioritaires = array( 'logos-defilants', 'conteneur-etroit', 'label-violet', 'titre-eteint', 'bouton-petit', 'pastille-bleue', 'pastille-orange', 'titre-point-bleu', 'titre-point-encre', 'bandeau-rec', 'bandeau-clair', 'fond-grille', 'halos-haut', 'halos-bas', 'halo-centre', 'lueur-peche', 'plaque-lecteur', 'plaque-claire', 'grand-bloc', 'carte-chiffre', 'carte-question', 'portrait-petit', 'image-couvrante', 'bouton-lecture-verre', 'titre-vignette-sombre', 'section-entete', 'section-suite', 'texte-legende', 'cadre-verre-grand', 'plaque-bas', 'carte-ecran', 'tuile-logo', 'carte-constat', 'halo-lilas', 'halo-bleu', 'halo-orange', 'etiquette-rec', 'chapitre-peche', 'chapitre-colle', 'photo-chapitre', 'viseur', 'rec-droite', 'label-orange', 'carte-etape', 'carte-vignette', 'carte-ia', 'carte-humain', 'label-peche', 'portrait-sombre', 'version-cachee', 'lecture-ecran', 'carte-prix', 'carte-prix-sombre', 'carte-promesses', 'prix-degrade', 'resume-offre-sombre', 'citation', 'carte-citation', 'portrait-moyen', 'carte-appel', 'section-finale', 'titre-souligne', 'temoin', 'temoin-une', 'carte-webinar', 'carte-webinar-une', 'cas-citation', 'lien-titre', 'vignette-lien', 'video-fiche', 'cadre-16-9', 'cadre-16-10', 'cadre-4-3', 'carte-outils', 'carte-sombre', 'carte-rubrique', 'une-photo', 'une-sans-photo' );

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

$variables        = json_decode( file_get_contents( $source . '/variables.json' ), true );
$sortie_variables = array();

foreach ( $variables as $variable ) {
	$valeur = 'global-color-variable' === $variable['type']
		? array(
			'$$type' => 'color',
			'value'  => $variable['value'],
		)
		: array(
			'$$type' => 'string',
			'value'  => $variable['value'],
		);

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
$record = array(
	'data'      => array(),
	'watermark' => 0,
	'version'   => 2,
);
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

$ancien              = $kit->get_json_meta( '_elementor_global_variables' );
$record['watermark'] = (int) ( $ancien['watermark'] ?? 0 ) + 1;
$kit->update_json_meta( '_elementor_global_variables', $record );
delete_option( 'rolling_variables' );

file_put_contents( $sortie . '/variables.json', wp_json_encode( $sortie_variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" );
echo 'Variables : ' . count( $sortie_variables ) . "\n";

/* 2. Classes -------------------------------------------------------------- */

$definition = json_decode( file_get_contents( $source . '/classes.json' ), true );

// Convertisseur officiel d'Elementor (celui de l'outil manage-classes), sans rien enregistrer en base.
$outil   = new \Elementor\Modules\Mcp\Abilities\Manage_Classes_Ability();
$methode = new ReflectionMethod( $outil, 'get_css_converter' );
$methode->setAccessible( true );
$convertir      = function () use ( $outil, $methode ) {
	return $methode->invoke( $outil );
};
$points         = Plugin::$instance->breakpoints->get_active_devices_list();
$resultats      = new \Elementor\Modules\Mcp\Abilities\Utils\Bulk_Operations_Result();
$merger         = '\Elementor\Modules\Mcp\Abilities\Utils\Style_Variants_Merger';
$erreurs        = 0;
$sortie_classes = array();

foreach ( $definition['groupes'] as $groupe ) {
	foreach ( $groupe['classes'] as $index => $classe ) {
		$analyse = $merger::parse_css_string( $classe['css'], $points, $index, 'create', $resultats, $convertir );

		if ( null === $analyse ) {
			echo 'ERREUR de CSS : ' . $classe['label'] . "\n";
			++$erreurs;
			continue;
		}

		$variants = $merger::build_variants( $analyse['breakpoint_blocks'], $convertir() );

		foreach ( $variants as &$variant ) {
			unset( $variant['null_props'] );
		}
		unset( $variant );

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
					'meta'       => array(
						'breakpoint' => 'desktop',
						'state'      => null,
					),
					'props'      => array(),
					'custom_css' => array( 'raw' => base64_encode( $classe['brut'] ) ),
				);
			}
		}

		$sortie_classes[] = array(
			'id'          => rolling_id( 'g-', $classe['label'] ),
			'label'       => $classe['label'],
			'groupe'      => $groupe['nom'],
			'description' => $classe['description'],
			'variants'    => $variants,
		);
	}
}

foreach ( $resultats->to_array()['errors'] ?? array() as $e ) {
	echo 'Erreur : ' . wp_json_encode( $e, JSON_UNESCAPED_UNICODE ) . "\n";
	++$erreurs;
}

usort(
	$sortie_classes,
	function ( $a, $b ) use ( $prioritaires, $tres_prioritaires ) {
		$rang = function ( $label ) use ( $prioritaires, $tres_prioritaires ) {
			$place = array_search( $label, $tres_prioritaires, true );
			return false !== $place ? $place : ( in_array( $label, $prioritaires, true ) ? 1000 : 1001 );
		};
		return $rang( $a['label'] ) <=> $rang( $b['label'] );
	}
);

$groupes = array_map(
	function ( $g ) {
		return $g['nom'];
	},
	$definition['groupes']
);

file_put_contents(
	$sortie . '/classes.json',
	wp_json_encode(
		array(
			'groupes' => $groupes,
			'classes' => $sortie_classes,
		),
		JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	) . "\n"
);

echo 'Classes : ' . count( $sortie_classes ) . ', erreurs : ' . $erreurs . "\n";
