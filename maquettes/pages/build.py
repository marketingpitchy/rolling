import sys, re, io, json, base64, html
from PIL import Image

S = sys.argv[1]
D = S + '/maq-pages/'
A = S + '/maquette/assets/'
I = S + '/imgs/'
C = S + '/charte/x/'

def data_uri(path, w=None, fmt='WEBP', q=72):
    if path.endswith('.svg'):
        return 'data:image/svg+xml;base64,' + base64.b64encode(open(path, 'rb').read()).decode()
    im = Image.open(path)
    im = im.convert('RGBA') if im.mode in ('RGBA', 'LA', 'P') else im.convert('RGB')
    if w and im.size[0] > w:
        im = im.resize((w, round(im.size[1] * w / im.size[0])), Image.LANCZOS)
    b = io.BytesIO(); im.save(b, fmt, quality=q, method=6)
    return 'data:image/webp;base64,' + base64.b64encode(b.getvalue()).decode()

IMGS = {
    'logo': C + 'logos-rolling/rolling-logo-couleur.svg', 'logo-negatif': C + 'logos-rolling/rolling-logo-couleur-negatif.svg',
    'coche': S + '/chiffres2/check-glass.svg',
    'n1': S + '/chiffres2/chiffre-1.png', 'n2': S + '/chiffres2/chiffre-2.png', 'n0': S + '/chiffres2/chiffre-0.png',
    'n3': S + '/chiffres2/chiffre-3.png', 'n4': S + '/chiffres2/chiffre-4.png',
    'n500': S + '/chiffres2/chiffre-500-plus.png', 'n1c': S + '/chiffres2/chiffre-1.png',
    'n5': S + '/chiffres2/chiffre-5.png', 'n6': S + '/chiffres2/chiffre-6.png', 'n7': S + '/chiffres2/chiffre-7.png',
    'n8': S + '/chiffres2/chiffre-8.png', 'n9': S + '/chiffres2/chiffre-9.png', 'nplus': S + '/chiffres2/chiffre-plus.png',
    'npourcent': S + '/chiffres2/chiffre-pourcent.png', 'nk': S + '/chiffres2/chiffre-k.png', 'nM': S + '/chiffres2/chiffre-M.png',
    'tournage': A + '14709dc17edfdbe8dac7a32700d88158.jpg', 'laptop': A + 'fa155c7d0fc750858043c8a956f1eb8c.jpg',
    'interview': A + '90874b8a645df060e10c4161152d8bcd.jpg', 'camera': A + 'cafdb76bd47293bb6aac26987a509625.jpg',
    'poster': A + 'd087d181a1a758466c53ee5d13cb4174.jpg', 'julie': A + '831a4d0e7cbe35c6a0b5a91ffd9f6d67.jpg',
    'thomas': A + 'a026641ac56c1b561034523bea01999b.jpg', 'av-a': A + '1b7a66fd938836f5faa431f6062479e5.jpg',
    'av-b': A + '3176c95410f4a3d254335499e7f3b906.jpg', 'av-c': A + '74925af8f938584975d83db5d75fc53d.jpg', 'av-d': A + 'e8f568cf44e7f5a81da775ba0635c788.jpg',
    'salon': A + '61156c7e433e879076d1a17eecb3cfd4.jpg', 'collab': A + '6f51f5e5a901bb504a63670f0073c1ce.jpg', 'equipe': A + '23ae00b892795ef7557cd0cc2a51d9ed.jpg',
    'drone': A + '800596cc95238009b058ae2cc26be647.jpg',
    'eq-thomas': '/home/user/rolling/theme/rolling/assets/medias/equipe-thomas.jpg',
    'eq-alex': '/home/user/rolling/theme/rolling/assets/medias/equipe-alex.jpg',
    'eq-victor': '/home/user/rolling/theme/rolling/assets/medias/equipe-victor.jpg',
    'eq-vincent': '/home/user/rolling/theme/rolling/assets/medias/equipe-vincent.jpg',
    'eq-marion-t': '/home/user/rolling/theme/rolling/assets/medias/equipe-marion-t.jpg',
    'eq-sophie': '/home/user/rolling/theme/rolling/assets/medias/equipe-sophie.jpg',
    'eq-ronan': '/home/user/rolling/theme/rolling/assets/medias/equipe-ronan.jpg',
    'eq-marion-l': '/home/user/rolling/theme/rolling/assets/medias/equipe-marion-l.jpg',
    'eq-ines': '/home/user/rolling/theme/rolling/assets/medias/equipe-ines.jpg',
    'eq-hakim': '/home/user/rolling/theme/rolling/assets/medias/equipe-hakim.jpg',
    'eq-maxime': '/home/user/rolling/theme/rolling/assets/medias/equipe-maxime.jpg',
    'cap-fnac': I + 'cap-FNACDARTY.png', 'cap-sncf': I + 'cap-SNCF.png', 'cap-weenect': I + 'cap-WEENECT.png',
    'fondateurs': I + 'fondateurs.webp',
    'cap-veolia': I + 'cap-VEOLIA.png', 'cap-groupama': I + 'cap-Groupama.png', 'cap-article': I + 'cap-ARTICLEBLOG.png',
}
WIDTH = {'fondateurs': 900, 'eq-thomas': 280, 'eq-alex': 280, 'eq-victor': 280, 'eq-vincent': 280, 'eq-marion-t': 280, 'eq-sophie': 280, 'eq-ronan': 280, 'eq-marion-l': 280, 'eq-ines': 280, 'eq-hakim': 280, 'eq-maxime': 280, 'n5': 90, 'n6': 90, 'n7': 90, 'n8': 90, 'n9': 90, 'nplus': 90, 'npourcent': 110, 'nk': 90, 'nM': 110, 'n0': 90, 'n1': 90, 'n2': 90, 'n3': 90, 'n4': 90, 'n500': 360, 'n1c': 90, 'julie': 240, 'thomas': 160,
         'av-a': 80, 'av-b': 80, 'av-c': 80, 'av-d': 80}
