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
	/*
	 * Aperçu de l'éditeur : l'éditeur ajoute ses styles de base après les feuilles des modèles de cartes (listes,
	 * encart des articles) et les écrase (conteneurs en rangée). Ces feuilles passent en fin de page, sauf celle de
	 * la page modifiée, dont l'éditeur gère lui-même les styles en direct.
	 */
	if ( editeur ) {
		var apercu = ( window.location.search.match( /[?&]elementor-preview=(\d+)/ ) || [] )[ 1 ];
		// Recopie des règles avec « html body » devant : plus prioritaires que les styles de base de l'éditeur,
		// quel que soit l'ordre dans lequel l'éditeur les ajoute.
		var renforcer = function ( feuille ) {
			var regles, texte = '';
			try {
				regles = feuille.sheet && feuille.sheet.cssRules;
			} catch ( e ) {
				return;
			}
			if ( ! regles || feuille.getAttribute( 'data-rl-renforce' ) ) {
				return;
			}
			var ecrire = function ( liste ) {
				var sortie = '';
				Array.prototype.forEach.call( liste, function ( r ) {
					if ( r.selectorText ) {
						sortie += r.selectorText.split( ',' ).map( function ( sel ) {
							return 'html body ' + sel.trim();
						} ).join( ',' ) + '{' + r.style.cssText + '}';
					} else if ( r.cssRules && r.conditionText ) {
						sortie += '@media ' + r.conditionText + '{' + ecrire( r.cssRules ) + '}';
					}
				} );
				return sortie;
			};
			texte = ecrire( regles );
			if ( feuille.media && feuille.media.mediaText && 'all' !== feuille.media.mediaText ) {
				texte = '@media ' + feuille.media.mediaText + '{' + texte + '}';
			}
			var style = document.createElement( 'style' );
			style.setAttribute( 'data-rl-renfort', feuille.href.split( '/' ).pop().split( '?' )[ 0 ] );
			style.textContent = texte;
			document.body.appendChild( style );
			feuille.setAttribute( 'data-rl-renforce', '1' );
		};
		document.querySelectorAll( 'link[rel="stylesheet"][href*="/elementor/css/local-"]' ).forEach( function ( feuille ) {
			if ( apercu && feuille.href.indexOf( '/local-' + apercu + '-' ) !== -1 ) {
				return;
			}
			document.body.appendChild( feuille );
			if ( feuille.sheet ) {
				renforcer( feuille );
			}
			feuille.addEventListener( 'load', function () {
				renforcer( feuille );
			} );
		} );
	}

	/* Filtres sur plusieurs groupes (Métier, Cas d'usage, Format) : onglets, une seule rangée visible à la fois.
	   L'onglet ouvert au départ = le premier groupe où un filtre est choisi. Un point pêche marque ces onglets.
	   Aussi dans l'éditeur (listes réaffichées : voir la surveillance plus bas). */
	var navsFiltres = 0;
	function ongletsFiltres( nav ) {
		var rangees = nav.querySelectorAll( ':scope > ul' ), onglets, ouvert = 0, n = navsFiltres++;
		if ( nav.classList.contains( 'rl-onglets' ) || rangees.length < 2 ) {
			return;
		}
		onglets = document.createElement( 'div' );
		onglets.className = 'onglets-filtres';
		onglets.setAttribute( 'role', 'tablist' );
		onglets.setAttribute( 'aria-label', nav.getAttribute( 'aria-label' ) || '' );
		var boutons = [];
		rangees.forEach( function ( ul, i ) {
			var nom = ul.querySelector( '.nom-filtre' ), choisi = ul.querySelector( '.chip-filtre[aria-current]' ), puces = ul.querySelectorAll( '.chip-filtre' );
			var actif = !! choisi && choisi !== puces[ 0 ];
			var bouton = document.createElement( 'button' );
			bouton.type = 'button';
			bouton.className = 'onglet-filtre';
			bouton.id = 'rl-onglet-' + n + '-' + i;
			bouton.setAttribute( 'role', 'tab' );
			bouton.textContent = nom ? nom.textContent : String( i + 1 );
			if ( actif ) {
				var point = document.createElement( 'span' );
				point.className = 'point-filtre';
				point.setAttribute( 'aria-hidden', 'true' );
				bouton.appendChild( point );
				var lu = document.createElement( 'span' );
				lu.className = 'screen-reader-text';
				lu.textContent = ' (filtre choisi)';
				bouton.appendChild( lu );
				if ( ! boutons.some( function ( b ) { return b.actif; } ) ) {
					ouvert = i;
				}
			}
			var panneau = document.createElement( 'div' );
			panneau.className = 'panneau-filtre';
			panneau.id = 'rl-filtres-' + n + '-' + i;
			panneau.setAttribute( 'role', 'tabpanel' );
			panneau.setAttribute( 'aria-labelledby', bouton.id );
			ul.parentNode.insertBefore( panneau, ul );
			panneau.appendChild( ul );
			bouton.setAttribute( 'aria-controls', panneau.id );
			boutons.push( { el: bouton, ul: panneau, actif: actif } );
			onglets.appendChild( bouton );
		} );
		var choisir = function ( k, focus ) {
			boutons.forEach( function ( b, i ) {
				b.el.setAttribute( 'aria-selected', i === k ? 'true' : 'false' );
				b.el.tabIndex = i === k ? 0 : -1;
				b.ul.hidden = i !== k;
			} );
			if ( focus ) {
				boutons[ k ].el.focus();
			}
		};
		boutons.forEach( function ( b, i ) {
			b.el.addEventListener( 'click', function () { choisir( i ); } );
			b.el.addEventListener( 'keydown', function ( e ) {
				if ( 'ArrowRight' === e.key || 'ArrowLeft' === e.key ) {
					e.preventDefault();
					choisir( ( i + ( 'ArrowRight' === e.key ? 1 : boutons.length - 1 ) ) % boutons.length, true );
				}
			} );
		} );
		nav.insertBefore( onglets, nav.querySelector( '.panneau-filtre' ) );
		nav.classList.add( 'rl-onglets' );
		choisir( ouvert );
	}
	document.querySelectorAll( '.filtres-liste' ).forEach( ongletsFiltres );

	/* Mur de montage : vrai mur maçonné (rangées de 8 px, chaque case garde la hauteur de son contenu : 16:9, 9:16,
	   grande case 16:9 sur 2 colonnes). Sans script : rangées automatiques. Dans l'éditeur, la liste est affichée
	   (et réaffichée à chaque réglage) après le chargement du script : on surveille la page pour caler chaque mur. */
	function murMacon( mur ) {
		if ( mur.rlCaler ) {
			mur.rlCaler();
			return;
		}
		var largeur = 0;
		var caler = function () {
			var ecart = parseFloat( window.getComputedStyle( mur ).columnGap ) || 0;
			mur.classList.add( 'rl-macon' );
			mur.querySelectorAll( ':scope > .mur-case' ).forEach( function ( c ) {
				c.style.setProperty( '--rl-rangs', String( Math.max( 1, Math.ceil( ( c.offsetHeight + ecart ) / 8 ) ) ) );
			} );
		};
		mur.rlCaler = caler;
		caler();
		if ( 'ResizeObserver' in window ) {
			new ResizeObserver( function () {
				if ( mur.offsetWidth !== largeur ) {
					largeur = mur.offsetWidth;
					window.requestAnimationFrame( caler );
				}
			} ).observe( mur );
		}
		// Une image qui arrive change la hauteur de sa case.
		mur.addEventListener( 'load', caler, true );
		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( caler );
		}
		window.addEventListener( 'load', caler );
	}
	document.querySelectorAll( '.mur-montage' ).forEach( murMacon );
	if ( editeur && 'MutationObserver' in window ) {
		var attenteMur = 0;
		new MutationObserver( function () {
			clearTimeout( attenteMur );
			attenteMur = setTimeout( function () {
				document.querySelectorAll( '.mur-montage' ).forEach( murMacon );
				document.querySelectorAll( '.filtres-liste:not(.rl-onglets)' ).forEach( ongletsFiltres );
			}, 150 );
		} ).observe( document.body, { childList: true, subtree: true } );
	}

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

	/* Une copie de chaque élément pour que la boucle soit continue ; elle est cachée aux lecteurs d'écran. */
	function doubler( bande ) {
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
	}

	/* Équipe : la bande est posée dans un rail qu'on fait glisser (doigt, pavé tactile) ; elle avance seule (vitesse de Personnaliser),
	   s'arrête dès qu'on la touche, suit le doigt (avec l'élan du téléphone) et repart 2 secondes après. Sans fin :
	   la bande contient deux fois les portraits. auto = false (« réduire les animations ») : rangée immobile, glissable. */
	function glisser( bande, auto ) {
		var rail = document.createElement( 'div' );
		var reglages = window.rollingReglages || {};
		var vitesse = window.matchMedia( '(max-width: 767px)' ).matches ? ( reglages.defileMobile || 60 ) : ( reglages.defileOrdi || 40 );
		var tenu = false, reprise = 0, dernier = 0, pos = 0, entier = 0, visible = true;
		rail.className = 'rl-glisse';
		rail.tabIndex = 0;
		rail.setAttribute( 'role', 'group' );
		rail.setAttribute( 'aria-label', 'Portraits de l’équipe (faire glisser pour voir la suite)' );
		bande.parentNode.insertBefore( rail, bande );
		rail.appendChild( bande );
		bande.classList.remove( 'rl-defile-statique' );
		bande.style.animation = 'none';
		if ( ! auto ) {
			return;
		}
		// Tout le monde tient dans la largeur : rien ne défile, la rangée est centrée.
		if ( bande.scrollWidth <= rail.clientWidth + 1 ) {
			bande.style.margin = '0 auto';
			return;
		}
		doubler( bande );
		if ( ! rail.parentNode.classList.contains( 'masque-bords' ) ) {
			rail.classList.add( 'rl-glisse-bords' );
		}
		function moitie() {
			return bande.scrollWidth / 2;
		}
		function lacher() {
			tenu = false;
			reprise = window.performance.now() + 2000;
		}
		function pas( t ) {
			var dt = dernier ? Math.min( 64, t - dernier ) : 0;
			dernier = t;
			if ( ! tenu && visible && t > reprise && moitie() > rail.clientWidth ) {
				pos += vitesse * dt / 1000;
				if ( pos >= moitie() ) {
					pos -= moitie();
				}
				// Le navigateur arrondit scrollLeft au pixel entier (à 40 px/s : 1, 1, 0, 1… px par image = à-coups).
				// Partie entière en défilement natif (le doigt garde la main), reste à virgule en translation.
				entier = Math.floor( pos );
				rail.scrollLeft = entier;
				bande.style.transform = 'translate3d(' + ( entier - pos ).toFixed( 3 ) + 'px, 0, 0)';
			}
			window.requestAnimationFrame( pas );
		}
		rail.addEventListener( 'scroll', function () {
			var m = moitie();
			if ( rail.scrollLeft >= m ) {
				rail.scrollLeft -= m;
			}
			// Les déplacements faits par le script arrondissent la position : on ne la relit que si le doigt l'a changée.
			if ( Math.abs( rail.scrollLeft - pos ) > 2 ) {
				pos = rail.scrollLeft;
				bande.style.transform = '';
			}
		}, { passive: true } );
		rail.addEventListener( 'touchstart', function () {
			tenu = true;
			bande.style.transform = '';
			pos = rail.scrollLeft;
			if ( rail.scrollLeft < 40 ) {
				rail.scrollLeft += moitie();
				pos = rail.scrollLeft;
			}
		}, { passive: true } );
		rail.addEventListener( 'touchend', lacher );
		rail.addEventListener( 'touchcancel', lacher );
		// Ordinateur : arrêt au survol.
		rail.addEventListener( 'mouseenter', function () {
			tenu = true;
		} );
		rail.addEventListener( 'mouseleave', function () {
			tenu = false;
			reprise = window.performance.now() + 300;
		} );
		rail.addEventListener( 'focusin', function () {
			tenu = true;
		} );
		rail.addEventListener( 'focusout', lacher );
		if ( 'IntersectionObserver' in window ) {
			new IntersectionObserver( function ( entrees ) {
				visible = entrees[ entrees.length - 1 ].isIntersecting;
			} ).observe( rail );
		}
		window.requestAnimationFrame( pas );
	}

	/* Logos qui défilent : toute bande dont l'animation s'appelle rl-defile */
	var conteneurs = document.querySelectorAll( '.e-flexbox-base, .e-div-block-base, .e-con, .rl-logos' );

	conteneurs.forEach( function ( bande, index ) {
		var style = window.getComputedStyle( bande );
		var anim = style.animationName || '';

		// Repère --rl-defile (non hérité) : la bande est reconnue même quand « réduire les animations » coupe l'animation.
		if ( anim.indexOf( 'rl-defile' ) === -1 && '1' !== style.getPropertyValue( '--rl-defile' ).trim() ) {
			return;
		}

		var zone = bande.parentElement;
		var section = bloc( bande );
		var bouton = section ? section.querySelector( 'a[href$="#rl-pause"]' ) : null;

		bande.classList.add( 'rl-defile' );
		zone.classList.add( 'rl-defile-zone' );
		bande.id = bande.id || 'rl-defile-' + index;

		// Équipes : rangée qui défile par script (ordinateur et mobile ; defile-mobile : mobile seulement), qu'on fait glisser.
		// « Réduire les animations » : plusieurs lignes sur ordinateur, rangée immobile qu'on fait glisser en mobile.
		var mobile = window.matchMedia( '(max-width: 767px)' ).matches;
		if ( bande.matches( '.equipe-defilante, .grille-equipe, .defile-partout' ) || ( mobile && bande.matches( '.defile-mobile' ) ) ) {
			if ( calme && ! mobile ) {
				bande.classList.add( 'rl-defile-statique' );
			} else {
				glisser( bande, ! calme );
			}
			return;
		}

		if ( calme ) {
			bande.classList.add( 'rl-defile-statique' );

			if ( bouton ) {
				bouton.hidden = true;
			}

			return;
		}

		doubler( bande );

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
		v.src = decodeURIComponent( m[ 1 ] ) + '#t=2';
		img.replaceWith( v );
	} );

	/* Son de l'aperçu en boucle (Vimeo, YouTube ou fichier), sans recharger la vidéo */
	function sonFond( fond, actif ) {
		if ( 'VIDEO' === fond.tagName ) {
			fond.muted = ! actif;
			if ( actif ) {
				var p = fond.play();
				if ( p && p.catch ) {
					p.catch( function () {} );
				}
			}
			return;
		}
		var fenetre = fond.contentWindow;
		if ( ! fenetre ) {
			return;
		}
		if ( fond.src.indexOf( 'player.vimeo.com' ) !== -1 ) {
			fenetre.postMessage( JSON.stringify( { method: 'setMuted', value: ! actif } ), 'https://player.vimeo.com' );
			fenetre.postMessage( JSON.stringify( { method: 'setVolume', value: actif ? 1 : 0 } ), 'https://player.vimeo.com' );
		} else {
			fenetre.postMessage( JSON.stringify( { event: 'command', func: actif ? 'unMute' : 'mute', args: [] } ), '*' );
			if ( actif ) {
				fenetre.postMessage( JSON.stringify( { event: 'command', func: 'setVolume', args: [ 100 ] } ), '*' );
			}
		}
	}

	/* Lien vers un fichier vidéo, y compris un fichier servi par Vimeo (player.vimeo.com/progressive_redirect/…/file.mp4) */
	function estFichier( url ) {
		return /\.(?:mp4|m4v|webm|mov|ogv)(?:[?#]|$)|\/progressive_redirect\//i.test( url );
	}

	/* Lecteur d'un lien Vimeo, YouTube ou fichier. fond = aperçu muet en boucle, sans commandes ni titre. */
	function lecteurLien( url, fond, titre ) {
		if ( estFichier( url ) ) {
			return lecteurFichier( url, fond );
		}
		// Toutes les formes de lien Vimeo : vimeo.com/123, /123/abc, player.vimeo.com/video/123, /manage/videos/123, /channels/x/123, /showcase/x/video/123…
		var vimeo = url.match( /vimeo\.com\/.*?videos?\/(\d{5,})(?:\/([0-9a-f]{6,}))?/i ) || url.match( /vimeo\.com\/(?:[a-z]+\/)*?(?:[\w-]+\/)??(\d{5,})(?:\/([0-9a-f]{6,}))?/i ), hash = url.match( /[?&]h=([0-9a-f]+)/ );
		var yt = url.match( /(?:youtu\.be\/|v=|embed\/|shorts\/)([\w-]{11})/ ), el;
		if ( vimeo ) {
			var cle = vimeo[ 2 ] || ( hash ? hash[ 1 ] : '' );
			el = document.createElement( 'iframe' );
			el.src = 'https://player.vimeo.com/video/' + vimeo[ 1 ] + '?' + ( cle ? 'h=' + cle + '&' : '' ) + 'dnt=1&title=0&byline=0&portrait=0&badge=0&vimeo_logo=0&' +
				( fond ? 'background=1&muted=1&loop=1&autopause=0' : 'autoplay=1' );
		} else if ( yt ) {
			el = document.createElement( 'iframe' );
			el.src = 'https://www.youtube-nocookie.com/embed/' + yt[ 1 ] + '?rel=0&playsinline=1&' +
				( fond ? 'autoplay=1&mute=1&loop=1&controls=0&disablekb=1&enablejsapi=1&playlist=' + yt[ 1 ] : 'autoplay=1' );
		} else {
			return lecteurFichier( url, fond );
		}
		el.allow = 'autoplay; fullscreen; picture-in-picture; encrypted-media';
		el.allowFullscreen = true;
		el.title = titre;
		return el;
	}

	function lecteurFichier( url, fond ) {
		var el = document.createElement( 'video' );
		el.src = url;
		el.playsInline = true;
		el.autoplay = true;
		if ( fond ) {
			el.muted = true;
			el.loop = true;
		} else {
			el.controls = true;
		}
		return el;
	}

	/* Vidéo muette à la place d'une image (widget Vidéo, mode « muet ») : chargée quand le cadre est visible, en fondu
	   sur l'image ; un fichier se met en pause hors de l'écran. Rien avec « réduire les animations ». */
	function videoMuette( cadre ) {
		// Widget posé dans un cadre sans la classe cadre-video-muette : il recouvre quand même l'image.
		var hote = cadre.closest( '.elementor-widget' );
		if ( hote && 'absolute' !== window.getComputedStyle( hote ).position ) {
			hote.style.cssText += ';position:absolute;top:0;right:0;bottom:0;left:0;z-index:0;margin:0;pointer-events:none';
		}
		if ( calme || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}
		var fond = null;
		new IntersectionObserver( function ( entrees ) {
			var visible = entrees[ 0 ].isIntersecting;
			if ( visible && ! fond ) {
				fond = lecteurLien( cadre.getAttribute( 'data-rl-video' ), true, '' );
				fond.setAttribute( 'tabindex', '-1' );
				fond.setAttribute( 'aria-hidden', 'true' );
				fond.classList.add( 'video-calque-fond' );
				fond.addEventListener( 'IFRAME' === fond.tagName ? 'load' : 'playing', function () {
					setTimeout( function () { fond.classList.add( 'pret' ); }, 'IFRAME' === fond.tagName ? 700 : 0 );
				} );
				cadre.appendChild( fond );
			} else if ( fond && 'VIDEO' === fond.tagName ) {
				if ( visible ) {
					var p = fond.play();
					if ( p && p.catch ) {
						p.catch( function () {} );
					}
				} else {
					fond.pause();
				}
			}
		}, { threshold: 0.2 } ).observe( cadre );
	}

	/* Miniatures YouTube en haute définition (maxresdefault) : si la vidéo n'en a pas (image grise de 120 px), on revient à hqdefault */
	document.querySelectorAll( 'img[src*="i.ytimg.com"][src*="/maxresdefault."]' ).forEach( function ( img ) {
		var repli = function () {
			if ( img.src.indexOf( '/maxresdefault.' ) !== -1 && ( ! img.naturalWidth || img.naturalWidth <= 120 ) ) {
				img.removeAttribute( 'srcset' );
				img.src = img.src.replace( '/maxresdefault.', '/hqdefault.' );
			}
		};
		if ( img.complete ) {
			repli();
		}
		img.addEventListener( 'load', repli );
		img.addEventListener( 'error', repli );
	} );

	/* Vidéo d'une fiche : chargée seulement au clic (YouTube sans cookie, ou fichier de la médiathèque) */
	document.querySelectorAll( '[data-rl-video]' ).forEach( function ( cadre ) {
		if ( 'muet' === cadre.getAttribute( 'data-rl-mode' ) ) {
			videoMuette( cadre );
			return;
		}
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
		/* Lien (widget Vidéo, « Lien ci-dessous ») : calque posé sur l'image d'affiche du cadre */
		var calque = cadre.classList.contains( 'video-calque' ), hote = calque ? ( cadre.closest( '.elementor-widget' ) || cadre ).parentElement : null;
		if ( calque && 'boucle' === cadre.getAttribute( 'data-rl-mode' ) && ! calme && 'IntersectionObserver' in window ) {
			var vue = new IntersectionObserver( function ( entrees ) {
				if ( ! entrees[ 0 ].isIntersecting || cadre.classList.contains( 'lit' ) ) {
					return;
				}
				vue.disconnect();
				var lancer = function () {
					var fond = lecteurLien( cadre.getAttribute( 'data-rl-video' ), true, bouton.getAttribute( 'aria-label' ) );
					if ( fond ) {
						var pret = function () {
							fond.classList.add( 'pret' );
							hote.classList.add( 'rl-boucle' );
							bouton.setAttribute( 'aria-pressed', 'false' );
						};
						fond.setAttribute( 'tabindex', '-1' );
						fond.setAttribute( 'aria-hidden', 'true' );
						fond.classList.add( 'video-calque-fond' );
						// La vidéo apparaît en fondu une fois prête (iframe chargée, ou fichier qui joue).
						fond.addEventListener( 'IFRAME' === fond.tagName ? 'load' : 'playing', function () { setTimeout( pret, 'IFRAME' === fond.tagName ? 700 : 0 ); } );
						cadre.insertBefore( fond, bouton );
					}
				};
				// Lancé dès que le cadre est visible (pas d'image d'affiche avant le showreel).
				setTimeout( lancer, 0 );
			}, { threshold: 0.35 } );
			vue.observe( cadre );
		}
		bouton.addEventListener( 'click', function () {
			if ( apercu ) {
				apercu.remove();
			}
			var url = cadre.getAttribute( 'data-rl-video' ), yt = url.match( /(?:youtu\.be\/|v=|embed\/|shorts\/)([\w-]{11})/ ), lecteur;
			var fondEnCours = calque ? cadre.querySelector( '.video-calque-fond.pret' ) : null;
			if ( fondEnCours ) {
				// L'aperçu tourne : on active (ou coupe) le son sur place, la vidéo garde son cadrage plein cadre.
				var pastille = cadre.querySelector( '.pastille-son' ), actif = 'true' !== bouton.getAttribute( 'aria-pressed' );
				sonFond( fondEnCours, actif );
				bouton.setAttribute( 'aria-pressed', actif ? 'true' : 'false' );
				if ( pastille ) {
					pastille.textContent = pastille.getAttribute( actif ? 'data-rl-son-off' : 'data-rl-son-on' );
					bouton.setAttribute( 'aria-label', pastille.textContent + ' : ' + ( bouton.getAttribute( 'data-rl-titre' ) || '' ) );
				}
				hote.classList.toggle( 'rl-son', actif );
				return;
			}
			if ( calque ) {
				cadre.querySelectorAll( '.video-calque-fond' ).forEach( function ( f ) { f.remove(); } );
				lecteur = lecteurLien( url, false, bouton.getAttribute( 'aria-label' ) );
				cadre.appendChild( lecteur );
				cadre.classList.add( 'lit' );
				hote.classList.remove( 'rl-boucle' );
				hote.classList.add( 'rl-lit' );
				bouton.remove();
				lecteur.focus();
				return;
			}
			if ( /vimeo\.com\//.test( url ) && ! estFichier( url ) ) {
				lecteur = lecteurLien( url, false, bouton.getAttribute( 'aria-label' ) );
			} else if ( yt ) {
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
			cadre.rlBouton = bouton;
			bouton.remove();
			lecteur.focus();
		} );
	} );

	/* Fiche réalisation : format d'un fichier vidéo lu sur le fichier (si le serveur ne le connaît pas), et boutons de versions */
	function formatRapport( r ) {
		return r < 0.7 ? '9-16' : ( r < 0.9 ? '4-5' : ( r <= 1.15 ? '1-1' : '16-9' ) );
	}
	document.querySelectorAll( '.video-fiche[data-rl-auto]' ).forEach( function ( cadre ) {
		var sonde = document.createElement( 'video' );
		sonde.preload = 'metadata';
		sonde.muted = true;
		sonde.addEventListener( 'loadedmetadata', function () {
			if ( ! sonde.videoWidth || ! sonde.videoHeight ) {
				return;
			}
			var format = formatRapport( sonde.videoWidth / sonde.videoHeight ), avant = cadre.getAttribute( 'data-rl-format' );
			if ( format === avant ) {
				return;
			}
			cadre.setAttribute( 'data-rl-format', format );
			var etiquette = cadre.querySelector( '.bandeau-video' );
			if ( etiquette ) {
				etiquette.textContent = etiquette.textContent.replace( avant.replace( '-', ':' ), format.replace( '-', ':' ) );
			}
			document.querySelectorAll( '.versions-video [data-rl-version="' + cadre.getAttribute( 'data-rl-version' ) + '"]' ).forEach( function ( b ) {
				b.setAttribute( 'data-rl-forme', format );
				b.textContent = b.textContent.replace( avant.replace( '-', ':' ), format.replace( '-', ':' ) );
			} );
		} );
		sonde.src = cadre.getAttribute( 'data-rl-video' );
	} );
	document.querySelectorAll( '.versions-video' ).forEach( function ( groupe ) {
		var zone = groupe.parentElement;
		while ( zone && ! zone.querySelector( '.video-fiche[data-rl-version]' ) ) {
			zone = zone.parentElement;
		}
		if ( ! zone ) {
			return;
		}
		groupe.addEventListener( 'click', function ( e ) {
			var choix = e.target.closest( 'button[data-rl-version]' );
			if ( ! choix ) {
				return;
			}
			var rang = choix.getAttribute( 'data-rl-version' );
			groupe.querySelectorAll( 'button[data-rl-version]' ).forEach( function ( b ) {
				b.setAttribute( 'aria-pressed', b === choix ? 'true' : 'false' );
			} );
			zone.querySelectorAll( '.video-fiche[data-rl-version]' ).forEach( function ( cadre ) {
				var montre = cadre.getAttribute( 'data-rl-version' ) === rang;
				if ( ! montre && ! cadre.hidden ) {
					// Version quittée : la lecture s'arrête et le bouton lecture revient.
					cadre.querySelectorAll( 'video' ).forEach( function ( v ) { v.pause(); } );
					if ( cadre.classList.contains( 'lit' ) ) {
						cadre.querySelectorAll( 'iframe, video:not(.apercu-video)' ).forEach( function ( l ) { l.remove(); } );
						cadre.classList.remove( 'lit' );
						if ( cadre.rlBouton ) {
							cadre.appendChild( cadre.rlBouton );
						}
					}
				}
				cadre.hidden = ! montre;
			} );
		} );
	} );

	/* Extraits vidéo du mur de montage : muets, en boucle, seulement quand ils sont à l'écran (rien avec « réduire les animations ») */
	if ( ! calme && ! editeur && 'IntersectionObserver' in window ) {
		var extraits = new IntersectionObserver( function ( entrees ) {
			entrees.forEach( function ( e ) {
				var v = e.target;
				if ( e.isIntersecting ) {
					v.preload = 'auto';
					var p = v.play();
					if ( p && p.catch ) {
						p.catch( function () {} );
					}
				} else {
					v.pause();
				}
			} );
		}, { threshold: 0.25 } );
		document.querySelectorAll( '.extrait-video video' ).forEach( function ( v ) { extraits.observe( v ); } );
	}

	/* Vignettes des listes : sur un écran tactile, celle qui passe au centre de l'écran « démarre » */
	if ( ! calme && ! editeur && window.matchMedia( '(hover: none)' ).matches && 'IntersectionObserver' in window ) {
		var centre = new IntersectionObserver( function ( entrees ) {
			entrees.forEach( function ( e ) { e.target.classList.toggle( 'joue', e.isIntersecting ); } );
		}, { rootMargin: '-40% 0px -40% 0px' } );
		document.querySelectorAll( 'a.vignette-lien' ).forEach( function ( v ) { centre.observe( v ); } );
	}

	/* Mur de montage : au survol d'une réalisation (souris seulement), sa vidéo se lance sans le son dans la vignette ;
	   elle se met en pause quand la souris part. Rien sur écran tactile, dans l'éditeur ni avec « réduire les animations ». */
	if ( ! calme && ! editeur && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
		document.querySelectorAll( '.mur-montage .mur-case[data-rl-survol]' ).forEach( function ( c ) {
			var cadre = c.querySelector( '.cadre-photo' ), lecteur = null, minuteur = 0;
			if ( ! cadre ) {
				return;
			}
			c.addEventListener( 'mouseenter', function () {
				clearTimeout( minuteur );
				if ( lecteur ) {
					if ( 'VIDEO' === lecteur.tagName ) {
						var p = lecteur.play();
						if ( p && p.catch ) {
							p.catch( function () {} );
						}
					}
					c.classList.add( 'rl-survol' );
					return;
				}
				// Petit délai : un simple passage de la souris ne charge rien.
				minuteur = setTimeout( function () {
					lecteur = lecteurLien( c.getAttribute( 'data-rl-survol' ), true, '' );
					lecteur.classList.add( 'apercu-survol' );
					lecteur.setAttribute( 'aria-hidden', 'true' );
					lecteur.setAttribute( 'tabindex', '-1' );
					lecteur.addEventListener( 'IFRAME' === lecteur.tagName ? 'load' : 'playing', function () {
						setTimeout( function () { lecteur.classList.add( 'pret' ); }, 'IFRAME' === lecteur.tagName ? 600 : 0 );
					} );
					cadre.appendChild( lecteur );
					c.classList.add( 'rl-survol' );
				}, 250 );
			} );
			c.addEventListener( 'mouseleave', function () {
				clearTimeout( minuteur );
				c.classList.remove( 'rl-survol' );
				if ( lecteur && 'VIDEO' === lecteur.tagName ) {
					lecteur.pause();
				} else if ( lecteur ) {
					// YouTube / Vimeo : retiré (rien ne tourne en arrière-plan).
					lecteur.remove();
					lecteur = null;
				}
			} );
		} );
	}

	/* Mur de montage (page Réalisations) : les cartes montent une à une en sortant du flou, une seule fois.
	   Les cartes qui arrivent ensemble se suivent de 90 ms. Sans script, sans animation : tout reste visible. */
	if ( ! calme && ! editeur && 'IntersectionObserver' in window ) {
		document.querySelectorAll( '.mur-montage' ).forEach( function ( mur ) {
			var vues = new IntersectionObserver( function ( entrees, obs ) {
				var rang = 0;
				entrees.forEach( function ( e ) {
					if ( e.isIntersecting ) {
						e.target.style.animationDelay = ( rang++ * 90 ) + 'ms';
						e.target.classList.add( 'vu' );
						obs.unobserve( e.target );
					}
				} );
			}, { threshold: 0.12 } );
			mur.classList.add( 'rl-anime' );
			mur.querySelectorAll( ':scope > .mur-case' ).forEach( function ( c ) { vues.observe( c ); } );
		} );
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

	/* Tableau des formules replié (landing) : le bouton « Comparer les formules en détail » (#rl-comparer) l'ouvre et le referme.
	   Sans script (et dans l'éditeur), le tableau reste visible. */
	document.querySelectorAll( '.tableau-depliable' ).forEach( function ( zone, n ) {
		var bouton = zone.querySelector( 'a[href="#rl-comparer"]' );
		var tableau = zone.querySelector( '.offres-tableau' );
		if ( ! bouton || ! tableau ) {
			return;
		}
		tableau.id = tableau.id || 'rl-tableau-' + ( n + 1 );
		bouton.setAttribute( 'role', 'button' );
		bouton.setAttribute( 'aria-expanded', 'false' );
		bouton.setAttribute( 'aria-controls', tableau.id );
		zone.classList.add( 'rl-replie' );
		bouton.addEventListener( 'click', function ( e ) {
			var ouvrir = zone.classList.contains( 'rl-replie' );
			e.preventDefault();
			zone.classList.toggle( 'rl-replie', ! ouvrir );
			zone.classList.toggle( 'rl-deplie', ouvrir );
			bouton.setAttribute( 'aria-expanded', ouvrir ? 'true' : 'false' );
		} );
		bouton.addEventListener( 'keydown', function ( e ) {
			if ( ' ' === e.key ) {
				e.preventDefault();
				bouton.click();
			}
		} );
	} );

	/* Ancre vers les offres (#offres) : arriver pile sur les prix (tableau sur ordinateur, cartes de prix en mobile), sous l'en-tête */
	function ciblePrix( id ) {
		var section = id ? document.getElementById( id ) : null;
		if ( ! section ) {
			return null;
		}
		// Les cartes de prix du mobile d'abord (le tableau du mobile n'affiche pas les prix), sinon le tableau.
		return [ '.offres-mobile', '.offres-tableau' ].map( function ( sel ) {
			return [].slice.call( section.querySelectorAll( sel ) ).filter( function ( e ) {
				return e.getBoundingClientRect().height > 0 && ! e.closest( '.tableau-depliable' );
			} )[ 0 ];
		} ).filter( Boolean )[ 0 ] || null;
	}
	function allerPrix( cible, doux ) {
		cible.style.scrollMarginTop = '24px';
		cible.scrollIntoView( { block: 'start', behavior: doux && ! calme ? 'smooth' : 'auto' } );
	}
	document.addEventListener( 'click', function ( e ) {
		var a = e.target.closest ? e.target.closest( 'a[href*="#"]' ) : null, cible;
		if ( ! a || ! a.hash || a.origin + a.pathname !== window.location.origin + window.location.pathname ) {
			return;
		}
		cible = ciblePrix( decodeURIComponent( a.hash.slice( 1 ) ) );
		if ( cible ) {
			e.preventDefault();
			allerPrix( cible, true );
			if ( window.history.pushState ) {
				window.history.pushState( null, '', a.hash );
			}
		}
	} );
	if ( window.location.hash && ciblePrix( decodeURIComponent( window.location.hash.slice( 1 ) ) ) ) {
		window.addEventListener( 'load', function () {
			window.setTimeout( function () {
				allerPrix( ciblePrix( decodeURIComponent( window.location.hash.slice( 1 ) ) ), false );
			}, 60 );
		} );
	}

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

	/* Calendrier HubSpot : le cadre (zone-calendrier) montre un calendrier squelette « Chargement des disponibilités… »
	   jusqu'à ce que HubSpot ait fini (message de son calendrier, ou chargement de la page + 2,5 s), 15 s au plus. */
	if ( ! editeur ) {
		document.querySelectorAll( '.meetings-iframe-container, iframe[src*="meetings.hubspot.com"]' ).forEach( function ( hubspot ) {
			var zone = hubspot.closest( '.zone-calendrier' );
			if ( ! zone || zone.rlAttente ) {
				return;
			}
			zone.rlAttente = true;
			var fini = false;
			var fin = function () {
				if ( ! fini ) {
					fini = true;
					zone.classList.remove( 'rl-cal-attente' );
					zone.removeAttribute( 'aria-busy' );
				}
			};
			var dejaCharge = function ( cadre ) {
				try {
					return performance.getEntriesByName( cadre.src ).some( function ( e ) { return e.responseEnd > 0; } );
				} catch ( e ) {
					return false;
				}
			};
			var suivre = function () {
				zone.querySelectorAll( 'iframe' ).forEach( function ( cadre ) {
					if ( cadre.rlSuivi ) {
						return;
					}
					cadre.rlSuivi = true;
					cadre.addEventListener( 'load', function () { setTimeout( fin, 2500 ); } );
					if ( dejaCharge( cadre ) ) {
						setTimeout( fin, 1500 );
					}
				} );
			};
			zone.classList.add( 'rl-cal-attente' );
			zone.setAttribute( 'aria-busy', 'true' );
			setTimeout( fin, 15000 );
			suivre();
			new MutationObserver( suivre ).observe( zone, { childList: true, subtree: true } );
			// Le calendrier HubSpot prévient la page quand il s'est affiché (il envoie sa hauteur).
			window.addEventListener( 'message', function ( e ) {
				if ( /(^|\.)hubspot\.com$/.test( ( e.origin || '' ).replace( /^https?:\/\//, '' ) ) ) {
					zone.querySelectorAll( 'iframe' ).forEach( function ( cadre ) {
						if ( cadre.contentWindow === e.source ) {
							setTimeout( fin, 300 );
						}
					} );
				}
			} );
		} );
	}

	/* Les liens #rl-… ne changent jamais l'adresse */
	document.addEventListener( 'click', function ( e ) {
		var lien = e.target.closest ? e.target.closest( 'a[href*="#rl-"]' ) : null;

		if ( lien ) {
			e.preventDefault();
		}
	} );
}() );
