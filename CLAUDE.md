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
- Environnement cloud « rolling » (personnel) : autorise api.wordpress.org, downloads.wordpress.org, wordpress.org.
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
- Typo française : espace insécable avant ? ! : ; — guillemets « » — « 500+ », « 30 minutes ». Pas d'emoji.
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