cache = {}
def img(k):
    if k not in cache:
        cache[k] = data_uri(IMGS[k], WIDTH.get(k, 1200))
    return cache[k]

LOGOS = json.load(open(S + '/maq-html/logos.json'))
LH = {'Groupement Mousquetaires': 46, 'Club Med': 26, 'SNCF': 40, "L'Occitane en Provence": 30, 'Air France': 14, 'BNP Paribas': 30}
def logo(alt):
    return next(o['src'] for o in LOGOS if o['alt'] == alt)
def logos6():
    out = []
    for o in LOGOS:
        if o['alt'] in LH:
            h = LH[o['alt']]; w = round(o['w'] * h / o['hh'])
            out.append(f'<span class="carte-verre tuile-logo" style="padding:0 14px;border-radius:18px"><img src="{o["src"]}" alt="{html.escape(o["alt"], quote=True)}" width="{w}" height="{h}" style="height:{h}px"></span>')
    return ''.join(out)

REA = {
    'fnac':    ('cap-fnac', 'Événement', '2:04', 'Fnac Darty', '13 jours de formation, une seule vidéo.', 'orange', 'Programme du séminaire', 'aftermovie de 2 min', 'Salle de formation Fnac Darty pendant le séminaire'),
    'sncf':    ('cap-sncf', 'Motion 2D', '1:28', 'SNCF', 'Nos valeurs, racontées en motion design.', 'orange', 'Charte des valeurs', 'vidéo motion de 90 s', 'Personnages en motion design portant les valeurs de SNCF'),
    'weenect': ('cap-weenect', 'Démo produit', '0:45', 'Weenect', 'Le module GPS, vu sous tous les angles.', 'bleu', 'Fiche produit', 'démo 3D de 45 s', 'Module GPS Weenect en vue éclatée 3D'),
    'veolia':  ('cap-veolia', 'Interview', '2:30', 'Veolia', 'Les opérations, expliquées par ceux qui les font.', 'orange', 'Note interne', 'interview métier', 'Une collaboratrice de Veolia filmée en extérieur'),
    'groupama':('cap-groupama', 'Interview', '1:50', 'Groupama', 'Un dirigeant raconte la transformation.', 'orange', 'Discours du dirigeant', 'interview de 2 min', 'Un dirigeant de Groupama filmé en interview'),
    'article': ('cap-article', 'Format 9:16', '0:58', 'Rolling', 'Un article de blog devenu vidéo verticale.', 'bleu', 'Article de blog', 'vidéo 9:16 de 60 s', 'Vidéo verticale tirée de notre article sur l’IA et la vidéo d’entreprise'),
}
def rea_card(k, h='h2'):
    im, ty, du, cl, ti, tone, av, ap, alt = REA[k]
    return (f'<article class="pile" style="gap:16px">'
            f'<a class="vignette-video" href="#realisation"><span class="cadre-photo" style="display:block;aspect-ratio:16/9;border-radius:20px"><img src="{img(im)}" alt="{html.escape(alt, quote=True)}" width="640" height="360" loading="lazy"><span class="bandeau-video">{ty}</span><span class="duree">{du}</span><span class="bouton-lecture" aria-hidden="true">{P["lecture"]}</span><span class="barre-progression" aria-hidden="true"><i></i></span></span></a>'
            f'<div><p class="label">{cl}</p><{h} class="titre-xs" style="margin-top:6px"><a href="#realisation" style="color:inherit;text-decoration:none">{ti}</a></{h}></div>'
            f'<p class="avant-apres"><span class="tag-{tone}">{av}</span>{P["transfo"]}<span>{ap}</span></p></article>')

