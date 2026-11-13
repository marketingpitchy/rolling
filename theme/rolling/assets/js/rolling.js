/**
 * Rolling : comportements du site.
 * Les éléments sont reconnus par leur style (nom d'animation) ou par leur lien (#rl-…),
 * jamais par un nom de classe : ils marchent aussi quand Elementor a recopié les classes en styles locaux.
 */
( function () {
	'use strict';

	var calme = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	// L'aperçu de l'éditeur se reconnaît à son adresse : la classe du body arrive plus tard.
	var editeur = document.body.classList.contains( 'elementor-editor-active' ) || /[?&]elementor-preview=/.test( window.location.search );

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

	/* Sous-menu de l'en-tête (ex. « Ressources ») : le bouton l'ouvre ; Échap ou un clic ailleurs le referme. */
	document.querySelectorAll( '.rl-menu__ouvrir' ).forEach( function ( bouton ) {
		var element = bouton.parentElement;
		function basculer( ouvrir ) {
			bouton.setAttribute( 'aria-expanded', ouvrir ? 'true' : 'false' );
			element.classList.toggle( 'rl-ouvert', ouvrir );
		}
		bouton.addEventListener( 'click', function () { basculer( bouton.getAttribute( 'aria-expanded' ) !== 'true' ); } );
		document.addEventListener( 'click', function ( e ) { if ( ! element.contains( e.target ) ) { basculer( false ); } } );
		element.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && bouton.getAttribute( 'aria-expanded' ) === 'true' ) {
				e.stopPropagation();
				basculer( false );
				bouton.focus();
			}
		} );
	} );

	/*
	 * Dans l'éditeur : l'écran « Votre vidéo » suit l'élément sélectionné. Sélectionner le bouton 9:16 ou EN change
	 * le format ou la langue affichés ; sélectionner un texte d'une version (même depuis la Structure) l'affiche.
	 */
	if ( editeur ) {
		new MutationObserver( function ( changements ) {
			changements.forEach( function ( c ) {
				var el = c.target;
				var ecran = document.getElementById( 'rl-ecran' );

				if ( ! ecran || ! el.classList || ! el.classList.contains( 'elementor-element-editable' ) ) {
					return;
				}

				var lien = el.matches( 'a[href*="#rl-"]' ) ? el : el.querySelector( 'a[href*="#rl-format-"], a[href*="#rl-langue-"]' );
				var version = el.closest( '[id^="rl-version-"]' );

				if ( lien ) {
					var cle = lien.getAttribute( 'href' ).split( '#rl-' )[ 1 ].split( '-' );
					var type = cle.shift();

					if ( type === 'format' ) {
						ecran.setAttribute( 'data-format', cle.join( ':' ) );
					} else if ( type === 'langue' ) {
						ecran.setAttribute( 'data-rl-langue', cle.join( '-' ) );
					}
				} else if ( version ) {
					ecran.setAttribute( 'data-rl-langue', version.id.replace( 'rl-version-', '' ) );
				}
			} );
		} ).observe( document.body, { subtree: true, attributes: true, attributeFilter: [ 'class' ] } );

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
	var conteneurs = document.querySelectorAll( '.e-flexbox-base, .e-div-block-base, .e-con, .rl-logos' );

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
			// Deux libellés dans le bouton (deux <span>) : « Arrêter… » puis « Relancer… ».
			var libelles = bouton.querySelectorAll( 'span' );
			var enPause = false;

			enBouton( bouton );
			bouton.setAttribute( 'aria-controls', bande.id );

			if ( libelles.length < 2 ) {
				bouton.setAttribute( 'aria-pressed', 'false' );
			}

			bouton.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				enPause = ! enPause;
				bande.classList.toggle( 'rl-defile-pause', enPause );

				if ( libelles.length >= 2 ) {
					libelles[ 0 ].style.display = enPause ? 'none' : 'inline';
					libelles[ 1 ].style.display = enPause ? 'inline' : 'none';
				} else {
					bouton.setAttribute( 'aria-pressed', enPause ? 'true' : 'false' );
				}
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

	/*
	 * Écran « Votre vidéo » : l'élément d'identifiant rl-ecran reçoit data-format (ses styles suivent),
	 * les versions rl-version-fr, -en, -es reçoivent leur langue et s'affichent une à la fois.
	 * Les boutons sont des liens #rl-format-16-9 / #rl-langue-en ; leur groupe prend le nom écrit dans le groupe.
	 */
	document.querySelectorAll( '#rl-ecran' ).forEach( function ( ecran ) {
		var section = bloc( ecran );
		var versions = section.querySelectorAll( '[id^="rl-version-"]' );
		var annonce = document.createElement( 'p' );
		var choix = { format: '16:9', langue: 'fr' };

		annonce.setAttribute( 'aria-live', 'polite' );
		annonce.style.cssText = 'position:absolute;width:1px;height:1px;margin:-1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;';
		ecran.parentElement.appendChild( annonce );
		ecran.setAttribute( 'data-format', choix.format );

		versions.forEach( function ( v ) {
			v.setAttribute( 'lang', v.id.replace( 'rl-version-', '' ) );
		} );

		function afficher() {
			ecran.setAttribute( 'data-format', choix.format );
			versions.forEach( function ( v ) {
				v.style.display = v.getAttribute( 'lang' ) === choix.langue ? 'block' : 'none';
			} );
		}

		section.querySelectorAll( 'a[href*="#rl-format-"], a[href*="#rl-langue-"]' ).forEach( function ( lien ) {
			var cle = lien.getAttribute( 'href' ).split( '#rl-' )[ 1 ].split( '-' );
			var type = cle.shift();
			var valeur = type === 'format' ? cle.join( ':' ) : cle.join( '-' );
			var groupe = lien.parentElement;
			var nom = groupe.querySelector( 'p' );

			enBouton( lien );
			lien.setAttribute( 'aria-pressed', valeur === choix[ type ] ? 'true' : 'false' );

			if ( nom && ! groupe.hasAttribute( 'role' ) ) {
				groupe.setAttribute( 'role', 'group' );
				groupe.setAttribute( 'aria-label', nom.textContent.trim() );
				nom.setAttribute( 'aria-hidden', 'true' );
			}

			lien.setAttribute( 'aria-label', ( nom ? nom.textContent.trim() + ' ' : '' ) + lien.textContent.trim() );

			lien.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				if ( ! enDemo ) {
					demarre();
				}
				choix[ type ] = valeur;
				groupe.querySelectorAll( 'a[href*="#rl-"]' ).forEach( function ( autre ) {
					autre.setAttribute( 'aria-pressed', autre === lien ? 'true' : 'false' );
				} );
				afficher();
				annonce.textContent = lien.getAttribute( 'aria-label' );
			} );
		} );

		/* Montrer que l'écran se pilote : l'astuce manuscrite (élément d'identifiant rl-ecran-astuce) reste jusqu'au
		   premier clic du visiteur ; une fois, quand l'écran arrive à l'écran, un curseur clique tout seul sur 9:16,
		   EN puis revient au départ ; ensuite le bouton 9:16 respire. Rien dans l'éditeur ni avec « réduire les animations ». */
		var astuce = document.getElementById( 'rl-ecran-astuce' ), minuteries = [], curseur = null, appel = null, fini = false, enDemo = false;
		function demarre() {
			if ( fini ) {
				return;
			}
			fini = true;
			minuteries.forEach( clearTimeout );
			if ( curseur ) {
				curseur.remove();
			}
			if ( appel ) {
				appel.classList.remove( 'rl-appel' );
			}
			if ( astuce ) {
				astuce.style.transition = 'opacity .5s ease';
				astuce.style.opacity = '0';
			}
		}
		function boutonEcran( v ) {
			return section.querySelector( 'a[href*="#rl-format-' + v + '"], a[href*="#rl-langue-' + v + '"]' );
		}
		appel = boutonEcran( '9-16' );
		if ( ! calme && ! editeur && appel && 'IntersectionObserver' in window ) {
			var hote = appel.parentElement.parentElement.parentElement, vu = false;
			new IntersectionObserver( function ( entrees, obs ) {
				if ( ! entrees[ 0 ].isIntersecting || vu || fini ) {
					return;
				}
				vu = true;
				obs.disconnect();
				curseur = document.createElement( 'span' );
				curseur.className = 'rl-curseur';
				curseur.setAttribute( 'aria-hidden', 'true' );
				hote.appendChild( curseur );
				curseur.style.transform = 'translate(' + ( hote.offsetWidth / 2 ) + 'px,' + ( hote.offsetHeight * 0.55 ) + 'px)';
				var t = 1500;
				[ '9-16', 'en', '16-9', 'fr' ].forEach( function ( v ) {
					var cible = boutonEcran( v );
					if ( ! cible ) {
						return;
					}
					minuteries.push( setTimeout( function () {
						var r = cible.getBoundingClientRect(), h = hote.getBoundingClientRect();
						curseur.style.opacity = '1';
						curseur.style.transform = 'translate(' + ( r.left - h.left + r.width / 2 ) + 'px,' + ( r.top - h.top + r.height / 2 ) + 'px)';
					}, t ) );
					minuteries.push( setTimeout( function () {
						curseur.classList.remove( 'rl-curseur--clic' );
						void curseur.offsetWidth;
						curseur.classList.add( 'rl-curseur--clic' );
						enDemo = true;
						cible.click();
						enDemo = false;
					}, t + 800 ) );
					t += 1900;
				} );
				minuteries.push( setTimeout( function () {
					curseur.style.opacity = '0';
					appel.classList.add( 'rl-appel' );
				}, t ) );
			}, { threshold: 0.6 } ).observe( ecran );
		}
	} );

	/* Webinars : compte à rebours réel jusqu'au début (heure de Paris, calculée dans le navigateur : compatible
	   avec le cache des pages), puis « En direct » pendant la durée, puis « Terminé ». */
	document.querySelectorAll( '[data-debut][data-fin]' ).forEach( function ( cr ) {
		var debut = parseInt( cr.getAttribute( 'data-debut' ), 10 ), fin = parseInt( cr.getAttribute( 'data-fin' ), 10 );
		var carte = cr.closest( 'article' ), bouton = carte ? carte.querySelector( 'a[href]' + ( carte.querySelector( '.e-button-base' ) ? '.e-button-base' : '' ) ) : null;
		var chiffres = {};
		cr.querySelectorAll( '[data-cr]' ).forEach( function ( b ) { chiffres[ b.getAttribute( 'data-cr' ) ] = b; } );
		function deux( n ) { return ( n < 10 ? '0' : '' ) + n; }
		function maj() {
			var maintenant = Date.now();
			if ( maintenant >= fin ) {
				cr.classList.remove( 'en-direct' );
				cr.classList.add( 'termine' );
				return false;
			}
			if ( maintenant >= debut ) {
				if ( ! cr.classList.contains( 'en-direct' ) ) {
					cr.classList.add( 'en-direct' );
					if ( bouton && cr.getAttribute( 'data-bouton' ) ) {
						var texte = bouton.querySelector( 'span' ) || bouton;
						texte.firstChild.nodeValue = cr.getAttribute( 'data-bouton' ) + ' ';
					}
				}
				return true;
			}
			var s = Math.floor( ( debut - maintenant ) / 1000 );
			if ( chiffres.j ) {
				chiffres.j.textContent = deux( Math.floor( s / 86400 ) );
				chiffres.h.textContent = deux( Math.floor( s % 86400 / 3600 ) );
				chiffres.m.textContent = deux( Math.floor( s % 3600 / 60 ) );
				chiffres.s.textContent = deux( s % 60 );
			}
			return true;
		}
		if ( maj() && ! editeur ) {
			var minuterie = setInterval( function () { if ( ! maj() ) { clearInterval( minuterie ); } }, 1000 );
		}
	} );

	/* Carte d'une réalisation sans image à la une, vidéo en fichier : l'affiche du thème porte le lien du fichier ;
	   elle est remplacée par la première image de la vidéo (muette, jamais lue). */
	document.querySelectorAll( 'img[src*="rl-video="]' ).forEach( function ( img ) {
		var m = img.getAttribute( 'src' ).match( /[?&]rl-video=([^&]+)/ ), v;
		if ( ! m ) {
			return;
		}
		v = document.createElement( 'video' );
		v.className = img.className;
		v.muted = true;
		v.preload = 'metadata';
		v.playsInline = true;
		v.setAttribute( 'aria-hidden', 'true' );
		v.setAttribute( 'tabindex', '-1' );
		v.src = decodeURIComponent( m[ 1 ] ) + '#t=0.1';
		img.replaceWith( v );
	} );

	/* Vidéo d'une fiche : chargée seulement au clic (YouTube sans cookie, ou fichier de la médiathèque) */
	document.querySelectorAll( '[data-rl-video]' ).forEach( function ( cadre ) {
		var bouton = cadre.querySelector( 'button' ), apercu = cadre.querySelector( 'video.apercu-video' );
		if ( ! bouton || editeur ) {
			return;
		}
		/* Fichier vidéo : aperçu muet en boucle tant que la vidéo est à l'écran (rien avec « réduire les animations ») */
		if ( apercu && ! calme && 'fixe' !== cadre.getAttribute( 'data-rl-apercu' ) && 'IntersectionObserver' in window ) {
			new IntersectionObserver( function ( entrees ) {
				entrees.forEach( function ( e ) {
					if ( ! apercu.isConnected ) {
						return;
					}
					if ( e.isIntersecting ) {
						var p = apercu.play();
						if ( p && p.catch ) {
							p.catch( function () {} );
						}
					} else {
						apercu.pause();
					}
				} );
			}, { threshold: 0.35 } ).observe( cadre );
		}
		bouton.addEventListener( 'click', function () {
			if ( apercu ) {
				apercu.remove();
			}
			var url = cadre.getAttribute( 'data-rl-video' ), yt = url.match( /(?:youtu\.be\/|v=|embed\/|shorts\/)([\w-]{11})/ ), lecteur;
			if ( yt ) {
				lecteur = document.createElement( 'iframe' );
				lecteur.src = 'https://www.youtube-nocookie.com/embed/' + yt[ 1 ] + '?autoplay=1&rel=0';
				lecteur.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
				lecteur.title = bouton.getAttribute( 'aria-label' );
			} else {
				lecteur = document.createElement( 'video' );
				lecteur.src = url;
				lecteur.controls = true;
				lecteur.autoplay = true;
				lecteur.playsInline = true;
			}
			cadre.appendChild( lecteur );
			cadre.classList.add( 'lit' );
			bouton.remove();
			lecteur.focus();
		} );
	} );

	/* Vignettes des listes : sur un écran tactile, celle qui passe au centre de l'écran « démarre » */
	if ( ! calme && ! editeur && window.matchMedia( '(hover: none)' ).matches && 'IntersectionObserver' in window ) {
		var centre = new IntersectionObserver( function ( entrees ) {
			entrees.forEach( function ( e ) { e.target.classList.toggle( 'joue', e.isIntersecting ); } );
		}, { rootMargin: '-40% 0px -40% 0px' } );
		document.querySelectorAll( 'a.vignette-lien' ).forEach( function ( v ) { centre.observe( v ); } );
	}

	/* Article : barre de lecture (en haut de l'écran) avec un repère par chapitre, sommaire qui suit la lecture,
	   bouton « Copier le lien » (partage natif sur téléphone). */
	document.querySelectorAll( '[data-rl-article]' ).forEach( function ( texte ) {
		if ( editeur ) {
			return;
		}
		var barre = document.createElement( 'div' ), avance = document.createElement( 'span' ), poses = false;
		barre.className = 'rl-progression';
		barre.setAttribute( 'aria-hidden', 'true' );
		avance.className = 'rl-progression__avance';
		barre.appendChild( avance );
		document.body.appendChild( barre );
		function maj() {
			var r = texte.getBoundingClientRect(), p = Math.min( 1, Math.max( 0, ( window.innerHeight * 0.5 - r.top ) / r.height ) );
			if ( ! poses && r.height ) {
				texte.querySelectorAll( 'h2' ).forEach( function ( h ) {
					var repere = document.createElement( 'span' );
					repere.className = 'rl-progression__chapitre';
					repere.style.left = ( ( h.getBoundingClientRect().top - r.top ) / r.height * 100 ).toFixed( 1 ) + '%';
					barre.appendChild( repere );
				} );
				poses = true;
			}
			barre.style.setProperty( '--rl-avance', ( p * 100 ).toFixed( 1 ) + '%' );
			barre.classList.toggle( 'rl-visible', p > 0 );
		}
		window.addEventListener( 'scroll', maj, { passive: true } );
		maj();

		var liens = document.querySelectorAll( '[data-rl-sommaire] a' );
		if ( liens.length && 'IntersectionObserver' in window ) {
			var suivi = new IntersectionObserver( function ( entrees ) {
				entrees.forEach( function ( e ) {
					if ( e.isIntersecting ) {
						liens.forEach( function ( l ) { l.classList.toggle( 'actif', l.getAttribute( 'href' ) === '#' + e.target.id ); } );
					}
				} );
			}, { rootMargin: '-20% 0px -70% 0px' } );
			texte.querySelectorAll( 'h2[id]' ).forEach( function ( h ) { suivi.observe( h ); } );
		}
	} );
	document.querySelectorAll( '[data-rl-copier]' ).forEach( function ( bouton ) {
		bouton.addEventListener( 'click', function () {
			var url = window.location.href;
			if ( navigator.share && window.matchMedia( '(hover: none)' ).matches ) {
				navigator.share( { url: url, title: document.title } ).catch( function () {} );
				return;
			}
			if ( navigator.clipboard ) {
				navigator.clipboard.writeText( url ).then( function () {
					bouton.setAttribute( 'title', bouton.getAttribute( 'data-rl-copier' ) );
					bouton.setAttribute( 'aria-label', bouton.getAttribute( 'data-rl-copier' ) );
				} );
			}
		} );
	} );

	/* Timecode « REC » (classe timecode-rec) écrit 00:00:00:00 : il défile sur le site, à 25 images par seconde */
	if ( ! calme && ! editeur ) {
		document.querySelectorAll( '.timecode-rec' ).forEach( function ( t ) {
			var m = t.textContent.trim().match( /^(\d{1,2}):(\d{2}):(\d{2})(?::(\d{2}))?$/ ), deux = function ( n ) { return ( '0' + n ).slice( -2 ); }, base, t0;
			if ( ! m ) {
				return;
			}
			base = ( ( +m[ 1 ] * 60 + +m[ 2 ] ) * 60 + +m[ 3 ] ) * 25 + +( m[ 4 ] || 0 );
			t0 = performance.now();
			t.setAttribute( 'aria-hidden', 'true' );
			( function tic( n ) {
				var img = base + Math.floor( ( n - t0 ) / 40 ), s = Math.floor( img / 25 );
				t.textContent = deux( Math.floor( s / 3600 ) % 100 ) + ':' + deux( Math.floor( s / 60 ) % 60 ) + ':' + deux( s % 60 ) + ( m[ 4 ] ? ':' + deux( img % 25 ) : '' );
				requestAnimationFrame( tic );
			}( t0 ) );
		} );
	}

	/* Frise de montage et clap : la tête de lecture avance, la barre du clap se ferme quand ils apparaissent (tout de suite dans l'éditeur ou sans animation) */
	document.querySelectorAll( '.montage, .clap' ).forEach( function ( frise ) {
		if ( editeur || calme || ! ( 'IntersectionObserver' in window ) ) {
			frise.classList.add( 'vu' );
			return;
		}
		var vue = new IntersectionObserver( function ( entrees ) {
			if ( entrees[ 0 ].isIntersecting ) {
				frise.classList.add( 'vu' );
				vue.disconnect();
			}
		}, { threshold: 0.4 } );
		vue.observe( frise );
	} );

	/* Lien « Copier le lien » (#rl-copier, élément Elementor) : partage du téléphone, sinon copie dans le presse-papiers */
	document.addEventListener( 'click', function ( e ) {
		var lien = e.target.closest ? e.target.closest( 'a[href$="#rl-copier"]' ) : null, url = window.location.href.split( '#' )[ 0 ];
		if ( ! lien ) {
			return;
		}
		if ( navigator.share && window.matchMedia( '(hover: none)' ).matches ) {
			navigator.share( { url: url, title: document.title } ).catch( function () {} );
			return;
		}
		if ( navigator.clipboard ) {
			navigator.clipboard.writeText( url ).then( function () {
				var texte = lien.querySelector( '.texte-masque' ) || lien, copie = 'en' === document.documentElement.lang.slice( 0, 2 ) ? 'Link copied' : 'Lien copié';
				lien.classList.add( 'copie' );
				lien.setAttribute( 'title', copie );
				texte.setAttribute( 'role', 'status' );
				texte.textContent = copie;
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
