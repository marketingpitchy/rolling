/* Formulaires des contenus Rolling : choix d'une image, lignes répétables (ajouter, monter, retirer). */
( function () {
	'use strict';

	document.querySelectorAll( '[data-rolling-image]' ).forEach( function ( bloc ) {
		var champ = bloc.querySelector( 'input' ), apercu = bloc.querySelector( '.rolling-apercu' ), retirer = bloc.querySelector( '[data-retirer]' ), cadre;
		bloc.querySelector( '[data-choisir]' ).addEventListener( 'click', function () {
			if ( ! cadre ) {
				cadre = wp.media( { library: { type: 'image' }, multiple: false } );
				cadre.on( 'select', function () {
					var image = cadre.state().get( 'selection' ).first().toJSON(), taille = ( image.sizes && image.sizes.thumbnail ) || image;
					champ.value = image.id;
					apercu.innerHTML = '';
					var img = document.createElement( 'img' );
					img.src = taille.url; img.alt = ''; img.width = 80;
					apercu.appendChild( img );
					retirer.hidden = false;
				} );
			}
			cadre.open();
		} );
		retirer.addEventListener( 'click', function () { champ.value = ''; apercu.innerHTML = ''; retirer.hidden = true; } );
	} );

	document.querySelectorAll( '[data-rolling-lignes]' ).forEach( function ( bloc ) {
		var liste = bloc.querySelector( '.rolling-liste' ), nom = bloc.dataset.nom;
		function renumeroter() {
			liste.querySelectorAll( '.rolling-ligne' ).forEach( function ( ligne, i ) {
				ligne.querySelectorAll( 'input' ).forEach( function ( champ ) { champ.name = nom + '[' + i + '][' + champ.dataset.colonne + ']'; } );
			} );
		}
		bloc.querySelector( '[data-ajouter]' ).addEventListener( 'click', function () {
			var modele = liste.querySelector( '.rolling-ligne' ), nouvelle = modele.cloneNode( true );
			nouvelle.querySelectorAll( 'input' ).forEach( function ( champ ) { champ.value = ''; } );
			liste.appendChild( nouvelle ); renumeroter();
			nouvelle.querySelector( 'input' ).focus();
		} );
		liste.addEventListener( 'click', function ( e ) {
			var ligne = e.target.closest( '.rolling-ligne' );
			if ( ! ligne ) { return; }
			if ( e.target.closest( '[data-retirer-ligne]' ) ) {
				if ( liste.children.length > 1 ) { ligne.remove(); } else { ligne.querySelectorAll( 'input' ).forEach( function ( c ) { c.value = ''; } ); }
				renumeroter();
			}
			if ( e.target.closest( '[data-monter]' ) && ligne.previousElementSibling ) {
				liste.insertBefore( ligne, ligne.previousElementSibling ); renumeroter();
				e.target.focus();
			}
		} );
	} );
}() );