ART = {
    1: ('collab', 'RH & marque employeur', 'orange', '5 min', '28 octobre 2026', 'Pourquoi vos vidéos d’onboarding sont-elles regardées jusqu’au bout ?'),
    2: ('salon', 'Communication interne', 'orange', '7 min', '21 octobre 2026', 'Interview de collaborateur : 7 questions pour un témoignage sincère'),
    3: ('equipe', 'IA & vidéo', 'bleu', '6 min', '14 octobre 2026', 'IA et vidéo d’entreprise : ce qui change vraiment pour vos équipes'),
    4: ('interview', 'Formation', 'orange', '4 min', '7 octobre 2026', 'Microlearning : quelle durée idéale pour une vidéo de formation ?'),
    5: ('drone', 'Marketing vidéo', 'bleu', '6 min', '30 septembre 2026', 'Vidéo et référencement : 5 formats que Google et les IA mettent en avant'),
    6: ('tournage', 'Communication interne', 'orange', '5 min', '23 septembre 2026', 'Préparer un tournage en entreprise : la check-list en 9 points'),
}
def art_card(k, h='h2'):
    im, cat, tone, tl, date, ti = ART[k]
    return (f'<article class="pile" style="gap:14px">'
            f'<a href="#article" class="cadre-photo" style="display:block;aspect-ratio:16/10;border-radius:22px" tabindex="-1" aria-hidden="true"><img src="{img(im)}" alt="" width="640" height="400" loading="lazy"></a>'
            f'<p class="rangee" style="gap:10px"><span class="tag-{tone}">{cat}</span><span class="texte-petit">{tl} de lecture · {date}</span></p>'
            f'<{h} class="titre-xs"><a href="#article" style="color:inherit;text-decoration:none">{ti}</a></{h}></article>')

SOMMAIRE = ''.join(f'<li><a href="#{i}">{t}</a></li>' for i, t in [('pourquoi', 'Pourquoi le microlearning fonctionne'), ('etapes', 'Les 5 étapes'), ('avis', 'Notre avis'), ('aretenir', 'À retenir'), ('faq', 'Questions fréquentes')])
ICO = {
    'LinkedIn': '<path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.84-2.05 3.78-2.05C20.6 8.65 21 11.2 21 14.5V21h-4v-5.8c0-1.4 0-3.2-1.95-3.2S12.8 13.5 12.8 15.1V21H9z" fill="currentColor"/>',
    'Facebook': '<path d="M14 8h3V4h-3c-2.8 0-4.5 1.8-4.5 4.6V11H7v4h2.5v7h4v-7H16l.7-4h-3.2V8.9c0-.6.3-.9.9-.9z" fill="currentColor"/>',
    'X': '<path d="M17.8 3h3.1l-6.8 7.8L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.3-8.3L2 3h6.4l4.4 5.8zm-1.1 16.2h1.7L7.4 4.7H5.6z" fill="currentColor"/>',
    'WhatsApp': '<path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-3.9-4.6-4.1-.1-.2-1.1-1.5-1.1-2.8s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.4.6-.4.4c-.1.2-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.3 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.7-.1l1.9.9c.3.1.5.2.5.3.1.2.1.6-.1 1.2z" fill="currentColor"/>',
    'E-mail': '<rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M3 7l9 6 9-6" fill="none" stroke="currentColor" stroke-width="2"/>',
    'Copier': '<rect x="9" y="9" width="12" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M5 15V5a2 2 0 0 1 2-2h8" fill="none" stroke="currentColor" stroke-width="2"/>',
}
def partage():
    out = []
    for n, lab in [('LinkedIn', 'Partager sur LinkedIn'), ('Facebook', 'Partager sur Facebook'), ('X', 'Partager sur X'), ('WhatsApp', 'Partager sur WhatsApp'), ('E-mail', 'Envoyer par e-mail')]:
        out.append(f'<li><a class="partage-bouton" href="#article" target="_blank" rel="noopener" title="{lab}"><svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">{ICO[n]}</svg><span class="sr">{lab} (nouvel onglet)</span></a></li>')
    out.append(f'<li><button class="partage-bouton partage-copier" type="button" title="Copier le lien, pour Instagram ou ailleurs"><svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">{ICO["Copier"]}</svg><span class="sr">Copier le lien de l\'article</span></button></li>')
    return ''.join(out)
