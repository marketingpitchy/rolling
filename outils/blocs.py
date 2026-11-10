"""Source des blocs Elementor du thème Rolling.

Chaque bloc est un arbre d'éléments atomiques. Ce script écrit outils/source/blocs/<clé>.json
au format attendu par l'outil officiel « elementor/build-composition » (XML + config + classes + style).
Les images de départ sont désignées par https://media.rolling.invalid/<clé du média>.<ext> :
le thème les remplace à l'installation par les images copiées dans la médiathèque.

Usage : python3 -I outils/blocs.py
"""
import json
import os

ICI = os.path.dirname(os.path.abspath(__file__))
MEDIA = 'https://media.rolling.invalid/'


class E:
    """Un élément : type atomique, nom (visible dans le Navigateur d'Elementor), réglages, classes, style local."""

    def __init__(self, type_, nom, cfg=None, cls=None, style=None, enfants=None):
        self.type, self.nom, self.cfg, self.cls, self.style = type_, nom, cfg or {}, cls or [], style or ''
        self.enfants = enfants or []


def F(nom, cls=None, style=None, enfants=None, tag=None, lien=None, ancre=None, onglet=False):
    cfg = {}
    if ancre:
        cfg['_cssid'] = ancre
    if tag:
        cfg['tag'] = tag
    if lien:
        cfg['link'] = {'destination': lien, 'isTargetBlank': True} if onglet else {'destination': lien}
    return E('e-flexbox', nom, cfg, cls, style, enfants)


def D(nom, cls=None, style=None, enfants=None, tag=None):
    return E('e-div-block', nom, {'tag': tag} if tag else {}, cls, style, enfants)


def H(nom, texte, tag='h2', cls=None, style=None, lien=None):
    cfg = {'tag': tag, 'title': texte}
    if lien:
        cfg['link'] = {'destination': lien}
    return E('e-heading', nom, cfg, cls, style)


def DYN(balise, **reglages):
    """Balise dynamique « Rolling » : convertie par construire-blocs.php en valeur dynamique d'Elementor."""
    return '⟦%s%s⟧' % (balise, ''.join('|%s=%s' % (k, v) for k, v in reglages.items()))


def W(nom, widget, **reglages):
    """Widget « Rolling » : un paragraphe repère, remplacé par le widget par construire-blocs.php."""
    return E('e-paragraph', nom, {'paragraph': '⟦widget:%s%s⟧' % (widget, ''.join('|%s=%s' % (k, v) for k, v in reglages.items()))})


def P(nom, texte, cls=None, style=None):
    return E('e-paragraph', nom, {'paragraph': texte}, cls, style)


def B(nom, texte, lien, cls=None, style=None, onglet=False):
    lien = {'destination': lien, 'isTargetBlank': True} if onglet else {'destination': lien}
    return E('e-button', nom, {'text': texte, 'link': lien}, cls, style)


def I(nom, media, alt='', cls=None, style=None, taille='full'):
    if media.startswith('⟦'):
        # Image dynamique : adresse valide pendant la construction, convertie ensuite par construire-blocs.php.
        nom_balise, *reglages = media.strip('⟦⟧').split('|')
        media = 'dynamique/%s/%s.png' % (nom_balise, '&'.join(reglages))
    src = {'url': MEDIA + media}
    if alt:
        src['alt'] = alt
    return E('e-image', nom, {'image': {'src': src, 'size': taille}}, cls, style)


def separer_imbrique(css):
    """Sépare les règles imbriquées « & … { … } » (hors @media) : l'outil de composition les ignore
    dans les styles locaux, le script de construction les pose ensuite dans le CSS personnalisé."""
    garde, brut, i, n = [], [], 0, len(css)
    while i < n:
        if css.startswith('&', i) or css.startswith('@media', i):
            debut = i
            j = css.index('{', i)
            profondeur, k = 1, j + 1
            while profondeur:
                profondeur += {'{': 1, '}': -1}.get(css[k], 0)
                k += 1
            (brut if css.startswith('&', debut) else garde).append(css[debut:k])
            i = k
        else:
            garde.append(css[i])
            i += 1
    return ''.join(garde).strip(), ' '.join(brut).strip()


def composer(racine):
    """Transforme l'arbre en entrée pour build-composition."""
    vus, config, classes, style, brut = set(), {}, {}, {}, {}

    def xml(e):
        if e.nom in vus:
            raise ValueError('Nom en double : ' + e.nom)
        vus.add(e.nom)
        if e.cfg:
            config[e.nom] = e.cfg
        if e.cls:
            classes[e.nom] = e.cls
        if e.style:
            natif, imbrique = separer_imbrique(e.style)
            if natif:
                style[e.nom] = natif
            if imbrique:
                brut[e.nom] = imbrique
        nom = e.nom.replace('&', '&amp;').replace('"', '&quot;')
        if not e.enfants:
            return '<%s configuration-id="%s"></%s>' % (e.type, nom, e.type)
        return '<%s configuration-id="%s">%s</%s>' % (e.type, nom, ''.join(xml(x) for x in e.enfants), e.type)

    x = xml(racine)
    return {'xml_structure': x, 'element_config': config, 'classes': classes, 'style': style}, brut


# --------------------------------------------------------------------------------------------
# Morceaux réutilisés. Règle : aucun conteneur vide (l'éditeur y glisse sa zone de dépôt) :
# les décors (points, halos, barres) sont des pseudo-éléments des classes.
# --------------------------------------------------------------------------------------------

def bandeau(nom, texte, cls=None, style=None):
    return F(nom, ['bandeau-video'] + (cls or []), style, [P(nom + ' · texte', texte)])


def lecture(nom, verre=False, style=None):
    if verre:
        return I(nom, 'bouton-lecture-verre.png', cls=['bouton-lecture', 'bouton-lecture-verre'], style=style, taille='thumbnail')
    return I(nom, 'bouton-lecture.png', cls=['bouton-lecture'], style=style, taille='thumbnail')


# --------------------------------------------------------------------------------------------
# Hero
# --------------------------------------------------------------------------------------------

