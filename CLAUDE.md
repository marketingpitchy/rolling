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
  Prochaines : 3 = contenus admin + modèles du site (balises maison) ; 4 = pages Offres, Contact, landing…, SEO, contrôle final.
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

- **Extension SEO (décidé)** : la cliente installe Rank Math (ou équivalent) pour les métas et l'indexation. C'est
  **lui** l'outil unique des métas (title, description, canonical, OG, sitemap, noindex) ; le thème n'en sort aucune.
  Le thème n'ajoute que ce que l'extension ne fait pas (VideoObject des réalisations, à vérifier selon l'extension
  choisie à la livraison 4, sans doublon de schéma).
- Hébergement OVH (offre Pro ou Performance). Pas de cache de pages côté OVH : recommandé WP Super Cache.
  Le thème videra aussi ce cache à sa mise à jour s'il est présent.
- Pied de page : validé = reprend le menu principal + 2e menu « Pied de page » (liens légaux). Mise en page
  à montrer dans les maquettes HTML des pages.
- Menu du haut (validé) : pages Offres, Réalisations, Témoignages, Blog + bouton « Réserver un échange ».
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
