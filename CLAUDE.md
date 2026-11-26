# Rolling — thème enfant Hello Elementor

Mémoire du projet. À relire en début de session et à tenir à jour à chaque décision.

## Statut

- Dossier de travail de la session : /workspace (non git). Le dépôt git est /home/user/rolling (lien /workspace/rolling).
  Les fichiers à envoyer à la cliente vont dans /workspace.

- Phase 0 terminée. **Maquettes HTML validées par la cliente** (logos/écran + 11 pages).
- Phase 1 en cours : écriture du thème enfant.
- **Livraison 1 envoyée (0.1.0)** : fondations + blocs Hero, Logos, Showreel. Zip : `outils/zip.sh` → `livraisons/` (hors git).
  Vérifié : site neuf + mise à jour par Apparence > Thèmes > Téléverser, axe 0 (1440, 390, réduire les animations),
  PHPCS 0 (`phpcs.xml.dist`), robots IA, LCP sans lazy + fetchpriority, `<main>` ajouté aux modèles Elementor.
- **Retours livraison 1** (test B fait par la cliente : rendu cassé en « case décochée » ; poignées des sections absentes ;
  étiquettes coupées en mobile ; pas de bouton « Arrêter le défilement ») ⇒ **0.1.1** corrige tout (voir Leçons).
  **0.1.1 validée par la cliente.** Elle insère toujours avec « Adopter le style de ce site ».