hero = F('Hero', ['section', 'section-entete', 'halos-haut'], tag='section', enfants=[
    F('Hero · contenu', ['conteneur'], 'display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; gap: 48px;', enfants=[
        F('Promesse', None, 'display: flex; flex-direction: column; flex: 1 1 560px; min-width: 0; gap: 0; padding: 40px 0 0; @media(--tablet) { padding: 0; }', enfants=[
            F('Pastille', ['pastille'], enfants=[P('Pastille · texte', 'Votre agence vidéo illimitée')]),
            H('Titre H1', "Le meilleur <u>de l'humain.</u><br>Le meilleur <b>de l'IA.</b>", 'h1', ['titre-lg'], 'margin-top: 26px;'),
            P('Chapô', "Vos clients, vos candidats, vos collaborateurs s'informent tous en vidéo. Mais vos équipes n'ont pas le temps d'en faire ? La nôtre s'en charge, à prix fixe, <u>en illimité.</u>", ['chapo'], 'margin-top: 26px; max-width: 620px;'),
            F('Boutons', ['rangee'], 'margin-top: 40px;', enfants=[
                B('Bouton principal', 'Réserver un échange', '/contact/', ['bouton-principal']),
                B('Bouton secondaire', 'Voir les offres', '/offres/', ['bouton-verre']),
            ]),
            F('Preuve', ['rangee'], 'margin-top: 24px;', enfants=[
                F('Visages', ['visages'], enfants=[
                    I('Visage 1', 'visage-1.jpg', taille='thumbnail'), I('Visage 2', 'visage-2.jpg', taille='thumbnail'),
                    I('Visage 3', 'visage-3.jpg', taille='thumbnail'), I('Visage 4', 'visage-4.jpg', taille='thumbnail'),
                ]),
                P('Preuve · texte', 'Par les équipes de <strong>Pitchy</strong> — 13 ans de vidéo, 500+ grandes entreprises.', ['texte-petit', 'texte-legende']),
            ]),
        ]),
        D('Visuels', ['lueur-peche'], 'position: relative; flex: 1 1 460px; min-width: 0; height: 680px; padding: 0; @media(--tablet) { height: auto; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; } @media(--mobile) { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }', enfants=[
            D('Tournage', ['cadre-verre', 'cadre-verre-grand'], 'position: absolute; left: 0; right: 0; top: 0; @media(--tablet) { position: relative; grid-column: 1 / -1; }', enfants=[
                D('Tournage · photo', ['cadre-photo'], 'height: 652px; @media(--tablet) { height: 420px; } @media(--mobile) { height: 280px; }', enfants=[
                    I('Tournage · image', 'hero-tournage.jpg', 'Une caméra sur trépied, en plein tournage dans des bureaux', ['image-couvrante'], 'object-position: 56% 50%;'),
                    bandeau('Tournage · bandeau', 'Tournage', ['bandeau-rec']),
                ]),
            ]),
            D('Interview', ['cadre-verre'], 'position: absolute; left: 28px; top: 252px; width: 208px; z-index: 2; @media(--tablet) { position: relative; left: auto; top: auto; width: auto; }', enfants=[
                D('Interview · vidéo', ['cadre-photo'], 'aspect-ratio: 9 / 16;', enfants=[
                    I('Interview · image', 'hero-interview.jpg', 'Une experte filmée en interview', ['image-couvrante'], 'object-position: 53% 28%;'),
                    bandeau('Interview · bandeau', 'Interview'),
                    F('Interview · plaque', ['plaque-verre', 'plaque-bas'], enfants=[
                        P('Interview · titre', '3 gestes<br><span>qui sauvent</span>', ['titre-vignette']),
                        lecture('Interview · lecture', verre=True),
                    ]),
                ]),
            ]),
            D('Démo produit', ['cadre-verre'], 'position: absolute; right: 28px; top: 24px; width: 294px; z-index: 2; @media(--tablet) { position: relative; right: auto; top: auto; width: auto; align-self: start; } @media(--mobile) { grid-column: 1 / -1; order: 3; }', enfants=[
                D('Démo produit · vidéo', ['cadre-photo'], 'aspect-ratio: 16 / 9;', enfants=[
                    I('Démo produit · image', 'hero-demo-produit.jpg', "Un pot de crème ouvert flotte au-dessus d'une pierre", ['image-couvrante'], 'object-position: 50% 40%;'),
                    bandeau('Démo produit · bandeau', 'Démo produit'),
                    F('Démo produit · lecteur', ['plaque-verre', 'plaque-bas', 'plaque-lecteur'], enfants=[
                        lecture('Démo produit · lecture', verre=True),
                    ]),
                ]),
            ]),
            D('Motion design', ['cadre-verre'], 'position: absolute; right: 28px; top: 432px; width: 208px; z-index: 2; @media(--tablet) { position: relative; right: auto; top: auto; width: auto; align-self: start; }', enfants=[
                D('Motion design · vidéo', ['fond-video-orange', 'illus-motion'], 'aspect-ratio: 1 / 1; overflow: hidden; padding: 0; border-radius: 19px;', enfants=[
                    bandeau('Motion design · bandeau', 'Motion design', ['bandeau-clair']),
                    F('Motion design · plaque', ['plaque-verre', 'plaque-bas', 'plaque-claire'], enfants=[
                        P('Motion design · titre', 'Résultats<br><span>2026</span>', ['titre-vignette', 'titre-vignette-sombre']),
                        lecture('Motion design · lecture', verre=True),
                    ]),
                ]),
            ]),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# Ils nous font confiance (logos qui défilent)
# --------------------------------------------------------------------------------------------

LOGOS = [
    ('logo-groupement-mousquetaires.png', 'Groupement Mousquetaires', 46),
    ('logo-club-med.png', 'Club Med', 26),
    ('logo-sncf.png', 'SNCF', 40),
    ('logo-loccitane.png', 'L’Occitane en Provence', 30),
    ('logo-air-france.png', 'Air France', 14),
    ('logo-bnp-paribas.png', 'BNP Paribas', 30),
    ('logo-fnac-darty.png', 'Fnac Darty', 30),
    ('logo-groupama.png', 'Groupama', 28),
    ('logo-veolia.png', 'Veolia', 32),
]

logos = F('Ils nous font confiance', ['section', 'section-suite'], tag='section', enfants=[
    F('Logos · titre', ['conteneur'], 'justify-content: center;', enfants=[
        P('Logos · libellé', 'Ils nous font confiance', ['label'], 'text-align: center; width: 100%;'),
    ]),
    # Les logos viennent de la liste « Logos clients » : une modification met à jour toutes les bandes du site.
    F('Logos · bande', None, 'margin-top: 22px; width: 100%; padding: 0;', enfants=[
        W('Logos · liste', 'rolling-logos'),
    ]),
])

# --------------------------------------------------------------------------------------------
# Showreel
# --------------------------------------------------------------------------------------------

showreel = F('Showreel', ['section', 'section-suite'], tag='section', enfants=[
    F('Showreel · contenu', ['conteneur'], 'display: flex; flex-direction: column; gap: 0;', enfants=[
        F('Annotation', None, 'display: flex; flex-direction: row; justify-content: flex-end; align-items: flex-start; gap: 6px; margin-bottom: 6px; padding: 0 30px 0 0; @media(--tablet) { display: none; }', enfants=[
            P('Annotation · texte', 'ce que nous faisons, en vrai', ['note-main']),
            I('Annotation · flèche', 'fleche-annotation.png', style='width: 34px; margin-top: 14px;'),
        ]),
        D('Showreel · cadre', ['carte-verre', 'carte-ecran'], enfants=[
            D('Showreel · écran', None, 'position: relative; overflow: hidden; padding: 0; border-radius: 30px; background: #2A2233; @media(--mobile) { border-radius: 22px; }', enfants=[
                E('e-self-hosted-video', 'Showreel · vidéo', {'autoplay': True, 'mute': True, 'loop': True, 'playsinline': True, 'controls': True, 'preload': 'none', 'poster_enabled': True, 'poster': {'src': {'url': MEDIA + 'showreel-affiche.jpg'}, 'size': 'full'}}, None, 'display: block; width: 100%; aspect-ratio: 16 / 9;'),
                B('Bouton son', 'Activer le son', '#rl-son', ['bouton-son']),
            ]),
        ]),
        F('Showreel · boutons', ['rangee'], 'justify-content: center; margin-top: 28px;', enfants=[
            B('Showreel · bouton principal', 'Réserver un échange', '/contact/', ['bouton-principal']),
            B('Showreel · bouton secondaire', 'Voir les offres', '/offres/', ['bouton-verre']),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# Morceaux des sections
# --------------------------------------------------------------------------------------------

COLONNE = 'display: flex; flex-direction: column; gap: 0;'


def entete(prefixe, timecode, surtitre, titres, intro=None, style=None):
    """Haut de section : timecode + sur-titre, titre(s), introduction. titres = [(texte, balise, classes)]."""
    enfants = [F(prefixe + ' · repère', ['sur-titre'], enfants=[
        P(prefixe + ' · timecode', timecode, ['timecode']),
        P(prefixe + ' · sur-titre', surtitre),
    ])]
    lignes = []
    for i, (texte, balise, cls) in enumerate(titres):
        lignes.append((H if balise.startswith('h') else P)(prefixe + ' · titre' + (' %d' % (i + 1) if len(titres) > 1 else ''), texte, *((balise, cls) if balise.startswith('h') else (cls,))))
    if len(lignes) > 1:
        enfants.append(F(prefixe + ' · titres', None, 'display: flex; flex-direction: column; gap: 0; padding: 0;', lignes))
    else:
        enfants += lignes
    if intro:
        enfants.append(P(prefixe + ' · introduction', intro, ['texte-intro'], 'max-width: 820px;'))
    return F(prefixe + ' · en-tête', ['entete-section'], style, enfants)


# --------------------------------------------------------------------------------------------
# 00:01 Pourquoi la vidéo
# --------------------------------------------------------------------------------------------

def constat(nom, etiquette, rec, image, question, mot, couleur, halo):
    return F('Carte · ' + nom, ['carte-verre', 'carte-constat'] + ([halo] if halo else []), enfants=[
        P(nom + ' · étiquette', etiquette, ['etiquette-carte'] + (['etiquette-rec'] if rec else [])),
        I(nom + ' · illustration', image, cls=['illustration-ui'], taille='full'),
        F(nom + ' · texte', None, 'display: flex; flex-direction: column; gap: 0; margin-top: auto;', enfants=[
            P(nom + ' · question', question, ['question-carte']),
            P(nom + ' · réponse', mot, ['mot-choc'], 'color: %s;' % couleur),
        ]),
    ])


pourquoi = F('Pourquoi la vidéo', ['section', 'section-sombre', 'halos-nuit'], tag='section', ancre='vague', enfants=[
    F('Pourquoi · contenu', ['conteneur'], COLONNE, enfants=[
        entete('Pourquoi', '00:01', 'Pourquoi la vidéo', [
            ("Hier, une entreprise sans site web n'existait pas.", 'p', ['titre-lg', 'titre-eteint']),
            ('Demain, sans vidéo, <em>elle sera ignorée.</em>', 'h2', ['titre-lg']),
        ]),
        F('Pourquoi · cartes', ['grille-4'], enfants=[
            constat('Emails', 'Déjà écrit', False, 'constat-email.webp', 'Vos emails internes&nbsp;?', 'Ignorés.', '#FFD1BF', None),
            constat('Blog', 'Déjà écrit', False, 'constat-blog.webp', 'Vos articles de blog&nbsp;?', 'Survolés.', '#D9CFFF', 'halo-lilas'),
            constat('Modules', 'Déjà écrit', False, 'constat-module.webp', 'Vos modules PowerPoint&nbsp;?', 'Subis.', '#C8CBFF', 'halo-bleu'),
            constat('Événements', 'À tourner', True, 'constat-evenement.webp', 'Vos événements&nbsp;?', 'Oubliés.', '#FFB48C', 'halo-orange'),
        ]),
        P('Pourquoi · chute', "Vous n'avez pas le temps&nbsp;? <strong>C'est notre métier&nbsp;: nous nous en occupons, sans limite.</strong>", ['phrase-chute'], 'margin-top: 52px;'),
    ]),
])

# --------------------------------------------------------------------------------------------
# 00:02 Pour qui
# --------------------------------------------------------------------------------------------

def liste_coches(prefixe, lignes):
    return F(prefixe, ['liste-coches'], enfants=[
        F(prefixe + ' · ligne %d' % (i + 1), ['ligne-coche'], enfants=[
            I(prefixe + ' · coche %d' % (i + 1), 'coche-verre.png', cls=['coche'], taille='thumbnail'),
            P(prefixe + ' · texte %d' % (i + 1), texte, ['texte-petit']),
        ]) for i, texte in enumerate(lignes)
    ])


def chapitre(nom, cls, photo, texte, photo_gauche=True, style=None):
    return F('Chapitre · ' + nom, ['chapitre'] + cls, style, enfants=photo + texte if photo_gauche else texte + photo, tag='article')


def colonne_chapitre(nom, etiquette, picto, titre, couleur_label, constat, contenus, projets, chips, chip_cls, bouton):
    return [F(nom + ' · texte', ['colonne-chapitre'], enfants=[
        F(nom + ' · haut', None, 'display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 16px; padding: 0;', enfants=[
            P(nom + ' · étiquette', etiquette, [etiquette_cls(couleur_label)]),
            I(nom + ' · picto', picto, cls=['picto-verre'], style='height: 76px; margin: -10px -8px -10px 0;', taille='thumbnail'),
        ]),
        H(nom + ' · titre', titre, 'h3', ['titre-md']),
        F(nom + ' · constat', ['panneau'], enfants=[
            P(nom + ' · constat · label', 'Le constat', ['label', 'label-violet' if couleur_label == 'bleu' else 'label-orange']),
            P(nom + ' · constat · texte', constat, ['texte-fort']),
        ]),
        F(nom + ' · listes', None, 'display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 22px; padding: 0;', enfants=[
            F(nom + ' · contenus', ['pile'], enfants=[P(nom + ' · contenus · titre', 'À partir de vos contenus', ['label']), liste_coches(nom + ' · contenus · liste', contenus)]),
            F(nom + ' · projets', ['pile'], enfants=[P(nom + ' · projets · titre', 'Pour vos nouveaux projets', ['label']), liste_coches(nom + ' · projets · liste', projets)]),
        ]),
        F(nom + ' · canaux', ['rangee'], enfants=[P(nom + ' · canal · ' + c, c, [chip_cls]) for c in chips]),
        B(nom + ' · bouton', bouton, '#contact', ['bouton-principal', 'bouton-petit']),
    ])]


def etiquette_cls(couleur):
    return 'etiquette-metier-bleu' if couleur == 'bleu' else 'etiquette-metier-orange'


pour_qui = F('Pour qui', ['section', 'halos-cotes'], tag='section', enfants=[
    F('Pour qui · contenu', ['conteneur'], COLONNE, enfants=[
        entete('Pour qui', '00:02', 'Pour qui', [('Ce que la vidéo <em>va vous apporter.</em>', 'h2', ['titre-lg'])]),
        chapitre('Marketing', ['chapitre-colle'], [
            D('Marketing · photo', ['cadre-photo', 'photo-chapitre'], enfants=[
                I('Marketing · image', 'pourqui-marketing.jpg', "Des mains tapent sur le clavier d'un ordinateur portable", ['image-couvrante'], 'object-position: 42% 55%;'),
                I('Marketing · illustration', 'pourqui-recherche.webp', cls=['illustration-photo']),
            ]),
        ], colonne_chapitre('Marketing', 'Marketing &amp; communication', 'picto-megaphone.png', 'Soyez vus là où vos clients vous <b>cherchent.</b>', 'bleu',
            'Google et les IA privilégient, dans leurs réponses, les marques qui ont des vidéos. Et YouTube ou TikTok sont devenus des moteurs de recherche à part entière.',
            ['Vos pages produit, en démos vidéo', 'Vos articles, en vidéos de 90&nbsp;s', 'Votre étude annuelle, en série'],
            ['Lancements produit', 'Salons &amp; événements', 'Campagnes LinkedIn'],
            ['SEO', 'GEO / IA', 'YouTube', 'TikTok', 'LinkedIn'], 'chip-bleu', 'Parlons de vos vidéos marketing')),
        chapitre('Interne', ['chapitre-peche'], style='margin-top: 28px; z-index: 2;', photo_gauche=False, photo=[
            D('Interne · photo', ['cadre-photo', 'photo-chapitre', 'viseur'], enfants=[
                I('Interne · image', 'pourqui-interne.jpg', 'Une collaboratrice, souriante, filmée pour une vidéo interne', ['image-couvrante'], 'object-position: 56% 30%;'),
                P('Interne · rec', 'REC', ['rec-libelle']),
                P('Interne · durée', '00:00:58', ['rec-libelle', 'rec-droite']),
                I('Interne · illustration', 'pourqui-intranet.webp', cls=['illustration-photo']),
            ]),
        ], texte=colonne_chapitre('Interne', 'Com interne · RH · Formation', 'picto-equipe.png', 'Soyez enfin <u>regardés.</u>', 'orange',
            "Livrets d'accueil, procédures, formations, notes internes&nbsp;: vous avez des centaines de pages que personne ne lit. Le fond est déjà là, il ne lui manque que la vidéo.",
            ["Votre livret d'accueil, en série d'onboarding", 'Vos procédures, en tutoriels courts', 'Vos formations, en microlearning de 60&nbsp;s', 'Vos notes internes, en rendez-vous vidéo'],
            ['Séminaires &amp; conventions', 'Témoignages collaborateurs', 'Projets de transformation'],
            ['Onboarding', 'Microlearning', 'Marque employeur', 'Actu interne'], 'chip-orange', 'Parlons de vos contenus internes')),
    ]),
])

# --------------------------------------------------------------------------------------------
# 00:03 La méthode
# --------------------------------------------------------------------------------------------

def etape(n, titre, texte):
    return F('Étape %d' % n, ['carte-verre', 'carte-etape'], enfants=[
        I('Étape %d · chiffre' % n, 'chiffre-%d.webp' % n, cls=['picto-verre'], style='height: 80px; align-self: flex-start;', taille='medium'),
        H('Étape %d · titre' % n, titre, 'h3', ['titre-xs']),
        P('Étape %d · texte' % n, texte, ['texte']),
    ])


methode = F('La méthode', ['section', 'section-lilas'], tag='section', ancre='methode', enfants=[
    F('Méthode · contenu', ['conteneur'], COLONNE, enfants=[
        entete('Méthode', '00:03', 'La méthode', [('Vous avez le fond. <em>Nous produisons la forme.</em>', 'h2', ['titre-lg'])]),
        F('Méthode · étapes', ['grille-etapes'], enfants=[
            etape(1, 'Partagez vos contenus et vos projets.', "Articles, présentations, modules de formation, vos propres rushs, mais aussi lancements ou événements à tourner&nbsp;: nous priorisons ensemble ce qui part en vidéo en premier."),
            etape(2, 'Nous produisons, un projet à la fois.', "Motion, habillage ou tournage, selon le projet et votre formule — à votre charte, sous l'œil d'un directeur artistique dédié. Traité à fond, livré, nous enchaînons."),
            etape(3, 'Vous diffusez. Partout.', "Des vidéos prêtes à l'emploi, déclinées dans tous les formats&nbsp;: horizontal, vertical, carré. Pour vos réseaux, vos sites, votre intranet et vos formations."),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# 00:04 Avant → Après
# --------------------------------------------------------------------------------------------

def exemple(n, image, alt, depart, tag, resultat):
    return F('Exemple %d' % n, None, 'display: flex; flex-direction: column; gap: 16px; padding: 0;', enfants=[
        F('Exemple %d · vignette' % n, ['carte-verre', 'carte-vignette'], enfants=[
            I('Exemple %d · image' % n, image, alt, ['image-vignette'], taille='medium_large'),
        ]),
        F('Exemple %d · légende' % n, ['rangee'], enfants=[
            P('Exemple %d · avant' % n, depart, [tag]),
            P('Exemple %d · après' % n, resultat, ['fleche-resultat']),
        ]),
    ])


exemples = F('Avant Après', ['section', 'halo-doux'], tag='section', ancre='exemples', enfants=[
    F('Exemples · contenu', ['conteneur'], COLONNE, enfants=[
        entete('Exemples', '00:04', 'Avant → Après', [('Ce que ça donne, <em>concrètement.</em>', 'h2', ['titre-lg'])]),
        F('Exemples · grille', ['grille-3'], enfants=[
            exemple(1, 'exemple-motion.webp', 'Exemple de vidéo en motion 2D : « 5 idées reçues sur le télétravail »', 'Article de blog', 'tag-bleu', 'vidéo de 90&nbsp;s'),
            exemple(2, 'exemple-demo-produit.webp', 'Exemple de démo produit : « Composition naturelle. »', 'Page web produit', 'tag-bleu', 'démo vidéo'),
            exemple(3, 'exemple-microlearning.webp', 'Exemple de microlearning vertical : « Le réflexe sécurité n°3 »', 'Module de formation', 'tag-orange', 'microlearning 60&nbsp;s'),
            exemple(4, 'exemple-linkedin.webp', "Exemple d'annonce LinkedIn : « Nous ouvrons à Lisbonne. »", 'Communiqué', 'tag-bleu', 'annonce vidéo LinkedIn'),
            exemple(5, 'exemple-serie.webp', 'Exemple de série vidéo : « Rapport RSE, épisode par épisode »', 'Rapport PPT', 'tag-bleu', 'série de 6 épisodes'),
            exemple(6, 'exemple-interview.webp', "Exemple d'interview métier : « 3 questions à Julien »", 'Séminaire &amp; équipes', 'tag-orange', 'interviews &amp; rétrospectives'),
        ]),
        F('Exemples · appel', ['rangee'], 'justify-content: center; margin-top: 64px; text-align: center;', enfants=[
            P('Exemples · question', 'Et avec vos contenus, <em>ça donnerait quoi&nbsp;?</em>', ['accroche']),
            B('Exemples · bouton', 'Réserver un échange', '/contact/', ['bouton-principal']),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# 00:05 Le meilleur de l'IA
# --------------------------------------------------------------------------------------------

LIGNE = 'display: flex; flex-direction: row; align-items: center; gap: 14px; padding: 0;'


def liste_icones(prefixe, lignes):
    return F(prefixe, ['liste-icones'], enfants=[
        F(prefixe + ' · ' + texte, ['ligne-icone'], enfants=[
            I(prefixe + ' · icône · ' + texte, 'ia-icone-' + icone + '.webp', cls=['icone-liste'], taille='thumbnail'),
            P(prefixe + ' · texte · ' + texte, texte, ['texte']),
        ]) for icone, texte in lignes
    ])


def version_ecran(langue, haut, titre, sous, cachee):
    return F('Écran · version ' + langue.upper(), ['version-langue'] + (['version-cachee'] if cachee else []), ancre='rl-version-' + langue, enfants=[
        P('Écran · ' + langue.upper() + ' · haut', haut, ['ecran-haut']),
        P('Écran · ' + langue.upper() + ' · titre', titre, ['ecran-titre']),
        P('Écran · ' + langue.upper() + ' · sous-titre', sous, ['ecran-sous-titre']),
    ])


def membre(prefixe, cle, nom, poste, sombre=False):
    return F(prefixe + ' · ' + nom, ['membre-equipe'], enfants=[
        I(prefixe + ' · ' + nom + ' · photo', 'equipe-' + cle + '.jpg', nom + ', ' + poste, ['portrait'] + (['portrait-sombre'] if sombre else []), taille='medium'),
        P(prefixe + ' · ' + nom + ' · prénom', nom, ['nom-membre']),
        P(prefixe + ' · ' + nom + ' · poste', poste, ['poste-membre']),
    ])


entete_ia = entete('IA', '00:05', 'Sous le capot', [("Le meilleur <em>de l'IA.</em>", 'h2', ['titre-xl'])],
                   intro='Nous orchestrons les meilleures IA du marché au sein de notre <strong>IA Factory</strong>. Pilotée par notre équipe tech, notre plateforme génère des pistes de scénarios, des storyboards et des premières versions. <strong>Nos créas choisissent, corrigent, adaptent… et perfectionnent chaque vidéo.</strong>')
def badges(prefixe, style):
    return F(prefixe + ' · badges', ['rangee'], style, enfants=[
        F(prefixe + ' · badge · ' + texte, ['badge-securite'], enfants=[
            I(prefixe + ' · badge · ' + texte + ' · icône', 'badge-' + icone + '.webp', cls=['icone-badge'], taille='thumbnail'),
            P(prefixe + ' · badge · ' + texte + ' · texte', texte),
        ]) for icone, texte in (('securite', 'IA sécurisée'), ('europe', 'Données hébergées en Europe'), ('rgpd', 'Conforme RGPD'))
    ])


entete_ia.enfants.append(badges('IA', 'margin-top: 8px;'))

ia = F("Le meilleur de l'IA", ['section', 'section-sombre', 'fond-grille', 'halos-ia'], tag='section', ancre='ia', enfants=[
    F('IA · contenu', ['conteneur'], COLONNE, enfants=[
        entete_ia,
        F('IA · schéma', ['grille-schema'], enfants=[
            F('IA · factory', ['carte-verre', 'carte-ia'], enfants=[
                F('Factory · tête', None, LIGNE + ' min-height: 84px;', enfants=[
                    I('Factory · icône', 'ia-icone-factory.webp', cls=['icone-carte'], taille='thumbnail'),
                    F('Factory · titres', None, COLONNE, enfants=[
                        P('Factory · rôle', 'Propose · génère · décline', ['label', 'label-violet']),
                        H('Factory · titre', 'Notre IA Factory', 'h3', ['titre-xs']),
                    ]),
                ]),
                liste_icones('Factory · liste', [('scenario', 'Pistes de scénario'), ('storyboards', 'Storyboards'), ('da', 'Pistes de DA'), ('images', 'Images et illustrations'), ('motion', 'Habillage et motion'), ('voix', 'Voix off'), ('son', 'Sound design'), ('sous-titres', 'Sous-titres et traductions'), ('formats', 'Déclinaisons de formats'), ('versions', 'Premières versions')]),
            ]),
            F('IA · centre', ['colonne-flux'], enfants=[
                P('Centre · vos contenus', 'Vos contenus &amp; vos briefs', ['pilule-flux']),
                I('Centre · espace projet', 'ia-espace-projet.webp', 'Votre espace projet : notre IA propose, nos créas corrigent. Vous suivez, échangez et validez.', ['illustration-hub']),
            ]),
            F('IA · créas', ['carte-verre', 'carte-humain'], enfants=[
                F('Créas · tête', None, LIGNE + ' min-height: 84px;', enfants=[
                    F('Créas · visages', ['visages'], enfants=[I('Créas · visage %d' % i, 'direction-%d.jpg' % i, taille='thumbnail') for i in (1, 2, 3)]),
                    F('Créas · titres', None, COLONNE, enfants=[
                        P('Créas · rôle', 'Choisit · corrige · adapte', ['label', 'label-peche']),
                        H('Créas · titre', 'Directeur artistique,<br>chef de projet<br>&amp; motion designer', 'h3', ['titre-xs']),
                    ]),
                ]),
                liste_icones('Créas · liste', [('intention', "Note d'intention"), ('planning', 'Planning et suivi de production'), ('piste', 'Choix de la piste'), ('direction', 'Direction artistique'), ('charte', 'Respect de votre charte'), ('main', 'Animation à la main'), ('montage', 'Rythme et montage'), ('corrections', 'Corrections et finitions'), ('cible', 'Adaptation à votre cible'), ('validation', 'Validation finale')]),
            ]),
        ]),
        F('IA · votre vidéo', ['suite-fleche'], 'display: flex; flex-direction: column; align-items: center; gap: 0; width: 100%; max-width: 620px; margin: 72px auto 0; padding: 0;', enfants=[
            F('Écran · boutons', ['rangee'], 'justify-content: center; margin-bottom: 18px;', enfants=[
                F('Écran · formats', ['groupe-choix'], enfants=[P('Écran · formats · nom', 'Format', ['nom-groupe'])] + [
                    B('Écran · format ' + f, f, '#rl-format-' + f.replace(':', '-'), ['choix-ecran']) for f in ('16:9', '9:16', '1:1')]),
                F('Écran · langues', ['groupe-choix'], enfants=[P('Écran · langues · nom', 'Langue', ['nom-groupe'])] + [
                    B('Écran · langue ' + l, l, '#rl-langue-' + l.lower(), ['choix-ecran']) for l in ('FR', 'EN', 'ES')]),
            ]),
            F('Écran · scène', ['scene-ecran'], 'width: 100%;', enfants=[
                F('Écran · cadre', ['ecran-cadre'], ancre='rl-ecran', enfants=[
                    F('Écran · vidéo', ['ecran-video'], enfants=[
                        D('Écran · fond', ['ecran-fond'], enfants=[I('Écran · image de fond', 'pourqui-interne.jpg', cls=['image-couvrante'], taille='large')]),
                        version_ecran('fr', 'Votre vidéo · 1:28', 'Bienvenue<br><em>chez nous.</em>', '«&nbsp;Votre premier jour commence ici.&nbsp;»', False),
                        version_ecran('en', 'Your video · 1:28', 'Welcome<br><em>aboard.</em>', '“Your first day starts here.”', True),
                        version_ecran('es', 'Tu vídeo · 1:28', 'Bienvenido<br><em>a bordo.</em>', '«&nbsp;Tu primer día empieza aquí.&nbsp;»', True),
                        I('Écran · lecture', 'bouton-lecture.png', cls=['bouton-lecture', 'lecture-ecran'], taille='thumbnail'),
                    ]),
                ]),
            ]),
            P('Écran · légende', 'Votre vidéo, <em>prête à publier.</em>', ['legende-ecran'], 'margin-top: 20px;'),
            P('Écran · sous-légende', 'Tous les formats, toutes les langues, à votre charte.', ['texte-petit'], 'margin-top: 4px; text-align: center;'),
        ]),
        F('IA · experts', None, COLONNE + ' margin-top: 104px;', enfants=[
            H('Experts · titre', 'Nos experts <em>IA.</em>', 'h3', ['titre-sm']),
            F('Experts · portraits', ['grille-equipe'], 'margin-top: 36px;', enfants=[
                membre('Experts', 'ronan', 'Ronan', 'Directeur Technique', True),
                membre('Experts', 'marion-l', 'Marion', 'Développeuse Full-Stack', True),
                membre('Experts', 'ines', 'Inès', 'DevOps', True),
                membre('Experts', 'hakim', 'Hakim', 'Développeur Back-End', True),
                membre('Experts', 'maxime', 'Maxime', 'Product Manager Senior', True),
            ]),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# 00:06 Le meilleur de l'humain
# --------------------------------------------------------------------------------------------

humain = F("Le meilleur de l'humain", ['section', 'section-chaude', 'halos-chauds'], tag='section', ancre='humain', enfants=[
    F('Humain · contenu', ['conteneur'], COLONNE, enfants=[
        entete('Humain', '00:06', 'Derrière chaque vidéo', [("Le meilleur <em>de l'humain.</em>", 'h2', ['titre-xl'])],
               intro="<strong>L'IA va vite. Nos créatifs font la différence.</strong> Une équipe créa derrière chaque vidéo pour un résultat qui accroche et respecte votre charte."),
        F('Humain · direction artistique', ['colonnes-media'], 'margin-top: 14px;', enfants=[
            D('DA · visuel', None, 'position: relative; padding: 50px 0 60px; @media(--tablet) { padding: 0; }', enfants=[
                P('DA · note accroche', 'accroche dès la 1<sup>re</sup> seconde', ['note-main'], 'position: absolute; left: 6px; top: 0; z-index: 2;'),
                I('DA · flèche', 'fleche-annotation.png', style='position: absolute; left: 300px; top: 14px; z-index: 2; width: 46px; @media(--tablet) { display: none; }', taille='medium'),
                I('DA · vidéo annotée', 'humain-direction.webp', "Une vidéo en motion 2D, « La semaine de 4 jours », annotée par le directeur artistique : titre et logo entourés.", ['illustration-cadre']),
                P('DA · note rythme', 'plus de rythme ici', ['note-main'], 'position: absolute; left: 30px; bottom: 6px; z-index: 2;'),
                P('DA · note charte', 'charte respectée', ['note-main'], 'position: absolute; right: 4px; bottom: 6px; z-index: 2;'),
            ]),
            F('DA · colonne texte', None, COLONNE, enfants=[
                P('DA · étiquette', 'Direction artistique', ['pilule-label']),
                H('DA · titre', 'Votre directeur artistique <b>dédié.</b>', 'h3', ['titre-sm'], 'margin-top: 18px;'),
                P('DA · texte', "Il connaît votre charte par cœur et la fait vivre&nbsp;: chaque vidéo accroche dès la première seconde, garde le rythme et reste fidèle à votre marque.", ['texte'], 'margin-top: 14px;'),
                F('DA · liste wrap', None, 'padding: 0; margin-top: 20px;', enfants=[liste_coches('DA · liste', ['Un directeur artistique dédié à votre compte', 'Une direction créative exigeante sur chaque livrable', 'Un interlocuteur qui connaît vos enjeux et votre charte', 'Le regard final, toujours humain'])]),
                F('DA · contact', ['carte-contact'], 'margin-top: 26px;', enfants=[
                    I('DA · contact · photo', 'equipe-thomas.jpg', 'Thomas, votre directeur artistique', ['portrait', 'portrait-petit'], taille='thumbnail'),
                    P('DA · contact · texte', '<strong>Thomas</strong><br>votre directeur artistique', ['texte-petit']),
                ]),
            ]),
        ]),
        F('Humain · équipe', None, COLONNE + ' margin-top: 110px;', enfants=[
            H('Équipe · titre', 'Le réseau de talents qui <em>sublime vos vidéos.</em>', 'h3', ['titre-sm']),
            F('Équipe · portraits', ['grille-equipe'], 'margin-top: 36px;', enfants=[
                membre('Équipe', 'thomas', 'Thomas', 'Directeur Artistique'),
                membre('Équipe', 'alex', 'Alex', 'Responsable Équipe Motion Design'),
                membre('Équipe', 'victor', 'Victor', 'Motion Designer'),
                membre('Équipe', 'vincent', 'Vincent', 'Cadreur'),
                membre('Équipe', 'marion-t', 'Marion', 'Responsable Studio'),
                membre('Équipe', 'sophie', 'Sophie', 'Chef de Projet'),
            ]),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# 00:07 L'offre (version courte de l'accueil)
# --------------------------------------------------------------------------------------------

def formule(nom, cls, prix, prix_cls, annuel, resume, resume_cls, lignes, badge=None):
    enfants = [P(nom + ' · badge', badge, ['badge-offre'])] if badge else []
    enfants += [
        H(nom + ' · nom', nom, 'h3', ['titre-xs']),
        P(nom + ' · prix', prix, ['prix'] + prix_cls),
        P(nom + ' · annuel', annuel, ['texte-petit'], 'margin-top: -10px;'),
        P(nom + ' · résumé', resume, ['resume-offre'] + resume_cls),
        F(nom + ' · coches wrap', None, 'padding: 0;', enfants=[liste_coches(nom + ' · coches', lignes)]),
    ]
    return F('Formule · ' + nom, ['carte-pleine', 'carte-prix'] + cls, enfants=enfants, tag='article')


offre = F("L'offre", ['section', 'halos-offre'], tag='section', ancre='offres', enfants=[
    F('Offre · contenu', ['conteneur'], COLONNE, enfants=[
        entete('Offre', '00:07', "L'offre", [('Un prix fixe. <em>Des vidéos sans fin.</em>', 'h2', ['titre-lg'])],
               intro='Deux formules, un abonnement annuel, une qualité garantie. <strong>Et comme le prix est fixe, plus vous faites de vidéos, moins chacune vous coûte.</strong>'),
        F('Offre · formules', ['grille-2'], 'align-items: stretch;', enfants=[
            formule('Création illimitée', [], '1&nbsp;250&nbsp;€<span> / mois</span>', [], 'Soit 15&nbsp;000&nbsp;€ / an', 'Tous vos contenus et vos projets, en vidéo', [],
                    ['Vos écrits, vos vidéos et vos nouveaux projets', 'Motion design 2D et 3D, montage, voix off', 'Sous-titres, langues et tous les formats', 'Un directeur artistique et un chef de projet dédiés']),
            formule('Création + Tournage', ['section-sombre', 'carte-prix-sombre'], '2&nbsp;080&nbsp;€<span> / mois</span>', ['prix-degrade'], 'Soit 25&nbsp;000&nbsp;€ / an', 'Tout Création, plus le tournage et le motion fait main', ['resume-offre-sombre'],
                    ['Captation offerte, partout en France', 'Interviews, témoignages et événements', 'Avatars et vidéos produit', 'Motion 100&nbsp;% fait main, sans IA'], badge='Le plus complet'),
        ]),
        F('Offre · promesses', ['carte-verre', 'grand-bloc', 'carte-promesses'], 'margin-top: 28px;', enfants=[
            H('Promesses · titre', "L'illimité qui tient ses promesses&nbsp;:", 'h3', ['titre-xs']),
            F('Promesses · liste', ['grille-4'], 'margin-top: 8px;', enfants=[
                F('Promesse %d' % n, ['ligne-coche'], enfants=[
                    I('Promesse %d · chiffre' % n, 'chiffre-%d.webp' % n, cls=['picto-verre'], style='height: 40px;', taille='medium'),
                    P('Promesse %d · texte' % n, t, ['texte-petit']),
                ]) for n, t in enumerate(['<strong>Un projet traité à fond</strong> à la fois', '<strong>1 projet = 1 vidéo livrée</strong>, prête à publier', '<strong>Des allers-retours cadrés</strong>, pour livrer vite', '<strong>1 abonnement = 1 équipe métier</strong>'], 1)
            ]),
        ]),
        F('Offre · boutons', ['rangee'], 'justify-content: center; margin-top: 36px;', enfants=[
            B('Offre · bouton principal', 'Réserver un échange', '/contact/', ['bouton-principal']),
            B('Offre · bouton secondaire', 'Voir les offres', '/offres/', ['bouton-verre']),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# 00:08 L'équipe (Née chez Pitchy)
# --------------------------------------------------------------------------------------------

def chiffre(nom, chiffres, lu, libelle):
    nombre = [I(nom + ' · chiffre %d' % (i + 1), 'chiffre-' + c + '.webp', taille='medium') for i, c in enumerate(chiffres)]
    nombre.append(P(nom + ' · valeur lue', lu, ['texte-masque']))
    return F('Chiffre · ' + nom, None, enfants=[
        F(nom + ' · nombre', ['chiffre-verre'], enfants=nombre),
        P(nom + ' · libellé', libelle, ['libelle-chiffre']),
    ])


entete_equipe = entete('Équipe', '00:08', "L'équipe", [('Née chez <em>Pitchy.</em>', 'h2', ['titre-lg'])])
entete_equipe.enfants += [
    P('Équipe · texte 1', "Rolling est lancée par les équipes de Pitchy, la plateforme française de création vidéo qui équipe les grandes entreprises depuis 13&nbsp;ans. Même exigence, nouvelle promesse&nbsp;: <strong>cette fois, c'est nous qui produisons.</strong>", ['texte']),
    P('Équipe · texte 2', 'Un studio motion intégré, une équipe tech qui industrialise la production, et treize ans passés à comprendre comment les grands comptes font — et regardent — de la vidéo.', ['texte']),
]

equipe = F('Née chez Pitchy', ['section'], tag='section', enfants=[
    F('Équipe · contenu', ['conteneur'], COLONNE, enfants=[
        F('Équipe · présentation', None, 'max-width: 820px; padding: 0;', enfants=[entete_equipe]),
        D('Équipe · chiffres', ['lueur-peche'], 'padding: 0;', enfants=[
            F('Chiffres · plaque', ['plaque-chiffres'], enfants=[
                chiffre('Ans', ['1', '3'], '13', 'ans de vidéo pour les entreprises'),
                chiffre('Entreprises', ['5', '0', '0', 'plus'], '500+', 'grandes entreprises équipées'),
                chiffre('Studio', ['1'], '1', 'studio motion &amp; tournage intégré'),
                chiffre('Plateforme', ['1'], '1', 'plateforme de production propriétaire'),
            ]),
        ]),
        F('Équipe · témoignage', ['carte-verre', 'citation', 'carte-citation'], 'margin-top: 64px;', enfants=[
            I('Témoignage · photo', 'temoin-julie-charlemagne.jpg', 'Julie Charlemagne', ['portrait', 'portrait-moyen'], taille='thumbnail'),
            F('Témoignage · texte', None, COLONNE, enfants=[
                P('Témoignage · citation', "«&nbsp;On n'avait pas la bande passante, et on n'avait pas l'expertise. Donc on devait absolument faire appel à quelqu'un qui avait le savoir-faire et qui était professionnel et l'équipe Rolling (ex Pitchy Studio) nous a tout de suite rassuré sur leur accompagnement sur mesure si bien de l'expression des besoins jusqu'à la livraison des vidéos.&nbsp;»", ['texte-citation']),
                P('Témoignage · auteur', '<strong>Julie Charlemagne</strong> — Responsable Fonctionnel SIRH, Groupement Mousquetaires', ['texte-petit'], 'margin-top: 16px;'),
            ]),
            B('Témoignage · bouton', 'Voir le cas client', '/temoignages/', ['bouton-principal', 'bouton-petit']),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# 00:09 FAQ
# --------------------------------------------------------------------------------------------

QUESTIONS = [
    ('«&nbsp;Illimité&nbsp;», vraiment&nbsp;?', "Oui&nbsp;: nous ne comptons pas les vidéos. Nous traitons un projet à fond à la fois, puis nous enchaînons sur le suivant. C'est ce qui rend l'illimité possible, avec une qualité garantie sur chaque vidéo."),
    ('La captation, vraiment offerte&nbsp;?', 'Oui, avec la formule Création&nbsp;+&nbsp;Tournage. Nos équipes se déplacent partout en France pour filmer vos collaborateurs, vos événements et vos produits, un projet à la fois.'),
    ("Le meilleur de l'humain et de l'IA, concrètement&nbsp;?", "Nos IA accélèrent tout ce qui peut l'être&nbsp;: pistes créatives, voix off, sous-titres, traductions, déclinaisons. Nos créas gardent la direction artistique, l'animation à la main et la validation finale. C'est ce qui permet l'illimité, sans rien lâcher sur la qualité."),
    ('Et nos nouveaux projets&nbsp;?', 'Bien sûr. Lancements, événements, campagnes&nbsp;: tout nouveau projet entre dans le même flux, avec la même équipe et la même charte.'),
    ('Et si nous avons déjà nos propres images&nbsp;?', "Envoyez-nous vos rushs&nbsp;: leur montage entre dans l'illimité, dès la formule Création."),
    ('Que devons-nous fournir&nbsp;?', "Vos contenus, vos idées de projets et votre charte graphique. Nous nous occupons de tout le reste, de la stratégie vidéo annuelle jusqu'à la livraison."),
]

faq = F('FAQ', ['section', 'halo-lilas-doux'], tag='section', enfants=[
    F('FAQ · contenu', ['conteneur', 'conteneur-etroit'], COLONNE, enfants=[
        entete('FAQ', '00:09', 'Les questions que vous nous posez', [('Avant de dire <em>«&nbsp;trop beau pour être vrai&nbsp;»</em>', 'h2', ['titre-md'])], style=None),
        F('FAQ · questions', ['liste-questions'], enfants=[
            F('Question %d' % n, ['carte-verre', 'carte-question'], enfants=[
                H('Question %d · titre' % n, q, 'h3', ['titre-question']),
                P('Question %d · réponse' % n, r, ['texte']),
            ]) for n, (q, r) in enumerate(QUESTIONS, 1)
        ]),
        F('FAQ · appel', ['carte-verre', 'carte-appel'], 'margin-top: 26px;', enfants=[
            P('FAQ · appel · texte', 'Une autre question&nbsp;? <em>Nous y répondons en 30&nbsp;minutes.</em>', ['texte-appel']),
            B('FAQ · appel · bouton', 'Réserver un échange', '/contact/', ['bouton-principal', 'bouton-petit']),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# Appel final (#contact)
# --------------------------------------------------------------------------------------------

final = F('Appel final', ['section', 'section-sombre', 'section-finale', 'halo-centre', 'decors-final'], tag='section', ancre='contact', enfants=[
    I('Final · verre cannelé', 'final-verre-cannele.webp', cls=['decor-flottant'], taille='medium_large'),
    F('Final · contenu', ['conteneur'], COLONNE + ' align-items: center; z-index: 1;', enfants=[
        H('Final · titre', 'Vos vidéos&nbsp;? Nous nous en occupons.<br><em>Sans limite.</em>', 'h2', ['titre-lg', 'titre-souligne']),
        P('Final · texte', '30 minutes pour passer en revue vos contenus et vos projets. Vous repartez avec votre stratégie vidéo et vos 3&nbsp;premières vidéos à produire.', ['texte-intro'], 'margin-top: 34px; max-width: 600px;'),
        F('Final · bouton', None, 'display: flex; justify-content: center; margin-top: 38px; padding: 0;', enfants=[
            B('Final · réserver', 'Réserver un échange', '/contact/', ['bouton-soleil']),
        ]),
        badges('Final', 'justify-content: center; margin-top: 34px;'),
    ]),
])

BLOCS = [
    ('01-hero', 'Rolling · 01 Hero', hero),
    ('02-logos', 'Rolling · 02 Ils nous font confiance', logos),
    ('03-showreel', 'Rolling · 03 Showreel', showreel),
    ('04-pourquoi', 'Rolling · 04 Pourquoi la vidéo', pourquoi),
    ('05-pour-qui', 'Rolling · 05 Pour qui', pour_qui),
    ('06-methode', 'Rolling · 06 La méthode', methode),
    ('07-exemples', 'Rolling · 07 Avant Après', exemples),
    ('08-ia', "Rolling · 08 Le meilleur de l'IA", ia),
    ('09-humain', "Rolling · 09 Le meilleur de l'humain", humain),
    ('10-offre', "Rolling · 10 L'offre", offre),
    ('11-equipe', 'Rolling · 11 Née chez Pitchy', equipe),
    ('12-faq', 'Rolling · 12 FAQ', faq),
    ('13-final', 'Rolling · 13 Appel final', final),
]

# --------------------------------------------------------------------------------------------
# Modèles du site (livraison 3) : cartes des listes et fiches. Les textes viennent des contenus de
# l'administration par les balises « Rolling » ; les parties facultatives portent une classe repère si-…
# --------------------------------------------------------------------------------------------

FICHE = DYN('rolling-lien', lien='fiche')


def avant_apres(prefixe):
    return F(prefixe + ' · avant après', ['avant-apres', 'si-avant'], enfants=[
        P(prefixe + ' · avant (orange)', DYN('rolling-champ', champ='avant'), ['tag-orange', 'sans-tonalite--bleu']),
        P(prefixe + ' · avant (bleu)', DYN('rolling-champ', champ='avant'), ['tag-bleu', 'si-tonalite--bleu']),
        P(prefixe + ' · après', DYN('rolling-champ', champ='apres'), ['si-apres']),
    ])


carte_realisation = F('Carte réalisation', ['carte-liste'], tag='article', enfants=[
    F('Réalisation · vignette', ['vignette-video', 'vignette-lien', 'si-image'], lien=FICHE, enfants=[
        D('Réalisation · cadre', ['cadre-photo', 'cadre-16-9'], enfants=[
            I('Réalisation · image', DYN('rolling-image', image='une'), cls=['image-couvrante'], taille='medium_large'),
            P('Réalisation · format', DYN('rolling-terme', taxonomie='rolling_format'), ['bandeau-video']),
            P('Réalisation · durée', DYN('rolling-champ', champ='duree'), ['duree', 'si-duree']),
            lecture('Réalisation · lecture'),
        ]),
    ]),
    F('Réalisation · textes', ['pile-serree'], enfants=[
        P('Réalisation · client', DYN('rolling-champ', champ='client'), ['label', 'si-client']),
        H('Réalisation · titre', DYN('rolling-titre', forme='titre'), 'h3', ['titre-xs', 'lien-titre'], lien=FICHE),
    ]),
    avant_apres('Réalisation'),
])


def carte_temoignage(une=False):
    p = 'Témoin à la une' if une else 'Témoin'
    return F('Carte témoignage' + (' à la une' if une else ''), ['carte-verre', 'temoin'] + (['temoin-une'] if une else []), tag='article', enfants=[
        F(p + ' · haut', ['temoin-haut'], enfants=[
            I(p + ' · logo', DYN('rolling-image', image='logo'), cls=['temoin-logo', 'si-logo'], taille='medium'),
            P(p + ' · métier', DYN('rolling-terme', taxonomie='rolling_metier'), ['tag-orange']),
        ]),
        P(p + ' · citation', DYN('rolling-champ', champ='citation'), ['temoin-citation', 'si-citation']),
        P(p + ' · chapô', DYN('rolling-champ', champ='chapo'), ['texte', 'sans-citation']),
        F(p + ' · auteur', ['temoin-auteur', 'si-auteur'], enfants=[
            I(p + ' · portrait', DYN('rolling-image', image='portrait'), cls=['portrait', 'portrait-petit', 'si-portrait'], taille='thumbnail'),
            F(p + ' · personne', ['pile-serree'], enfants=[
                P(p + ' · nom', DYN('rolling-champ', champ='auteur'), ['cas-info-valeur']),
                P(p + ' · poste', DYN('rolling-champ', champ='poste'), ['texte-petit', 'si-poste']),
            ]),
        ]),
        B(p + ' · lien', 'Lire le cas', FICHE, ['temoin-lien']),
    ])


carte_article = F('Carte article', ['carte-liste'], tag='article', enfants=[
    F('Article · vignette', ['vignette-lien', 'si-image'], 'padding: 0; border-radius: 22px;', enfants=[
        D('Article · cadre', ['cadre-photo', 'cadre-16-10'], enfants=[
            I('Article · image', DYN('rolling-image', image='une'), cls=['image-couvrante'], taille='medium_large'),
        ]),
    ]),
    F('Article · infos', ['rangee'], enfants=[
        P('Article · catégorie', DYN('rolling-terme', taxonomie='category'), ['tag-orange']),
        P('Article · lecture', DYN('rolling-article', info='lecture_c'), ['texte-petit']),
        P('Article · date', DYN('rolling-article', info='date'), ['texte-petit']),
    ]),
    H('Article · titre', DYN('rolling-article', info='titre'), 'h3', ['titre-xs', 'lien-titre'], lien=FICHE),
])

HUBSPOT = DYN('rolling-lien', lien='lien')
carte_guide = F('Carte guide', ['carte-guide'], tag='article', enfants=[
    F('Guide · vignette', ['vignette-lien', 'si-image'], 'padding: 0; border-radius: 22px;', enfants=[
        D('Guide · cadre', ['cadre-photo', 'cadre-4-3'], enfants=[
            I('Guide · image', DYN('rolling-image', image='une'), cls=['image-couvrante'], taille='medium_large'),
            P('Guide · type', DYN('rolling-champ', champ='type_doc'), ['bandeau-video', 'si-type-doc']),
        ]),
    ]),
    F('Guide · infos', ['rangee'], enfants=[
        P('Guide · thème', DYN('rolling-terme', taxonomie='rolling_theme'), ['tag-orange']),
        P('Guide · pages', DYN('rolling-champ', champ='pages', apres=' pages'), ['texte-petit', 'si-pages']),
    ]),
    H('Guide · titre', DYN('rolling-article', info='titre'), 'h3', ['titre-xs', 'lien-titre'], lien=HUBSPOT),
    P('Guide · résumé', DYN('rolling-champ', champ='resume'), ['texte-petit', 'si-resume']),
    B('Guide · bouton', 'Télécharger le guide', HUBSPOT, ['bouton-verre', 'bouton-petit']),
])

INSCRIPTION = DYN('rolling-lien', lien='inscription')
carte_webinar = F('Carte webinar', ['carte-verre', 'carte-webinar'], tag='article', enfants=[
    F('Webinar · date', ['webinar-date'], enfants=[
        P('Webinar · jour', DYN('rolling-webinar', info='jour'), ['webinar-jour']),
        P('Webinar · mois', DYN('rolling-webinar', info='mois'), ['webinar-mois']),
        P('Webinar · heure', DYN('rolling-webinar', info='heure'), ['webinar-heure']),
    ]),
    F('Webinar · textes', ['pile-serree'], enfants=[
        F('Webinar · infos', ['rangee'], enfants=[
            P('Webinar · thème', DYN('rolling-terme', taxonomie='rolling_theme'), ['tag-bleu']),
            P('Webinar · durée', DYN('rolling-webinar', info='duree'), ['texte-petit']),
        ]),
        H('Webinar · titre', DYN('rolling-article', info='titre'), 'h3', ['titre-xs']),
        P('Webinar · intervenants', DYN('rolling-webinar', info='intervenants'), ['texte-petit', 'si-intervenants']),
    ]),
    B('Webinar · bouton', "S'inscrire", INSCRIPTION, ['bouton-principal', 'bouton-petit']),
])

carte_webinar_une = F('Prochain webinar', ['carte-verre', 'carte-webinar-une'], tag='article', enfants=[
    D('Prochain · cadre', ['cadre-photo', 'cadre-16-10', 'si-image'], enfants=[
        I('Prochain · image', DYN('rolling-image', image='une'), cls=['image-couvrante'], taille='large'),
        P('Prochain · bandeau', 'Prochain webinar', ['bandeau-video', 'bandeau-rec']),
    ]),
    F('Prochain · textes', ['pile'], enfants=[
        F('Prochain · infos', ['rangee'], enfants=[
            P('Prochain · thème', DYN('rolling-terme', taxonomie='rolling_theme'), ['tag-orange']),
            P('Prochain · date', DYN('rolling-webinar', info='complete'), ['texte-petit']),
        ]),
        W('Prochain · compte à rebours', 'rolling-compte-a-rebours'),
        H('Prochain · titre', DYN('rolling-article', info='titre'), 'h3', ['titre-md']),
        P('Prochain · résumé', DYN('rolling-champ', champ='resume'), ['texte', 'si-resume']),
        P('Prochain · intervenants', DYN('rolling-webinar', info='intervenants'), ['texte-petit', 'si-intervenants']),
        B('Prochain · bouton', "S'inscrire au webinar", INSCRIPTION, ['bouton-principal', 'bouton-petit']),
    ]),
])

REPLAY = DYN('rolling-lien', lien='replay')
carte_replay = F('Carte replay', ['carte-liste'], tag='article', enfants=[
    F('Replay · vignette', ['vignette-video', 'vignette-lien', 'si-image'], lien=REPLAY, enfants=[
        D('Replay · cadre', ['cadre-photo', 'cadre-16-9'], enfants=[
            I('Replay · image', DYN('rolling-image', image='une'), cls=['image-couvrante'], taille='medium_large'),
            P('Replay · bandeau', 'Replay', ['bandeau-video']),
            lecture('Replay · lecture'),
        ]),
    ]),
    F('Replay · infos', ['rangee'], enfants=[
        P('Replay · thème', DYN('rolling-terme', taxonomie='rolling_theme'), ['tag-orange']),
        P('Replay · date', DYN('rolling-webinar', info='diffuse'), ['texte-petit']),
    ]),
    H('Replay · titre', DYN('rolling-article', info='titre'), 'h3', ['titre-xs', 'lien-titre'], lien=REPLAY),
    B('Replay · bouton', 'Voir le replay', REPLAY, ['bouton-verre', 'bouton-petit']),
])

# Fiche réalisation : haut, vidéo, récit, générique (facultatif), autres réalisations, appel final.
fiche_realisation = F('Fiche réalisation', None, COLONNE + ' align-items: stretch; padding: 0; width: 100%;', tag='article', enfants=[
    F('Fiche réalisation · haut', ['section', 'section-entete'], tag='section', enfants=[
        F('Fiche réalisation · haut · contenu', ['conteneur'], COLONNE, enfants=[
            W('Fiche réalisation · fil d’Ariane', 'rolling-ariane'),
            F('Fiche réalisation · étiquettes', ['rangee'], 'margin-top: 28px;', enfants=[
                P('Fiche réalisation · métier', DYN('rolling-terme', taxonomie='rolling_metier'), ['etiquette-metier-orange']),
                P('Fiche réalisation · format', DYN('rolling-terme', taxonomie='rolling_format'), ['chip-neutre']),
            ]),
            H('Fiche réalisation · titre', DYN('rolling-titre', forme='client'), 'h1', ['titre-lg'], 'margin-top: 22px; max-width: 960px;'),
            F('Fiche réalisation · transformation', None, 'margin-top: 20px; padding: 0;', enfants=[avant_apres('Fiche réalisation')]),
        ]),
    ]),
    F('Fiche réalisation · vidéo', ['section', 'section-suite'], tag='section', enfants=[
        F('Fiche réalisation · vidéo · contenu', ['conteneur'], COLONNE, enfants=[
            D('Fiche réalisation · cadre vidéo', ['vignette-video'], 'width: 100%; max-width: 1040px; margin: 0 auto;', enfants=[
                W('Fiche réalisation · lecteur', 'rolling-video'),
            ]),
        ]),
    ]),
    F('Fiche réalisation · récit', ['section', 'section-suite'], tag='section', enfants=[
        F('Fiche réalisation · récit · contenu', ['conteneur', 'conteneur-etroit'], COLONNE + ' gap: 40px;', enfants=[
            F('Fiche réalisation · besoin', ['pile', 'si-besoin'], enfants=[
                H('Fiche réalisation · besoin · titre', 'Le besoin.', 'h2', ['titre-sm']),
                P('Fiche réalisation · besoin · texte', DYN('rolling-champ', champ='besoin'), ['texte']),
            ]),
            F('Fiche réalisation · produit', ['pile', 'si-produit'], enfants=[
                H('Fiche réalisation · produit · titre', 'Ce que nous avons produit.', 'h2', ['titre-sm']),
                W('Fiche réalisation · produit · liste', 'rolling-coches', champ='produit'),
            ]),
            F('Fiche réalisation · résultat', ['pile', 'si-resultat'], enfants=[
                H('Fiche réalisation · résultat · titre', 'Le résultat.', 'h2', ['titre-sm']),
                P('Fiche réalisation · résultat · texte', DYN('rolling-champ', champ='resultat'), ['texte']),
            ]),
            W('Fiche réalisation · transcription', 'rolling-transcription'),
        ]),
    ]),
    F('Fiche réalisation · générique', ['section', 'section-suite', 'si-generique'], tag='section', enfants=[
        F('Fiche réalisation · générique · contenu', ['conteneur', 'conteneur-etroit'], COLONNE + ' align-items: center; text-align: center;', enfants=[
            F('Fiche réalisation · générique · filet', ['generique-filet'], 'width: 100%;', enfants=[
                P('Fiche réalisation · générique · timecode', DYN('rolling-champ', champ='duree'), ['timecode', 'si-duree']),
                P('Fiche réalisation · générique · fin', 'Fin', ['label']),
            ]),
            H('Fiche réalisation · générique · titre', 'Le générique.', 'h2', ['titre-sm'], 'margin-top: 20px;'),
            W('Fiche réalisation · générique · lignes', 'rolling-generique'),
            F('Fiche réalisation · générique · signature', ['generique-fin'], 'margin-top: 56px;', enfants=[
                P('Fiche réalisation · générique · production', 'Une production', ['label']),
                I('Fiche réalisation · générique · logo', 'logo-rolling.png', 'Rolling', taille='medium'),
            ]),
            B('Fiche réalisation · générique · bouton', 'Réserver un échange', '/contact/', ['bouton-principal', 'bouton-petit'], 'margin-top: 30px;'),
        ]),
    ]),
    F('Fiche réalisation · autres', ['section', 'section-lilas'], tag='section', enfants=[
        F('Fiche réalisation · autres · contenu', ['conteneur'], COLONNE, enfants=[
            H('Fiche réalisation · autres · titre', "D'autres <em>réalisations.</em>", 'h2', ['titre-sm'], 'margin-bottom: 36px;'),
            W('Fiche réalisation · autres · liste', 'rolling-liste', contenu='autres', nombre='3', classe='grille-3'),
        ]),
    ]),
    final,
])

INFOS_PROJET = [('secteur', 'Secteur'), ('equipes', 'Équipes'), ('formats', 'Formats'), ('duree', 'Durée')]
fiche_temoignage = F('Fiche témoignage', None, COLONNE + ' align-items: stretch; padding: 0; width: 100%;', tag='article', enfants=[
    F('Fiche témoignage · haut', ['section', 'section-entete'], tag='section', enfants=[
        F('Fiche témoignage · haut · contenu', ['conteneur'], COLONNE, enfants=[
            W('Fiche témoignage · fil d’Ariane', 'rolling-ariane'),
            F('Fiche témoignage · étiquettes', ['rangee'], 'margin-top: 28px;', enfants=[
                D('Fiche témoignage · logo', ['cas-logo', 'si-logo'], enfants=[
                    I('Fiche témoignage · logo · image', DYN('rolling-image', image='logo'), taille='medium'),
                ]),
                P('Fiche témoignage · métier', DYN('rolling-terme', taxonomie='rolling_metier'), ['etiquette-metier-orange']),
            ]),
            H('Fiche témoignage · titre', DYN('rolling-titre', forme='client'), 'h1', ['titre-lg'], 'margin-top: 22px; max-width: 980px;'),
            P('Fiche témoignage · chapô', DYN('rolling-champ', champ='chapo'), ['texte-intro', 'si-chapo'], 'margin-top: 20px; max-width: 760px;'),
            F('Fiche témoignage · bandeau', ['cas-bandeau'], 'margin-top: 30px;', enfants=[
                F('Fiche témoignage · infos', ['cas-bandeau-infos'], enfants=[
                    F('Fiche témoignage · ' + nom, ['si-' + cle], enfants=[
                        P('Fiche témoignage · ' + nom + ' · libellé', nom, ['label']),
                        P('Fiche témoignage · ' + nom + ' · valeur', DYN('rolling-champ', champ=cle), ['cas-info-valeur']),
                    ]) for cle, nom in INFOS_PROJET
                ]),
                F('Fiche témoignage · appel', ['cas-bandeau-appel'], enfants=[
                    P('Fiche témoignage · appel · texte', '<strong>Un projet similaire&nbsp;?</strong>', ['texte-petit']),
                    B('Fiche témoignage · appel · bouton', 'Réserver un échange', '/contact/', ['bouton-principal', 'bouton-petit']),
                ]),
            ]),
        ]),
    ]),
    F('Fiche témoignage · chiffres', ['section', 'section-suite', 'si-chiffres'], tag='section', enfants=[
        F('Fiche témoignage · chiffres · contenu', ['conteneur'], COLONNE, enfants=[
            H('Fiche témoignage · chiffres · titre', 'En chiffres', 'h2', ['label', 'label-violet'], 'margin-bottom: 18px;'),
            W('Fiche témoignage · chiffres · plaque', 'rolling-chiffres'),
        ]),
    ]),
    F('Fiche témoignage · cas', ['section', 'section-suite'], tag='section', enfants=[
        F('Fiche témoignage · cas · grille', ['conteneur', 'cas-grille'], enfants=[
            F('Fiche témoignage · citation', ['carte-verre', 'temoin', 'cas-citation', 'si-citation'], enfants=[
                P('Fiche témoignage · citation · texte', DYN('rolling-champ', champ='citation'), ['temoin-citation']),
                F('Fiche témoignage · citation · auteur', ['temoin-auteur', 'si-auteur'], enfants=[
                    I('Fiche témoignage · citation · portrait', DYN('rolling-image', image='portrait'), cls=['portrait', 'portrait-petit', 'si-portrait'], taille='thumbnail'),
                    F('Fiche témoignage · citation · personne', ['pile-serree'], enfants=[
                        P('Fiche témoignage · citation · nom', DYN('rolling-champ', champ='auteur'), ['cas-info-valeur']),
                        P('Fiche témoignage · citation · poste', DYN('rolling-champ', champ='poste'), ['texte-petit', 'si-poste']),
                    ]),
                ]),
            ]),
            F('Fiche témoignage · récit', ['cas-recit'], enfants=[
                F('Fiche témoignage · contexte', ['pile', 'si-contexte'], enfants=[
                    H('Fiche témoignage · contexte · titre', 'Le contexte.', 'h2', ['titre-sm']),
                    P('Fiche témoignage · contexte · texte', DYN('rolling-champ', champ='contexte'), ['texte']),
                ]),
                F('Fiche témoignage · besoin', ['pile', 'si-besoin'], enfants=[
                    H('Fiche témoignage · besoin · titre', 'Leur besoin.', 'h2', ['titre-sm']),
                    P('Fiche témoignage · besoin · texte', DYN('rolling-champ', champ='besoin'), ['texte']),
                ]),
                F('Fiche témoignage · accompagnement', ['pile', 'si-accompagnement'], enfants=[
                    H('Fiche témoignage · accompagnement · titre', 'Notre accompagnement.', 'h2', ['titre-sm']),
                    W('Fiche témoignage · accompagnement · liste', 'rolling-coches', champ='accompagnement'),
                ]),
                F('Fiche témoignage · vidéos', ['pile', 'si-videos'], enfants=[
                    H('Fiche témoignage · vidéos · titre', 'Les vidéos.', 'h2', ['titre-sm']),
                    W('Fiche témoignage · vidéos · liste', 'rolling-liste', contenu='liees', nombre='6', classe='grille-2'),
                ]),
            ]),
        ]),
    ]),
    final,
])

fiche_article = F('Fiche article', None, COLONNE + ' align-items: stretch; padding: 0; width: 100%;', tag='article', enfants=[
    F('Article · haut', ['section', 'section-entete'], tag='section', enfants=[
        F('Article · grille', ['conteneur', 'article-grille'], enfants=[
            F('Article · principal', ['article-principal'], enfants=[
                W('Article · fil d’Ariane', 'rolling-ariane'),
                H('Article · titre', DYN('rolling-article', info='titre'), 'h1', ['titre-lg'], 'margin-top: 24px;'),
                F('Article · infos', ['rangee'], 'margin-top: 20px;', enfants=[
                    P('Article · catégorie', DYN('rolling-terme', taxonomie='category'), ['tag-orange']),
                    P('Article · publié', DYN('rolling-article', info='publie'), ['texte-petit']),
                    P('Article · mis à jour', DYN('rolling-article', info='modifie'), ['texte-petit', 'si-mise-a-jour']),
                    P('Article · lecture', DYN('rolling-article', info='lecture_c'), ['texte-petit']),
                ]),
                D('Article · image', ['cadre-photo', 'cadre-16-9', 'si-image'], 'margin-top: 28px;', enfants=[
                    I('Article · image · fichier', DYN('rolling-image', image='une'), cls=['image-couvrante'], taille='large'),
                ]),
                W('Article · texte', 'rolling-contenu'),
            ]),
            F('Article · outils', ['article-cote'], enfants=[
                F('Article · outils · colle', ['article-cote-colle'], enfants=[
                    F('Article · lecture et sommaire', ['carte-verre', 'carte-outils'], enfants=[
                        P('Article · lecture · libellé', 'Temps de lecture', ['label']),
                        P('Article · lecture · durée', DYN('rolling-article', info='lecture'), ['titre-xs']),
                        P('Article · sommaire · libellé', 'Sommaire', ['label'], 'margin-top: 14px;'),
                        W('Article · sommaire', 'rolling-sommaire'),
                    ]),
                    F('Article · partage et IA', ['carte-verre', 'carte-outils'], enfants=[
                        P('Article · partage · libellé', 'Partager l’article', ['label']),
                        F('Article · partage', ['partage'], enfants=[
                            F('Article · partage · %s' % nom, ['partage-bouton'], lien=lien, onglet=cle not in ('email', 'copier'), enfants=[
                                I('Article · partage · %s · icône' % nom, 'partage-%s.png' % cle),
                                P('Article · partage · %s · texte' % nom, texte, ['texte-masque']),
                            ]) for cle, nom, texte, lien in [
                                ('linkedin', 'LinkedIn', 'Partager sur LinkedIn', DYN('rolling-partage', vers='linkedin')),
                                ('facebook', 'Facebook', 'Partager sur Facebook', DYN('rolling-partage', vers='facebook')),
                                ('x', 'X', 'Partager sur X', DYN('rolling-partage', vers='x')),
                                ('whatsapp', 'WhatsApp', 'Partager sur WhatsApp', DYN('rolling-partage', vers='whatsapp')),
                                ('email', 'E-mail', 'Envoyer par e-mail', DYN('rolling-partage', vers='email')),
                                ('copier', 'Copier', 'Copier le lien', '#rl-copier'),
                            ]
                        ]),
                        P('Article · résumer · libellé', 'Résumer avec', ['label'], 'margin-top: 14px;'),
                        F('Article · résumer', ['rangee', 'resumer'], enfants=[
                            B('Article · résumer · %s' % nom, nom, DYN('rolling-partage', vers=cle), ['bouton-ia'], onglet=True)
                            for cle, nom in [('chatgpt', 'ChatGPT'), ('claude', 'Claude'), ('perplexity', 'Perplexity'), ('lechat', 'Le Chat')]
                        ]),
                    ]),
                    F('Article · appel', ['carte-verre', 'section-sombre', 'carte-sombre'], enfants=[
                        P('Article · appel · titre', 'Et si ce contenu devenait <em>une vidéo&nbsp;?</em>', ['titre-xs']),
                        P('Article · appel · texte', '30 minutes pour passer en revue vos contenus. Vous repartez avec vos 3 premières vidéos à produire.', ['texte-petit']),
                        B('Article · appel · bouton', 'Réserver un échange', '/contact/', ['bouton-principal', 'bouton-petit']),
                    ]),
                ]),
            ]),
        ]),
    ]),
    F('Article · à lire aussi', ['section', 'section-lilas'], tag='section', enfants=[
        F('Article · à lire aussi · contenu', ['conteneur'], COLONNE, enfants=[
            H('Article · à lire aussi · titre', 'À lire <em>aussi.</em>', 'h2', ['titre-sm'], 'margin-bottom: 36px;'),
            W('Article · à lire aussi · liste', 'rolling-liste', contenu='lire_aussi', nombre='3', classe='grille-3'),
        ]),
    ]),
    final,
])

page_404 = F('Page 404', ['section', 'section-sombre', 'fond-grille', 'halo-centre'], 'min-height: 72vh;', tag='section', enfants=[
    F('404 · contenu', ['conteneur'], COLONNE + ' align-items: center; text-align: center;', enfants=[
        D('404 · mire', ['mire'], enfants=[
            P('404 · signal', 'Signal perdu · 04:04', ['mire-signal']),
        ]),
        H('404 · titre', 'Cette page a été <em>coupée au montage.</em>', 'h1', ['titre-lg'], 'margin-top: 44px;'),
        P('404 · texte', 'Elle n’existe plus, ou elle a changé d’adresse. Reprenons depuis le début.', ['texte-intro'], 'margin-top: 22px; max-width: 560px;'),
        F('404 · boutons', ['rangee'], 'justify-content: center; margin-top: 34px;', enfants=[
            B('404 · accueil', 'Revenir à l’accueil', '/', ['bouton-principal']),
            B('404 · offres', 'Voir les offres', '/offres/', ['bouton-verre']),
        ]),
        P('404 · liens', '<a href="/realisations/">Nos réalisations</a> <a href="/temoignages/">Les témoignages</a> <a href="/blog/">Le blog</a> <a href="/contact/">Contact</a>', ['liens-utiles'], 'margin-top: 40px;'),
    ]),
])

MODELES_SITE = [
    ('c1-carte-realisation', 'Rolling · Carte réalisation', carte_realisation, 'rolling_realisation'),
    ('c2-carte-temoignage', 'Rolling · Carte témoignage', carte_temoignage(), 'rolling_temoignage'),
    ('c3-carte-temoignage-une', 'Rolling · Carte témoignage à la une', carte_temoignage(True), 'rolling_temoignage'),
    ('c4-carte-article', 'Rolling · Carte article', carte_article, 'post'),
    ('c5-carte-guide', 'Rolling · Carte guide', carte_guide, 'rolling_guide'),
    ('c6-carte-webinar', 'Rolling · Carte webinar', carte_webinar, 'rolling_webinar'),
    ('c7-carte-webinar-une', 'Rolling · Carte prochain webinar', carte_webinar_une, 'rolling_webinar'),
    ('c8-carte-replay', 'Rolling · Carte replay', carte_replay, 'rolling_webinar'),
    ('f1-fiche-realisation', 'Rolling · Fiche réalisation', fiche_realisation, 'rolling_realisation'),
    ('f2-fiche-temoignage', 'Rolling · Fiche témoignage', fiche_temoignage, 'rolling_temoignage'),
    ('f3-fiche-article', 'Rolling · Article de blog', fiche_article, 'post'),
    ('f4-page-404', 'Rolling · Page 404', page_404, ''),
]

if __name__ == '__main__':
    dossier = os.path.join(ICI, 'source', 'blocs')
    os.makedirs(dossier, exist_ok=True)
    cles = []
    for cle, titre, racine in BLOCS:
        composition, brut = composer(racine)
        donnees = {'cle': cle, 'titre': titre, 'type': 'e-flexbox', 'composition': composition, 'brut': brut}
        cles.append(cle)
        with open(os.path.join(dossier, cle + '.json'), 'w', encoding='utf8') as f:
            json.dump(donnees, f, ensure_ascii=False, indent=1)
        print(cle, len(donnees['composition']['xml_structure']), 'caractères de XML')
    for cle, titre, racine, apercu in MODELES_SITE:
        composition, brut = composer(racine)
        donnees = {'cle': cle, 'titre': titre, 'type': 'e-flexbox', 'apercu': apercu, 'composition': composition, 'brut': brut}
        with open(os.path.join(dossier, cle + '.json'), 'w', encoding='utf8') as f:
            json.dump(donnees, f, ensure_ascii=False, indent=1)
        print(cle, len(donnees['composition']['xml_structure']), 'caractères de XML')
    # « Page complète » : tous les blocs de l'accueil à la suite, dans un modèle de type page.
    with open(os.path.join(dossier, '99-page-complete.json'), 'w', encoding='utf8') as f:
        json.dump({'cle': '99-page-complete', 'titre': 'Rolling · Page complète (accueil)', 'type': 'page', 'assemble': cles}, f, ensure_ascii=False, indent=1)
    print('99-page-complete :', len(cles), 'blocs')
