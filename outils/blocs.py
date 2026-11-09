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


def F(nom, cls=None, style=None, enfants=None, tag=None, lien=None, ancre=None):
    cfg = {}
    if ancre:
        cfg['_cssid'] = ancre
    if tag:
        cfg['tag'] = tag
    if lien:
        cfg['link'] = {'destination': lien}
    return E('e-flexbox', nom, cfg, cls, style, enfants)


def D(nom, cls=None, style=None, enfants=None, tag=None):
    return E('e-div-block', nom, {'tag': tag} if tag else {}, cls, style, enfants)


def H(nom, texte, tag='h2', cls=None, style=None):
    return E('e-heading', nom, {'tag': tag, 'title': texte}, cls, style)


def P(nom, texte, cls=None, style=None):
    return E('e-paragraph', nom, {'paragraph': texte}, cls, style)


def B(nom, texte, lien, cls=None, style=None):
    return E('e-button', nom, {'text': texte, 'link': {'destination': lien}}, cls, style)


def I(nom, media, alt='', cls=None, style=None, taille='full'):
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
                P('Preuve · texte', 'Par les équipes de <strong>Pitchy</strong> — 10 ans de vidéo, 500+ grandes entreprises.', ['texte-petit', 'texte-legende']),
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
    F('Logos · bande', ['masque-bords'], 'margin-top: 22px; width: 100%;', enfants=[
        F('Logos · liste', ['logos-defilants', 'grille-6'], enfants=[
            F('Logo · ' + alt, ['carte-verre', 'tuile-logo'], enfants=[
                I('Logo · image · ' + alt, fichier, cls=None, style='height: %dpx;' % h, taille='medium'),
            ]) for fichier, alt, h in LOGOS
        ]),
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
            constat('Emails', 'Déjà écrit', False, 'constat-email.png', 'Vos emails internes&nbsp;?', 'Ignorés.', '#FFD1BF', None),
            constat('Blog', 'Déjà écrit', False, 'constat-blog.png', 'Vos articles de blog&nbsp;?', 'Survolés.', '#D9CFFF', 'halo-lilas'),
            constat('Modules', 'Déjà écrit', False, 'constat-module.png', 'Vos modules PowerPoint&nbsp;?', 'Subis.', '#C8CBFF', 'halo-bleu'),
            constat('Événements', 'À tourner', True, 'constat-evenement.png', 'Vos événements&nbsp;?', 'Oubliés.', '#FFB48C', 'halo-orange'),
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
                I('Marketing · illustration', 'pourqui-recherche.png', cls=['illustration-photo']),
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
                I('Interne · illustration', 'pourqui-intranet.png', cls=['illustration-photo']),
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
        I('Étape %d · chiffre' % n, 'chiffre-%d.png' % n, cls=['picto-verre'], style='height: 80px; align-self: flex-start;', taille='medium'),
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

BLOCS = [
    ('01-hero', 'Rolling · 01 Hero', hero),
    ('02-logos', 'Rolling · 02 Ils nous font confiance', logos),
    ('03-showreel', 'Rolling · 03 Showreel', showreel),
    ('04-pourquoi', 'Rolling · 04 Pourquoi la vidéo', pourquoi),
    ('05-pour-qui', 'Rolling · 05 Pour qui', pour_qui),
    ('06-methode', 'Rolling · 06 La méthode', methode),
]

if __name__ == '__main__':
    dossier = os.path.join(ICI, 'source', 'blocs')
    os.makedirs(dossier, exist_ok=True)
    for cle, titre, racine in BLOCS:
        composition, brut = composer(racine)
        donnees = {'cle': cle, 'titre': titre, 'type': 'e-flexbox', 'composition': composition, 'brut': brut}
        with open(os.path.join(dossier, cle + '.json'), 'w', encoding='utf8') as f:
            json.dump(donnees, f, ensure_ascii=False, indent=1)
        print(cle, len(donnees['composition']['xml_structure']), 'caractères de XML')
