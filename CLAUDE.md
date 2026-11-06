# Rolling — thème enfant Hello Elementor

Mémoire du projet. À relire en début de session et à tenir à jour à chaque décision.

## Statut

- Phase 0 (sans code) : analyse faite, questions + liste des blocs + liste des classes envoyées.
  **En attente de validation de la cliente avant d'écrire du code.**
- Le WordPress de production existe déjà chez la cliente : on livre seulement le thème (zip à importer).

## Environnement de la cliente

- WordPress 7.1.4, Elementor 4.3.6, Elementor Pro (One) : oui. Administration en français.
- Site : agence-rolling.fr. Langue : fr (anglais plus tard : prévoir hreflang).
- Test local : image Docker `wordpress:7.1.4-php8.3-apache` (Docker Hub accessible).
  wordpress.org est bloqué par le réseau de la session : le zip d'Elementor 4.3.6
  (et celui de Pro) doit être fourni par la cliente ou le domaine autorisé.
- Capture de l'écran Extensions : annoncée mais pas reçue (à redemander).

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

- Labels en 12 px (pas 11).
- Les illustrations uniques de la maquette (mini-email, mini-article, module PPT, carte convention,
  résultat de recherche, carte intranet, storyboard, schéma IA Factory) sont des éléments Elementor
  modifiables avec styles locaux (usage unique = pas de classe globale).
- Écran « Votre vidéo » de la section 00:05 : rendu interactif (16:9, 9:16, 1:1 et FR, EN, ES) —
  maquette HTML à faire valider avant intégration.

## Défauts relevés dans la maquette (à corriger dans le thème)

- 390 px : pas de menu burger (le menu passe sur 3 lignes) ; visuels du hero cassés (barre vide verticale) ;
  cartes « Pour qui », « Le meilleur de l'humain » et « Ce que Création + Tournage ajoute » coupées à droite ;
  écran « Votre vidéo » (00:05) : textes superposés ; tableau des offres très serré.
- 1024 px : « Pourquoi la vidéo » en 3 + 1 carte orpheline ; décor en verre du CTA final qui chevauche le titre.
- axe sur la maquette : 0 problème de contraste mesurable ; manquent `<main>` et les repères (landmarks).
  Les textes en dégradé et sur photo ne sont pas mesurables par axe : contrôle manuel prévu.

## Questions en attente (phase 0)

Voir le message de phase 0 dans la conversation ; reporter ici les réponses dès qu'elles arrivent.
