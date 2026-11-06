/**
 * Rolling : comportements du site.
 * Les éléments sont reconnus par leur style (nom d'animation) ou par leur lien (#rl-…),
 * jamais par un nom de classe : ils marchent aussi quand Elementor a recopié les classes en styles locaux.
 */
( function () {
	'use strict';

	var calme = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var editeur = document.body.classList.contains( 'elementor-editor-active' );

	/* Menu burger de l'en-tête */
	document.querySelectorAll( '.rl-burger' ).forEach( function ( bouton ) {
		var entete = bouton.closest( '.rl-entete' );
		var libelles = [ bouton.getAttribute( 'data-ouvrir' ), bouton.getAttribute( 'data-fermer' ) ];

		bouton.addEventListener( 'click', function () {
			var ouvert = bouton.getAttribute( 'aria-expanded' ) !== 'true';
			bouton.setAttribute( 'aria-expanded', ouvert ? 'true' : 'false' );
			bouton.setAttribute( 'aria-label', ouvert ? libelles[ 1 ] : libelles[ 0 ] );
			entete.classList.toggle( 'rl-entete--ouvert', ouvert );
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && bouton.getAttribute( 'aria-expanded' ) === 'true' ) {
				bouton.click();
				bouton.focus();
			}
		} );
	} );

	if ( editeur ) {
		return;
	}

	/* Bloc le plus proche (section du bloc Elementor) */
	function bloc( el ) {
		return el.closest( '[data-elementor-type] > .elementor-element' ) || el.closest( 'section' ) || document.body;
	}

	/* Transforme un lien #rl-… en vrai bouton accessible */
	function enBouton( lien ) {
		lien.setAttribute( 'role', 'button' );
		lien.addEventListener( 'keydown', function ( e ) {
			if ( e.key === ' ' ) {
				e.preventDefault();
				lien.click();
			}
		} );
	}

	/* Logos qui défilent : toute bande dont l'animation s'appelle rl-defile */
	var conteneurs = document.querySelectorAll( '.e-flexbox-base, .e-div-block-base, .e-con' );

	conteneurs.forEach( function ( bande, index ) {
		var anim = window.getComputedStyle( bande ).animationName || '';

		if ( anim.indexOf( 'rl-defile' ) === -1 ) {
			return;
		}

		var zone = bande.parentElement;
		var section = bloc( bande );
		var bouton = section ? section.querySelector( 'a[href$="#rl-pause"]' ) : null;

		bande.classList.add( 'rl-defile' );
		zone.classList.add( 'rl-defile-zone' );
		bande.id = bande.id || 'rl-defile-' + index;

		if ( calme ) {
			bande.classList.add( 'rl-defile-statique' );

			if ( bouton ) {
				bouton.hidden = true;
			}

			return;
		}

		// Une copie pour que la boucle soit continue ; elle est cachée aux lecteurs d'écran.
		Array.prototype.slice.call( bande.children ).forEach( function ( enfant ) {
			var copie = enfant.cloneNode( true );
			copie.setAttribute( 'aria-hidden', 'true' );
			copie.removeAttribute( 'id' );
			copie.querySelectorAll( 'img' ).forEach( function ( img ) {
				img.alt = '';
			} );
			copie.querySelectorAll( 'a, button' ).forEach( function ( l ) {
				l.setAttribute( 'tabindex', '-1' );
			} );
			bande.appendChild( copie );
		} );

		if ( bouton ) {
			enBouton( bouton );
			bouton.setAttribute( 'aria-pressed', 'false' );
			bouton.setAttribute( 'aria-controls', bande.id );
			bouton.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var pause = bouton.getAttribute( 'aria-pressed' ) !== 'true';
				bouton.setAttribute( 'aria-pressed', pause ? 'true' : 'false' );
				bande.classList.toggle( 'rl-defile-pause', pause );
			} );
		}
	} );

	/* Vidéos en lecture automatique (showreel) : sans le son, quand elles sont visibles */
	document.querySelectorAll( 'video[autoplay]' ).forEach( function ( video ) {
		var section = bloc( video );
		var bouton = section ? section.querySelector( 'a[href$="#rl-son"]' ) : null;

		video.muted = true;
		video.setAttribute( 'preload', 'none' );

		if ( calme ) {
			video.removeAttribute( 'autoplay' );
			video.pause();
		} else if ( 'IntersectionObserver' in window ) {
			video.pause();
			new IntersectionObserver( function ( entrees ) {
				var e = entrees[ entrees.length - 1 ];

				if ( e.intersectionRatio >= 0.55 && video.paused ) {
					var lecture = video.play();

					if ( lecture && lecture.catch ) {
						lecture.catch( function () {} );
					}
				} else if ( e.intersectionRatio < 0.25 && ! video.paused ) {
					video.pause();
				}
			}, { threshold: [ 0, 0.25, 0.55, 0.8 ] } ).observe( video );
		}

		if ( bouton ) {
			enBouton( bouton );
			bouton.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				video.muted = false;
				video.controls = true;

				if ( video.paused ) {
					video.play();
				}

				bouton.hidden = true;
				video.focus();
			} );
		}
	} );

	/* Les liens #rl-… ne changent jamais l'adresse */
	document.addEventListener( 'click', function ( e ) {
		var lien = e.target.closest ? e.target.closest( 'a[href*="#rl-"]' ) : null;

		if ( lien ) {
			e.preventDefault();
		}
	} );
}() );