RESUMER = ''.join(f'<li><a class="chip-neutre" href="#article" target="_blank" rel="noopener">{n}<span class="sr"> (nouvel onglet)</span></a></li>' for n in ['ChatGPT', 'Claude', 'Perplexity', 'Le Chat'])

# --- parties communes
raw = open(D + 'parts.html', encoding='utf8').read()
P = {}
for m in re.finditer(r'<!--@([\w-]+)-->(.*?)(?=<!--@|\Z)', raw, re.S):
    P[m.group(1)] = m.group(2).strip()
P['citation-sans-bouton'] = re.sub(r'\s*<a class="bouton-principal bouton-petit" href="#temoignage">.*?</a>', '', P['citation'], flags=re.S).replace('grid-template-columns', 'x')
def etoiles(note):
    # 5 étoiles remplies au prorata de la note (4,8 = 96 %) ; la note réelle est lue par les lecteurs d'écran.
    e = ''.join(f'<path transform="translate({i*20} 0)" d="M9 1.5l2.3 4.8 5.2.7-3.8 3.6.9 5.2L9 13.3l-4.6 2.5.9-5.2L1.5 7l5.2-.7z"/>' for i in range(5))
    pct = round(float(note.replace(',', '.')) / 5 * 100)
    return (f'<span class="etoiles" role="img" aria-label="Note : {note} sur 5"><svg width="98" height="18" viewBox="0 0 98 18" aria-hidden="true">'
            f'<defs><linearGradient id="et-{pct}"><stop offset="{pct}%" stop-color="#FFB547"/><stop offset="{pct}%" stop-color="rgba(255,255,255,.22)"/></linearGradient></defs>'
            f'<g fill="url(#et-{pct})">{e}</g></svg></span>')
AVIS = ''.join(f'<li><a class="carte-avis" href="#accueil-placeholder" target="_blank" rel="noopener"><span class="avis-image" aria-hidden="true">{im}</span><span class="avis-texte">{etoiles(n)}<span><strong>{n}/5</strong> · {src}</span></span><span class="sr"> (nouvel onglet)</span></a></li>'
               for im, n, src in [('[image]', '4,9', '[37 avis Google]'), ('[badge]', '4,8', '[24 avis, plateforme]'), ('[image]', '5,0', '[12 avis, plateforme]')])
P['pied-avis'] = P['pied'].replace('<!--AVIS-->', '<ul class="pied-avis" aria-label="Avis de nos clients">' + AVIS + '</ul>')
P['cta-landing'] = re.sub(r'<!--BADGES-->.*?<!--/BADGES-->', '', P['cta'], flags=re.S).replace('href="#contact"', 'href="#landing-rdv"')

def verre(texte):
    # « 87 % » ou « 2 500+ » en chiffres en verre ; le texte réel reste lu par les lecteurs d'écran.
    noms = {'+': 'nplus', '%': 'npourcent', 'k': 'nk', 'M': 'nM'}
    imgs = ''
    for c in texte:
        if c.isdigit():
            imgs += f'<img src="{img("n" + c)}" alt="" height="60">'
        elif c in noms:
            imgs += f'<img src="{img(noms[c])}" alt="" height="60">'
        elif c in ' \u00a0\u202f':
            imgs += '<i class="espace-verre"></i>'
    return f'<span class="chiffre-verre" aria-hidden="true">{imgs}</span><span class="sr">{html.escape(texte)}</span>'

