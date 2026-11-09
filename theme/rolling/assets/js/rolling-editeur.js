/**
 * Rolling : aide pour l'éditeur Elementor.
 * Elementor 4.3.6 ne charge pas les styles des classes globales d'un modèle inséré depuis « Mes modèles »
 * tant que la page n'a pas été enregistrée puis rechargée. On lui demande de charger les classes du thème
 * à l'ouverture de l'éditeur et après chaque insertion, pour que le bloc s'affiche tout de suite comme sur le site.
 */
( function () {
	'use strict';

	var ids = ( window.rollingEditeur && window.rollingEditeur.classes ) || [];

	function charger( essais ) {
		var paquet = window.elementorV2 && window.elementorV2.editorGlobalClasses;

		if ( paquet && typeof paquet.loadExistingClasses === 'function' ) {
			paquet.loadExistingClasses( ids );
			return;
		}

		if ( essais > 0 ) {
			window.setTimeout( function () {
				charger( essais - 1 );
			}, 500 );
		}
	}

	window.addEventListener( 'elementor/global-styles/imported', function () {
		// Elementor réinitialise sa liste de classes juste après l'insertion : on recharge ensuite.
		window.setTimeout( function () {
			charger( 10 );
		}, 800 );
		window.setTimeout( function () {
			charger( 10 );
		}, 2500 );
	} );

	window.addEventListener( 'elementor/init', function () {
		window.setTimeout( function () {
			charger( 20 );
		}, 1500 );
	} );

	charger( 40 );
}() );
