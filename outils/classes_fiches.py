"""Classes globales des fiches et des listes (livraison 3). Ajoutées au groupe « Fiches et listes » de source/classes.json.

Usage : python3 -I outils/classes_fiches.py
"""
import json
import os

ICI = os.path.dirname(os.path.abspath(__file__))

REPERE = "Repère sans style : le thème masque ce bloc quand le champ « %s » du contenu est vide."
CLASSES = [
    # Repères des parties facultatives (aucun style : seulement lus par le thème).
    *[(('si-' + c), REPERE % c, '--rl-repere: 1;') for c in ['chapo', 'logo', 'secteur', 'equipes', 'formats', 'duree', 'chiffres', 'citation', 'auteur', 'poste', 'portrait', 'videos', 'generique', 'besoin', 'resultat', 'contexte', 'pages', 'type-doc', 'resume', 'intervenants', 'image', 'avant', 'apres', 'client', 'produit', 'accompagnement']],
    ('si-mise-a-jour', "Repère sans style : affiché seulement quand l'article a été modifié après le jour de sa publication.", '--rl-repere: 1;'),
    ('sans-citation', "Repère sans style : affiché seulement quand le témoignage n'a pas de citation.", '--rl-repere: 1;'),
    ('si-tonalite--bleu', "Repère sans style : affiché seulement quand la couleur de l'étiquette est « bleu ».", '--rl-repere: 1;'),
    ('sans-tonalite--bleu', "Repère sans style : affiché quand la couleur de l'étiquette n'est pas « bleu » (orange par défaut).", '--rl-repere: 1;'),

    # Cartes des listes.
    ('carte-liste', "Carte d'une liste : image, puis textes empilés.", 'display: flex; flex-direction: column; gap: 16px; padding: 0;'),
    ('pile-serree', 'Petits textes empilés et serrés (étiquette + titre).', 'display: flex; flex-direction: column; gap: 6px; padding: 0;'),
    ('vignette-lien', "Vignette cliquable : au survol (ou au centre de l'écran sur mobile), l'image zoome et la barre de lecture avance.",
     "overflow: hidden; & .image-couvrante { transition: transform 1.4s cubic-bezier(0.2,0.8,0.2,1); } & .bouton-lecture { transition: transform 0.4s cubic-bezier(0.2,0.8,0.2,1); } "
     "&::after { content: ''; position: absolute; z-index: 3; left: 8px; bottom: 8px; width: 0; height: 5px; border-radius: 0 0 0 20px; background: #FFD1BF; transition: width 0.4s ease; } "
     "&:hover .image-couvrante, &.joue .image-couvrante { transform: scale(1.06); } &:hover .bouton-lecture, &.joue .bouton-lecture { transform: scale(1.14); } &:hover::after, &.joue::after { width: calc(100% - 16px); transition: width 7s linear; }"),
    ('cadre-16-9', 'Cadre au format 16:9.', 'aspect-ratio: 16 / 9;'),
    ('cadre-16-10', 'Cadre au format 16:10.', 'aspect-ratio: 16 / 10;'),
    ('cadre-4-3', 'Cadre au format 4:3.', 'aspect-ratio: 4 / 3;'),
    ('lien-titre', "Titre cliquable : garde sa couleur, souligné au survol.", '& a { color: inherit; text-decoration: none; } & a:hover { text-decoration: underline; }'),
    ('avant-apres', 'Contenu de départ → vidéo produite (la flèche est ajoutée devant le dernier élément).',
     "display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; gap: 8px 10px; padding: 0; font-family: Hanken Grotesk; font-size: 15px; line-height: 1.4; font-weight: 700; color: var(--rl-texte, var(--encre)); & > :last-child::before { content: '→'; margin-right: 10px; color: #E0562A; }"),
    ('temoin', 'Carte de témoignage (avec carte-verre) : logo, citation, personne, lien. Se soulève au survol, logo en couleur.',
     "gap: 14px; padding: 26px; transition: transform 0.45s cubic-bezier(0.2,0.8,0.2,1), box-shadow 0.45s ease; &:hover { transform: translateY(-6px); } &:hover .temoin-logo, &:focus-within .temoin-logo { filter: none; opacity: 1; }"),
    ('temoin-une', "Témoignage à la une : sur deux colonnes, fond lilas et pêche, citation plus grande.",
     "grid-column: span 2; padding: 34px 38px; background: radial-gradient(70% 60% at 10% 0%, rgba(255,255,255,0.95), rgba(255,255,255,0) 72%), linear-gradient(160deg, rgba(237,230,255,0.9) 0%, rgba(255,226,212,0.7) 100%); & .temoin-citation { font-size: clamp(20px, 1.9vw, 25px); } @media(--mobile) { grid-column: auto; padding: 26px; }"),
    ('temoin-haut', "Haut d'une carte de témoignage : logo à gauche, étiquette à droite.", 'display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 12px; min-height: 34px; padding: 0;'),
    ('temoin-logo', "Logo d'un client dans une carte : gris, en couleur au survol de la carte.", 'height: 28px; width: auto; max-width: 140px; object-fit: contain; filter: grayscale(1) contrast(1.1); opacity: 0.85; transition: filter 0.35s ease, opacity 0.35s ease;'),
    ('temoin-citation', "Citation d'une carte, précédée d'un grand guillemet en dégradé.",
     "display: block; font-family: Bricolage Grotesque; font-size: 18px; line-height: 1.5; font-weight: 600; letter-spacing: -0.015em; color: var(--rl-texte, var(--encre)); &::before { content: '“'; display: block; height: 30px; margin-bottom: 4px; font-size: 72px; line-height: 1; background-image: linear-gradient(95deg, #5B3BEA 0%, #8E4DE6 42%, #E0562A 100%); -webkit-background-clip: text; background-clip: text; color: transparent; }"),
    ('temoin-auteur', "Personne qui témoigne : portrait, nom et poste, séparée par un filet.", 'display: flex; flex-direction: row; align-items: center; gap: 12px; margin-top: auto; padding: 16px 0 0; border-top: 1px solid rgba(15,15,18,0.08); & .portrait { width: 48px; height: 48px; }'),
    ('temoin-lien', "Lien texte avec flèche (« Lire le cas »).", "display: inline-flex; width: fit-content; align-items: center; gap: 8px; min-height: 32px; padding: 0; font-family: Hanken Grotesk; font-size: 15px; font-weight: 700; color: #4A2BB8; background-color: transparent; &::after { content: '→'; } &:hover { text-decoration: underline; }"),
    ('mur-temoins', 'Mur de témoignages : 3 colonnes, 2 sous 980 px, 1 sur mobile.', 'display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 22px; align-items: stretch; padding: 0; @media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); } @media(--mobile) { grid-template-columns: minmax(0, 1fr); }'),

    # Fiche témoignage.
    ('cas-logo', 'Logo du client en haut de la fiche, sur fond blanc.', 'width: fit-content; display: flex; align-items: center; justify-content: center; min-height: 64px; padding: 0 20px; border-radius: 18px; background: #FFFFFF; box-shadow: 0 12px 30px -20px rgba(76,48,170,0.4); & img { height: 40px; width: auto; }'),
    ('cas-bandeau', "Bandeau horizontal des infos du projet (secteur, équipes, formats, durée) et bouton.",
     'display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 18px 32px; padding: 20px 20px 20px 28px; border-radius: 26px; background: rgba(255,255,255,0.7); box-shadow: inset 0 1px 0 #FFFFFF, inset 0 0 0 1px rgba(91,59,234,0.12), 0 24px 50px -34px rgba(76,48,170,0.4); @media(--tablet) { padding: 22px; }'),
    ('cas-bandeau-infos', 'Infos du projet sur une ligne, séparées par des filets ; 2 × 2 sous 980 px.',
     'width: auto; display: flex; flex-direction: row; flex-wrap: wrap; gap: 14px 0; padding: 0; & > * { width: auto; display: flex; flex-direction: column; gap: 4px; padding: 0 26px; border-left: 1px solid rgba(91,59,234,0.16); } & > :first-child { padding-left: 0; border-left: 0; } @media(--tablet) { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 20px; width: 100%; & > * { padding: 0; border-left: 0; } }'),
    ('cas-info-valeur', "Valeur d'une info du projet.", 'display: block; font-family: Hanken Grotesk; font-size: 16px; line-height: 1.4; font-weight: 600; color: var(--rl-texte, var(--encre));'),
    ('cas-bandeau-appel', '« Un projet similaire ? » et son bouton.', 'width: auto; display: flex; flex-direction: row; align-items: center; gap: 14px; padding: 0; @media(--mobile) { flex-direction: column; align-items: flex-start; gap: 10px; }'),
    ('plaque-libre', "Plaque en verre de chiffres clés en nombre libre (1 à n) : colonnes automatiques, 2 sous 980 px ; un chiffre impair seul en mobile prend la largeur.",
     "position: relative; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); row-gap: 22px; padding: 28px 0; overflow: hidden; border-radius: 30px; background: radial-gradient(70% 60% at 15% 0%, rgba(255,255,255,0.95), rgba(255,255,255,0) 72%), linear-gradient(150deg, rgba(255,255,255,0.78), rgba(255,255,255,0.46)); backdrop-filter: blur(24px) saturate(170%); box-shadow: inset 0 1px 0 #FFFFFF, 0 30px 70px -34px rgba(76,48,170,0.34); "
     "& > * { display: flex; flex-direction: column; gap: 10px; padding: 4px 24px; box-shadow: -1px 0 0 rgba(91,59,234,0.16); } & .chiffre-verre img { height: 60px; width: auto; } & .espace-chiffre { display: inline-block; width: 12px; } "
     "@media(--tablet) { grid-template-columns: repeat(2, minmax(0, 1fr)); & > :last-child:nth-child(odd) { grid-column: 1 / -1; } & .chiffre-verre img { height: 46px; } } @media(--mobile) { & > * { padding: 4px 16px; } & .chiffre-verre img { height: 40px; } }"),
    ('cas-grille', "Fiche témoignage : citation à gauche, récit à droite ; sans citation, le récit se centre.",
     'display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.25fr); gap: 56px; align-items: start; padding: 0; & > :only-child { grid-column: 1 / -1; width: 100%; max-width: 820px; justify-self: center; } @media(--tablet) { grid-template-columns: minmax(0, 1fr); gap: 40px; }'),
    ('cas-citation', 'Citation de la fiche témoignage (avec carte-verre et temoin) : fond lilas et pêche, reste visible pendant la lecture du récit sur ordinateur.', 'position: sticky; top: 120px; padding: 34px 38px; background: radial-gradient(70% 60% at 10% 0%, rgba(255,255,255,0.95), rgba(255,255,255,0) 72%), linear-gradient(160deg, rgba(237,230,255,0.9) 0%, rgba(255,226,212,0.7) 100%); & .temoin-citation { font-size: clamp(20px, 1.9vw, 25px); } @media(--tablet) { position: static; } @media(--mobile) { padding: 26px; }'),
    ('cas-recit', 'Récit de la fiche : sections empilées.', 'display: flex; flex-direction: column; gap: 44px; padding: 0;'),

    # Fiche réalisation : vidéo, transcription, générique.
    ('video-fiche', "Vidéo de la fiche : affiche au format 16:9, la vidéo se charge au clic.", 'aspect-ratio: 16 / 9; & iframe, & video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; z-index: 4; }'),
    ('lecture-centre', 'Bouton lecture centré (vidéo de la fiche), sans mouvement.', 'position: absolute; z-index: 3; top: 50%; left: 50%; width: 84px; height: 84px; margin: -42px 0 0 -42px; padding: 0; border: 0; border-radius: 50%; background: transparent; cursor: pointer; filter: drop-shadow(0 8px 14px rgba(255,138,91,0.55)); & img { display: block; width: 84px; height: 84px; } @media(--mobile) { width: 64px; height: 64px; margin: -32px 0 0 -32px; & img { width: 64px; height: 64px; } }'),
    ('transcription', 'Transcription : une ligne « + Lire la transcription » qui s’ouvre au clic.',
     "display: block; & summary { display: inline-flex; align-items: center; gap: 8px; min-height: 44px; font-family: Hanken Grotesk; font-size: 15px; font-weight: 700; color: #4A2BB8; cursor: pointer; list-style: none; } & summary::-webkit-details-marker { display: none; } & summary::before { content: '+'; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; color: #5B3BEA; background: #EDE6FF; } &[open] summary::before { content: '–'; } & p { margin: 10px 0 0; font-size: 15px; line-height: 1.6; color: var(--rl-paragraphe, var(--paragraphe)); }"),
    ('generique-filet', 'Filet du générique : timecode entre deux traits.', "display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 14px; padding: 0; &::before, &::after { content: ''; flex: 1; max-width: 220px; height: 1px; background: rgba(91,59,234,0.22); }"),
    ('generique-liste', "Lignes du générique : intitulé à droite, valeur à gauche ; elles montent une à une à l'apparition.",
     'display: grid; gap: 16px; margin: 48px 0 0; padding: 0; & > div { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; align-items: baseline; } & dt { text-align: right; font-family: Hanken Grotesk; font-size: 12px; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; color: var(--rl-legende, var(--legende)); } & dd { margin: 0; text-align: left; font-family: Bricolage Grotesque; font-size: 22px; font-weight: 650; letter-spacing: -0.02em; color: var(--rl-texte, var(--encre)); } @media(--mobile) { & > div { grid-template-columns: minmax(0, 1fr); gap: 4px; } & dt, & dd { text-align: center; } & dd { font-size: 20px; } }'),
    ('generique-fin', '« Une production Rolling » et le logo.', 'display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 12px; padding: 0; & img { height: 24px; width: auto; }'),

    # Compte à rebours des webinars.
    ('compte-rebours', "Compte à rebours d'un webinar : avant, pendant (« En direct ») et après (« Terminé »).",
     'display: block; & .cr-direct, & .cr-fin { display: none; } &.en-direct .cr-avant, &.termine .cr-avant { display: none; } &.en-direct .cr-direct { display: inline-flex; } &.termine .cr-fin { display: block; } & p { margin: 0; }'),
    ('cr-pilule', 'Pilule du compte à rebours, au style des timecodes.', 'display: inline-flex; flex-direction: row; align-items: center; gap: 16px; padding: 10px 22px 10px 16px; border-radius: 999px; background: linear-gradient(170deg, rgba(255,255,255,0.95), rgba(255,255,255,0.6)); box-shadow: inset 0 1px 0 #FFFFFF, inset 0 0 0 1px rgba(91,59,234,0.16), 0 12px 26px -18px rgba(76,48,170,0.5); @media(--mobile) { gap: 10px; padding: 8px 14px 8px 12px; }'),
    ('cr-point', 'Point pêche devant « Début dans ».', 'flex-shrink: 0; width: 8px; height: 8px; border-radius: 50%; background: #FF8A5B; box-shadow: 0 0 0 4px rgba(255,138,91,0.22);'),
    ('cr-label', '« Début dans ».', 'font-family: Hanken Grotesk; font-size: 12px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: var(--rl-legende, var(--legende));'),
    ('cr-temps', 'Temps restant : « 22j 04:32:10 ».', 'display: inline-flex; flex-direction: row; align-items: baseline; gap: 16px; font-family: Bricolage Grotesque; font-size: 20px; font-weight: 650; letter-spacing: -0.01em; color: #5B3BEA; font-variant-numeric: tabular-nums; & b { font-weight: 650; } & .cr-unite { margin-left: 2px; font-size: 0.72em; } & .cr-deux { margin: 0 5px; opacity: 0.55; } @media(--mobile) { font-size: 18px; gap: 12px; }'),
    ('cr-direct', '« En direct maintenant », avec un point rouge qui clignote.',
     "align-items: center; gap: 10px; padding: 9px 18px 9px 14px; border-radius: 999px; font-family: Hanken Grotesk; font-size: 13px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: var(--rl-texte, var(--encre)); background: linear-gradient(170deg, rgba(255,255,255,0.95), rgba(255,255,255,0.6)); box-shadow: inset 0 1px 0 #FFFFFF, inset 0 0 0 1px rgba(91,59,234,0.16); & .cr-rec { width: 8px; height: 8px; border-radius: 50%; background: #FF3B30; box-shadow: 0 0 0 3px rgba(255,59,48,0.28); animation: rl-clignote 1.6s ease-in-out infinite; }"),
    ('cr-fin', 'Texte affiché une fois le webinar terminé.', 'font-family: Hanken Grotesk; font-size: 15px; line-height: 1.5; color: var(--rl-paragraphe, var(--paragraphe));'),

    # Webinars, guides, replays.
    ('carte-webinar', 'Webinar dans la liste « Les suivants » (avec carte-verre) : date, textes, bouton.', 'display: grid; grid-template-columns: 132px minmax(0, 1fr) auto; gap: 28px; align-items: center; padding: 22px 28px 22px 22px; @media(--tablet) { grid-template-columns: 96px minmax(0, 1fr); gap: 20px; & > :last-child { grid-column: 1 / -1; justify-self: start; } } @media(--mobile) { padding: 16px; }'),
    ('carte-webinar-une', 'Prochain webinar (avec carte-verre) : image à gauche, textes à droite.', 'display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); gap: 36px; align-items: center; padding: 12px 40px 12px 12px; @media(--tablet) { grid-template-columns: minmax(0, 1fr); padding: 12px 12px 26px; }'),
    ('webinar-date', 'Date du webinar dans un carré en dégradé (jour, mois, heure).', 'display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; min-height: 132px; padding: 0; border-radius: 22px; text-align: center; color: #FFFFFF; background: radial-gradient(120% 100% at 20% 0%, #8E4DE6 0%, #5B3BEA 55%, #3434E0 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.35), 0 18px 34px -18px rgba(52,52,224,0.6); @media(--tablet) { min-height: 96px; }'),
    ('webinar-jour', 'Jour (grand chiffre).', 'display: block; font-family: Bricolage Grotesque; font-size: 46px; line-height: 1; font-weight: 650; letter-spacing: -0.04em; color: #FFFFFF; @media(--tablet) { font-size: 34px; }'),
    ('webinar-mois', 'Mois abrégé.', 'display: block; font-family: Hanken Grotesk; font-size: 13px; line-height: 1.3; font-weight: 800; letter-spacing: 0.14em; text-transform: uppercase; color: #FFFFFF;'),
    ('webinar-heure', 'Heure.', 'display: block; margin-top: 6px; font-family: Hanken Grotesk; font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.92);'),
    ('carte-guide', 'Guide téléchargeable : image, thème et pages, titre, résumé, bouton.', 'display: flex; flex-direction: column; gap: 14px; padding: 0; & .bouton-verre { align-self: flex-start; }'),
    ('filtres-liste', 'Filtres au-dessus d’une liste (boutons arrondis).', 'display: flex; flex-direction: column; gap: 12px; margin-bottom: 40px; & ul { margin: 0; padding: 0; list-style: none; }'),
    ('chip-filtre', 'Bouton de filtre ; le filtre actif est foncé.', 'display: inline-flex; align-items: center; min-height: 40px; padding: 6px 16px; border-radius: 999px; font-family: Hanken Grotesk; font-size: 14px; font-weight: 600; color: #2A2840; text-decoration: none; background: linear-gradient(170deg, rgba(255,255,255,0.94), rgba(255,255,255,0.56)); box-shadow: inset 0 1px 0 #FFFFFF, inset 0 0 0 1px rgba(15,15,18,0.08); &:hover { color: #4A2BB8; } &[aria-current] { color: #FFFFFF; background: #0F0F12; box-shadow: none; }'),
    ('liste-plus', 'Bouton « Voir plus » sous une liste.', 'display: flex; justify-content: center; margin: 48px 0 0;'),
    ('ariane', "Fil d'Ariane (Accueil › Section › Page).", "display: block; & ol { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; font-family: Hanken Grotesk; font-size: 14px; color: var(--rl-legende, var(--legende)); } & li + li::before { content: '›'; margin-right: 6px; color: #8F89A8; } & a { display: inline-flex; min-height: 24px; align-items: center; color: var(--rl-paragraphe, var(--paragraphe)); text-decoration: none; } & a:hover { color: #5B3BEA; text-decoration: underline; }"),
    # Article de blog.
    ('article-grille', "Article : texte à gauche, colonne d'outils à droite (dessous sous 980 px).", 'display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 56px; align-items: start; padding: 0; @media(--tablet) { grid-template-columns: minmax(0, 1fr); gap: 40px; }'),
    ('article-principal', "Colonne principale de l'article (largeur de lecture).", 'display: flex; flex-direction: column; gap: 0; min-width: 0; max-width: 780px; padding: 0;'),
    ('article-cote', "Colonne d'outils de l'article.", 'align-self: stretch; padding: 0;'),
    ('article-cote-colle', "Outils de l'article qui suivent la lecture (sur ordinateur).", 'position: sticky; top: 110px; display: flex; flex-direction: column; gap: 16px; padding: 0; @media(--tablet) { position: static; }'),
    ('carte-outils', 'Petite carte d’outils (avec carte-verre) : marges réduites.', 'gap: 6px; padding: 20px 22px;'),
    ('carte-sombre', "Carte foncée (avec carte-verre et section-sombre) : appel à l'action de la colonne d'outils.", 'padding: 24px; background: linear-gradient(160deg, #1C1730, #0F0F12);'),
    ('article-texte', "Texte d'un article : paragraphes, intertitres, listes, images, liens.",
     "display: block; margin-top: 40px; & > p, & > ul, & > ol { margin: 20px 0 0; font-family: Hanken Grotesk; font-size: 18px; line-height: 1.7; color: var(--rl-paragraphe, var(--paragraphe)); } & strong { color: var(--rl-texte, var(--encre)); } & a { color: #5B3BEA; text-decoration: underline; } & > h2 { margin: 52px 0 0; font-family: Bricolage Grotesque; font-weight: 650; font-size: clamp(26px, 2.6vw, 34px); line-height: 1.15; letter-spacing: -0.04em; color: var(--rl-texte, var(--encre)); scroll-margin-top: 110px; } & > h3 { margin: 32px 0 0; font-family: Bricolage Grotesque; font-weight: 650; font-size: 23px; letter-spacing: -0.035em; color: var(--rl-texte, var(--encre)); } & img { max-width: 100%; height: auto; border-radius: 22px; } & figure { margin: 32px 0; } & blockquote { margin: 28px 0; padding: 20px 24px; border-left: 3px solid #5B3BEA; border-radius: 0 18px 18px 0; background: #EDE6FF; font-family: Bricolage Grotesque; font-size: 20px; line-height: 1.5; font-weight: 600; } @media(--mobile) { & > p, & > ul, & > ol { font-size: 17px; } }"),
    ('sommaire', "Sommaire de l'article : le chapitre lu est en violet.", "display: grid; gap: 2px; margin: 8px 0 0; padding: 0; list-style: none; & a { display: flex; min-height: 34px; align-items: center; padding: 4px 0 4px 14px; border-left: 2px solid rgba(15,15,18,0.1); font-family: Hanken Grotesk; font-size: 15px; line-height: 1.35; color: var(--rl-paragraphe, var(--paragraphe)); text-decoration: none; } & a:hover, & a.actif { color: #5B3BEA; border-left-color: #5B3BEA; }"),
    ('partage', "Rangée des boutons de partage de l'article.", 'display: flex; flex-direction: row; flex-wrap: wrap; gap: 8px; margin: 12px 0 0; padding: 0; list-style: none;'),
    ('partage-bouton', "Bouton rond de partage (lien) : icône en image + texte masqué. Au survol, fond violet et icône blanche.",
     'display: inline-flex; flex-direction: row; align-items: center; justify-content: center; width: 44px; height: 44px; padding: 0; border: 0; border-radius: 50%; color: #0F0F12; background: #FFFFFF; box-shadow: inset 0 0 0 1px rgba(15,15,18,0.1), 0 6px 14px -10px rgba(70,40,150,0.4); text-decoration: none; cursor: pointer; & img { display: block; width: 18px; height: 18px; } &:hover, &:focus-visible { background: #5B3BEA; } &:hover img, &:focus-visible img { filter: brightness(0) invert(1); } &.copie { background: #EDE6FF; } &.copie img { filter: none; }'),
    ('resumer', '« Résumer avec » : rangée des liens vers les IA.', 'margin: 12px 0 0; padding: 0; list-style: none; gap: 8px;'),
    ('bouton-ia', '« Résumer avec » : petit bouton-étiquette vers une IA (ChatGPT, Claude…).',
     'display: inline-flex; width: fit-content; align-items: center; min-height: 36px; padding: 6px 15px; border: 0; border-radius: 999px; font-family: Hanken Grotesk; font-size: 14px; line-height: 1.2; font-weight: 600; color: #2A2840; text-decoration: none; background-color: #FFFFFF; background-image: linear-gradient(170deg, rgba(255,255,255,0.94), rgba(255,255,255,0.56)); box-shadow: inset 0 1px 0 #FFFFFF, inset 0 0 0 1px rgba(15,15,18,0.1), 0 6px 14px -10px rgba(70,40,150,0.4); &:hover { color: #5B3BEA; }'),

    # Page 404.
    ('mire', "Mire de fin d'émission aux couleurs Rolling (page 404), avec « Signal perdu ».",
     "position: relative; width: 100%; max-width: 560px; aspect-ratio: 16 / 9; margin: 0 auto; padding: 0; overflow: hidden; border-radius: 26px; display: flex; align-items: center; justify-content: center; background: linear-gradient(90deg, #FAF9F7 0 14.28%, #FFD1BF 14.28% 28.57%, #FF8A5B 28.57% 42.85%, #E0562A 42.85% 57.14%, #C4B5FD 57.14% 71.42%, #8E4DE6 71.42% 85.71%, #5B3BEA 85.71%) top / 100% 74% no-repeat, linear-gradient(90deg, #3434E0 0 14.28%, #0F0F12 14.28% 28.57%, #8E4DE6 28.57% 42.85%, #17112C 42.85% 57.14%, #FF8A5B 57.14% 71.42%, #0F0F12 71.42% 85.71%, #FAF9F7 85.71%) bottom / 100% 26% no-repeat; box-shadow: 0 0 0 8px rgba(255,255,255,0.06), 0 40px 80px -30px rgba(0,0,0,0.8); "
     "&::after { content: ''; position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: repeating-linear-gradient(180deg, rgba(15,15,18,0.16) 0 2px, transparent 2px 4px); animation: rl-scintille 3.2s steps(2) infinite; pointer-events: none; }"),
    ('mire-signal', '« Signal perdu · 04:04 » au centre de la mire.', "position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 10px; padding: 10px 18px; border-radius: 999px; font-family: Hanken Grotesk; font-size: 14px; font-weight: 800; letter-spacing: 0.14em; text-transform: uppercase; white-space: nowrap; color: #FFFFFF; background: rgba(15,15,18,0.88); &::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: #FF3B30; box-shadow: 0 0 0 3px rgba(255,59,48,0.28); animation: rl-clignote 1.6s ease-in-out infinite; } @media(--mobile) { font-size: 12px; padding: 8px 14px; }"),
    ('liens-utiles', 'Liens utiles en ligne, centrés (page 404).', "display: block; font-family: Hanken Grotesk; font-size: 16px; line-height: 2; text-align: center; color: var(--rl-paragraphe, var(--paragraphe)); & a { display: inline-flex; align-items: center; min-height: 44px; margin: 0 12px; color: inherit; text-decoration: underline; } & a:hover { color: #FFFFFF; }"),
]


def main():
    chemin = os.path.join(ICI, 'source', 'classes.json')
    d = json.load(open(chemin, encoding='utf8'))
    d['groupes'] = [g for g in d['groupes'] if g.get('nom') != 'Fiches et listes']
    existants = {c['label'] for g in d['groupes'] for c in g['classes']}
    doublons = [c[0] for c in CLASSES if c[0] in existants]
    if doublons:
        raise SystemExit('Classes déjà définies ailleurs : ' + ', '.join(doublons))
    d['groupes'].append({'nom': 'Fiches et listes', 'classes': [{'label': l, 'description': desc, 'css': css} for l, desc, css in CLASSES]})
    json.dump(d, open(chemin, 'w', encoding='utf8'), ensure_ascii=False, indent=1)
    print(len(CLASSES), 'classes dans « Fiches et listes »')


if __name__ == '__main__':
    main()