def fill(t, page):
    for _ in range(4):
        t = t.replace('{{page}}', page)
        t = t.replace('{{curres}}', ' data-courant="oui"' if page in ('blog', 'article', 'guides', 'webinars', 'replays') else '')
        t = re.sub(r'\{\{cur:(\w+)\}\}', lambda m: ' aria-current="page"' if m.group(1) == page or (m.group(1) + 's') == page else '', t)
        t = re.sub(r'\{\{rea:(\w+)\}\}', lambda m: rea_card(m.group(1), 'h2'), t)
        t = re.sub(r'\{\{rea3:(\w+)\}\}', lambda m: rea_card(m.group(1), 'h3'), t)
        t = re.sub(r'\{\{art:(\d)\}\}', lambda m: art_card(int(m.group(1)), 'h2'), t)
        t = re.sub(r'\{\{art3:(\d)\}\}', lambda m: art_card(int(m.group(1)), 'h3'), t)
        t = re.sub(r'\{\{logo:([^}]+)\}\}', lambda m: logo(m.group(1)), t)
        t = re.sub(r'\{\{verre:([^}]+)\}\}', lambda m: verre(m.group(1)), t)
        t = t.replace('{{logos-defile}}', '<div class="bande-logos" aria-label="Ils nous font confiance"><div class="piste-logos">' + logos6() + '</div><div class="piste-logos" aria-hidden="true">' + logos6().replace(' alt="', ' data-alt="').replace('<img ', '<img alt="" ') + '</div></div>')
        t = t.replace('{{logos}}', P['logos']).replace('{{logos6}}', logos6())
        t = t.replace('{{sommaire}}', SOMMAIRE).replace('{{partage}}', partage()).replace('{{resumer}}', RESUMER)
        for k, v in P.items():
            t = t.replace('{{' + k + '}}', v)
        t = re.sub(r'\{\{img:([\w-]+)\}\}', lambda m: img(m.group(1)), t)
    return t

pages = []
for f in ['p-offres.html', 'p-realisations.html', 'p-temoignages.html', 'p-blog.html', 'p-ressources.html', 'p-agence.html', 'p-autres.html']:
    src = open(D + f, encoding='utf8').read()
    for m in re.finditer(r'<!--@page ([\w-]+)\|([^>]*)-->(.*?)(?=<!--@page|\Z)', src, re.S):
        pages.append((m.group(1), m.group(2), fill(m.group(3), m.group(1))))

# Espaces insécables avant ? ! : ; dans les textes (pas dans les attributs, styles ni scripts)
def nbsp(t):
    parts = re.split(r'(<[^>]+>)', t)
    return ''.join(p if p.startswith('<') else re.sub(r' ([?!:;»])', ' \\1', re.sub(r'« ', '« ', p)) for p in parts)

css = open(D + 'style.css', encoding='utf8').read() + open(D + 'extra.css', encoding='utf8').read()
CLASSES = sorted(set(re.findall(r'\.([a-z][a-z0-9-]+)', open(D + 'style.css', encoding='utf8').read())) - {'ouvert', 'menu', 'logo', 'burger', 'entete', 'entete-barre', 'pied', 'pied-haut', 'pied-bas', 'ariane', 'sr', 'aller-contenu'})
sel = ''.join(f'<option value="{i}">{html.escape(t)}</option>' for i, t, _ in pages)
body = ''.join(f'<div class="page" id="{i}" data-titre="{html.escape(t)}">{nbsp(c)}</div>' for i, t, c in pages)
out = open(D + 'shell.html', encoding='utf8').read().replace('/*CSS*/', css).replace('<!--PAGES-->', body).replace('<!--OPTIONS-->', sel).replace('/*CLASSES*/', json.dumps(CLASSES))
open(D + 'maquettes-pages.html', 'w', encoding='utf8').write(out)
print(len(pages), 'pages', round(len(out) / 1e6, 2), 'Mo')