- **Livraison 2 envoyée (0.2.0)** : blocs 04 à 13 de l'accueil + « Page complète (accueil) ». Vérifié : 1440/1024/390/360
  vs maquette, axe 0, contrastes sur photo (outil au rectangle du texte), PHPCS 0, SEO (1 H1, titres sans saut, alt,
  dimensions, sizes="auto" sur les images différées), insertion Page complète (identique au site), case décochée
  (310/310 styles gardés), mise à jour 0.1.1 → 0.2.0 par téléversement (zip 2,9 Mo ; limite d'envoi à surveiller chez OVH).
- **Retours livraison 2 ⇒ 0.2.1** : flèches de la méthode portées par l'étape suivante (elles étaient sous la carte) ;
  équipe en flex centré (ajout/suppression libres) ; portraits toujours ronds (aspect-ratio) ; flèche vers l'écran en
  mobile (`suite-fleche`) ; dans l'éditeur, la version EN/ES de l'écran s'affiche quand on sélectionne un de ses textes
  (CSS du thème sur les ids `rl-version-…` et `.elementor-element-editable`).
  Chiffres 00:08 : maquette `maquettes/chiffres/` (A, B, C) ⇒ **option C choisie** (une seule plaque en verre), « ans »
  ramené dans le libellé (« ans de vidéo… »), 500+ composé de chiffres séparés ⇒ **0.2.2**.
- **0.2.2** : plaque de chiffres (`plaque-chiffres`, `libelle-chiffre`) sous le texte ; écran « Votre vidéo » piloté dans
  l'éditeur (clic sur EN/ES/9:16/1:1 ou sélection d'un texte d'une version ⇒ rolling.js pose data-format / data-rl-langue).
  rolling.js reconnaît l'aperçu de l'éditeur à `?elementor-preview=` (la classe du body arrive après le script différé :
  avant, le comportement du site tournait dans l'éditeur).
  **0.2.2 validée par la cliente.**
- **Nouvelle demande : Ressources.** Menu « Ressources » (sous-menu : Blog, Guides, Webinars, Replays) + 3 pages :
  Guides (liste de ressources téléchargeables → pages HubSpot), Webinars (à venir → fiche d'inscription Livestorm ou
  HubSpot ; Livestorm connecté peut-être plus tard), Replays (→ pages HubSpot). Maquette ajoutée à `maquette-pages.html`
  (pages guides, webinars, replays ; sous-menu en en-tête et colonne « Ressources » au pied), **en attente de validation**.
  Proposition : types de contenu « Guide » (image, thème, nb de pages, résumé, lien HubSpot) et « Webinar » (date, heure,
  durée, intervenants, lien d'inscription, lien du replay) : à venir ⇒ page Webinars, passé + lien replay ⇒ page Replays.
  **Maquette Ressources validée** (guides, webinars, replays « top »).
- **Retours maquette (tour suivant)** : Témoignages refaite (mur de cartes identiques : logo, citation, personne, lien ;
  témoignage « à la une » sur 2 colonnes ; filtres ; plus de chiffres en double). Landing revue selon le playbook :
  titre avec « agence vidéo » (correspondance avec l'annonce), 2e showreel remplacé par des résultats chiffrés
  [à fournir] + appel au milieu, preuve sociale = plaque de chiffres + 3 témoignages courts (dont 1 vidéo), calendrier
  HubSpot dans la page, objections du premier contact (engagement, délais, si le résultat ne plaît pas, droits) [réponses
  à confirmer]. Témoignages « pas mal » ⇒ animations demandées.
- **Tour suivant (maquette)** : animations (apparition au défilement = module Interactions d'Elementor dans le thème ;
  cartes qui se soulèvent ; logos gris → couleur au survol ; bande de logos qui défile sur la landing ; visuel du hero qui
  flotte ; onde autour du bouton lecture), tout arrêté avec « réduire les animations ».
  Fiche témoignage : chiffres clés **en nombre libre (0 à n)**, saisis en texte (« 87 % », « 2 500+ ») et affichés en
  chiffres en verre par le thème (0-9, +, %, k, M ; les mots vont dans le libellé, pas d'unité à part) ; sections
  « Le contexte. », « Leur besoin. », « Notre accompagnement. », « Les vidéos. » (bloc absent si pas de vidéo diffusable).
  Deux pages de maquette : cas complet et cas sans chiffres ni vidéo. Landing : timecodes 00:01 à 00:05 en sur-titres ;
  les étiquettes en pointillés de la maquette sont des annotations (pas sur le site).
  Prochaines : 3 = contenus admin (réalisations, témoignages, logos, offres, articles, guides, webinars) + modèles du site
  (balises maison) + sous-menu [fait en 0.3.0] ; 4 = pages Offres, Réalisations, Témoignages, Blog, Guides, Webinars, Replays, Contact,
  landing, 404, SEO, contrôle final.
- **Tour suivant (maquette)** : fiche témoignage = carte « Le projet » (client, secteur, équipes, formats, durée + « Réserver
  un échange ») toujours à gauche, citation **facultative** dessous ; cas à 4 chiffres (cas habituel) ; en mobile un
  chiffre impair prend toute la largeur. Pied de page façon PlayPlay : logo + phrase + réseaux, encart « Réserver un
  échange », colonnes = menu « Pied de page » (1er niveau = titre de colonne), badges IA sécurisée / Europe / RGPD,
  **avis clients facultatifs** (Personnaliser : image, note, nb d'avis, lien ; 4 max ; rien si vide), mentions légales.
  Page de maquette « Pied de page avec avis clients (plus tard) ».
  Sections partagées : **composants Elementor** (clic droit sur un flexbox/div-block > « Créer un composant », onglet
  « Composants » ; création/modification = licence Pro active et Pro ≥ 4.0 ; 100 composants max). Non testable sur ma copie.
- **Fiche témoignage : option B validée** (infos du projet en bandeau horizontal sous le chapô : secteur, équipes,
  formats, durée + « Réserver un échange » ; citation facultative seule à gauche ; sans citation, récit centré 820 px).
  Badges IA sécurisée / Europe / RGPD sous le bouton du CTA final = élément à part, supprimable (pas sur le CTA landing).
- **Page « L'agence »** (maquette, à valider) : lancée en novembre 2026 par les fondateurs de Pitchy ; Paris + toute la
  France ; 500+ clients historiques Pitchy, 10 ans de vidéo ; équipe = celle de l'accueil ; photo backstage à fournir ;
  citation des fondateurs **proposée, à valider par eux** ; infos des badges (hébergement, outils IA) à fournir.
  Sections : hero + photo, 00:01 histoire (frise 3 temps) + citation, 00:02 convictions (4) + plaque de chiffres,
  00:03 équipe (créas, experts IA), 00:04 Paris / France + badges, CTA. Lien « Qui sommes-nous » dans la colonne L'agence du pied.
  **Ne jamais desservir Pitchy (encore vendue)** : Pitchy = les équipes qui créent elles-mêmes, Rolling = celles qui
  délèguent (complémentaires, jamais « l'outil ne suffit pas »).
- **13 ans d'expérience partout** (plus 10) : bandeau du hero, textes et chiffre de « Née chez Pitchy » (blocs reconstruits,
  à livrer en 0.3.0), maquettes (landing, agence). Citation des fondateurs **validée pour l'instant** (texte de la cliente,
  mot pour mot) : Lionel Chouraqui et Benjamin Chouraqui, frères, fondateurs et dirigeants (Pitchy et Rolling).
  Frise de « L'agence » = **timeline de montage** (règle 00:00:00 → 13:00:00, 3 clips colorés avec forme d'onde,
  tête de lecture rouge qui avance jusqu'au clip Rolling « En cours » ; arrêtée avec « réduire les animations » ;
  en mobile, règle verticale à gauche, un repère par clip et tête de lecture rouge pointée sur le clip Rolling ; libellé du
  clip sous son numéro). Tête de lecture = poignée en écusson (clip-path) + trait qui s'arrête sous les clips.
  Dans le thème : tête de lecture et repères = pseudo-éléments (pas de conteneur vide), avec remise à zéro du `::before`
  (sinon il hérite des styles d'un autre `::before`, ex. le liseré de `carte-verre`). Frise validée (« très stylé »).
- **Idées « film » (maquettes, à valider)** : 1 article = lecteur de lecture (pilule en bas : ▶, barre, « 02:10 / 06:00 »,
  visible pendant le texte) **go** ; 2 témoignage = récit en « Scène 1…4 » le long d'une pellicule (page complète seulement,
  à voir) ; 3 réalisation = page d'essai « générique de fin » (fiche technique + équipe en générique, section sombre,
  lignes qui montent une à une ; remplace la carte « La fiche ») ; 4 contact = clap au-dessus du calendrier (valeurs en
  Caveat, barre qui se ferme à l'apparition) ; 5 404 = mire aux couleurs de la charte + « Signal perdu · 04:04 ».
  Webinars : **compte à rebours réel** (date + heure du webinar, heure de Paris, calculé dans le navigateur ⇒ compatible
  WP Super Cache) ; pendant la durée : « En direct maintenant » + bouton « Rejoindre le direct » ; après : « Terminé ».
  Offres : frise de montage à 4 clips « Ce qui se passe après votre échange » (délais [à confirmer]), 00:03 ; FAQ passe en 00:04.
  Réalisations : au survol (ou au centre de l'écran sur mobile), image qui zoome + barre de progression qui avance.
  Lien d'évitement placé dans `<header>` (sinon axe « region »).
  **Retours** : article ⇒ plus de pilule lecteur (faisait penser à un audio) : fine barre de progression en haut de l'écran
  (dégradé brand) avec un repère blanc par chapitre H2 ; témoignage ⇒ pellicule et « Scène » **retirées** (trop lourd) ;
  générique ⇒ **gardé**, facultatif (affiché seulement si rempli), fond clair dans la continuité de la page (plus de section
  sombre), filet « 02:04 · Fin » ; transcription ⇒ facultative, simple ligne « + Lire la transcription » (absente si vide) ;
  pas d'onde autour du bouton lecture sur la fiche réalisation (classe `sans-onde`) ; contact ⇒ clap réduit (Scène/Prise/
  Durée, 330 px) posé sur le coin du calendrier ; « Vous préférez écrire » supprimable sans gêne ; 404 mire **validée** ;
  compte à rebours ⇒ pilule au style des timecodes (« Début dans 22 j 04:32:10 ») ; frise des offres **validée**.
  Survol des réalisations = simulation (zoom + barre) ; proposé : extrait vidéo facultatif lu au survol (ordinateur seulement).
  Tour suivant : générique, article (barre à chapitres), témoignage sobre, contact **validés** ; survol réservé aux
  cartes-liens (`a.vignette-video`) : la vidéo de la fiche ne bouge plus (le `scale` du survol écrasait le centrage du
  bouton lecture) ; compte à rebours : « 22j » collé, groupes espacés.
- **Mots-clés Google Ads de la landing** (chacun au moins une fois, en français naturel) : agence vidéo (H1), agence de
  production vidéo à Paris (pastille du hero), agence de production vidéo (chapô), agence de communication vidéo (fin du
  constat), agence de vidéo (intro du comparatif), agence vidéo motion design (carte « Une équipe vidéo à l'année »),
  studio de tournage à Paris + agence audiovisuelle à Paris (nouvelle question « Où se passent les tournages ? »).
- **Menu du haut** : Offres, Réalisations, Témoignages, Ressources, **L'agence** + bouton. La page « L'agence » s'ajoute aux
  pages créées à l'activation ; chez la cliente (menu déjà créé), elle ajoute le lien elle-même dans Apparence > Menus.
- Mots en dégradé (background-clip: text) : prévoir `padding-inline: .06em; margin-inline: -.06em;
  box-decoration-break: clone` (sinon la 1re lettre est rognée) ⇒ **à vérifier dans les classes du thème** (livraison 3).
- **Livraison 3 envoyée (0.3.0)** : contenus admin (Réalisations, Témoignages, Logos clients, Guides, Webinars + filtres
  Métiers / Formats vidéo / Thèmes ; champs du thème, lignes répétables) ; balises « Rolling » (7) et widgets « Rolling »
  (Liste de cartes, Chiffres clés, Liste à coches, Générique, Compte à rebours, Vidéo, Transcription, Logos clients, Fil
  d'Ariane, Texte / Sommaire / Partage / Résumer de l'article) ; modèles du site dans Mes modèles : 8 cartes + fiches
  réalisation, témoignage, article, 404 (choix dans Personnaliser > Rolling : modèles des fiches) ; en-tête avec sous-menu
  (élément parent = bouton) ; pied façon PlayPlay (menu « Pied de page : colonnes », réseaux, badges, avis facultatifs) ;
  bloc Logos = widget relié à la liste (9 logos créés au départ) ; badges sous l'appel final ; 13 ans ; dégradés aérés ;
  pages Guides, Webinars, Replays, L'agence créées une fois. Vérifié : axe 0 (fiches, listes, article, 404, accueil, 1440/390,
  réduire les animations), PHPCS 0, mobile 390, éditeur (aperçu avec vrais contenus), mise à jour 0.2.2 → 0.3.0 sur wp2.
  Pages Réalisations, Témoignages, Blog, Guides, Webinars, Replays, Offres, Contact, L'agence, landing = **livraison 4**
  (blocs de page avec les widgets Liste).
- Leçons 0.3.0 : Elementor ne prépare les styles que des documents qu'il affiche ⇒ fiches (gabarit modeles/fiche.php)
  et cartes : `elementor/post/render` + CSS du modèle dans `elementor/frontend/after_enqueue_styles`, cartes rattachées au
  document par `elementor/document/related_posts` (une seule feuille globale, ordre des classes respecté) ; classes
  écrites par les widgets déclarées par `elementor/global_classes/extract_class_ids_from_post` ; enveloppe `.elementor`
  des cartes en `display: contents` (`.rl-liste`) ; `.site-main.rl-fiche` pleine largeur (Hello limite à 1140) ;
  Hello appelle `template-parts/dynamic-*.php` (copies supprimées : elles incluent header/footer.php) ; hors admin
  (wp-cli), Elementor allège les réglages des balises et refuse l'enregistrement ⇒ construire.sh simule l'écran
  d'administration ; positions des éléments de menu = compteur global ; `trim()` avec « · » casse l'UTF-8.
- **0.3.1 (préparée)** : noindex page par page = champ ACF de la cliente nommé `noindex` (Vrai / Faux ou case à cocher),
  lu par `Rolling_Seo` (filtres `wp_robots`, `rank_math/frontend/robots`, `wpseo_robots_array`) + retiré des sitemaps ;
  archives auteurs / catégories / étiquettes / dates redirigées en 301 vers le blog (catégorie ⇒ `?categorie=`) et retirées
  du sitemap de WordPress ; article : « Mis à jour le … » (balise Article `modifie`, repère `si-mise-a-jour` = modifié un
  autre jour que la publication ; « 1er » pour le premier du mois). Maquette L'agence : nouvelle citation (« milliers
  d'entreprises ») + photo des fondateurs côte à côte (hors git : scratchpad/imgs/fondateurs.webp), photo 4:3 à gauche.
- Composants Elementor : **éléments atomiques seulement** (les widgets maison v3 comme Partage ne peuvent pas y entrer).
  ⇒ **0.3.2** : « Partager l'article » et « Résumer avec » refaits en éléments atomiques (lien = balise maison
  « Partager / Résumer avec une IA » `rolling-partage` : linkedin, facebook, x, whatsapp, email, chatgpt, claude,
  perplexity, lechat, page ; demande à l'IA réglable) ; icônes = images `partage-*.png` (générées par
  `outils/icones-partage.js`, blanches au survol par filter) + texte masqué ; « Copier le lien » = lien `#rl-copier`
  (rolling.js). Anciens widgets Partage / Résumer gardés (compatibilité). Classe `bouton-ia`.
- Réalisation / témoignage : champs en double (besoin, produit/accompagnement, résultat) ⇒ proposé : récit dans le
  témoignage, réalisation = page vidéo, encart « Le cas client » automatique si lié. Contre-proposition (la cliente
  voulait des témoignages = citations seules) : témoignage = citation + lien vers la réalisation par défaut, récit
  facultatif (fiche seulement si rempli) ; maquette « Proposition : témoignages et réalisations » (saisie, 3 cas de
  cartes : vidéo / cas client / sans lien, citation automatique sur la fiche réalisation) ; **validée** (« pourquoi pas »)
  ⇒ à intégrer (livraison 0.4.0, avec les compositions d'article).
- **Tableaux d'article** (maquette, page Article, à valider) : bloc « Tableau » de WordPress stylé par le thème (en-tête
  violet, lignes alternées, carte arrondie, défilement horizontal si trop large, figure focusable) + style de bloc
  « Comparatif » (dernière colonne mise en avant) + composition « Rolling : tableau ». En mobile, 3 colonnes tiennent.
  **Validé** (« good »). Autres éléments d'écriture : **go pour tous** ⇒ maquette page Article (annotations en
  pointillés « Composition « … » ») : En bref, Étapes, Vidéo, Chiffre clé, Liste à coches, Avis d'expert, Encart guide,
  Encart appel (sombre), À retenir, FAQ (details), Auteur ; **à valider**. Dans le thème : compositions Gutenberg
  (catégorie Rolling) + styles du texte d'article ; rien ne s'affiche si on ne les insère pas.
- **Livraison 4 envoyée (0.4.0)** (12/11/2026) : compositions d'article, témoignages (citation / récit), 10 pages
  (blocs + « Page complète »), landing, chargement C + D. Vérifié : axe 0 (13 pages, 1440/390/réduire les animations),
  1 H1 et pas de saut de titre partout, 1024 sans débordement, PHPCS 0, insertion « Page complète (Offres) » dans
  l'éditeur (Adopter le style), mise à jour 0.3.2 → 0.4.0 sur wp2 avec **Elementor 4.4.0** (rendu identique à wp).
  Leçon : **l'éditeur ajoute `.elementor-element-overlay` en 1er enfant de chaque conteneur** ⇒ dans les classes, jamais
  `:first-child` / `:nth-child(n)` sur les enfants d'un conteneur : compter depuis la fin (`:nth-last-child`, `:last-child`).
  Listes sans H2 : titre H2 masqué (`texte-masque`) avant les cartes h3 (Réalisations, Guides, Replays).
- **Livraison 4 (0.4.0), détails** :
  - Articles : 13 compositions Gutenberg (`data/compositions/*.html` + `compositions.json`, catégorie « Rolling », articles
    seulement) ; styles de blocs « Liste à coches », « Étapes numérotées » (liste), « Comparatif » (tableau) ; rendu =
    règles de la classe `article-texte` ; le widget Texte de l'article (`enrichir()`) pose coches en verre, chiffres en
    verre (`rl-chiffre-valeur`), vidéo YouTube au clic (affiche i.ytimg.com), tableaux focusables ; il porte la classe
    `elementor-widget-theme-post-content` (sinon Elementor remet à zéro les marges des figures) ; aperçu dans l'éditeur
    WordPress = `assets/css/rolling-article-editeur.css` généré par `classes_fiches.py` (CSS déplié).
  - Témoignages : citation par défaut, récit facultatif (`Rolling_Contenus::a_recit`) ; sans récit = redirection 301
    vers la réalisation liée (sinon /temoignages/) et hors sitemap ; carte : « Lire le cas client » (si-recit) ou « Voir
    la vidéo » (sans-recit + si-videos, balise Lien `realisation`) ; fiche réalisation : citation du témoignage lié
    (réglage « Lire dans : témoignage lié » des balises Champ/Image, repères `si-temoignage-…`). Repères de filtres
    `si-metier`, `si-format`, `si-theme`.
  - Pages : blocs `p-<page>-<n>-…` + « Page complète (<page>) » (`pc-…`, avec 13-final) pour Réalisations, Témoignages,
    Blog, Guides, Webinars, Replays, Offres, Contact, L'agence ; **reste la landing**. Widget Liste : titre affiché
    seulement s'il y a des cartes, « Première carte à la une » (c9 carte article à la une), liste vide des webinars =
    c10 « Aucun webinar ». Classes « très prioritaires » (`$tres_prioritaires`, ordre de la liste = priorité) pour les
    modificateurs de classes déjà prioritaires (entete-page, titre-point-bleu…).
  - Photo des fondateurs : **pas dans le thème** (photo provisoire hero-interview.jpg) : la cliente l'importe et la
    remplace dans Elementor. Le contrôle automatique a refusé de la copier, puis a bloqué les commits suivants.
- **0.4.1 (retours livraison 4, prête)** : modèle « Page de texte » (`modeles/texte.php`, fiche f5 : fil d'Ariane, H1,
  « Mis à jour le », texte de l'éditeur WordPress) pour mentions légales / CGV / confidentialité ; Blog modifiable dans
  Elementor (le thème ne pose plus « Page des articles » ; `Rolling_Dynamique::page_blog()` = page des articles sinon /blog/) ;
  zones formulaire Contact + landing = un seul widget HTML ; Contact : rappel des 2 formules ; FAQ sur chaque page
  (`FAQ_PAGES`, blocs p-…-faq) ; offres et comparatif refaits en mobile (critère = en-tête sombre, 3 lignes dessous) ;
  tous les textes ≥ 12 px ; réalisation : champ « Témoignage lié » (`temoignage_lie` / `realisations_liees` dans les deux
  sens). `blocs.py` supprime les fichiers des blocs qu'il ne produit plus. `.rl-fiche` : sélecteur `html body` (Hello
  limite `.site-main` à 1140 px sur les pages à modèle).
  Non reproduit : avatars sur le titre de la carte IA « Choisit · corrige · adapte » (infos demandées à la cliente).
- **0.4.2 (retours 0.4.1)** : hero mobile = **option C validée** (« écran de montage », maquette `maquettes/hero-mobile/`) :
  classe `hero-montage` sur « Visuels » (mobile seulement) : vignettes empilées qui s'enchaînent (3 s, `rl-plan-hero`),
  timeline 4 clips (::after) + tête de lecture (::before, `rl-tete-hero`) ; règles `& > .e-con` (spécificité 0,3,0 >
  styles locaux 0,2,0) ; `align-self: stretch` obligatoire (Chrome aligne les éléments absolus : `align-self: start` local
  les écrasait) ; sans animation, la 1re vignette reste visible. SEO accueil (règles SEO de la cliente : H1 avec le mot-clé,
  gras = mot-clé seul) : **H1 = texte de la pastille « Votre agence vidéo illimitée »** (classe `texte-herite`), « Le meilleur
  de l'humain… » devient un paragraphe `titre-lg` (« Accroche ») ; chapô « Notre **agence vidéo** s'en charge » ; H2 00:08
  « Une agence vidéo née chez Pitchy. » ; title / description proposés pour Rank Math. Carte IA : `visages` ne rétrécit plus
  (avatars sur le titre sous 1440 px). Landing : conteneur des logos en colonne (libellé à gauche des logos).
  Leçon : `F(nom, None, style)` sans flex-direction = ligne ⇒ toujours COLONNE si plusieurs enfants empilés.
- **Maquette « Motion, frise et showreel »** (`maquettes/hero-mobile/showreel-motion.html`, copie /workspace, **à valider**) :
  vignette motion du hero mobile jugée vide + frise « simplette » ⇒ M1 graphique animé (+87 % qui compte, courbe, barres) /
  M2 formes en mouvement ; F1 pistes de montage (timecode, règle, piste V1 avec noms des plans, piste son, tête de lecture)
  / F2 barres façon stories en haut de l'image ; conseil M1 + F1. Arrivée du showreel : S1 ouverture cinéma (bandes noires),
  S2 grandit au défilement (animation-timeline, conseil), S3 projecteur qui s'allume. **Choix : M2 + F2 et S2** ⇒ **0.4.3** :
  `hero-montage` refaite (barres stories = ::after masqué en 4 segments, `rl-stories-hero` ; timecode = ::before avec
  compteur `--rl-s` (@property dans rolling.css), `content: … / ''` ; plus de timeline), `chapitres-hero` (p avec 4 span,
  1er enfant de Visuels pour garder le compte nth-last-child), `motion-formes` + `mot-motion` (vignette motion mobile :
  fond pêche, rond / carré = pseudo du cadre, triangle / trait = pseudo du mot ; mot animé par la couleur, pas de transform
  sinon le triangle se cale sur le mot), plaque « Vos chiffres en motion » (aussi sur ordinateur) ; `showreel-grandit`
  (72 % → 100 %, 88 % en mobile) et `apparait-defile` (boutons). Leçon : le convertisseur retire `animation` **et ses
  longhands** du CSS de premier niveau ⇒ champ `brut` (4e élément du tuple dans classes_fiches.py), animation-timeline
  écrit après le raccourci ; fond qui doit écraser un dégradé : `background: linear-gradient(c, c)` (une couleur seule
  ne retire pas l'image).
- **0.4.4** : police du H1 de la pastille = « inherit » pris comme nom de police par l'éditeur (police à empattements ;
  reproduit sur wp2) ⇒ `texte-herite` en CSS brut (aucune prop native `inherit`). Noms des plans sous les stories retirés
  (classe `chapitres-hero` supprimée ; chez la cliente : supprimer l'élément « Visuels · chapitres »).
  **Maquette « Hero sur ordinateur : plus léger »** (`maquettes/hero-mobile/hero-ordinateur.html`, **à valider**) : bloc
  d'images jugé trop massif ⇒ A collage aéré (conseil : 4 vignettes flottantes inclinées sur halo, plus de grand cadre),
  B photo fondue dans la page (masque radial) + 3 vignettes, C pellicule verticale qui défile + interview flottante.
- **0.4.5 : hero ordinateur = option A (collage aéré) validée** : classe `hero-collage` sur « Visuels » (CSS brut dans
  `@media (min-width: 981px)` : cadres fins 6 px, rotations par nth-last-child, `rl-flotte` décalé) ; positions locales
  des 4 vignettes revues (Visuels max 560 px centré, 630 px de haut ; Tournage 380 px en 16:10, Interview / Motion 190 px).
  Leçon : `margin-inline` et `max-width: none` sont ignorés par le convertisseur ⇒ `margin: 0 auto`, `max-width: 100%`.
- **0.4.6** : flèche « Vos contenus & vos briefs » raccourcie (52 px, ne croise plus le cercle de l'image) ; pointillés
  animés (`rl-pointilles` : couche de tirets en `repeat-y` + `content-box` avec `padding-bottom: 4px`, la pointe reste en bas)
  sur pilule-flux, colonne-flux, suite-fleche ; `halos-offre` recalés dans la section (top / bottom 0 : plus de halo coupé
  aux jonctions 00:06/00:07/00:08) ; pied de page : fond qui part de #0F0F12 (comme l'appel final), plus de filet, halo
  déplacé en bas à droite. Illustrations capturées : ombres coupées net au bord ⇒ `outils/adoucir-bords.py` (rampe de
  transparence sur les bords encore visibles) ; l'installateur **remplace le fichier des images de départ** quand celle du
  thème change (`rafraichir_media` : même pièce jointe, nouveau nom `<base>-<md5 8>`, ancien fichier gardé ; jamais si la
  cliente a mis un autre fichier). Écran « Votre vidéo » : l'image de fond se change par le Navigateur (« Écran · image de fond »).
  page-essai avec toutes les clés plante (modèles de cartes) ⇒ passer la liste des blocs de l'accueil.
- **0.4.7** : `halo-centre` (appels finaux, 404) calé dans la section (top / bottom 7 %, largeur max 860 px centrée par
  left/right) : plus coupé avant le pied de page. **La cliente n'installe pas Rank Math** (elle a Semrush, scan à la mise
  en ligne) ⇒ personne n'écrit description / Open Graph / schémas : question posée (extension SEO ou métas par le thème).
  Version anglaise expliquée (extension multilingue WPML ou Polylang, /en/, hreflang par l'extension ; à tester avec
  les éléments atomiques d'Elementor 4 avant ; livraison dédiée : contenus traduisibles, textes fixes du thème, sélecteur).
  Docker : si `dockerd` échoue sur « timeout waiting for containerd », supprimer /var/run/docker/containerd/containerd.pid
  (pid périmé repris par un autre processus).
- **0.4.8 : extension SEO = Yoast SEO** (décision cliente, à la place de Rank Math ; option A « extension » plutôt que
  métas dans le thème). Yoast écrit title, description, canonical, Open Graph, schéma et plan du site ; le thème ajoute :
  exclusions du plan du site Yoast (`wpseo_exclude_from_sitemap_by_post_ids` : pages cochées noindex + témoignages sans
  récit `Rolling_Contenus::citations_seules()` ; catégories / étiquettes / auteurs exclus) et le **VideoObject** des
  réalisations dans le graphe Yoast (`wpseo_schema_graph` : lien vidéo + image à la une obligatoires ; YouTube ⇒ embedUrl,
  fichier ⇒ contentUrl ; description = chapô ; durée « 2:04 » ⇒ PT2M4S). Noindex ACF lu aussi par `wpseo_robots_array`.
  Testé sur la copie avec Yoast 28.8 (zip hors git, scratchpad/plugins/yoast) : 1 title / 1 robots / 1 canonical / 1 schéma,
  noindex ⇒ « noindex, follow » et hors plan du site, robots IA gardés. Yoast installé sur wp et wp2.
- **Livraison 5 envoyée (0.5.0) = recette finale** : métas de chaque page (`outils/metas.py` ⇒ /workspace/metas-seo.html,
  contrôle 50-60 / 140-160 caractères, marque à la fin, title ≠ H1) ; compositions d'article reliées à leurs pièces jointes
  (`{{media-id:clé}}` dans le bloc image + classe `wp-image-…` ⇒ dimensions et srcset ; blocs valides dans l'éditeur) ;
  recette sur la copie avec Yoast (19 pages axe 0, Lighthouse a11y / bonnes pratiques 100, SEO 92 = description manquante,
  perf mobile 74-87 sans cache ; redirections 301, 404, plan du site, robots) ⇒ /workspace/livraison-5-recette.html
  (résultats + liste de contrôle avant / pendant / après la mise en ligne). Lighthouse : scratchpad/lh (npm lighthouse,
  CHROME_PATH=/opt/pw-browsers/chromium). Adresses réelles : /agence/ (L'agence), /agence-video/ (landing).
- **Maquette « Bloc Ressources de l'accueil »** (`maquettes/ressources-accueil/ressources.html`, copie
  /workspace/maquette-ressources-accueil.html, **à valider**) : bloc 00:10 entre la FAQ et l'appel final, vers blog,
  guides, webinars, replays (pas encore de contenus ⇒ chaque option montrée vide / remplie). A les 4 portes (conseil :
  4 cartes à liseré de couleur, dernier élément de chaque rubrique ou « Bientôt » non cliquable + bandeau « Me prévenir »
  = bloc HTML HubSpot qui disparaît quand tout est rempli) ; B à la une + 3 raccourcis (prochain webinar > dernier guide >
  dernier article) ; C chutier de montage (onglets dossiers + 3 dernières vignettes ; vide = triste, pour plus tard).
  Retour : **mix A + B** (les liserés de couleur de A « ne font pas charte » ; garder l'image de B) ⇒ maquette
  `maquettes/ressources-accueil/ressources-mix.html` (copie /workspace/maquette-ressources-mix.html, **à valider**) :
  Mix 1 (conseil) grande vignette « à la une » (cadre fin + étiquette + plaque en verre, comme le hero ; prochain webinar >
  dernier guide > dernier article ; vide = invitation « Me prévenir » HubSpot) + 4 rubriques en cartes de verre avec pictos
  en verre violet / pêche ; Mix 2 rangée de 4 vignettes avec image, la 1re deux fois plus large (mobile : grande + 3 lignes).
- **0.5.1 : bloc Ressources = Mix 1 validé**, après la FAQ de l'accueil (00:10, dans « Page complète (accueil) ») et
  utilisable partout (Mes modèles : « Rolling · Ressources (blog, guides, webinars, replays) », clé `14-ressources`).
  **Contextes** (`Rolling_Dynamique`, crochets `elementor/frontend/before_render` / `after_render`) : une classe repère
  `contexte-une | contexte-dernier-article | contexte-dernier-guide | contexte-prochain-webinar | contexte-dernier-replay`
  posée sur un conteneur empile un contenu ; `contenu_courant()` renvoie le haut de la pile ⇒ toutes les balises et repères
  existants lisent ce contenu le temps du conteneur (« une » = prochain webinar > dernier guide > dernier article ;
  0 si rien de publié). Repères `si-contexte` / `sans-contexte` (« Bientôt », invitation « Me prévenir » = widget HTML
  HubSpot) et `si-type--webinar|guide|article`. La balise Article lisait `get_the_ID()` ⇒ passe par le contexte
  (`en_contexte()`). Pictos en verre `picto-blog|guides|webinars|replays.webp` capturés depuis la maquette
  (`outils/pictos-ressources.js` + `outils/captures/ressources.json`, marge transparente 24 px pour l'ombre).
  Classes : ressources-grille, ressource-une, une-photo, une-sans-photo, plaque-une, titre-une, texte-une,
  grille-rubriques, carte-rubrique (`:has(.etiquette-bientot)` = carte discrète), picto-rubrique, etiquette-bientot.
  `page-essai.php` simule l'écran d'administration (sinon « image: invalid_value » sur les images dynamiques).
- **0.5.2 (retours 0.5.1)** : grande case du bloc Ressources = **dernier article seulement** (avant : choix automatique
  prochain webinar > dernier guide > dernier article, sans réglage ; variantes et compte à rebours retirés) ; sans article =
  invitation « Nos premiers articles arrivent. » + HubSpot ; article sans image à la une ⇒ dégradé (`une-sans-photo` sous
  l'image, image en `si-image`). Carte Blog = description + lien (pas de doublon du titre). « Bientôt » seulement dans les
  cartes vides. Balises article / lien / titre / webinar / champ / image : réglage « Lire dans » = dernier article, dernier
  guide, prochain webinar, dernier replay (`source`, rendu juste aussi dans l'éditeur). **Éditeur** : CSS en ligne de
  l'aperçu (`Rolling_Dynamique::apercu_contextes`, préfixe `html.elementor-html` : le body de l'aperçu n'a pas
  `elementor-editor-preview` en 4.3.6) masque les variantes qui ne s'affichent pas sur le site. Carte vide atténuée par
  `:not(:has(.si-contexte))` (plus `:has(.etiquette-bientot)`, présent dans l'éditeur). La cliente doit remplacer le bloc
  déjà inséré (la mise à jour ne touche pas aux pages).
- **0.5.3** : Ressources en mobile = **liste** (choix cliente parmi actuel / 1 par ligne / liste) : `grille-rubriques` 1 colonne,
  `carte-rubrique` en grille 48 px + texte (picto `grid-row: 1 / span 3`). Hero : **2 versions** dans Mes modèles,
  « Rolling · 01 Hero » (collage, dans la Page complète) et « Rolling · 01 Hero (version maquette : grande photo) »
  (`01-hero-maquette`, hors Page complète : `BLOCS_VARIANTES` ; `bloc_hero(maquette)` + `POSITIONS_HERO`). Collage jugé pas
  assez « verre » ⇒ cadres `cadre-verre` d'origine (14 px, plus de padding 6 px) + taches violet / lilas / pêche floues
  derrière (`hero-collage::after`, z-index -1) : le verre laisse voir la couleur. Taille de police : fonctionne en
  « local » sur ma copie ; causes probables chez elle = pastille de classe sélectionnée, taille mobile de la classe qui
  passe devant la taille réglée sur ordinateur, cache.
- **0.5.4 (contrastes)** : la cliente voyait des contrastes faux sur les fonds foncés (IA et ailleurs). Cause : fonds foncés
  = dégradés sans couleur unie dessous ⇒ les outils (Ally, WAVE, DevTools) remontent jusqu'au fond clair de la page et
  voient du blanc sur blanc ; axe les classe « à vérifier », pas en erreur. **Règle : tout fond en dégradé qui porte du
  texte a aussi un `background-color` uni** (section-sombre, carte-prix-sombre, ecran-video, bouton-soleil, etiquette-metier-*,
  webinar-date, clip-barre et ses couleurs, clap-corps, pilule-flux, bandeau-clair, pied de page, intro, bouton du pied).
  Vrais contrastes trop faibles corrigés : `--legende` #6E6B7B → #5F5C6C (labels sur fonds teintés), dégradé des mots
  `#5B3BEA → #7A45E0 → #C94A1E` (au lieu de #8E4DE6 → #E0562A), dégradé humain `#B8400F → #C94A1E → #CF4F22`, étiquette
  orange `#9A3412 → #B8400F`, clip mauve `#6E3FD0 → #923784`, milieu du dégradé des mots #6F3FD6, mots soulignés
  (humain) en pêche clair dans les sections sombres (`--rl-degrade-humain` posé par section-sombre), `accroche` ≥ 24 px,
  pilule de l'en-tête plus opaque (mire de la 404 dessous), deux-points du compte à rebours en gris légende, REC /
  durée sur photo dans une pilule sombre. Outils : `scratchpad/tools/contraste-page.js <url> <largeur>` (tous les textes,
  pire pixel sous le texte, dégradés compris) et `scratchpad/fonds2.js <urls>` (fond uni vu par les outils simples).
  Reste : colonne sombre du tableau des offres (bande = `::before`, pas de fond sur les cases ; contraste réel bon).
- **Maquette « Clap de la page Contact »** (`maquettes/clap/clap.html`, copie /workspace/maquette-clap.html, **à valider**) :
  clap noir et blanc jugé hors charte ⇒ A clap en verre clair (conseil : barre rayée violet / verre, valeurs Caveat
  orange foncé, timecode REC qui tourne en pied) ; B plaque timecode en verre fumé sans clap (REC, scène manuscrite,
  compteur vers 30:00 + barre de progression) ; C clap en verre fumé (barre en dégradé violet → pêche, timecode en pilule).
  Problème du 13/11 (« site cassé sur toutes les pages, bon dans l'éditeur ») : **parenthèse manquante dans le CSS
  personnalisé de la classe `timecode`** modifiée par la cliente (`box-shadow: var(--rl-etiquette-ombre, … #000000;` sans `)`)
  ⇒ en 4.4 toutes les classes d'une page sont dans un seul fichier `global-<id>-frontend-*.css` : une parenthèse
  ouverte avale toutes les classes écrites après (ordre inverse : titres, textes, boutons, section-sombre…). L'éditeur
  rend chaque classe à part ⇒ rien ne s'y voit. Idée : avertissement dans l'administration si un CSS de classe a des
  parenthèses / accolades non fermées.
- **0.5.5** : Contact = **plus de clap** (choix cliente) : timecode `timecode` + `timecode-rec` (point REC) + `timecode-coin`
  (à cheval sur le bord haut droit du cadre du formulaire) ; écrit 00:00:00:00, il défile (rolling.js, 25 i/s, rien avec
  « réduire les animations » ni dans l'éditeur) ; classes clap-* retirées (gardées tant qu'une page s'en sert).
  Chiffres clés : `chiffres_en_verre()` écrit les caractères sans image (« , / : < », lettres) en `span.chiffre-texte`
  (dégradé #FFAB96 → #E697BD → #A77BF0 des chiffres), lettres seules = `chiffre-unite` plus petites ; tailles dans
  `chiffre-verre` et `plaque-libre` (mobile 44 px). Valeurs de la cliente : « 4,2/5 », « 35:00 », « <3 mois ».
  Vidéo des réalisations : sans image à la une ⇒ miniature YouTube (i.ytimg.com) ; fichier vidéo ⇒ `video.apercu-video`
  muette en boucle, lancée quand visible (rolling.js), retirée au clic sur lecture ; `video-fiche` met l'aperçu sous le
  bouton (`z-index: 0; pointer-events: none`).
  **Maquette « Témoignages avec vidéo »** (`maquettes/temoignages-video/`, copie /workspace, **à valider**) : carte
  témoignage avec la vignette de la réalisation liée (A lecture dans la carte, conseil ; B clic vers la réalisation) +
  lecteur en grand dans la fiche témoignage après le bandeau des infos.
- **0.5.6** : calendrier HubSpot trop étroit (≈300 px : `zone-calendrier` centrait ses enfants ⇒ le bloc HTML se réduisait et
  l'iframe prenait sa largeur par défaut) ⇒ `align-items: stretch`, widget et iframe à 100 %, cadre fin (padding 14 px),
  iframe arrondie. Le fond blanc du calendrier vient de HubSpot (iframe d'un autre domaine : aucun CSS du site ne l'atteint).
  Témoignages avec vidéo = **conseil suivi** (option A) : vignette de la 1re réalisation liée en haut de la carte (repère
  `si-video-liee` = elle a une vidéo ; classe `vignette-lecteur` : zoom + barre au survol, rien une fois lancée `.lit`),
  lecture dans la carte au clic ; widget Vidéo : réglages `source` (courant / réalisation liée), `apercu` (anime / image
  fixe ⇒ `data-rl-apercu="fixe"`, pas d'aperçu muet dans les listes), `priorite` (haut = eager + fetchpriority, bas = lazy).
  Fiche témoignage : grand lecteur après le bandeau des infos ; grille « Les autres vidéos. » (`liees_suite`, repère
  `si-autres-videos` = 2 réalisations liées ou plus). Lien de la carte sans récit : « Voir la réalisation ».
- **0.5.7** : fiche réalisation : bouton « Lire le cas client » sous la citation du témoignage lié (balise Lien `fiche` +
  `source=temoignage`, repère `si-temoignage-recit` : seulement si le témoignage a un récit). Calendrier HubSpot reproduit
  (hôtes meetings.hubspot.com, static.hsappstatic.net, api.hubspot.com, app.hubspot.com ajoutés à l'environnement « rolling ») :
  le fond de l'iframe est transparent ; HubSpot dessine une carte blanche de 400 px centrée (iframe < 800 px), deux colonnes
  date + heures à partir de ~800 px ; hauteur posée par son script 756 px, carte ~670 px. ⇒ `zone-calendrier` calée sur la
  carte (max 440 px centré, iframe 680 px, padding 52/10/10) et `timecode-coin` **à l'intérieur** en haut à droite.
  Fond rose chez la cliente : chez moi la même adresse donne une carte blanche ⇒ cause hors thème (réglage HubSpot ou
  navigateur / extension) ; test proposé : ouvrir le lien HubSpot seul.
- **Maquettes (après 0.5.7, à valider, HubSpot « all good »)** : écran « Votre vidéo » à rendre visiblement cliquable
  (`maquettes/ecran-cliquable/ecran-cliquable.html`, copie /workspace/maquette-ecran-cliquable.html ; capture 1 de la cliente
  non reçue, supposée = cet écran) : A flèche manuscrite Caveat « Essayez un autre format ! » + bouton 9:16 qui respire
  (conseil), B démo qui se lance seule (curseur qui clique 9:16, EN, retour), C boutons plus parlants (consigne, formes des
  formats, reflet) ; tout s'efface au 1er clic. Chargement jugé trop rapide et bandes en 2 temps
  (`maquettes/loading/loading-v2.html`, copie /workspace/maquette-loading-v2.html) : cause = fond du centre qui s'efface
  avant les bandes + palier à 76 % dans `rl-intro-haut/bas` ⇒ B 2,1 s (conseil) / C 2,5 s : bandes seules en noir, une
  ouverture continue cubic-bezier(.76,0,.24,1) pendant que le logo s'efface.
- **Choix** : chargement = **B validé** (2,1 s, une seule ouverture) ⇒ à intégrer. Écran cliquable = **écriture manuscrite
  de A + curseur de B**, à tester ⇒ maquette `maquettes/ecran-cliquable/ecran-cliquable-2.html` (copie
  /workspace/maquette-ecran-realisations.html, **à valider**) : flèche dessinée, puis curseur qui clique 9:16, EN, retour
  (une fois), puis 9:16 respire et l'annotation reste jusqu'au 1er clic. Même maquette, page Réalisations : la cliente ne
  comprenait pas les 2 lignes de boutons (filtres métier puis format, sans nom) ⇒ proposé : nom de ligne « Métier » /
  « Format » + « Tous » par ligne ; cartes sans image à la une = rien d'affiché chez elle ⇒ image de la vidéo : image à
  la une > miniature YouTube > 1re image du fichier vidéo ; clic = fiche réalisation.
- **0.5.8 (tout validé)** : chargement B (bandes seules en noir, centre sans fond, tête de lecture 0,9 s, ouverture 0,9 s à
  1,15 s, cubic-bezier(.76,0,.24,1), fin 2,1 s, retrait 2,3 s) ; écran « Votre vidéo » : astuce `annotation-ecran` (P d'id
  `rl-ecran-astuce`, Caveat #FFD1BF, flèche ::after qui se dessine `rl-trace` ; au-dessus des boutons sous 1024 px) +
  démo au curseur (rolling.js : `.rl-curseur`, clics 9:16 / EN / 16:9 / FR une fois à 60 % visible, puis `.rl-appel` sur
  9:16 ; premier clic du visiteur = astuce effacée ; drapeau `enDemo` car la barre d'espace appelle `click()`) ; filtres
  sur deux lignes : nom de ligne (`li.nom-filtre`, réglages `nom_metier` / `nom_format`) + « Tous » par ligne qui ne
  retire que son filtre ; cartes : balise Image « une » sans image à la une ⇒ miniature YouTube, sinon
  `assets/img/affiche-video.webp?rl-video=<fichier>` remplacée par rolling.js par la 1re image (`video` muette) ; repère
  `si-image` vrai aussi si la réalisation a une vidéo. i.ytimg.com bloqué dans mon environnement (non visible ici).
  Timecode du formulaire jugé stressant ⇒ **A (figé) choisi** (0.5.12).
- **0.5.9** : astuce de l'écran replacée (ordinateur : à gauche des boutons, flèche vers le groupe Format, left -162 /
  top -24 ; sous 1024 px : une ligne en 28 px, petite flèche vers le bas centrée dessous). Cartes « on voit pas les images »
  chez la cliente (affiche d'attente restée : le remplacement par rolling.js ne s'est pas fait chez elle, cause non
  trouvée, OK sur wp et wp2 4.4) ⇒ remplacement **côté serveur** : `Rolling_Dynamique::affiches_video( $html, $titre )`
  appliqué à chaque carte du widget Liste (`video` muette `#t=0.1` + `span.screen-reader-text` = titre ; miniature
  YouTube : alt = titre, sinon lien de vignette sans nom pour axe). Le script reste en secours.
  Image de fond de l'écran : Navigateur > « Écran · image de fond » (dans IA · votre vidéo > Écran · scène > cadre > vidéo > fond).
- **0.5.10 (contrastes « Accessibility » d'Elementor / Ally)** : règle du vérificateur reconstituée sur les 11 captures de
  la cliente (`scratchpad/fonds6.js`) : il remonte au 1er ancêtre qui a un fond ; **dégradé seul (une couche) ⇒ sa 1re
  couleur ; plusieurs couches ⇒ ignorées, il lit `background-color`** ; couleur translucide posée sur du blanc ; les
  pseudo-éléments ne comptent pas ; un texte en dégradé (background-clip: text) sert de « fond » à ses enfants (« / mois »).
  ⇒ **Règle** : fond translucide d'un élément à texte = 2 couches (`linear-gradient(c, c), linear-gradient(rgba(0,0,0,0),
  rgba(0,0,0,0))` + `background-color: transparent`) pour que l'outil remonte au vrai fond uni ; ne pas écrire
  `transparent` dans un dégradé (le convertisseur l'abandonne : `rgba(0,0,0,0)`). Appliqué : badges (var
  `--rl-verre-bouton-fond`), timecode sombre (`--rl-etiquette-fond`), groupe-choix, bandeau-video, etiquette-carte,
  plaque-verre, plaque-une, resume-offre-sombre, offres-resume-sombre, prix-degrade, ecran-video (2e couche ⇒ lit
  #3434E0), pied (rl-badge, rl-pied__appel) ; clip-numero = `rgba(15,15,18,.62)` (passe même posé sur du blanc) ;
  colonne sombre du tableau des offres = `background-color: #1C1730` + `background-clip: text` (invisible, lu par l'outil ;
  pas sur la case unique des titres de groupe). Storyboard des offres : `note-main` visible en mobile dans
  `cadre-storyboard`. Carte « à la une » sans image : marges du texte (26/28 px). Bouton de l'en-tête : Personnaliser >
  « Rolling : en-tête et pied de page ». Carte « Aucun webinar » = Mes modèles « Rolling · Aucun webinar programmé »
  (boutons centrés sur le site, à gauche seulement dans l'éditeur).
  **Maquette Webinars** (`maquettes/webinars/webinars.html`, copie /workspace/maquette-webinars.html, **à valider**) :
  nouveau champ « Programme (une ligne par point) » en liste à coches, résumé court ; sans image ⇒ visuel de date (jour,
  mois, heure, durée, compte à rebours) ; intervenants en pastilles d'initiales ; bouton + intervenants en bas.
- **0.5.11** : réalisation : champ « besoin » renommé **« La problématique »** (clé `besoin` gardée ; titre de la fiche
  « La problématique. ») — « Leur besoin. » reste dans le témoignage. **Cas d'usage** = nouvelle taxonomie `rolling_usage`
  (réalisations ; termes de départ : Événement, Onboarding, Formation, Lancement produit, Marque employeur, Prise de parole
  des dirigeants ; paramètre `?usage=`) : métier = équipe qui commande, format = type de vidéo, cas d'usage = occasion /
  objectif ; widget Liste : filtres `usage` (métiers + cas d'usage) et `trois` (métiers, cas d'usage, formats ; page
  Réalisations), réglage `nom_usage`, ligne masquée si aucun terme utilisé ; fiche réalisation : étiquette `si-usage`.
  Le format « Événement » de départ doublonne avec le cas d'usage (à renommer par la cliente, ex. Aftermovie).
  **Webinars = maquette validée** : champ `programme` (lignes) ⇒ widget Coches `champ=programme` dans `programme-webinar`
  (`si-programme`) ; carte c7 refaite (`webinar-une` : visuel `webinar-visuel` = image `cadre-remplir si-image` OU
  `webinar-affiche sans-image` (grande-date, affiche-jour / mois-nom / heure), bandeau « Prochain webinar », compte à
  rebours en bas `compte-visuel` ; textes `webinar-textes`, `webinar-bas`) ; nouveau widget **Intervenants**
  (`rolling-intervenants`, pastilles d'initiales `avatars-initiales`, classe `intervenants-webinar`). Image à la une
  facultative. **Image du thème** : `screenshot.png` 1200×900 (logo sur carte en verre, halos), rendue par Playwright.
- **0.5.12** : timecode du formulaire Contact = **option A (figé)** : texte « 00:30:00 » (durée de l'échange), point REC
  fixe (plus d'animation `rl-clignote` dans `timecode-rec`), défilement retiré de rolling.js (la cliente change le texte
  de son élément). Vignettes des cartes (`vignette-lien`) : voile `cadre-photo::after` allégé (34 % / 30 % au lieu de
  55 / 60 + teintes) ; image d'un fichier vidéo prise à 2 s (`#t=2`, cartes et widget Vidéo en « image fixe ») au lieu de
  0,1 s (souvent noire). Image à la une d'une réalisation = vignette des cartes (prioritaire sur la vidéo).
- **Maquette « Chiffres de la fiche témoignage »** (`maquettes/chiffres-temoignage/chiffres.html`, copie
  /workspace/maquette-chiffres-temoignage.html, **à valider**, « ne relance pas le thème avant ») : plaque `plaque-libre`
  jugée trop écartée (4 colonnes égales, chiffre d'un caractère) ⇒ A plaque ajustée au contenu, **B chiffre + libellé côte
  à côte (conseil ; mobile = liste, une ligne par chiffre)**, C centrée et chiffres plus grands, D chiffres dans le bandeau
  du projet (une seule carte, plus de titre « En chiffres »). Prochaine version aussi : message « Aucune réalisation pour
  ces filtres » + lien « Tout voir » quand une combinaison de filtres est vide (filtres cumulables : vérifié, ET logique).
- **0.5.13** : chiffres de la fiche témoignage = **B validé** (`plaque-libre` : chiffre + libellé côte à côte, colonnes
  auto ≥ 220 px, libellé `flex 1 1 120px` ; mobile = liste une ligne par chiffre avec filets ; widget Chiffres ajoute
  `plaque-large` (2 colonnes) si une valeur dépasse 2 caractères hors espaces, ex. « 87 % », « 2 500+ »). Widget Liste :
  filtres sans résultat ⇒ carte `carte-verre carte-vide` « Aucune réalisation pour ces filtres. » + bouton « Tout voir »
  (réglages `texte_aucun`, `texte_tout_voir` ; textes adaptés : témoignages / articles / guides / replays). **Textes de la
  cliente repris dans le modèle Contact** (intro « Un premier contact pour faire le point… feuille de route vidéo… »,
  preuve « Par les équipes de Pitchy - 13 ans de vidéo, +500 grandes entreprises. » mot pour mot, plus de « [adresse à
  confirmer] »). **Règle cliente** : quand elle transmet le site publié, reprendre dans les modèles du thème chaque texte
  qu'elle a modifié (pour ne pas les perdre si un modèle est refait).
- **Maquette « Landing à revoir »** (`maquettes/landing-v2/landing.html`, copie /workspace/maquette-landing-v2.html,
  **à valider**) : 1 formulaire 00:04 = timecode figé « 00:30:00 » dans le cadre du calendrier (comme Contact) ;
  2 « Ce que ça change pour nos clients » sans chiffres : **A avant / après des 3 dernières réalisations (conseil,
  champs avant / après existants, automatique)**, B 4 bénéfices sans chiffres (risque de redite avec les 3 cartes),
  C retirer la partie (garder l'encart d'appel) ; 3 chiffres « Née chez Pitchy » au format B des fiches témoignage :
  4 chiffres en 2 colonnes (conseil) ou B' 13 + 500+ seulement, studio et plateforme passés dans la phrase.
- **Landing, retours** : timecode 00:30:00 **ok**, chiffres « Née chez Pitchy » = **B (4 chiffres, 2 colonnes)**. A répétait
  les vidéos des témoignages ⇒ maquette `maquettes/landing-v2/landing-2.html` (copie /workspace/maquette-landing-2.html,
  **à valider**) : « Ce que ça change » = 4 bénéfices (la cliente a aimé l'option B, appelée « C » dans son message) :
  Vos contenus enfin regardés, **Zéro devis** (pas « Plus de devis », ambigu), Toujours une vidéo en cours, Tous vos formats ;
  preuve : témoignages **avec citation seulement** sur la landing (réglage de la liste à créer), citation 16 px (au lieu
  de titre-xs gras) ; variante avant / après rangée (lignes fixes Au départ / La vidéo, subgrid) ⇒ témoignages sans vidéo.
- **0.5.14 (landing, « tout niquel go »)** : 00:02 « Ce que ça change pour nos clients » = 4 bénéfices (`plaque-benefices`,
  coche en verre + `titre-benefice` h4 + texte-petit ; 2 × 2 sous 980 px, liste en mobile) à la place des chiffres [x] ;
  00:03 chiffres au format B (`plaque-libre` + nouvelle classe globale `plaque-large`, prioritaire) ; témoignages de la
  landing : réglage de la liste « seulement ceux qui ont une citation » (`avec_citation`, meta `_rolling_citation` ≠ '')
  + classe de grille `temoins-courts` (citation 16 px, cartes alignées en haut) ; 00:04 timecode « 00:30:00 » dans le
  cadre du calendrier (comme Contact). Leçons : lancer `python3 -I outils/classes_fiches.py` avant construire.sh (sinon
  « Unknown global class label » et le bloc n'est pas reconstruit, construire.sh renvoie quand même 0 : chercher ERREUR
  dans la sortie) ; dans `plaque-libre`, `.chiffre-verre` d'un conteneur Elementor a `width: 100 %` ⇒ `width: auto`.
- **Maquette « Retours d'expérience »** (`maquettes/rex/rex.html`, copie /workspace/maquette-rex.html, **à valider**) : REX =
  guide (lien HubSpot) avec thème « Retours d'expérience » (filtre de la page Guides) + nouveau champ « Témoignage lié »
  (un REX a toujours un témoignage). Nom de page proposé « Guides et retours d'expérience » (« cas client » = déjà les
  témoignages ; idée cliente « Guides et cas clients »). Carte : bande « Retour d'expérience » (champ type_doc), sans image
  = logo du client du témoignage lié, bouton « Télécharger » pour toutes les cartes, lien « Lire le témoignage » (si récit).
  Fiche témoignage : encart après le récit, avant « Les autres vidéos » (validé dans le principe) ; fiche réalisation :
  ligne discrète sous la citation (REX du témoignage lié, validé dans le principe). Pas de lien dans les cartes des listes.
- **0.5.15 : retours d'expérience (REX), maquette validée « go pour tout », nom de page « Guides et REX »** : REX = guide
  avec thème `retours-experience` (« Retours d'expérience », créé une fois par l'installateur via `ajouts` de la
  taxonomie : fait `terme:<tax>:<slug>`, un terme supprimé par la cliente ne revient pas) + champ guide `temoignage`
  (relation). `Rolling_Contenus::rex( $id )` (témoignage, ou témoignage lié d'une réalisation) ; contexte `contexte-rex`
  + repère `si-rex`. **Leçon : Elementor lance `before_render` puis rend le contenu, et lit `should_render` APRÈS** ⇒ un
  repère posé sur le conteneur « contexte-… » lui-même se lit dans le contexte (le guide) : `si-rex` vrai si le contenu
  courant est un guide. Corrigé aussi : `si-contexte` hors contexte relisait « ! id » (la section « Le contexte. » du
  témoignage s'affichait même vide) ⇒ seulement dans un contexte. Carte guide : couverture logo du client si pas
  d'image (`couverture-logo`, `logo-couverture`, repère `si-temoignage-logo`, balise Image `source=temoignage` qui lit le
  champ `temoignage` du guide), bouton « Télécharger », lien « Lire le témoignage » (si-temoignage-recit). Fiche
  témoignage : `encart-rex` (carte-verre, couverture 220 px + textes, avant « Les autres vidéos ») ; fiche réalisation :
  `rex-ligne` sous la citation. Page Guides : H1 « Guides et REX, à télécharger. », filtre « Tout voir ». Bloc Ressources :
  carte « Guides et REX ». encart-rex / rex-ligne dans `$prioritaires` (sinon carte-verre les remet en colonne).
- **Maquette « Page Réalisations plus stylée »** (`maquettes/realisations-v2/realisations.html`, copie
  /workspace/maquette-realisations-v2.html, **à valider**) : grille jugée « scolaire » ⇒ A mur de montage (conseil : tailles
  selon le format, dernière en grand 2×3, 9:16 en hauteur, cartes qui montent en sortant du flou en cascade) ; B pistes de
  montage (une piste par cas d'usage, V1/V2, règle, défilement horizontal, tête de lecture rouge qui allume les vidéos) ;
  C ouverture cinéma (dernière réalisation en tête avec avant / après, grille régulière, chaque vignette s'ouvre par deux
  bandes noires + REC, en cascade). Combinables (A + C). Une seule fois, rien avec « réduire les animations ».
  Articles et pages de texte : rappel donné = éditeur WordPress (compositions Rolling vues dans l'outil d'insertion sur ma
  copie ; chez la cliente introuvables : captures demandées) ; page de texte = modèle de page « Page de texte… » (ne pas
  insérer « Rolling · Page de texte » dans Elementor : « Retour à l'éditeur WordPress »).
- **0.5.16 : page Réalisations = A « mur de montage » (go A)** : classe de grille `mur-montage` (widget Liste : si la
  classe est présente, chaque carte va dans `div.mur-case`, la 1re en `mur-large` (2 col × 4 rangées), les réalisations
  dont un format a un slug contenant `9-16` en `mur-haute` (3 rangées) ; grille 4 col, rangées 136 px, `dense` ; 3 col
  sous 1024, 2 en mobile, durée masquée sur les petites cases mobiles). Apparition : rolling.js pose `rl-anime` sur le mur
  (pas dans l'éditeur, pas avec « réduire les animations », sans script tout reste visible) puis `vu` sur chaque case
  qui entre dans l'écran (90 ms d'écart entre cases arrivées ensemble), keyframes `rl-mur` (flou + montée). FAQ des
  articles : puces qui débordaient (liste avec 20 px de marge dans `.wp-block-details`) ⇒ `padding-left: 46px`.
- **Elementor 4.4.1** (16/11/2026) testé sur wp2 avec le thème 0.5.16 : page d'essai de l'accueil identique à wp 4.3.6
  (0,01 % de pixels différents, même hauteur), axe 0, éditeur sans erreur JS ⇒ mise à jour sans risque pour le thème.
  wp2 est maintenant en 4.4.1 (zip dans scratchpad/plugins/el441).
- **Maquette « Ressources dans les articles »** (`maquettes/article-ressources/article-ressources.html`, copie
  /workspace/maquette-article-ressources.html, **à valider**) : A fin d'article avant « À lire aussi » = « Nos autres
  ressources. » (version compacte du bloc Ressources : Guides et REX, prochain webinar, dernier replay ; rubrique vide
  masquée, pas de « Bientôt ») ; B colonne de côté = dernier guide (couverture + Télécharger) entre Partager et Réserver ;
  C carte automatique après le 2e H2 (prochain webinar sinon dernier guide ; rien si < 3 chapitres). Conseil A + B.
- **Ressources dans les articles, retours** : **B validé** (dernier guide dans la colonne de côté) ; **C validé** (carte au
  milieu du texte, seulement à partir de 4 H2) ; A « plus de visuels » et pas encore de replays ⇒ maquette
  `maquettes/article-ressources/article-ressources-2.html` (copie /workspace/maquette-article-ressources-2.html, **à valider**) :
  A1 trois cartes illustrées (conseil : couverture du dernier guide, visuel de date du prochain webinar, vignette de la
  dernière réalisation qui remplace « Replays » tant qu'il n'y en a pas) ; A2 guide en grand + 2 lignes ; rubrique vide
  masquée (2 cartes si pas de webinar).
- **0.5.17 : ressources dans les articles (A1 + B + C validés)** : fin d'article `bloc-ressources-article` (carte-verre,
  repère `si-ressources` = au moins un guide / prochain webinar / replay ou réalisation) + grille `ressources-article`
  (auto-fit ≥ 200 px) de `carte-ressource` (image en haut ; en mobile, lignes 112 px + texte) : dernier guide
  (`couvertures_guide` = image > logo du client > `couverture-degrade` + picto), prochain webinar (image ou
  `webinar-affiche affiche-petite`), contexte `replay-ou-realisation` (dernier replay, sinon dernière réalisation ;
  `si-type--webinar` / `si-type--realisation`). Colonne : `guide-cote` (dernier guide en petit, horizontal pour ne pas
  rallonger la colonne collante). Milieu : modèle `c11-encart-article` (« Rolling · Encart au milieu des articles »,
  choisi aussi dans Personnaliser via FICHES['encart']) inséré par le widget Texte de l'article avant le 3e H2 si
  ≥ 4 H2 (réglage `encart`), contexte `webinar-ou-guide`, classes `encart-milieu`, `date-mini*` ; enveloppe
  `.rl-encart-milieu` et règles `& a` / `& img` de `article-texte` en `:not(.rl-encart-milieu …)`. Leçon test : réinstaller
  la même version ne relance pas l'installateur ⇒ `Rolling_Installateur::installer()` à la main.
- **Maquette « Équipe en carrousel »** (`maquettes/equipe-carrousel/equipe-carrousel.html`, copie
  /workspace/maquette-equipe-carrousel.html, **à valider**) : demande « une version des photos en carrousel, un modèle
  supplémentaire » = les portraits de l'équipe (« Le réseau de talents qui sublime vos vidéos. », 7 portraits, le 7e seul
  à la ligne). A bande qui défile en continu comme les logos (conseil : arrêt au survol / clavier, sur plusieurs lignes
  avec « réduire les animations ») ; B carrousel à flèches + points (scroll-snap, rien d'automatique) ; C portrait à la une
  au centre, voisins atténués, suivant toutes les 3 s + flèches. Version à côté de l'actuelle dans Mes modèles.
- **0.5.18 : équipe qui défile (A validé)** : bloc « Rolling · 09 Le meilleur de l'humain (version équipe qui défile) »
  (`09-humain-defile` dans BLOCS_VARIANTES, `bloc_humain(defile)` + `portraits_equipe()`) : portraits dans
  `masque-bords` > `equipe-defilante` (même mécanique que les logos : animation `rl-defile` en brut, copie par rolling.js,
  arrêt au survol / clavier). Textes de la cliente repris : Marion = « Responsable Agence vidéo », Marion en dernier
  (Tomasz ajouté par elle sur son site : pas de photo dans le thème). **Corrigé** : avec « réduire les animations »,
  rolling.js ne reconnaissait plus les bandes (animation coupée) ⇒ repère `--rl-defile: 1` (propriété `@property` non
  héritée dans rolling.css) sur `logos-defilants` et `equipe-defilante` ⇒ `rl-defile-statique` (plusieurs lignes).
  `classes_fiches.py` : `feuille_editeur` lit `c[2]` (tuples à 4 éléments acceptés).
- **0.5.19 : L'agence 00:03 qui défile** : bloc « Rolling · L'agence · 00:03 L'équipe (version qui défile) »
  (`p-agence-4-equipe-defile`, ajouté à BLOCS_VARIANTES après sa définition : `bloc_agence_equipe(defile)` +
  `grille_ou_bande()`) : créas et experts IA en deux bandes `masque-bords` > `equipe-defilante`. Marion (créas) =
  « Responsable Agence vidéo » et en dernier, comme à l'accueil, dans les deux versions. Test wp2 : attendre
  `wp option get siteurl` (et pas `wp core version`, qui répond sans base) avant d'installer.
- **0.5.20 : cartes des listes cassées dans l'éditeur** (carte « à la une » du blog en rangées, bon sur le site) :
  reproduit. Cause : dans l'aperçu, l'éditeur injecte après coup un `<style>` avec ses styles de base
  (`.elementor .e-flexbox-base { flex-direction: row }`, même spécificité que les styles locaux) **après** les feuilles
  `local-<id>-preview-desktop.css` des modèles de cartes ⇒ les styles locaux des cartes perdent. Correctif : rolling.js
  (aperçu seulement) déplace en fin de `<body>` les feuilles `local-*` sauf celle de la page modifiée (`elementor-preview=`),
  dont l'éditeur gère les styles en direct ; + `Rolling_Dynamique::styles_apercu` (crochet `elementor/preview/enqueue_styles`)
  charge les styles de tous les modèles `c*` et de l'encart. Vérifié sur wp (4.3.6) et wp2 (4.4.1).
- **0.5.21 : cartes toujours cassées dans l'éditeur chez la cliente après 0.5.20** (non reproduit chez moi, même avec
  « Méthode d'impression CSS : intégrée ») ⇒ correctif rendu indépendant de l'ordre : rolling.js recopie, dans l'aperçu,
  les règles des feuilles `local-*` des modèles (sauf la page modifiée) dans un `<style data-rl-renfort>` avec
  `html body ` devant chaque sélecteur (spécificité > `.elementor .e-flexbox-base`). Vérifié sur wp et wp2, y compris
  « pire cas » simulé (feuilles remises en tête + styles de base ajoutés en dernier) : colonne. Si ça persiste chez elle :
  demander version affichée, Ctrl+F5, et le HTML de l'aperçu (feuilles `local-*` présentes ?).
- **0.5.22 : toujours cassé chez la cliente en 0.5.21** ⇒ correctif côté serveur, sans fichier ni ordre :
  `Rolling_Dynamique::renfort_apercu` (wp_footer, aperçu de l'éditeur seulement) lit les styles locaux des modèles de
  cartes (c*, encart, modèles choisis dans les listes de la page ; pas la page modifiée) et les écrit dans
  `<style id="rl-renfort-cartes">` avec `Styles_Renderer::make( breakpoints, 'html body .elementor' )` (même API en 4.3.6
  et 4.4.1). Test `scratchpad/ed-blog5.js` : feuilles `local-*` des modèles bloquées + renfort JS retiré + styles de base
  ajoutés en dernier ⇒ colonne ; témoin sans le style PHP ⇒ ligne (bug reproduit ainsi).
- **0.5.22 validée** (« c'est fixé » pour le blog). **Maquette « Nouveautés »** (`maquettes/nouveautes-v6/nouveautes.html`,
  copie /workspace/maquette-nouveautes.html, **à valider**) : 1 landing = vrai showreel Vimeo dans le hero (widget Vidéo,
  nouveau réglage lien ; titre / nom / photo du compte Pitchy masqués par `title=0&byline=0&portrait=0`, sinon réglage
  d'intégration Vimeo ou .mp4) : A lecture au clic dans le cadre (conseil), B aperçu muet en boucle (Vimeo payant
  « background » ou .mp4) ; 2 témoignages plus de verre + cartes à la taille de la citation (courte / moyenne / longue) :
  A mur maçonné en colonnes (conseil), B rangées équilibrées (largeur selon la longueur) ; 3 réalisations : nouveau
  contenu « Extraits vidéo » (fichier court muet, format, description, sans fiche) : A glissés dans le mur toutes les
  3 réalisations, pastille « Studio Rolling » (conseil), B bande « Dans les coulisses » entre les rangées ; + cadres en
  verre et titre en plaque de verre fumé ; 4 offres « L'illimité qui tient ses promesses » : A 4 cartes en verre (conseil),
  B bande sombre, C cartes à jouer en éventail (phrases d'explication proposées) ; 5 L'agence : A fondu vertical créas
  couleur → experts IA sombre avec pastille « Ensemble sur chaque projet » (conseil), B côte à côte fondu au centre ;
  6 contact « melting pot » après formulaire + FAQ : A mosaïque en verre (showreel, 2 citations, 13 / +500, 2 réalisations,
  « Toutes nos réalisations ») (conseil), B bande de réalisations + citations qui tournent, C showreel cinéma + bulles.
- **Choix maquette Nouveautés** : **1B** (aperçu Vimeo muet en boucle + son au clic ; la cliente aura le lien adapté),
  **2A** (mur maçonné), **3A** (extraits glissés dans le mur), **4C** (cartes à jouer ; poids : CSS seul, 0 image / 0 script),
  **5B** (côte à côte), **6C** (showreel + bulles) + nouvelle partie dessous. Maquette de suite
  (`maquettes/nouveautes-v6/suite.html`, photos de l'équipe = jetons `__EQ_<fichier>__` remplacés à la copie
  /workspace/maquette-nouveautes-suite.html depuis scratchpad/imgs, **à valider**) : 4C sans « Règle n°1 » (chiffre en
  double) ; 5B en vrai (pleine largeur, fond pêche + halos à gauche, fond sombre nuit → encre + halos à droite, fondu au
  milieu sur toute la hauteur, pastille « Ensemble sur chaque projet » verticale ; mobile : créas, bande de fondu avec la
  pastille, IA) ; **pas de défilement en 5B** (3-4 personnes visibles par moitié) : grilles 4 + 3 et 3 + 2 ; phrases sous
  les titres proposées ; poste de Tomasz à compléter ; 6C puis « Née chez Pitchy, déjà à l'œuvre. » : A plaque de chiffres
  (13, +500, 1 studio, 1 plateforme) + 3 dernières réalisations + bouton (conseil), B chiffres en colonne + 3 cartes portrait.
- **Choix finaux** : 6C + **A** (plaque de chiffres + 3 réalisations) ; 4C ok ; 5B jugé bizarre ⇒ **retour à l'horizontal (5A, bandes)**.
- **0.6.0 (livrée)** :
  - « +500 » partout (chiffres en verre `plus, 5, 0, 0`), tirets longs retirés des textes (cases « — » du comparatif ⇒ « Non »).
  - **Showreel par lien** : widget Vidéo, `source=lien` + `lien`, `titre`, `mode` (boucle | clic), `son` ; rend un calque
    `div.video-calque[data-rl-video][data-rl-mode]` + `button.video-calque-bouton` (+ `span.pastille-son`) posé sur l'image
    d'affiche (élément Image d'Elementor) d'un cadre `cadre-showreel` (règle `& > .elementor-widget-rolling-video` en absolu).
    rolling.js `lecteurLien()` : Vimeo `player.vimeo.com/video/ID?h=…&dnt=1&title=0&byline=0&portrait=0&badge=0&vimeo_logo=0`
    + `background=1&muted=1&loop=1` (aperçu, compte Vimeo payant) ou `autoplay=1` (clic) ; YouTube nocookie ; fichier.
    Aperçu chargé quand visible, après `load` ; rien avec « réduire les animations » ni dans l'éditeur ; hôte `rl-boucle` /
    `rl-lit`. Fond iframe en `container-type: size` (max(100cqw, 177.78cqh)) ; vidéo en `object-fit: cover`.
    Landing (hero) et Contact (showreel) ; lien vide = rien sur le site, rappel dans l'éditeur.
  - **Témoignages** : `mur-temoins` devient un mur maçonné (colonnes CSS), le widget Liste met chaque carte dans
    `div.macon-case macon-court|moyen|long|une` (citation ≤ 70 / ≥ 170 caractères) ; taches de couleur en pseudo-éléments de la
    grille ; automatique sur la page existante (même classe).
  - **Extraits vidéo** : type `rolling_extrait` (menu sous Réalisations : `menu` dans types()), champs video / format
    (16-9, 9-16, 1-1) / description ; widget Liste réglages `extraits` (défaut 2 depuis 0.6.1, mur de montage seulement) et `texte_extrait`
    (« Studio Rolling ») ; `div.mur-case.extrait-case > .extrait-cadre > .extrait-video[role=img]` (vidéo muette, rolling.js la lance
    quand visible) ; mur de montage en verre (cadre 7 px, plaque de titre en verre fumé rgba(22,14,52,.82) : .72 échouait au
    contrôle Ally) ; filtres (`filtres-liste`) dans une barre en verre partout.
  - **Offres 4C** : `promesses-jeu` > `cartes-jeu` > `carte-verre carte-jeu` (chiffre-jeu, titre-xs h4, texte-petit, note-jeu) ;
    inclinaisons par `nth-last-child`.
  - **L'agence 5A** : `equipe-bandes` > `bande-creas` / `bande-fondu` (pastille-ensemble) / `section-sombre bande-ia`
    (halos propres, pas halos-nuit : coupure nette en haut) ; experts en `portrait-sombre`.
  - **Contact** : `p-contact-4-showreel` (cadre-cinema, bulles = widget Liste `carte_theme=bulle` ⇒ modèle `c12-carte-bulle`,
    `citations_courtes` = les 3 plus courtes, classe `bulles-showreel` ; `scene-bulles` met le widget en absolu) et
    `p-contact-5-realisations` (plaque-libre + plaque-large, Liste `grille-verre`, extraits=0).
  - Leçons : dans l'aperçu de l'éditeur, le widget Liste écrit un `<style>` avant chaque carte ⇒ compter les cartes avec
    `div:nth-last-of-type()` (pas nth-last-child) ; les classes globales des cartes arrivent quelques secondes après
    l'ouverture de l'éditeur (attendre ~12 s avant une capture) ; `bouton-son` existait déjà (nom de classe en double ⇒
    `classes_fiches.py` refuse) ; page d'essai modifiée à la main : supprimer `_elementor_element_cache`.
- **0.6.1 (retours 0.6.0)** :
  - Filtres des listes : seulement les termes utilisés par le type de la liste (`object_ids` = contenus publiés ;
    webinars / replays = `Rolling_Webinars::liste`) : « IA & vidéo » (thème d'un webinar) ne s'affiche plus dans Guides.
  - L'agence : `bande-creas` = fond clair #FAF9F7 + touches orangées (halos en % inclus dans la bande : plus de coupure
    nette aux bords) ; `bande-fondu` = dégradé progressif (smoothstep, 11 arrêts) #FAF9F7 → #17112C sur 260 px (plus de
    bande violette) ; `bande-ia` = section-sombre, halos en % aussi.
  - Mur des réalisations : rangées automatiques, cases normales en 16:9 (`aspect-ratio` sur le cadre), grande case 2 × 2,
    9:16 sur 3 rangées (léger recadrage seulement) ; image des cartes réalisation en « large » ; miniatures YouTube en
    `maxresdefault` (1280 × 720) avec repli `hqdefault` par rolling.js (image de 120 px = pas de HD) ; 1 extrait toutes les
    2 réalisations (défaut du widget + bloc).
  - Showreel en boucle : plus d'image d'affiche visible (classe `cadre-showreel`, CSS brut : `@media (prefers-reduced-motion:
    no-preference) { &:has(.video-calque[data-rl-mode=boucle]) …}` ⇒ image masquée, fond sombre) ; la vidéo se charge dès que
    le cadre est visible et apparaît en fondu (`.pret`, iframe : load + 700 ms ; fichier : playing). Leçon : un `@media` autre
    que `@media(--x)` doit aller dans le champ brut (sinon le convertisseur le jette).
  - **Maquette « Bulles du showreel »** (`maquettes/nouveautes-v6/bulles-contact.html`, copie
    /workspace/maquette-bulles-contact.html, **à valider**) : les bulles cachaient les textes de la vidéo ⇒ A vidéo à 64 %
    au centre, bulles dans les marges (ne mordent que le bord ; « Showreel » et « Activer le son » à droite) (conseil),
    B vidéo pleine largeur, bulles en rangée sous la vidéo (chevauchent 40 px du bas), C une bulle à la fois dans un coin.
  - 404 : rien à régler dans WordPress (gabarit 404 du thème automatique) ; modèle choisi dans Personnaliser > « Rolling :
    modèles des fiches », modifié dans Mes modèles « Rolling · Page 404 ».
- **0.6.2** : bulles du showreel = **option B** (`bulles-showreel` en grille de 3 sous la vidéo, marge -42 px : ne chevauchent
  que le bas ; 1 colonne en mobile ; `scene-bulles` ne met plus le widget en absolu). Mur des réalisations jugé illisible
  (cases trop petites, plaque de titre sur l'image) ⇒ **3 colonnes** (2 sous 1024, 1 en mobile), client + titre **sous**
  l'image dans le cadre en verre (plus de plaque fumée), grande case 2 × 2, 9:16 sur **2 rangées** (≈ 0,64 : peu de recadrage),
  extraits remplissent leur case (min 200 px) ; en mobile, tout en une colonne (9:16 en 4:5).
- **0.6.3** : showreel par lien : pendant l'aperçu en boucle, **le clic active le son sur place** (plus de lecteur Vimeo avec
  bandes noires) : `sonFond()` (fichier : `muted` ; Vimeo : postMessage `setMuted` / `setVolume` ; YouTube : `enablejsapi=1`
  + `unMute`), 2e clic = couper ; pastille « Activer le son » / « Couper le son » (réglage `son_off`), `aria-pressed`.
  Le lecteur classique (avec commandes) ne s'ouvre plus que si l'aperçu n'a pas démarré (« réduire les animations »,
  mode clic). Contact : pastille du son remontée au-dessus des bulles (`scene-bulles .pastille-son` bottom 64 px).
  Mur des réalisations = **vrai mur maçonné** : rolling.js pose `rl-macon` (rangées de 8 px, `--rl-rangs` = hauteur réelle
  de chaque case) ⇒ chaque case garde ses proportions : 16:9 partout (grande case 16:9 sur 2 colonnes, plus aplatie),
  9:16 vraiment vertical (réalisation au format 9:16 et extrait au format 9:16) ; sans script / éditeur : rangées auto.
  Vimeo : son par postMessage non testable ici (player.vimeo.com bloqué) ⇒ à vérifier chez la cliente.
- **0.6.4** : mur des réalisations : **lecture au survol** sans le son (souris seulement, 250 ms de délai ; `data-rl-survol`
  = lien vidéo de la réalisation posé par le widget Liste sur la `mur-case` ; rolling.js réutilise `lecteurLien(url, true)`,
  vidéo `.apercu-survol` dans le `.cadre-photo` (container-type size, iframe en cover), fondu `.pret`, pause / retrait au
  départ de la souris ; rien sur tactile, éditeur, « réduire les animations »). Extraits : format **1:1** (`extrait-carre`),
  **ordre** = champ « Ordre » (encadré « Attributs », `page-attributes` ; la requête trie déjà menu_order puis date),
  **liseré différent** : cadre pêche → lilas avec bordure pointillée orange (insert, pas un cas client).
- **Maquette « Cadre des extraits et filtres »** (`maquettes/nouveautes-v6/extraits-filtres.html`, copie
  /workspace/maquette-extraits-filtres.html, **à valider**) : pointillés des extraits jugés « brouillon » ⇒ A viseur de
  caméra (4 coins + REC, cadre pêche léger) (conseil), B liseré dégradé pêche → violet, C pellicule (perforations, cadre
  foncé), D verre fumé sombre + note « dans les coulisses », E onglet « Studio Rolling » au-dessus. Filtres trop hauts
  (3 lignes), pas de menu déroulant : F1 onglets Métier / Cas d'usage / Format + une rangée (point pêche = filtre choisi)
  (conseil), F2 une seule ligne qui défile, F3 phrase à compléter (mots qui changent au clic), F4 bouton « Filtrer » qui
  ouvre les lignes dans la page + filtres actifs en pastilles.
- **0.6.5 (choix 1A + F1)** : extraits = **viseur de caméra** (cadre pêche #FFF7F1 + filet pêche, 4 coins blancs en
  `extrait-video::after`, « REC » + point rouge clignotant en `::before`, pastille en bas à gauche). Filtres = **onglets** :
  rolling.js transforme une `nav.filtres-liste` à plusieurs `ul` en `div.onglets-filtres[role=tablist]` + chaque `ul` enveloppé
  dans `div.panneau-filtre[role=tabpanel]` (role sur l'ul = erreur axe aria-allowed-role / listitem), onglet ouvert = 1er groupe
  avec un filtre choisi, point pêche + texte masqué « (filtre choisi) », flèches gauche / droite ; sans script : une ligne par
  groupe ; mobile : rangée qui défile. **Leçon** : `filtres-liste` est définie dans classes_fiches.py (groupe « Fiches et
  listes », réécrit à chaque construction) : la barre en verre ajoutée en 0.6.0 dans classes.json avait été écrasée.
- **0.6.6** : experts IA de l'accueil qui **défilent en mobile** (nouvelle classe modificatrice `defile-mobile` sur
  `grille-equipe` : en mobile seulement `--rl-defile: 1` + animation `rl-defile` en brut sous `@media (max-width: 767px)`,
  rolling.js clone les portraits ; `.rl-defile-zone { overflow-x: clip }` dans rolling.css ; prioritaire). Chiffres en verre :
  images qui rétrécissent (`max-height` + `flex: 0 1 auto` au lieu d'une hauteur fixe) ⇒ « +500 » entier dans les 2 colonnes
  mobiles de `plaque-chiffres`. SEO des chiffres : images en alt vide, texte « valeur lue » (`texte-masque`, clip) = « +500 »
  d'un seul tenant ⇒ c'est ce que Google et les lecteurs d'écran lisent. Bloc Ressources mobile cassé chez la cliente (texte
  dans la colonne du picto) **non reproduit** (wp 4.3.6 et wp2 4.4.1 corrects) ⇒ le placement du picto est aussi porté par
  `picto-rubrique` (brut mobile `grid-column: 1; grid-row: 1 / span 3`), sans dépendre des règles `& > *` de `carte-rubrique`.
- **0.6.7 (retours mobile)** : calendrier HubSpot : il lui faut **≥ 345 px** (grille 7 × 50 px), sinon barre de défilement
  horizontale dans l'iframe ; la hauteur forcée (680 px !important) ajoutait une barre verticale ⇒ hauteur laissée à HubSpot,
  `zone-calendrier` en **pleine largeur d'écran en mobile** (brut : `width: 100vw; margin-left/right: calc(50% - 50vw)`),
  `landing-action` sans cadre en mobile (fond = 2 couches transparentes, Ally) ; tester avec
  `chromium.launch({ ignoreDefaultArgs: ['--hide-scrollbars'] })` (barres classiques). Page de texte ouverte avec
  « Modifier avec Elementor » : Elementor convertit le contenu en conteneur + widget « Éditeur de texte » ⇒ police Roboto,
  hors des règles `& > p` d'`article-texte` ⇒ règles doublées pour `.elementor-widget-text-editor > …` (+ `.elementor-text-editor`
  de l'éditeur), padding des `.e-con` à 0. Cartes à jouer mobile : placement porté par `note-jeu` (ligne 2, pleine largeur,
  sans retour à la ligne) et `chiffre-jeu` (colonne 1) en brut, plus par `:last-child`. Programme du webinar : 15 px en mobile.
  Passage mobile 360 / 390 de 16 pages (`scratchpad/audit-mob.js` : débordements, textes < 12 px ; `full390.js` + `planche.py`
  pour revoir les captures, images différées forcées). **Toujours revoir le mobile avant de livrer.**
- **0.6.8** : page Réalisations « ancien design » dans l'éditeur = mur sans calage maçonné (trous) et filtres sans onglets :
  rolling.js s'arrête tôt dans l'éditeur (`if ( editeur ) { … return; }`) et la liste y est affichée après le script.
  ⇒ `murMacon()` et `ongletsFiltres()` placés **avant** ce retour, et dans l'éditeur un MutationObserver (150 ms) les relance
  sur chaque liste réaffichée (garde : `mur.rlCaler`, classe `rl-onglets`) ; ResizeObserver + chargement des images recalent
  le mur. Test : `scratchpad/ed-mur2.js <base> <id>` (wp 4.3.6 et wp2 4.4.1).
- **0.6.9** : calendrier « trop large, pas d'espace à gauche et à droite » ⇒ `zone-calendrier` mobile = largeur de la page,
  au moins 357 px (345 + 2 × 6 de padding) : brut `width: max(357px, 100%)` + `margin-left/right: calc(50% - max(178.5px, 50%))`,
  rayon 22 px ; marges normales dès ~375 px, presque bord à bord à 360. `zoom` sur l'iframe ne marche pas (HubSpot voit la
  largeur avant zoom, la barre revient). **Maquette « Promesses de l'accueil »** (`maquettes/offre-accueil/promesses-accueil.html`,
  copie /workspace/maquette-promesses-accueil.html, **à valider**) : 00:07 de l'accueil en cartes comme Offres, ordinateur
  + mobile (requêtes de conteneur) : A cartes à jouer courtes sans phrase, 2 × 2 en mobile (conseil) ; B mêmes cartes,
  titres reformulés (aucune phrase identique à Offres) ; C étiquettes collées (stickers). Doublon : pas de pénalité pour
  un petit bloc répété.
- **0.6.10 : promesses de l'accueil = A validé** : bloc `10-offre`, la carte « L'illimité qui tient ses promesses : » (liste)
  devient un titre h3 centré + `cartes-mini` > 4 × `carte-verre carte-mini` (`chiffre-mini`, `titre-mini` h4, `note-jeu`),
  mêmes titres que la page Offres sans la phrase ; éventail par nth-last-child, 2 × 2 sous 1024 px (600 px max) et en mobile.
  `carte-mini` prioritaire. Classe `carte-promesses` gardée (pages existantes). La cliente remplace le bloc sur son accueil.
- **0.6.11** : showreel de l'accueil (fichier .mp4 par lien : gris sur le site chez la cliente, bon dans l'éditeur ; lien
  direct .mp4 testé chez moi = lu ⇒ cause côté lien / consentement / codec, non reproduite) ⇒ `03-showreel` = **par lien**
  comme la landing (`cadre-photo cadre-16-9 cadre-showreel` + widget Rolling · Vidéo `source=lien mode=boucle` ; Vimeo
  `background=1` : ni titre, ni photo, ni logo ; son au clic sur place) ; ancienne version fichier =
  `03-showreel-fichier` (BLOCS_VARIANTES, `bloc_showreel(fichier)`). Avant / Après : nom du client facultatif
  (`client-exemple`, `&:empty { display: none }` ; rappel en pointillés dans l'éditeur via `apercu_contextes`).
  Carte réalisation : lien « Voir la réalisation → » (`temoin-lien`, pas « En savoir plus » : texte de lien non
  descriptif pour Lighthouse / SEO).
- **Maquette « Accueil v2 »** (`maquettes/accueil-v2/propositions.html`, copie /workspace/maquette-accueil-v2.html, **à valider**,
  ordinateur + mobile) : 1 Avant / Après cliquable : A toute la carte + « Voir la réalisation → » (conseil), B pastille sur
  l'image au survol, C bouton lecture dans la carte (lien vidéo en plus) ; lien de chaque exemple réglé dans Elementor.
  2 **Page Offres mise de côté** : menu « Offres » ⇒ ancre #offres de l'accueil ; nouveau modèle 00:07 complet : A tout
  visible (tableau, « Ce que Création + Tournage ajoute », promesses, bouton), B 2 cartes prix + tableau dépliable
  « Comparer en détail » (conseil). 3 **Nouvelle page « Pourquoi la vidéo »** (/pourquoi-la-video/ proposé) : hero,
  00:01 constat (Survolées / Pas finis / Oubliés, chiffre à sourcer), 00:02 « Un contenu, plusieurs vidéos » (section
  sombre, 1 article au centre, 6 vidéos autour), 00:03 Avant / Après (3 exemples + Toutes nos réalisations), 00:04
  bénéfices, 00:05 FAQ, 00:06 Ressources, appel final.
- **Accueil v2, retours** : Avant / Après = A mais « trop de texte à côté » ; **Offres = A** (tout visible : tableau,
  « Ce que Création + Tournage ajoute », promesses ; page Offres mise de côté, menu ⇒ #offres) ⇒ à intégrer avec le reste.
  **Maquette tour 2** (`maquettes/accueil-v2/propositions-2.html`, copie /workspace/maquette-accueil-v2-tour2.html, **à valider**) :
  Avant / Après A1 une ligne + flèche ronde, **A2 tout dans une carte en verre, client en pastille sur l'image (conseil)**,
  A3 « Au départ ··· à l'arrivée » ; 00:02 avec flèches en pointillés animées (SVG `stroke-dasharray`) : **V1 éventail
  (conseil)**, V2 chaîne contenu → équipe → vidéos, V3 arbre 1 contenu / 3 angles / 6 vidéos (mobile : vertical).
  **Page réécrite « Du contenu à la vidéo »** (/du-contenu-a-la-video/) : la 1re version reprenait des textes existants
  (« Ce que ça donne, concrètement. », « Et avec vos contenus, ça donnerait quoi ? », « 3 premières vidéos », constat
  Survolés / Oubliés, bénéfices de la landing) ⇒ nouvel angle (comment un contenu devient une vidéo ; requêtes
  « transformer un PowerPoint en vidéo », « contenu en vidéo » ; ne pas concurrencer l'accueil sur « agence vidéo ») :
  hero + bande de réassurance (DA, confidentialité, Europe, sans devis), 00:01 types de contenus (PPT, article, rapport,
  webinar, formation), 00:02 schéma, 00:03 méthode en 4 temps (qui fait quoi), 00:04 cas client réel [à fournir : capture
  du document, vidéos, 2-3 chiffres, citation], 00:05 Avant / Après « D'autres contenus, déjà en vidéo. », encart
  « Combien ça coûte ? Rien de plus » + Voir les formules, 00:06 FAQ, 00:07 Ressources, appel « Quel contenu
  transformez-vous en premier ? ». **Toujours vérifier les doublons de textes avec blocs.py avant de proposer une page.**
- **0.7.0 (choix : Avant / Après A2, schéma V1 avec « 1 document », page go, offres A)** :
  - Avant / Après : `exemple()` = `carte-verre carte-exemple` (lien `/realisations/` à régler dans Elementor) > `cadre-exemple`
    (barre au survol en ::after) + `pastille-client` (vide = masquée) ; `bas-exemple` = légende + `fleche-ronde` (texte « Voir
    la réalisation » caché par text-indent, lu par Google ; flèche en ::after). Liste `EXEMPLES` partagée.
  - **10-offre = offres complètes** (`bloc_offre_complete()` : copies profondes du tableau / prix mobile / cartes « ajoute »
    de la page Offres + promesses `cartes-mini`) ; ancienne version courte = `10-offre-courte` (variante). Tous les liens
    « Voir les offres » ⇒ `/#offres` ; menu principal d'une nouvelle installation : « Offres » = `/#offres` ;
    `Rolling_Seo::offres_vers_accueil` : `/offres/` en 404 (page dépubliée) ⇒ 301 vers `/#offres`.
  - **Page « Du contenu à la vidéo »** (`/du-contenu-a-la-video/`, créée une fois, `depuis 0.7.0`) : blocs `p-contenu-01…09`
    + Page complète (sans 13-final : appel final propre « Quel contenu transformez-vous en premier ? ») ; schéma = classes
    `schema-eventail`, `source-schema` (pointillés en ::before/::after), `branches-schema` + `branches-gauche|droite`,
    `sortie-schema`, `texte-sortie` (+ `forme-verticale|carree`) ; pointillés animés `rl-tirets-h/v` (rolling.css) ; mobile :
    document en haut, trait vertical à gauche. Autres : `bande-reassurance`, `grille-types`/`carte-type`/`vers-type`,
    `carte-temps`/`numero-temps`/`etiquette-qui`, `cas-client`/`apercu-cas`/`chiffres-cas`/`chiffre-cas`/`citation-cas`,
    `encart-prix`. Ressources = copie du bloc 14 avec timecode 00:07 (`trouver()`).
- **0.7.1 (retours articles)** : vidéo Vimeo collée dans un article (bloc Intégrer / Vimeo) qui débordait sur le texte ⇒
  `enrichir()` remplace les iframes `is-provider-vimeo` par le cadre `video-fiche` (affiche = vignette Vimeo par oEmbed,
  `image_vimeo()`, transient 1 semaine ; lecture au clic par `lecteurLien` : title / byline / portrait / logo masqués) ;
  `article-texte` : `.wp-block-embed__wrapper` en 16:9 avec iframe absolue (tout autre intégré). Composition « Auteur » =
  **Inès, Rédactrice chez Rolling** (photo equipe-ines). Cartes « Pour aller plus loin » : visuel de date du webinar sans
  image trop haut chez la cliente (non reproduit, affiche-petite non appliquée) ⇒ forcé par `carte-ressource > .webinar-affiche`
  (4:3, flex none, padding 0 22px). Fiche article : temps de lecture retiré sous le titre (gardé dans la colonne).
  **Maquette « Newsletter »** (`maquettes/newsletter/newsletter.html`, copie /workspace/maquette-newsletter.html, **à valider**) :
  nom proposé « La lettre Rolling », formulaire HubSpot (bloc HTML) ; blog A bandeau en verre sous l'article à la une
  (conseil), B carte sombre dans la grille, C grande bande sombre après les articles ; articles D fin d'article « Cet article
  vous a été utile ? » + encart dans la colonne (conseil) ; pied de page E ligne d'inscription (Personnaliser, rien si vide) ;
  ailleurs : Guides / Webinars / Replays (même bandeau), « Me prévenir » du bloc Ressources, 404 ; pas sur landing ni Contact.
- **0.7.2 : newsletter « La lettre Rolling » (choix 1A, 2D, pages Ressources ; pied de page E non fait, la cliente hésite)** :
  code HubSpot collé une fois dans Personnaliser > « Rolling : newsletter » (`rolling_newsletter_code`) ; widget
  `rolling-newsletter` (classe `zone-newsletter` qui habille le formulaire HubSpot ; vide = rappel dans l'éditeur seulement) ;
  repère `si-newsletter` (tout masqué tant que le code est vide). Blog : widget Liste réglage `newsletter_apres` (n° de carte,
  1 dans le bloc) ⇒ modèle `c13-bandeau-newsletter` dans `div.rl-encart-liste` (pleine largeur). Guides / Webinars / Replays :
  blocs `p-…-25-newsletter` avant la FAQ. Article (f3) : bandeau `newsletter-article` en fin de texte + carte de la colonne
  (`newsletter-colonne`). Classes `bandeau-newsletter` (prioritaire), `newsletter-article` / `newsletter-colonne`
  (très prioritaires : modificateurs d'une classe prioritaire), `inscription-newsletter`.
- **0.7.3** : newsletter absente d'un article publié chez la cliente : sur wp2 l'encart apparaît dès que le code HubSpot est
  collé (rien tant que Personnaliser > « Rolling : newsletter » est vide, voulu) ⇒ causes : code non collé ou modèle
  d'article modifié (V2). Nom du client des Avant / Après : en A2 il est dans `pastille-client` (vide par défaut, invisible
  dans l'éditeur) ⇒ rappel en pointillés dans l'éditeur (`apercu_contextes`). Widget Vidéo : mode `muet` (« Vidéo muette en
  boucle, sans son ni bouton ») = `div.video-calque[data-rl-mode=muet][aria-hidden]`, `videoMuette()` dans rolling.js (fondu
  sur l'image, pause hors écran pour un fichier, rien avec « réduire les animations ») ; classe `cadre-video-muette` (widget
  en absolu sur l'image) ; page contenu : hero, 3 vidéos du cas client, 3 exemples (`exemple(video=True)`, `video_muette()`).
  **Mot-clé « content repurposing »** : H1, chapô (gras), question de FAQ ; adresse `/content-repurposing/` (installation.json)
  + 301 depuis `/du-contenu-a-la-video/` si la page existe (`Rolling_Seo::offres_vers_accueil`) ; title / description Yoast
  proposés. WordPress ne redirige pas l'ancien slug d'une page (seulement les articles).
- **0.7.4** : Contact « déjà à l'œuvre » = cartes au format des Avant / Après de l'accueil : modèle `c14-carte-exemple`
  (carte-exemple liée à la fiche, image à la une, nom du client en `pastille-client si-client`, légende avant / après, titre
  h3 si pas de « Au départ » = repère `sans-avant`, flèche ronde) ; widget Liste `carte_theme=exemple`
  (`c1-carte-realisation-exemple` ⇒ c14). Équipes qui défilent en mobile : rolling.js fixe la durée à ~55 px/s
  (`.equipe-defilante, .defile-mobile`, ≤ 767 px ; 15-18 s au lieu de 40-50 s). Newsletter : formulaire HubSpot réel
  reproduit (hôtes js.hsforms.net, forms.hsforms.com, forms-na1.hsforms.com ajoutés ; en test, v2.js servi par route
  Playwright car `//js.hsforms.net` passe en http en local) : case de consentement stylée comme un champ (géante) + puce
  ⇒ `zone-newsletter` : liste sans puce, case 18 px, libellé en ligne, bouton à côté du champ (ordre : champ, bouton,
  consentement, mentions).
- **Maquette « Retours 0.7.4 »** (`maquettes/retours-0-7/retours.html`, générée par `scratchpad/c067/gen-retours.py`, copie
  /workspace/maquette-retours-074.html, **à valider**, « ne refais pas de thème avant validation ») : 1 accueil 00:02 (encarts
  ≈ 810 px, trop hauts) : A encarts compacts ≈ 520 px (conseil : constat 1 phrase, une liste de 4 coches sur 2 colonnes),
  B onglets, C encarts qui s'empilent ; 2 storyboard « Sans IA » = dessin de la maquette d'origine
  (scratchpad/maquette/G2v11-polices.dc.html, lignes ~1055-1090) à capturer en image ; 3 filtres vides : 4 phrases (conseil
  « Encore en salle de montage. ») ; landing : 4 H2 du constat FOMO (5 propositions, conseil « Vos concurrents passent à la
  vidéo. Et vos contenus, ils attendent quoi ? »), 5 experts IA + créas (A deux cartes, conseil ; B chaîne ; C bande de
  portraits avec badge), 6 prix à l'année (A « Soit 15 000 / 25 000 € / an » visibles, conseil ; B bascule), 7 bénéfices en
  4 cartes, 8 ordre : hero, constat, équipe, formules, pourquoi Rolling, échange, Née chez Pitchy, FAQ, final.
  À faire aussi dans la même version : encart newsletter du blog **après toutes les cartes** ; encart de fin d'article cassé
  chez la cliente (non reproduit sur wp / wp2 : texte dans une colonne étroite = grille 3 colonnes de bandeau-newsletter
  appliquée) ⇒ mettre la grille 2 colonnes en `&.newsletter-article` dans bandeau-newsletter (indépendant de l'ordre).
  Médias : l'installateur n'écrit titre / texte alternatif **qu'à la création** d'une image de départ ; `rafraichir_media`
  change seulement le fichier ⇒ les alt / titres de la cliente sont gardés.
- **0.7.5 (choix 1A en gardant tous les textes + empilement, 2, 3 phrase 1, 4-1, 5A, 6A, 7 cartes à jouer, 8)** :
  accueil 00:02 = mêmes textes, encarts 765 / 822 px ⇒ 642 / 651 px (tiennent en 1366 × 768) : `chapitre` colonnes
  0.86fr / 1.14fr (texte plus large ; brut `@media (min-width: 1025px)` + `:has(> .colonne-chapitre:nth-last-child(2))` pour
  l'encart texte à gauche), `colonne-chapitre` gap 14 / padding 26 36, `panneau` 14 18, `photo-chapitre` 540, picto 60 px,
  listes 1.35fr / 1fr, `chapitre-colle` top min(110px, 100vh − 660px) (empilement gardé). Storyboard « Sans IA » = image
  `storyboard-fait-main.webp` (capturée dans la maquette d'origine, `div:has(> .paper)`, alt vide) dans `cadre-storyboard`
  (Offres + 00:07 accueil). Filtres vides des réalisations : « Encore en salle de montage. » + réglage `texte_aucun_detail`
  (anciens défauts gardés pour les autres listes ; la liste des réalisations restée aux textes d'origine prend la nouvelle
  phrase au rendu). Blog : encart newsletter **après la dernière carte** (`newsletter_apres` = 99 : un nombre > nombre de
  cartes = fin). Fin d'article : grille 2 colonnes portée aussi par `bandeau-newsletter` (`&.newsletter-article`, brut).
  Landing : constat « Vos concurrents passent à la vidéo. / Et vos contenus, ils attendent quoi ? » ; nouvelle partie
  `p-landing-2b-equipe` (`duo-equipe`, `carte-equipe-ia` sombre, `visages-equipe` + `plus-visage` +1 / +2, `note-equipe`) ;
  `p-landing-2c-formules` (prix + `prix-annuel` « Soit 15 000 / 25 000 € / an », ancre `landing-offres` déplacée ici,
  plus de prix dans la FAQ) ; bénéfices = `promesses-jeu` + `cartes-jeu` (chiffres en verre, textes d'origine, notes
  « enfin vus / sans négo / non-stop / tout inclus ») ; ordre : hero, constat, équipe, formules, pourquoi Rolling, échange,
  Née chez Pitchy, questions, final (timecodes 00:01 à 00:07). Installateur : met aussi à jour le **titre** d'un bloc non
  modifié quand il change (ex. timecode).
- **0.7.6** : **landing B** (A/B test Google Ads) = page « Landing B (l'accueil) » `/agence-video-illimitee/` créée une fois
  (installation.json, modèle `modeles/landing.php` = logo seul, `depuis 0.7.6`) + modèle « Rolling · Page complète
  (landing B : l'accueil) » (`98-landing-accueil` : mêmes blocs que `99-page-complete`, clé `remplacer` gérée par
  construire-blocs.php : `/#offres` ⇒ `#offres` pour rester sur la page) ; noindex par la cliente (Yoast).
  Vidéos muettes de la page content repurposing « ne s'affichent pas » chez la cliente : **non reproduit** (hero et cas
  client OK sur wp avec un .mp4) ; site de la cliente injoignable d'ici ⇒ deux causes probables corrigées dans rolling.js :
  widget posé sans `cadre-video-muette` (`videoMuette()` met le widget en absolu sur l'image) et liens Vimeo autres que
  vimeo.com/ID (manage/videos, channels, showcase…, regex élargie). Lien utilisé demandé à la cliente.
- **0.7.7** : landing B = hero **« version maquette : grande photo »** (`98-landing-accueil` assemble `01-hero-maquette` à la
  place de `01-hero`). **Maquette « Fiche réalisation : formats et plusieurs vidéos »** (`maquettes/fiche-formats/formats.html`,
  générée par `scratchpad/c067/gen-formats.py`, copie /workspace/maquette-fiche-formats.html, **à valider**) : constat = cadre
  `video-fiche` toujours 16:9 + étiquette = 1er terme `rolling_format` par ordre alphabétique (« 1:1 » avant « 9:16 ») ⇒ proposé :
  format **lu sur la vidéo** (fichier : largeur × hauteur de la médiathèque ; YouTube shorts = 9:16 ; Vimeo : oEmbed) + réglage
  de secours par vidéo ; fiche 16:9 = comme aujourd'hui, 9:16 A lecteur 380 px à gauche + infos à droite (conseil) / B centré sur
  fond flou, 1:1 = lecteur 560 px + infos ; plusieurs vidéos = champ répétable « Les vidéos » (lien, format auto, nom de version ;
  1re = principale, le champ `video` actuel devient la 1re ligne) : M1 sélecteur de versions au-dessus du lecteur (conseil) /
  M2 principale + rangée « Toutes les versions ».
- **0.7.8 (fiche réalisation : formats + plusieurs vidéos, choix 9:16 A, 1:1, M1)** : champ répétable `videos` (lien, format
  auto | 16-9 | 9-16 | 1-1 | 4-5, nom de version ; colonnes « choix » = `<select>` dans les lignes) à la place de `video`
  (repli : ancien `_rolling_video` = 1re ligne ; à l'enregistrement `_rolling_video` = lien de la 1re ligne, cartes / mur /
  schéma inchangés). `Rolling_Contenus::videos()` / `format_lien()` : fichier = largeur × hauteur de la médiathèque, YouTube
  /shorts/ = 9:16, Vimeo = oEmbed, transient `rl_format_<md5>` ; champ calculé `formats_videos` (balise Champ). Widget Vidéo :
  `adapter=oui` (cadre au format de la vidéo, `data-rl-format`, une `video-fiche` par version, `hidden` sauf la 1re) et
  `partie=versions` (`versions-video` : boutons `data-rl-version` + forme du format ; rien sous 2 vidéos) ; rolling.js bascule
  les versions (arrête celle qui jouait) et corrige le format d'un fichier sur `loadedmetadata`. Fiche (f1) : `scene-video`
  (grille ; 9:16 / 4:5 = lecteur 380 px + `infos-video` à droite ; 1:1 = 560 px ; 16:9 = lecteur seul ; mobile empilé),
  `cadre-scene`, `ligne-info`, `libelle-info`, `valeur-info` ; infos montrées sous le lecteur dans l'éditeur (apercu_contextes).
  Étiquette des cartes : 1er format coché qui n'est pas un ratio, sinon format réel ; balise Terme met en 1er le format de la
  vidéo principale ; `vertical()` du mur lit la vidéo principale.
- **0.7.9 (retours 0.7.8)** : vidéo de la fiche qui ne se lançait plus toute seule = en 0.7.8 l'aperçu muet d'un fichier n'était
  plus posé quand la réalisation a une image à la une (avant 0.7.8, il jouait par-dessus) ⇒ rétabli. Plusieurs versions :
  plus d'étiquette sur la vidéo (les boutons disent déjà le format) ; texte d'une version = `texte_version()` : nom seul s'il
  contient déjà un format (« Format 9:16 »), sinon « 9:16 · Stories », format seul sans nom.
- **0.7.10** : liens .mp4 servis par Vimeo (`player.vimeo.com/progressive_redirect/…/file.mp4…`, ceux de la cliente) pris
  pour des pages Vimeo (test `vimeo.com` dans le lien) ⇒ pas d'aperçu muet sur la fiche, pas de détection du format, et dans
  rolling.js `lecteurLien()` ouvrait le lecteur Vimeo (id tiré de l'adresse) au lieu de lire le fichier : cause probable du
  showreel gris (0.6.11) et des vidéos muettes absentes (0.7.6). Corrigé : `Rolling_Contenus::est_fichier_video()` /
  `est_vimeo()` (extension .mp4/.webm/.mov/.m4v/.ogv ou `/progressive_redirect/`), `estFichier()` + `lecteurFichier()`
  dans rolling.js. Test : route Playwright qui sert un .mp4 local à l'adresse Vimeo (player.vimeo.com bloqué ici).
- **0.7.11** : accueil 00:02 encarts 645 ⇒ 490 px sur ordinateur (≥ 1025 px) : brut de `colonne-chapitre` (h3 28-34 px,
  étiquette 15 px, picto 44 px, constat 15 px, coches 20 px, listes 14 px, chips 13 px, marges réduites), `photo-chapitre`
  min-height 420 (540 imposait la hauteur), `chapitre-colle` top min(110px, 100vh − 560px). Ancre `#offres` : rolling.js
  (`ciblePrix` / `allerPrix`) fait arriver sur `.offres-mobile` (cartes de prix, mobile) sinon `.offres-tableau`, 24 px sous
  l'en-tête, au clic et à l'arrivée par /#offres (marche sur les pages déjà construites). Page content repurposing : textes
  repris des idées de la maquette « Glass 2 » de la cliente (patrimoine écrit, « enfin vus, en interne comme en externe »,
  « Vos contenus existent déjà ») : chapô qui explique pourquoi, H2 00:01 « Tout ce que vous avez déjà écrit peut devenir
  vidéo. », 6e type « Une page produit » (`grille-types` 3 colonnes, 2 sous 1024), intro du schéma, étapes 1 et 4, encart
  prix, FAQ (définition + « Par quels contenus commencer ? »), appel final « Vos contenus existent déjà. Faites-les voir. ».
  Sans « vidéotiser » (règle de rédaction).
- **0.7.12** : fiche réalisation « D'autres réalisations » et fiche témoignage « Les autres vidéos » = cartes façon Avant → Après
  (widget Liste `carte_theme='exemple'` ⇒ c14, comme l'accueil et Contact). Métas proposées pour la page content repurposing
  (« Vos contenus en vidéo » chez la cliente).
- **Maquette « Corrections mobile »** (`maquettes/corrections-mobile/corrections.html`, générée par `scratchpad/c067/gen-mobile.py`,
  copie /workspace/maquette-corrections-mobile.html, **à valider**, « ne relance pas le thème avant ») : 1 générique de la fiche
  réalisation : trait de gauche de `generique-filet` mal placé (le `::before` n'a pas la remise à zéro du `.e-con::before`,
  aussi sur ordinateur) ⇒ correction simple ; 2 équipes qui défilent (accueil, L'agence) : 55 px/s trop lent et pas de glisser
  au doigt ⇒ proposé : bande en défilement par script (`scrollLeft`, `overflow-x: auto`), arrêt au toucher, élan natif,
  reprise 2 s après, sans fin ; vitesse au choix 80 / 100 (conseil) / 130 px/s ; « réduire les animations » = rangée immobile
  qu'on fait glisser ; 3 « Le plus complet » (`offres-badge`) calé à droite en mobile (colonne sombre 84 px, étiquette 136 px)
  ⇒ centré sur la colonne : A une ligne qui dépasse, B deux lignes dans la colonne (conseil) ; centré aussi sur ordinateur.
- **0.7.13 (corrections mobile validées : 80 px/s, 3B)** : `generique-filet` (`::before` avec RAZ + `width: auto`) ; équipes en
  mobile (`.equipe-defilante`, `.defile-mobile`, ≤ 767 px) : rolling.js `glisser()` pose la bande dans un rail `div.rl-glisse`
  (rolling.css : overflow-x auto, barre masquée ; `tabindex=0`, `role=group` + aria-label pour axe), animation CSS coupée
  (`style.animation = none`), défilement par `scrollLeft` à 80 px/s (rAF, position flottante gardée à part car le navigateur
  arrondit), arrêt au toucher / au focus, reprise 2 s après, boucle sur la moitié (bande doublée), pause hors écran ;
  « réduire les animations » = rail immobile glissable, sans copie. Ordinateur inchangé (animation CSS). `offres-badge` centré
  sur la bande sombre (`right: 139px` / mobile `50px` + brut `transform: translateX(50%)`), mobile sur deux lignes (76 px).
- **0.7.14** : 7 experts IA et 9 créas chez la cliente ⇒ nouvelle classe modificatrice `defile-partout` (sur `grille-equipe`,
  prioritaire : rangée nowrap, 186 px / 150 px en mobile, `--rl-defile: 1`) posée sur les grilles d'équipe des blocs (08-ia experts,
  09 réseau de talents, L'agence version grille). rolling.js : toute bande d'équipe (`equipe-defilante`, `defile-partout`,
  `defile-mobile` en mobile) passe par `glisser()` sur ordinateur aussi (arrêt au survol, reprise 0,3 s ; pavé tactile) ;
  si tout tient dans la largeur : pas de copie, rangée centrée, immobile ; `doubler()` = copies ; `rl-glisse-bords` (fondu
  des bords) si le parent n'est pas `masque-bords` ; rail avec marges négatives (ombres et levée au survol non coupées).
  « Réduire les animations » sur ordinateur = plusieurs lignes (`rl-defile-statique`). Chez la cliente : ajouter la classe
  `defile-partout` aux conteneurs de portraits existants (pages non touchées par la mise à jour).
- **0.7.15** : la cliente voyait encore 4 + 1 experts dans l'éditeur (classe `defile-partout` pas ajoutée sur sa page) ⇒ plus
  de classe à ajouter : **`grille-equipe` elle-même = rangée d'une ligne qui défile** (même CSS que defile-partout, `--rl-defile: 1`)
  et rolling.js la traite comme une bande d'équipe (`glisser()`, ordinateur + mobile) ; `defile-partout` / `defile-mobile` gardées
  (redondantes). Éditeur : `apercu_contextes` met `.grille-equipe` / `.equipe-defilante` à `width: 100%` + `overflow-x: auto`
  (une ligne qu'on fait défiler pour atteindre tout le monde). Rangée qui tient dans la largeur = centrée, immobile.
- **Maquette « L'agence 00:03 sans dégradé »** (`maquettes/agence-equipe/equipe-sans-degrade.html`, générée par
  `scratchpad/c067/gen-agence-equipe.py`, copie /workspace/maquette-agence-equipe.html, **à valider**) : la cliente ne veut plus
  la bande `bande-fondu` (dégradé clair → foncé) dans les deux modules « L'équipe » (grille et défilement) ⇒ A deux bandes à
  coupure nette, pastille à cheval sur la jonction ; **B section claire + experts IA dans une carte foncée arrondie (conseil,
  comme `carte-equipe-ia` de la landing), pastille dans l'en-tête de la carte** ; C tout sur fond clair, filet + pastille.
- **0.7.16** : défilement des équipes jugé trop rapide ⇒ réglage **Personnaliser > « Rolling : défilement des équipes »**
  (`rolling_defile_ordi` défaut 40 px/s, `rolling_defile_mobile` défaut 60 px/s, 10 à 200, `vitesse_defile()`), passé à
  rolling.js par `window.rollingReglages` (inline avant `rolling`), lu par `glisser()`.
- **Maquette « Landing v3 »** (`maquettes/landing-v3/landing.html`, générée par `scratchpad/c067/gen-landing-v3.py`, copie
  /workspace/maquette-landing-v3.html, **à valider**) : 1 « Pour qui » de l'accueil + 1 à n réalisations choisies (widget Liste,
  réglage « Réalisations choisies » à créer, sinon dernières du métier) : **A réalisation à la place de la photo (conseil, 1re en
  grand + petites dessous)**, B chapitre tel quel + bande de cartes dessous, C colonne compacte à droite, D onglets Marketing /
  Com interne ; placé en 00:02 après le constat (timecodes recalés). 2 prix = version courte (2 cartes + 4 coches + promesses) +
  tableau complet : **A bouton « Comparer les formules en détail + » qui déplie le tableau (conseil)**, B « + Tout ce qui est
  inclus » dans chaque carte ⇒ fenêtre, C début du tableau en fondu + « Voir tout ». 3 Pourquoi Rolling : 4 cartes d'abord, puis
  phrase des autres solutions + comparatif, 3 atouts, appel. 4 cartes sans chiffres : **A note manuscrite en tête (conseil)**,
  B coche en verre, C texte seul.
- **0.7.17 (landing v3 : 1A, 2A sans les 4 promesses, 3, 4A)** : nouveau bloc `p-landing-2a-pour-qui` (00:02, textes du Pour qui
  de l'accueil = `TEXTES_MARKETING` / `TEXTES_INTERNE`, bouton vers `#landing-rdv` ; `colonne_chapitre(..., lien=)`) : à la place de la
  photo, `zone-realisations` > widget Liste `classe=realisations-chapitre carte_theme=exemple metier=…` (1re carte en grand, les
  suivantes en petites, 2 par ligne, pastille / étiquette masquées) ; widget Liste : réglages **`choisies`** (répéteur, une ligne =
  une réalisation, clés `r<id>` pour garder l'ordre alphabétique dans l'éditeur ; titres des lignes par `rollingEditeur.realisations`)
  et **`metier`** (sinon dernières réalisations du métier). Sans réalisation : zone masquée et texte pleine largeur (site seulement ;
  règle `chapitre` `&:has(> .zone-realisations):not(:has(.carte-exemple)):not(:has(.elementor-element-overlay))`).
  **Leçon : un `:has()` ne peut pas contenir un autre `:has()`** (règle rejetée en silence par le navigateur ; `:not(:has())` passe).
  Formules (00:04) = 2 cartes `formule()` de l'ancienne version courte + `tableau-depliable` (bouton `bouton-verre bouton-deplier`
  « Comparer les formules en détail » lien `#rl-comparer` + copie du tableau de la page Offres, liens ⇒ `#landing-rdv`) : rolling.js
  pose `rl-replie` (role=button, aria-expanded, aria-controls), clic / espace ⇒ `rl-deplie` (animation `rl-deplie`) ; sans script et
  dans l'éditeur, tableau visible ; `ciblePrix` ignore les tableaux repliables. Pourquoi Rolling (00:05) : H2, cartes `carte-jeu
  carte-jeu-note` (très prioritaire) avec `note-tete` (Caveat 40 px, violet sur les cartes impaires depuis la fin) et sans chiffre,
  puis phrase (ancien intro) + comparatif, atouts, appel. Timecodes 00:03 à 00:08 recalés. **Leçon éditeur** : un paragraphe y est
  enveloppé ⇒ pas de `> .classe-du-texte` dans les règles d'une carte (descendant). Chez la cliente : insérer le nouveau bloc
  Pour qui, remplacer les blocs Formules et Pourquoi Rolling (la mise à jour ne touche pas aux pages).
- **0.7.18** : défilement des équipes saccadé sur ordinateur (vidéo de la cliente, L'agence) : le navigateur arrondit `scrollLeft`
  au pixel entier ⇒ à 40 px/s, 1, 1, 0, 1… px par image. `glisser()` : partie entière en `scrollLeft` (le doigt garde la main),
  reste à virgule en `transform: translate3d()` sur la bande (remis à zéro au toucher et quand l'utilisateur fait défiler).
  Mesuré : 0,67 px par image à 40 px/s, 1 px à 60 px/s.
- **0.7.19** : partage WhatsApp en erreur 429 chez la cliente (wa.me limite les requêtes, surtout depuis un ordinateur) ⇒
  lien `https://api.whatsapp.com/send?text=` (adresse officielle vers laquelle wa.me redirige) dans la balise Partager
  et l'ancien widget Partage ; texte toujours encodé (`rawurlencode`). Métas des 4 réalisations et 4 témoignages écrites
  (hôtes agence-rolling.fr / www ajoutés à l'environnement « rolling » pour lire le site en ligne).
- **Maquette « LinkedIn et YouTube »** (`maquettes/reseaux/reseaux.html`, générée par `scratchpad/c067/gen-reseaux.py`, copie
  /workspace/maquette-reseaux.html, **à valider**) : le pied de page sait déjà afficher LinkedIn / YouTube / Instagram
  (Personnaliser > « Rolling : en-tête et pied de page », `rolling_reseau_*`, vide = rien). A pied de page icône + nom (déjà
  prêt), **B bande « Suivez-nous. » en bas du pied (conseil)**, **C bandeau « Voir la chaîne YouTube » à la fin du mur des
  Réalisations (conseil)**, **D carte « Suivez Rolling » dans la colonne des articles (conseil)**, E 2 cartes dans « Où nous
  trouver » de L'agence, F liens sous Ressources (Apparence > Menus, liens personnalisés) + bas du menu mobile, G barre
  flottante (déconseillée).
- **0.7.20 : réseaux = B, C, D, E, F** (pas A ni G). Liens remplis une fois dans Personnaliser > « Rolling : en-tête et pied de
  page » (`rolling_reseau_linkedin|youtube|instagram` ; vide = rien nulle part). `Rolling_Entete_Pied::RESEAUX` (noms + icônes),
  `reseaux()`, `lien_reseau()`, `reglage()` + `defauts()` (titre / phrase de la bande, nom du sous-menu, textes des liens du menu).
  **B** : bande `rl-pied__suivre` (« Suivez-nous. », pastilles icône + nom) entre les colonnes et les badges ; plus d'icônes sous
  le logo. **F** : `wp_nav_menu_objects` ajoute les réseaux à la fin du sous-menu dont le titre = `rolling_reseaux_sous_menu`
  (« Ressources » par défaut ; éléments factices db_id 990000+, classe `rl-menu__reseau`, rendus par `element_de_menu`) ;
  mobile ≤ 980 px : ces liens masqués, pastilles `rl-menu__reseaux` en bas du menu ouvert. Balise **`rolling-reseau`**
  (lien du réseau) + repères `si-reseau--linkedin|youtube|instagram` et `si-reseaux` (`condition_remplie`). **C** : bloc
  `p-rea-25-youtube` (`bandeau_chaine()` : `bandeau-chaine`, `cadre-chaine` avec bouton lecture rouge en ::after, `bouton-youtube`
  #C4002A) sur la page Réalisations + sous « D'autres réalisations » de la fiche réalisation. **D** : carte `carte-suivre` dans la
  colonne des articles (après Partager). **E** : `cartes-reseaux` > `carte-reseau` + `reseau-*` (variables d'icône en brut) +
  `titre-reseau` (pictogramme carré en ::before **display: block** : le titre reste en block, pas de flex) dans « Où nous trouver ».
- **0.7.21** : blog : bandeau newsletter retiré de la liste (`newsletter_apres` plus réglé dans le bloc ; chez la cliente, mettre
  « Encart newsletter après la carte n° » à 0 dans sa liste) ⇒ bloc seul **`15-newsletter`** « Rolling · Newsletter (bandeau à
  placer où vous voulez) » (BLOCS_VARIANTES, section `section-aeree` 72 / 56 / 48 px + `si-newsletter`). Cartes réseaux de
  L'agence jugées trop grandes ⇒ pictogramme 32 px **devant** le titre (inline-block), padding 18 / 22, 135 px de haut.
- **Maquette « Vidéo du témoignage (interview du client) »** (`maquettes/temoignages-video/interview.html`, générée par
  `scratchpad/c067/gen-interview.py`, copie /workspace/maquette-interview-temoignage.html, **à valider**) : une vidéo où le client
  raconte le projet n'est pas une réalisation ⇒ nouveau champ du témoignage « Vidéo du témoignage » (lien + format auto) ; **validée (2A, 3A, 4)** ;
  carte A vignette en haut + pastille « Interview » (conseil) / B citation en plaque sur l'image ; fiche A interview en grand sous
  le bandeau du projet (conseil ; 9:16 = lecteur 340 px + citation à côté), réalisations liées en « Les vidéos réalisées
  ensemble. » / B interview dans l'en-tête ; fiche réalisation liée : « Voir l'interview » à côté de la citation ; VideoObject.
- **0.7.22 : interview du client (choix carte 2A, fiche 3A, réalisation 4)** : témoignage, groupe « L'interview du client
  (facultatif) » : `interview` (lien), `interview_format` (auto | 16-9 | 9-16 | 1-1 | 4-5), `interview_duree`.
  `Rolling_Contenus::interview()`, `video_du_temoignage()` (interview, sinon 1re réalisation liée) ; `videos()` d'un témoignage =
  son interview. Widget Vidéo : `source=realisation` sur un témoignage = interview d'abord ; nouvelle source `interview` (témoignage
  ou témoignage lié d'une réalisation) ; pastille réglable `texte_interview` (« Interview ») + durée ; dans une carte, interview
  verticale en 4:5 (`data-rl-format` sans adapter). Fiche témoignage : `scene-video` (lecteur `adapter=oui` + `infos-video
  si-video-cote si-citation` = citation `citation-cote` + auteur à côté d'une vidéo verticale / carrée ; `cas-citation` reçoit
  `sans-video-cote` pour ne pas doubler) ; scene-video sans infos = lecteur centré ; « Les vidéos réalisées ensemble. » (si-interview)
  / « Les autres vidéos. » (sans-interview), `liees_suite` garde toutes les réalisations quand l'interview est en grand.
  Fiche réalisation : `citation-avec-interview` (très prioritaire) + `vignette-interview` (si-temoignage-interview). Carte `temoin`
  ne se soulève plus au survol si elle contient une vidéo (le bouton lecture ne bouge jamais). VideoObject de la fiche témoignage
  (interview + image à la une, ou miniature YouTube ; description = citation). Chez la cliente : modèles Fiche témoignage /
  réalisation mis à jour s'ils ne sont pas modifiés (sinon V2) ; cartes : rien à faire.
- **Maquette « Extrait sans son »** (`maquettes/sans-son/sans-son.html`, générée par `scratchpad/c067/gen-sans-son.py`, copie
  /workspace/maquette-sans-son.html, **à valider**) : certaines réalisations sont des extraits muets ⇒ case de la réalisation
  « Extrait présenté sans son » (texte réglable dans Personnaliser) ; 7 formulations (conseil « Extrait · sans son » sur les
  cartes, « Extrait de réalisation · Présenté sans son » sur la fiche) ; looks A pastille en bas à gauche de l'image (conseil),
  B ligne lilas sous la vidéo, C sous le bouton lecture, D icône seule + bulle, E A + rappel 4 s pendant la lecture, F étiquette
  sous le titre. Aucun mouvement du bouton lecture au survol. **Validée : A, 1 sur les cartes, 2 sur les fiches.**
- **0.7.23 : extrait sans son (choix A, formulation 1 sur les cartes, 2 sur les fiches)** : réalisation, case `sans_son`
  (« Extrait présenté sans son », groupe « La vidéo », toutes les versions) + repère `si-sans-son` ; classe `pastille-sans-son`
  (prioritaire : bas gauche, haut-parleur barré en `::before` masque SVG, fond uni translucide rgba(15,10,36,.74) pour Ally,
  `.lit > &` masquée pendant la lecture, 13,5 px sur un lecteur `[data-rl-format]`) ; cartes c1 (mur, listes) et c14 (Avant → Après) :
  paragraphe « Extrait · sans son » modifiable ; widget Vidéo : réglages `sans_son_carte` (« Extrait · sans son ») et `sans_son_fiche`
  (« Extrait de réalisation · Présenté sans son », lecteur au format de la vidéo), rien pour une interview.
- **Maquette « Signature e-mail »** (`maquettes/signature/signature.html`, copie /workspace/maquette-signature.html, **à valider**) :
  générateur dans la page (nom, poste, téléphone, LinkedIn, photo, lien du bouton, adresse du logo = logo du thème sur
  agence-rolling.fr) + « Copier la signature » (presse-papiers HTML) / « Copier le code HTML » ; tableaux, styles en ligne, Arial ;
  A classique logo + filet pêche (conseil, pour tous), B photo ronde ou initiales + barre violet → pêche, C bouton noir +
  badges en texte, D une ligne pour les réponses (conseil) ; marche à suivre Gmail, Outlook web / Windows, Apple Mail, iPhone.
- **Générateur de signature (modèle A)** : `maquettes/signature/generateur-signature.html` (copie
  /workspace/generateur-signature-rolling.html), page autonome noindex : champs (nom, poste, téléphone, LinkedIn + réglages
  communs repliés), aperçu dans un faux e-mail, « Copier la signature » (HTML + texte) / « Copier le code HTML », onglets
  Gmail / Outlook web / Windows / Apple Mail / iPhone, champs gardés dans le navigateur (localStorage). axe 0, 390 sans débordement.
- **Maquette « Chargement du calendrier HubSpot »** (`maquettes/calendrier-chargement/chargement.html`, copie
  /workspace/maquette-chargement-calendrier.html, **à valider**) : indicateur dans `zone-calendrier` jusqu'au chargement de
  l'iframe HubSpot (15 s max) : A squelette du calendrier qui scintille (conseil), B règle de montage + tête de lecture,
  C trois points ; texte « Chargement des disponibilités… / Quelques secondes, nous ouvrons notre agenda. ». **A validé.**
- **Générateur de signature publié en lien privé** : https://claude.ai/artifact/5ZPDTtuEtRKeubd1oGardr (source
  `maquettes/signature/signature-artefact.html` : logos clair / sombre intégrés en data:, l'aperçu montre le logo intégré, la
  signature copiée garde l'adresse du logo sur agence-rolling.fr ; mode sombre par jetons). Partage = menu Partager de la page.
- **0.7.24 : chargement du calendrier HubSpot (option A validée)** : `zone-calendrier::after` (CSS brut de la classe) = carte
  blanche + calendrier squelette (barres, 4 rangées de 7 ronds en `space no-repeat`, 2 créneaux) dessinés en 12 couches de fond
  (origine padding-box, positions en px depuis le haut), reflet animé par `--rl-reflet` (@property + `@keyframes rl-reflet` dans
  rolling.css), texte en `content` (« Chargement des disponibilités… / Quelques secondes, nous ouvrons notre agenda. »,
  padding-top 414 px) ; visible seulement avec `.rl-cal-attente`. rolling.js (site seulement) : zone qui contient
  `.meetings-iframe-container` ⇒ `rl-cal-attente` + `aria-busy`, retirés au 1er message de l'iframe HubSpot (origine
  *.hubspot.com), sinon `load` + 2,5 s, sinon 15 s ; iframe déjà chargée (Performance API) ⇒ 1,5 s. Mesuré : 2 à 5 s avec
  meetings.hubspot.com/agenda-rolling. Automatique sur les pages existantes (classe mise à jour). Leçon : `space`/`repeat-x`
  peint aussi dans la marge (bouts de ronds) ⇒ motif sur toute la largeur.
- **Générateur de signature pour une page WordPress** (choix de la cliente, page non indexée) : `maquettes/signature/signature-wordpress.html`
  (copie /workspace/generateur-signature-a-coller.txt) = un seul widget HTML d'Elementor : `div#rl-signature` + `<style>` limité à
  `#rl-signature` (bat Hello : boutons, champs, labels, styles de tableau neutralisés dans l'aperçu) + script ; ids `rls-…` ; sans
  logo en tête (en-tête du site) ; padding-top 150 px sous l'en-tête collant. Testé sur wp (page Elementor pleine largeur, titre masqué) :
  axe 0, 390 sans débordement, copie OK.
- **Maquette « GIF et auteur »** (`maquettes/gif-auteur/gif-auteur.html`, jetons `__GIF__` / `__GIF_FIXE__` / `__INES__` remplacés
  à la copie /workspace/maquette-gif-auteur.html, **à valider**) : code d'intégration Giphy dans un bloc « HTML personnalisé » =
  iframe (taille figée, lourd, cookies Giphy, pas de pause) ⇒ proposé : le thème remplace l'iframe giphy.com/embed/ID (ou un lien
  giphy.com/gifs/…) par la vidéo mp4 de Giphy (media.giphy.com/media/ID/giphy.mp4) au format réel, bouton pause, alt tiré du
  slug ou de la légende, « via GIPHY » ; looks A comme une image (conseil), B pastille « GIF », C carte blanche ; composition
  « Rolling : GIF ». Auteur : lien LinkedIn dans la composition « Auteur » (tout lien linkedin.com de `.rl-auteur` stylé) :
  A icône à côté du nom, B « Suivre Inès sur LinkedIn » (conseil), C bouton en haut à droite ; bonus Yoast (profil LinkedIn
  de l'utilisateur). Blog : 10 cartes aujourd'hui (1 à la une + 9, « Articles suivants » ⇒ ?voir=2), conseillé 13 dès ~20
  articles, pages numérotées au-delà de ~40. Hôtes giphy.com / media.giphy.com bloqués ici (à autoriser pour tester).
- **0.7.25 (GIF : B + C, LinkedIn : A)** : widget « Texte de l'article » (`enrichir()` ⇒ `gifs()`, `giphy()`) remplace tout GIF
  Giphy du texte (iframe giphy.com/embed + cadre en % + ligne « via GIPHY », lien seul sur sa ligne, adresse seule, bloc
  Intégrer, image media/i.giphy.com ; un lien au milieu d'une phrase reste un lien) par `figure.rl-gif` (carte blanche) >
  `rl-gif-cadre` (aspect-ratio) > `video.rl-gif-video` (media.giphy.com/media/ID/giphy.mp4, affiche giphy_s.gif, muette,
  boucle, preload none, aria-label « GIF : légende / titre du slug ») + pastille `rl-gif-pastille` + bouton `rl-gif-bouton`
  (aria-pressed, textes data-rl-pause / data-rl-lecture) + `rl-gif-legende` (légende + « via GIPHY »). Largeur : `--rl-ratio`
  (posé par le PHP, corrigé par rolling.js) ⇒ hauteur max 560 px (440 en mobile), carte centrée. Bouton visible au survol /
  clavier sur ordinateur (brut `@media (hover: hover)`), toujours sur tactile. rolling.js : lecture quand visible (30 %),
  pause hors écran, « réduire les animations » = image fixe + bouton lecture, format lu sur l'affiche puis la vidéo ; rien
  dans l'éditeur. Textes réglables dans le widget (`gif_pastille`, `gif_credit`, `gif_pause`, `gif_lecture`, `gif_nom`).
  Composition « Rolling : GIF » (clé **`gif-giphy`** : `sanitize_file_name('gif')` = « unnamed-file.gif » !) = groupe
  `rl-gif-bloc` (bloc HTML + paragraphe `rl-gif-texte` = légende). Auteur : composition = **nouvelle photo `auteur-ines`**
  (Inès Rédactrice ≠ `equipe-ines` Inès DevOps de l'équipe) + lien `https://www.linkedin.com/in/` dans `rl-nom` ; tout lien
  linkedin.com de `.rl-auteur` = icône « in » 32 px (texte en font-size 0, lu par les lecteurs d'écran) ; lien d'exemple sans
  profil retiré au rendu ; dans l'éditeur WordPress le texte du lien reste visible (règle `apres` de `feuille_editeur`).
  Testé wp + wp2 (route Playwright pour media.giphy.com) : axe 0, 1440 / 390, réduire les animations, blocs valides.
- **0.7.26** : code Giphy récent de la cliente (`<div style="padding-bottom:56%">` + iframe `width="100%" height="100%"`) :
  « 100% » était lu comme 100 px ⇒ cadre carré au départ ⇒ dimensions de l'iframe lues seulement si ce sont des pixels,
  sinon format du cadre en % (56 % ⇒ 1000 / 560). Testé avec son code exact (wp + mise à jour wp2).
- **0.7.27** : WordPress 7 ouvre une composition insérée en mode « contenu seul » (bouton « Modifier la composition ») : le bloc
  HTML de « Rolling : GIF » y était caché ⇒ composition = groupe `rl-gif-bloc` avec 2 paragraphes : `rl-gif-code` (« Collez ici le
  code d'intégration Giphy ou le lien du GIF ») + `rl-gif-texte` (légende). Coller le code Giphy dans un paragraphe : Gutenberg le
  change en bloc Intégrer + paragraphe « via GIPHY » (géré : une seule carte, légende reprise, crédit retiré, format lu sur
  l'affiche) ; code resté en texte : id, titre (giphy.com/gifs/…) et format (padding-bottom %) lus dans le texte. LinkedIn de la
  carte auteur : dans l'éditeur WordPress aussi, logo seul ; le texte du lien n'apparaît que sur la ligne sélectionnée
  (`.is-selected`, règle `apres` de `feuille_editeur`) pour pouvoir cliquer dessus et modifier le lien (vérifié).
- **0.7.28** : bloc Intégrer avec une adresse Giphy = « Désolé, ce contenu ne peut pas être embarqué » dans l'éditeur ⇒
  `Rolling_Compositions::giphy()` enregistre un gestionnaire d'intégration (`wp_embed_register_handler`, giphy.com/embed|gifs|
  stickers|clips et media/i.giphy.com) qui rend `<img class="rl-giphy" src="media.giphy.com/media/ID/giphy.gif">` sans appel
  réseau (le proxy oEmbed de l'éditeur s'en sert en repli ⇒ aperçu du GIF) ; sur le site, `gifs()` en fait la carte.
  Lecteur d'adresse déplacé dans `Rolling_Compositions::giphy_infos()` (classe toujours chargée ; le widget n'existe qu'avec
  Elementor). Logo LinkedIn centré : `.rl-nom:has(> a[href*=linkedin.com])` en flex centré, lien sans vertical-align décalé.
- **Maquette « Guides et REX »** (`maquettes/guides-rex/guides-rex.html`, jetons `__COUV1..3__` = couvertures des guides de la
  cliente rognées de sa capture (scratchpad/guides) et `__LOGO__`, copie /workspace/maquette-guides-rex.html, **à valider**) :
  bouton « Télécharger » blanc sur blanc invisible + guides et REX identiques ⇒ guides : **A document posé incliné (épaisseur
  des pages, se redresse au survol, « gratuit ! » Caveat, bouton violet plein « Télécharger le guide » + « PDF gratuit · X pages »,
  conseil)**, B pochette avec onglet (document qui sort au survol, bouton rond noir), C couverture pleine + plaque fumée + bouton
  pêche ; REX : **R1 fiche client sombre (logo, « Retour d'expérience », 1er chiffre clé du témoignage lié en grand, bouton pêche
  « Télécharger le REX » + « Lire le témoignage », conseil)**, R2 citation sous un clap rayé, R3 carte A en version pêche + logo
  dans le coin. Admin : case « C'est un retour d'expérience » dans la fiche du guide (remplace le thème ; anciens REX reconnus),
  témoignage lié, nom du document libre pour les guides.
- **Guides et REX, retours** : couleurs différentes (guides violet, REX pêche) ; A aimé mais guides **horizontaux et verticaux** ;
  logo du client mis en avant = bonne idée ; **pas de REX sombre** ⇒ maquette tour 2 `maquettes/guides-rex/guides-rex-2.html`
  (copie /workspace/maquette-guides-rex-2.html, **à valider**) : orientation lue sur l'image à la une (zone d'image de hauteur
  fixe) ; guides A document posé (conseil), B pages qui s'écartent en éventail au survol, C sur tablette / téléphone + bulle de
  téléchargement, D coin corné « PDF » ; REX clairs : R4 pass client (talon vertical, pointillés), **R5 logo en vitrine (tuile en
  verre sur fond pêche, couverture derrière, pastille chiffre clé ; même construction que la carte guide, conseil)**, R6 duo
  « client × Rolling » + citation + 2 chiffres, R7 dossier client (chemise pêche, logo en autocollant, « cas réel »).
- **Guides et REX, tour 3** (`maquettes/guides-rex/guides-rex-3.html`, générée en Python, jetons `__LEX__` / `__FREE__` /
  `__REX__` / `__COUV2__` = vrais documents de la cliente dans scratchpad/guides : lexique horizontal, « Freelance vs agence »
  vertical, REX L'Oréal ; copie /workspace/maquette-guides-rex-3.html, **à valider**) : guide A retenu, corrigé (cartes à la
  largeur réelle, bouton plein sur toute la largeur + « PDF gratuit · X pages » dessous, « gratuit ! » = pastille ronde posée sur
  le coin du document, devant ; image à la une horizontale ⇒ 1,414, verticale ⇒ 0,707). REX : dossier client intéressant mais
  sans couverture ⇒ **R7a couverture qui sort d'une chemise pêche + étiquette logo + « cas réel » (conseil)**, R7b dossier ouvert
  (logo à gauche, couverture à droite), R5 vitrine pour comparer ; chiffres clés du témoignage en pastilles.
- **Guides et REX, tour 4** (`maquettes/guides-rex/guides-rex-4.html`, générée par `scratchpad/gen-guides4.py`, jetons `__LEX__`,
  `__FREE__`, `__COUV2__`, `__REX__`, `__REXPHOTO__` (photo rognée du REX L'Oréal), `__LOGOR__`, `__CH4__`, `__CH2__` ; copie
  /workspace/maquette-guides-rex-4.html, **à valider**) : retours « "gratuit" fait cheap », guides « vieux, pas moderne », REX « rien
  ne va » ⇒ tout à plat, sans « gratuit », sans document penché. Guides : **G1 couverture nette sur un halo flou de ses propres
  couleurs (conseil)**, G2 couverture en grand + panneau en verre dépoli (titre + bouton « Télécharger »), G3 fiche horizontale
  2 par ligne avec timecode 00:01. REX (pêche, clairs) : **R1 le REX en miniature (conseil : « Retour d'expérience », Rolling ×
  logo client, photo du projet en cadre de verre, titre avec mot en dégradé, 2 chiffres clés en verre)**, R2 le résultat d'abord
  (grand chiffre en verre, logo sur étiquette blanche, couverture à droite), R3 la photo du projet + onglet Rolling × logo.
- **Guides et REX, tour 5 : fiches horizontales** (`maquettes/guides-rex/guides-rex-5.html`, générée par `scratchpad/gen-guides5.py`
  qui reprend les fonctions de gen-guides4.py ; copie /workspace/maquette-guides-rex-5.html, **à valider**) : demande « à l'horizontal
  pour les deux, REX sans chiffres clés » ⇒ 2 par ligne, image à gauche (42 %), texte + bouton à droite ; guide = couverture sur halo
  flou de ses couleurs (G1 couché) ; REX sans chiffres : **H1 photo du projet pleine hauteur + onglet blanc Rolling × logo (conseil)**,
  H2 couverture du REX sur fond pêche + étiquette logo + Rolling × logo au-dessus du titre. Mobile empilé.
  **Choix : H1** (guides en fiche horizontale + REX photo). Saisie proposée (question de la cliente, à confirmer avant thème) :
  case « C'est un retour d'expérience », image à la une = photo du projet, nouveau champ « Logo du client » (+ nom du client
  pour l'alt), témoignage lié **facultatif** (lien « Lire le témoignage » seulement s'il y en a un ; logo du témoignage en repli).
- **Chargement de page effet film** (demande avant la fin de la livraison 4) : maquette `maquettes/loading/maquette-loading.html`
  (copie /workspace/maquette-loading.html), 4 options : A amorce 3-2-1, B clap qui se ferme puis rideau, C ouverture
  cinéma (bandes noires + REC + timecode + règle avec la tête de lecture rouge des frises, plus de filet fin), D barre de lecture en haut sans écran. Conseil : C à l'arrivée (première page
  de la visite, sessionStorage), D entre les pages ; < 1,5 s, clic / Échap pour passer, rien avec « réduire les
  animations », rien dans l'éditeur, pas sur la landing ; **C choisie (avec la tête de lecture), D validée entre les pages**.
  Intégré en 0.4.0 : `inc/class-rolling-chargement.php` (script dans le head : classe `rl-intro-joue` ou `rl-barre-joue`
  selon sessionStorage ; écran après `wp_body_open` ; logo pour fond sombre de Personnaliser) ; CSS dans rolling.css ;
  désactivable dans Personnaliser > « Rolling : chargement de page ».
  Option E ajoutée (idée de la cliente) : un document « (guillemets du logo) traverse le logo et devient une vidéo ▶,
  tête de lecture sur la règle, puis ouverture comme C (1,5 s). **Choix final : C** (E écartée) ⇒ à intégrer en 0.4.0.
- Google Tag Manager : chez la cliente, le Custom Code d'Elementor Pro est dans le nouveau menu **Elementor > Éléments
  personnalisés > Code** (capture du 12/11 ; absent sur ma copie sans licence) ⇒ elle y colle GTM (head + body début) ;
  ne pas modifier les fichiers du thème (écrasés à chaque mise à jour). Ancienne note :
  proposé : vérifier la licence (`edit.php?post_type=elementor_snippet`) ou champ « Identifiant Google Tag Manager »
  dans Personnaliser (le thème écrit le code dans le head et après body) ; GA4 dans GTM, mode Consentement.
- **Elementor 4.4.0** (sortie le 9/11/2026) testée sur wp2 (8081) avec le thème 0.3.2 + Pro 4.3.3 : rendu de la page
  d'essai et de l'accueil identique au pixel (4.4.0 charge les images différées plus tard : faire défiler lentement avant
  une capture pleine page), éditeur sans erreur JS, classes appliquées. Pro 4.4 non testée (pas accessible). wp2 reste en
  4.4.0 pour tester la mise à jour 0.3.2 → 0.4.0 ; la construction reste sur wp (4.3.6).
- FAQ : la cliente ajoute elle-même les réponses ; garder les questions telles quelles.
- Le WordPress de production existe déjà chez la cliente : on livre seulement le thème (zip à importer).

## Environnement de la cliente

- WordPress 7.1.4, Elementor 4.3.6, Elementor Pro (One) : oui. Administration en français.
- Site : agence-rolling.fr. Langue : fr (anglais plus tard : prévoir hreflang).
- Copie de test (scratchpad/wp, docker compose : mariadb 11.4, wordpress:7.1.4-php8.3-apache,
  wordpress:cli-2.12.0-php8.3) : WordPress 7.1.4 fr_FR, Hello Elementor 3.5.2, Elementor 4.3.6, Elementor Pro 4.3.3
  (zips de la cliente, hors git, scratchpad/plugins), ACF 6.8.11. http://localhost:8080 (admin / admin).
  Démarrer dockerd à la main (`dockerd &`) si le socket manque. Les conteneurs n'ont pas le proxy :
  télécharger sur l'hôte puis `docker compose cp`.
- Environnement cloud « rolling » (personnel) : autorise api.wordpress.org, downloads.wordpress.org, wordpress.org,
  meetings.hubspot.com, static.hsappstatic.net, api.hubspot.com, app.hubspot.com (calendrier de la cliente),
  js.hsforms.net, forms.hsforms.com, forms-na1.hsforms.com (formulaire newsletter).
- Elementor Pro n'a pas de traduction sur wordpress.org : une partie de l'interface Pro reste en anglais (comme chez la cliente).

## Méthode imposée

- Phase 0 sans code, puis validation. Une idée nouvelle (animation, mise en page) : maquette HTML d'abord.
- « question » ou « ne relance pas le thème » : répondre sans rien modifier.
- Bug chez la cliente : le reproduire avant de corriger, sinon demander capture / vidéo / HTML.
- Tests : d'abord dans l'éditeur (insertion de chaque bloc depuis Mes modèles avec les deux choix de
  « Choisissez comment appliquer les styles », poignées roses au survol, changement d'une image et
  d'une classe), puis le site. Vrais contenus, images de formats différents.
- Par bloc : capture maquette vs thème à 1440, 1024 et 390 px, écarts corrigés avant livraison.
- À chaque livraison : axe 0 violation (contrastes compris), PHPCS 0 erreur, règles SEO vérifiées une à une.
- Livraison : un seul zip par tour, testé comme mise à jour d'une version précédente.
  Message : ce qui a changé en 5 lignes, puis étapes numérotées avec les libellés français exacts
  de WordPress et d'Elementor (dont vider le cache).
- Jamais de vidéos ni de photos clients dans le dépôt git.
- Répondre en français, simplement et court.
- Site publié transmis par la cliente : relever ses textes modifiés et les reporter dans les modèles (blocs.py) avant toute refonte.

## Architecture technique (constatée dans Elementor 4.3.6, à respecter)

- **Classes globales** : stockées comme posts (`Global_Classes_Repository`), données = `variants[]`
  (`meta.breakpoint` desktop/tablet/mobile…, `meta.state` null/hover…, `props` typés `$$type`, `custom_css.raw` en base64
  pour ce qu'Elementor ne convertit pas). Libellé = nom de classe dans le HTML (`class="titre-lg"`), l'élément référence
  l'id (`g-xxxx`). Libellés : `[a-zA-Z0-9_-]`, pas de chiffre au début, pas d'accent. Limite 1000.
  CSS généré : `.elementor .titre-lg{…}` ; `:hover` devient `:hover,:focus-visible`.
- **Variables** : `--libellé` (ex. `var(--encre)`), ids `e-gv-xxxx`. Couleur et police en gratuit ; taille = Pro
  (bloqué sur ma copie sans licence). Police = nom Google Fonts seul ; le thème héberge les polices et coupe Google Fonts.
- **Outils officiels utilisés au build** (abilities, option `elementor_mcp_enabled` = 1 sur MA copie seulement) :
  `elementor/manage-global-variable`, `elementor/manage-classes` (CSS brut → props natives), `elementor/build-composition`
  (XML + config + classes + style → `_elementor_data`), `get-widget-schema`. Il faut une visite admin pour créer les
  capacités Elementor. Scripts d'essai : scratchpad/wp/essais.
- **Le thème n'appelle pas ces outils chez la cliente** : il embarque les données natives exportées (classes, variables,
  éléments JSON) et les installe via les API Elementor (repository des classes/variables, `templates_manager` source
  local `save_item`, ids fixes pour pouvoir mettre à jour).
- **Fenêtre « Choisissez comment appliquer les styles »** (insertion d'un modèle) : `match_site` (« Adopter le style de
  ce site »), `keep_create` (« Conserver les styles du modèle » + case « Ajouter les classes et variables… »),
  `keep_flatten` (même choix, case décochée : les classes sont **recopiées en styles locaux**, leur nom disparaît).
  ⇒ Règle : **tout le visuel est dans les styles Elementor** (props ou `custom_css` des classes), jamais dans du CSS du
  thème accroché à un nom de classe. Contexte sombre : la classe `section-sombre` pose des propriétés CSS
  (`--rl-…`) héritées par les enfants, les composants lisent `var(--rl-…, valeur claire)`.
  CSS du thème limité à : @font-face, @keyframes `rl-…`, réinitialisations, « réduire les animations », gel des
  animations dans l'éditeur, en-tête et pied de page (parties PHP du thème).
  Les animations sont posées en `custom_css` des classes (le convertisseur supprime `animation`).
- **Types de modèles** : `e-flexbox`, `e-div-block`, `e-grid`, `container`, `page`, `section` (Pro), `loop-item`,
  `single-post`, `archive`, `error-404`, `header`, `footer`. Composants v4 : `elementor_component`.
- Éléments atomiques : e-flexbox, e-div-block, e-grid, e-heading, e-paragraph, e-button, e-image, e-svg, e-divider,
  e-list (pas géré par build-composition), e-youtube, e-self-hosted-video, e-background-video, e-tabs, e-accordion,
  e-form… Pro : e-checklist, e-anchor. Le texte accepte b, strong, span, em, a, br…
- **Licence Pro** : ma copie n'a pas de licence ⇒ balises dynamiques et variables de taille Pro bloquées. On ne
  contourne pas la licence. Ce qui en dépend se teste chez la cliente (ou thème = balises dynamiques maison).

## Livraison 3 : architecture du contenu dynamique (vérifiée sur la copie, sans licence Pro)

- **Balises maison** : classes `\Elementor\Core\DynamicTags\Tag` / `Data_Tag` enregistrées sur `elementor/dynamic_tags/register`
  (groupe « Rolling »). Elles apparaissent dans les éléments atomiques (icône base de données) **seulement si chaque
  contrôle a name/section/label/default** : redéfinir `register_advanced_section() {}` (les contrôles Avant/Après/Repli
  du Tag de base n'ont pas de défaut ⇒ balise rejetée en silence). Rendu vérifié : la balise lit l'article courant.
- **Fiches** (réalisation, témoignage, webinar…) : le thème affiche, dans son modèle PHP de fiche, un **modèle Elementor
  de Mes modèles** (`get_builder_content_for_display`), avec l'article courant en place ⇒ modifiable dans Elementor, sans
  Theme Builder Pro. Modèle utilisé choisi dans Personnaliser (V2 à côté si la cliente a modifié le sien).
- **Listes** (cartes) : widget maison « Rolling · Liste » (v3) qui boucle sur les contenus et affiche pour chacun un modèle
  de carte Elementor (vérifié : un rendu par article, pas de mélange). `is_dynamic_content()` = vrai (cache des éléments).
- **Parties facultatives** (citation, chiffres, vidéos, générique, transcription) : classes repères vides `si-…` +
  filtre `elementor/frontend/{type}/should_render` qui retire le bloc si le champ est vide.
- Contenus répétés (chiffres clés) : widget maison qui écrit le HTML avec les classes globales existantes.
- Collection Loop / Loop Grid / Display Conditions = Pro : non utilisés (option A).

## Leçons de construction (Elementor 4.3.6, vérifiées sur la copie de test)

- **Aucun conteneur vide dans un bloc** : l'éditeur glisse sa barre et sa zone de dépôt dans chaque conteneur, les
  décors vides y sont déformés. Points, halos, barres, pistes = pseudo-éléments des classes ; boutons lecture = image du kit.
- **`.e-con::before` est positionné par Elementor** (position, top/left, height, border, opacity) : tout `&::before`
  d'une classe commence par une remise à zéro (`position: relative; top/right/bottom/left: auto; height: auto…`).
- **CSS personnalisé des styles locaux = rendu seulement avec licence Pro** (Elementor le retire sinon). Donc aucun CSS
  non natif ni règle imbriquée en style local : `construire-blocs.php` refuse (« À METTRE DANS UNE CLASSE »).
  Le CSS personnalisé des **classes** est rendu sans licence.
- Mode « Conserver les styles, case décochée » (keep_flatten) : classes recopiées en styles locaux ⇒ leur CSS
  personnalisé n'est rendu qu'avec la licence ⇒ **à faire tester par la cliente** (test B).
- Mode « Conserver les styles, case cochée » (keep_create) : Elementor crée **toujours** des copies `DUP_…` des classes
  (comportement voulu par Elementor). Rendu identique, mais classes en double ⇒ recommander « Adopter le style de ce site ».
- Mode « Adopter le style de ce site » : rendu identique au site (vérifié, 37 px d'écart sur 1440×820).
- Après insertion, l'éditeur réinitialise ses classes et ne recharge que celles de la page enregistrée (défaut Elementor) :
  `assets/js/rolling-editeur.js` appelle `elementorV2.editorGlobalClasses.loadExistingClasses(ids du thème)` à
  l'ouverture et après l'évènement `elementor/global-styles/imported`. Bloc stylé tout de suite (vérifié).
- Supprimer une classe utilisée fait retirer sa référence des pages par Elementor ⇒ l'installateur met à jour les
  modèles d'abord, puis ne supprime une classe obsolète **que si plus rien ne l'utilise** (Global_Classes_Relations).
- L'éditeur réécrit les données (null → « », nombres → chaînes) : empreintes normalisées (Rolling_Donnees::empreinte)
  et relues après enregistrement, sinon fausses « V2 » / fausses « classes modifiées ».
- `e-button` a un fond bleu par défaut : toute classe de bouton pose `background-color`.
- Règle `& img` dans une classe de cadre = attrape aussi les icônes posées dedans : utiliser `image-couvrante`.
- Texte riche : la classe d'un `<span>` est retirée ⇒ mots en dégradé par la mise en forme (italique = dégradé de
  section, gras = IA, souligné = humain ; chapô : souligné = mot manuscrit).
- Contrastes sur photo/dégradé : `scratchpad/tools/contraste-photo.js` (pire pixel, 2e centile). Dégradé humain finit
  à #EC5E2B ; plaques en verre fumé (rgba(22,14,52,.66→.46)) ; plaque-claire pour la vignette orange.
- Recommander à la cliente « Adopter le style de ce site » (keep_create crée des `DUP_`).
- **Bug Elementor 4.3.6 (keep_flatten)** : la variable de police devient `$$type: string` alors que le validateur attend
  `font-family` ⇒ tout le style local est jeté à l'enregistrement (titres, textes, boutons sans style). ⇒ **pas de
  variable de police** : les classes écrivent le nom de la police (`font-family` natif). Variables police-* retirées.
- keep_flatten ajoute les variantes des classes **après** le style local de l'élément (le local perd), puis les classes
  dans l'ordre de l'élément (la dernière gagne). Sur le site : local > classes, et entre classes l'ordre global.
  ⇒ règle : **une propriété = une seule source par élément** (pas de réglage local qui reprend une propriété d'une de
  ses classes ; sinon une classe modificatrice prioritaire) et classes de l'élément rangées par priorité
  (`construire-blocs.php`). Contrôle bloquant : `outils/chevauchements.py` (lancé par construire.sh).
- Fichiers CSS chargés : global-desktop, local-desktop, global-tablet, local-tablet, global-mobile, local-mobile.
- `.e-con` pose déjà `position: relative` : les classes de cadre ne la posent pas (sinon conflit avec un local absolu).
- Valeurs qui dépendent du parent : propriétés `--rl-…` posées par le parent (ex. `--rl-rayon-photo`, `--rl-marge-vignette`,
  `--rl-lecture`), lues par l'enfant avec une valeur par défaut.
- Poignées d'Elementor : accrochées 24 px au-dessus de chaque section ⇒ `section` a `overflow-clip-margin: 24px`, et
  `main.site-main { overflow-x: clip }` (thème) empêche le défilement horizontal des halos.
- Les classes **modificatrices** d'un `::before` (couleur d'un point) ne contiennent pas la remise à zéro (elle écrasait
  la taille posée par la classe de base).
- `.e-con::before` reçoit aussi `width: 100%` d'Elementor : un `::before` calé par top/right/bottom/left pose `width: auto`.
- Ordre des classes (priorité) : l'installateur le réapplique **même si aucune classe n'a changé** (bug corrigé en 0.2.0 :
  une nouvelle priorité n'était pas prise en compte). Toute classe modificatrice va dans `$prioritaires`.
- Illustrations de fausses interfaces (mails, cartes, schéma « espace projet », vignettes d'exemples) = **images capturées
  dans la maquette** (`outils/capturer-maquette.js` + `outils/captures/accueil.json`, puis `vers-webp.py`), alt vide si
  décoratives, alt descriptif sinon. Raison : pas de faux textes lus par Google ni par les lecteurs d'écran ; la cliente
  remplace l'image dans Elementor. Les vrais textes (titres, listes, légendes) restent des éléments Elementor.
- Tailles d'image : « thumbnail » = carré recadré ⇒ seulement pour des images carrées ; chiffres et flèches en « medium ».
- `<small>` est retiré du texte riche ; `<span>` (sans classe) et `<sup>` passent : `prix` met le `span` en petit.
- Écran « Votre vidéo » : boutons = liens `#rl-format-9-16`, `#rl-langue-en` ; cadre d'id `rl-ecran` (data-format posé
  par rolling.js) ; versions d'id `rl-version-fr/en/es` (lang posé par rolling.js) ; tailles par variables `--rl-…`.
- « Page complète » : `99-page-complete` assemble les blocs construits (type page), refaite à chaque construction.
- `construire.sh` s'arrête en silence si `blocs.py` échoue (ex. « Nom en double ») : ne pas filtrer sa sortie par grep
  sans vérifier le code de retour. Noms d'éléments uniques (chiffres répétés numérotés).
- Aperçu mobile de l'éditeur = 360 px : tester aussi à 360.
- Logos : pas de bouton pause (choix cliente) ; défilement arrêté au survol / au clavier et avec « réduire les animations » ;
  la cliente coupe le défilement en retirant la classe `logos-defilants`.
- Copie de test hors ligne : mu-plugin `scratchpad/wp/mu/copie-hors-ligne.php` (répond aux appels elementor.com, sinon
  « Mes modèles » plante sur `reorder_categories`). Jamais dans le thème.
- Chaîne : `outils/construire.sh [styles|blocs|tout]` ; sources `outils/source/{variables,classes}.json`, `outils/blocs.py` ;
  page d'essai `outils/page-essai.php` ; tests éditeur `scratchpad/tools/editeur*.js`, `insertion-debug.js`.

## Exigences du thème

1. Tout se modifie dans Elementor 4 (éléments atomiques, classes globales, variables) : aucun texte ni
   image en dur dans le PHP. Chaque élément se modifie, se déplace, se duplique, change de style.
2. Styles prêts à l'emploi, sans doublons. Une classe = un rôle, nom français + description.
   Les classes de mise en page (colonnes) ne sont jamais doublées par un réglage local.
3. Un bloc Elementor par rubrique de la maquette + « Page complète », installés par le thème dans
   Modèles > Mes modèles, images de départ copiées dans la médiathèque. Pas de fichier d'import.
4. En-tête et pied de page dans le thème, fidèles à la maquette, menu d'Apparence > Menus (menu unique),
   réglages dans Apparence > Personnaliser.
5. Modèles du site (article, cartes des listes, réalisation, témoignage, 404) = modèles Elementor
   modifiables. Un modèle modifié par la cliente n'est jamais écrasé : la nouvelle version arrive à côté (« V2 »).
6. Rendu identique éditeur / site, quel que soit le choix de « Choisissez comment appliquer les styles ».
7. Mise à jour du thème : remet à jour classes, blocs et modèles non modifiés, retire l'inutile,
   vide le cache d'Elementor, ne touche pas aux pages.
8. Animations de la maquette. Rien ne bouge dans l'éditeur ni avec « réduire les animations ».
9. Accessibilité : 0 violation axe, contrastes compris (effets de la maquette compris).

Contenus gérés dans l'administration : réalisations vidéo, témoignages, logos clients, offres et packs, articles.
SEO : métas gérées par le thème, un seul outil (pas de doublon avec une extension SEO).
Formulaire contact / RDV : HubSpot, ajouté plus tard par la cliente.
Pas d'image contenant du texte (SEO) : tout élément de la charte avec du texte est recréé en éléments Elementor.

## Pages

Accueil, Offres, Réalisations, Témoignages, Blog, Contact, Mentions légales, Politique de confidentialité,
CGV (texte plus tard), Landing page Google Ads (logo seul dans l'en-tête, ordre du playbook :
Hero > Douleur > Produit > Preuve sociale > Action > Objections/FAQ + preuves de confiance).

Article de blog (dessin de la cliente) : colonne principale = fil d'Ariane, titre, catégorie + « Publié le »,
image, texte, « À lire aussi » ; colonne latérale = temps de lecture, partage, « Résumer avec »
(ChatGPT, Claude, Perplexity, Le Chat), bloc CTA. Sommaire demandé (emplacement à valider).

## Rédaction (règles de la cliente)

- « nous », jamais « on » (sauf citations client, mot pour mot). Parler au client : « vous », « vos équipes ».
- Phrases courtes, sans jargon. Verbes concrets (« Partagez vos contenus et vos projets »).
- Le motion est une capacité parmi d'autres (tournage, interviews, démos produit, avatars, voix off).
- Ne pas nommer les IA internes : « notre IA Factory ». Pas de « vidéotiser » / « vidéotisation ».
- L'illimité reste visible : « à prix fixe, en illimité ».
- CTA principal « Réserver un échange », secondaire « Voir les offres ».
- Pitchy cité ouvertement ; ne pas nommer Pitchy Studio (sauf citation client verbatim).
- Titres en phrase avec point final. Capitales seulement dans les intitulés de poste.
- Typo française : espace insécable avant ? ! : ; , guillemets « », « 30 minutes ». Pas d'emoji.
- **« +500 » (jamais « 500+ »)** et **aucun tiret long « — »** dans les textes (« ça fait très IA », demande du 18/11) :
  à corriger partout à la prochaine version (blocs.py : méthode, équipe, témoignage, note des offres, comparatif ; chiffres
  en verre « + 5 0 0 » ; textes des classes / widgets / contenus).
- Mot-clé principal : « agence vidéo ».
- À ne pas véhiculer : agence de pub type Havas/Publicis, usine low cost, agence au devis.

## Charte graphique (résumé)

- Polices : Bricolage Grotesque (titres, 650, −0,05 em), Hanken Grotesk (texte), Caveat (annotations, ≥ 27 px).
- Couleurs clair / sombre : surface #FAF9F7/#0F0F12, raised #FFFFFF/#1C1730, tint #FFF1E7/#17112C,
  ink #0F0F12/#FAF9F7, ink-2 #4A4858/#C9C3DE, ink-muted #6E6B7B/#B6B0CC, ink-faint #8F89A8 (≥ 24 px),
  violet #5B3BEA/#C4B5FD, violet-deep #4A2BB8/#E9E2FF, blue #3434E0/#8C8EFF, blue-deep #2A2AB8/#C9CBFF,
  orange #E0562A/#FFA27A (≥ 24 px en clair), orange-deep #A63C14/#FFD1BF, note #D9531F (≥ 27 px),
  peach #FF8A5B (jamais du texte sur clair), peach-soft #FFD1BF, lilac-soft #EDE6FF, focus #5B3BEA/#C4B5FD.
- Tailles : title-xl 72, lg 58, md 48, sm 42, xs 23, lead 22, body-lg 19, body 17, body-sm 15,
  button 16, eyebrow 12, **label 12 px (décision cliente : 11 px de la charte remonté à 12 px pour le SEO)**.
- Mise en page : conteneur 1240, gouttière 28, sections 124 haut/bas, 56 titre → contenu, 104 entre sous-parties.
  Rayons 10 / 18 / 26 / 40 / 999. Sous 980 px : une colonne, annotations sur visuel masquées,
  recouvrement « Pour qui » arrêté, titres à ~60 %.
- Showreel : démarre sans le son à 55 % visible, pause sous 25 %, bouton « Activer le son », poster, preload="none".
- Logos clients : gris à 72 %, couleur au survol (demande cliente).

## Décisions

- Elementor 4.3.6 : jusqu'à 1000 classes globales (pas de contrainte de nombre). Éléments atomiques
  disponibles : heading, paragraph, button, image, svg, divider, list (vraie liste ul/li), div-block, flexbox,
  grid, youtube, self-hosted-video, background-video, tabs, accordion, form, collection-loop, template-renderer.
  Module « Interactions » (moteur Motion JS) = le « Motion » du cahier des charges : apparitions fade/slide/scale
  réglables par la cliente. Les animations en boucle de la maquette (halos, flottement, anneau, REC, défilement
  des logos) restent en CSS du thème.
- Le logo renvoie à l'accueil ; pas de lien « Accueil » dans le menu.
- **Balises dynamiques : option A** (balises maison du thème, testées chez moi, ~15 balises dans ~8 modèles du site).
  Exception B : si une balise demande plus (ex. un cas avancé), utiliser celle de Pro et **dire à la cliente laquelle
  tester chez elle**.
- Chiffres en verre : **nouveau jeu de la cliente** (chiffres-verre_1.zip : 0-9, +, %, k, M, 500-plus ; style ballon rose-violet),
  à la place de ceux de la maquette. Un nombre = chiffres composés côte à côte (ex. « 1 » + « 0 »), images décoratives
  (alt vide) + texte réel masqué pour les lecteurs d'écran. Coche des listes : **check-glass.svg** de la cliente.
- L'en-tête (pilule en verre, collant) se pose sur le hero : les halos passent dessous, pas de coupure.
- Page Contact : FAQ propre à l'échange (5 questions, réponses [À confirmer]), pas la FAQ de l'accueil.

- Labels en 12 px (pas 11).
- Les illustrations uniques de la maquette sont des éléments Elementor modifiables ; styles locaux seulement s'ils sont
  natifs (sinon une classe dédiée, cf. « Leçons de construction »). Exception (livraison 2) : les fausses interfaces
  décoratives sont des images capturées (voir Leçons).
- Écran « Votre vidéo » de la section 00:05 : rendu interactif (16:9, 9:16, 1:1 et FR, EN, ES) —
  maquette HTML à faire valider avant intégration.

## Défauts relevés dans la maquette (à corriger dans le thème)

- 390 px : pas de menu burger (le menu passe sur 3 lignes) ; visuels du hero cassés (barre vide verticale) ;
  cartes « Pour qui », « Le meilleur de l'humain » et « Ce que Création + Tournage ajoute » coupées à droite ;
  écran « Votre vidéo » (00:05) : textes superposés ; tableau des offres très serré.
- 1024 px : « Pourquoi la vidéo » en 3 + 1 carte orpheline ; décor en verre du CTA final qui chevauche le titre.
- axe sur la maquette : 0 problème de contraste mesurable ; manquent `<main>` et les repères (landmarks).
  Les textes en dégradé et sur photo ne sont pas mesurables par axe : contrôle manuel prévu.

## Réponses de la cliente (phase 0, tour 2)

- Extensions installées : Activity Log, ACF 6.8.11, Angie, Cookie Consent (Elementor), Elementor 4.3.6,
  **Elementor Pro 4.3.3**, Image Optimization, Manage, Web Accessibility (Ally : injecte un widget, à
  vérifier avec axe). Pas encore d'extension SEO ni de cache.
- Pro utilisé : Theme Builder (modèles article, 404, réalisation, témoignage) et Loop Grid (cartes des listes).
- Médias : la cliente importe elle-même ses médias (dont le showreel) dans WordPress. Photos hors de git.
- Logos clients : modèle Elementor avec **défilement continu + bouton pour l'arrêter** (maquette HTML faite).
- **Pas de packs de tokens.** Offres : s'en tenir strictement à la maquette (2 formules).
- Animations : en CSS seulement (pas de bibliothèque JS).
- L'illimité est assumé. Textes de la maquette gardés tels quels (« Création + Tournage », « Choisir Complet »).
  Intitulés de poste avec capitales partout (« Directeur Artistique », « Directeur Technique »…). « Inès ».
- « Réserver un échange » → page Contact (la cliente y met son calendrier HubSpot).
- Article : partage LinkedIn, Facebook, Instagram, X, WhatsApp, e-mail ; « Résumer avec » ChatGPT, Claude,
  Perplexity, Le Chat ; sommaire dans la colonne latérale qui suit le défilement.
  Instagram n'a pas de lien de partage web : bouton = partage natif du téléphone, sinon copie du lien.
- Landing : l'indexation est gérée par la cliente avec son extension SEO (le thème ne s'en occupe pas).
- Robots IA : autoriser recherche/citation, bloquer entraînement. Liens obligatoires (règle 1.12) adaptés :
  /offres/ et « Réserver un échange » au lieu des pages Pitchy.
- Maquette HTML d'abord pour chaque page sans maquette (Offres, Réalisations, Témoignages, Blog, Article,
  Contact, Landing, 404).
- Polices hébergées dans le thème.

## Questions encore ouvertes

- **Extension SEO (décidé, mis à jour)** : **Yoast SEO** (plus Rank Math). La cliente installe Rank Math (ou équivalent) pour les métas et l'indexation. C'est
  **lui** l'outil unique des métas (title, description, canonical, OG, sitemap) ; exception demandée par la cliente :
  le thème pose le noindex des pages cochées (champ ACF `noindex`) et retire auteurs / catégories (0.3.1).
  Le thème n'ajoute que ce que l'extension ne fait pas (VideoObject des réalisations, à vérifier selon l'extension
  choisie à la livraison 4, sans doublon de schéma).
- Hébergement OVH (offre Pro ou Performance). Pas de cache de pages côté OVH : recommandé WP Super Cache.
  Le thème videra aussi ce cache à sa mise à jour s'il est présent.
- Pied de page : validé = reprend le menu principal + 2e menu « Pied de page » (liens légaux). Mise en page
  à montrer dans les maquettes HTML des pages.
- Menu du haut (validé, mis à jour) : Offres, Réalisations, Témoignages, Ressources (sous-menu), L'agence + bouton « Réserver un échange ».
  La cliente ajoutera elle-même des liens vers les sections de l'accueil (ancres). Les blocs gardent les
  ancres de la maquette : #vague (Pourquoi), #methode, #exemples, #ia, #offres, #contact (+ #humain).
- Activation du thème : crée une seule fois les pages manquantes (vides) et les deux menus
  (« Menu principal », « Pied de page »), placés aux bons emplacements. Ne les retouche plus ensuite.
- WP Super Cache installé chez la cliente.
- Partage article : Instagram = bouton « Copier le lien » (validé).
- Classes : validées dans le principe, à affiner plus tard avec la cliente.

## Maquettes HTML

Sources dans `maquettes/` (sans images). Fichiers à ouvrir : `maquette-logos-ecran.html`, `maquette-pages.html` (hors git).

- `maquette-pages.html` : 10 pages (Offres, Réalisations, Réalisation, Témoignages, Témoignage = cas client,
  Blog, Article, Contact, Landing, 404), en-tête avec menu burger, pied de page 3 colonnes. axe 0 à 1440 et 390.
  Textes marqués [Texte d'exemple] / [À compléter] à fournir par la cliente (témoignages : elle les ajoute).
  Retours tour 1 appliqués (chiffres, coche, en-tête landing, FAQ contact).
- **Validé : Accueil ≠ Offres.** Accueil, section 00:07 = version courte (2 cartes prix + 4 coches chacune,
  promesses de l'illimité, boutons « Réserver un échange » + « Voir les offres ») : page « Accueil · L'offre en
  version courte » de la maquette. Page Offres = tableau complet + Création + Tournage + FAQ de l'abonnement
  (réponses [À fournir] par la cliente). H1 Offres : « Nos formules d'agence vidéo, à prix fixe. »
  (le H2 de l'accueil garde « Un prix fixe. Des vidéos sans fin. »).
- Landing, comparatif : « En interne avec Canva » (pas « stagiaire »). Ce libellé vaut partout.

- `maquette-logos-ecran.html` (hors git, contient les logos clients) : **validée**.
  - Logos : le défilement s'active / se coupe **depuis Elementor** (classe `logos-defilants`). Côté visiteur :
    arrêt au survol / au clavier, **pas de bouton pause** (choix de la cliente, livraison 0.1.1).
  - Écran 00:05 : validé tel quel. Ajouter une image de fond remplaçable dans Elementor (voile violet
    gardé pour la lisibilité). Choix A validé : une seule image, recadrée selon le format.
