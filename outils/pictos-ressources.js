// Capture les pictos en verre des rubriques Ressources depuis la maquette validée (fond transparent, marge pour l'ombre).
// node outils/pictos-ressources.js <maquette-ressources-mix.html> <dossier de sortie>
// Puis : python3 -I outils/vers-webp.py <fichiers.png> (même réglage que les autres illustrations).
const { chromium } = require('playwright');
const path = require('path');

(async () => {
  const [maquette, sortie] = process.argv.slice(2);
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 1320, height: 900 }, deviceScaleFactor: 2 });
  await p.goto('file://' + path.resolve(maquette), { waitUntil: 'networkidle' });
  await p.evaluate(() => {
    document.documentElement.style.background = 'transparent';
    document.body.style.background = 'transparent';
    document.querySelectorAll('.scene, .sec, .verre').forEach(e => { e.style.background = 'transparent'; e.style.boxShadow = 'none'; });
    const style = document.createElement('style');
    style.textContent = '.sec::before, .verre::before { display: none !important; } .scene { box-shadow: none !important; } .rub h3, .rub p, .rub .lien { visibility: hidden; }';
    document.head.appendChild(style);
  });
  const noms = ['blog', 'guides', 'webinars', 'replays'];
  await p.click('[data-cible="s1"] [data-etat="plein"]');
  const pictos = await p.$$('#s1 .rubriques .rub.si-contenu .picto');
  for (let i = 0; i < noms.length; i++) {
    await pictos[i].evaluate(e => e.scrollIntoView({ block: 'center' }));
    const r = await pictos[i].boundingBox();
    const m = 24;
    await p.screenshot({ path: path.join(sortie, `picto-${noms[i]}.png`), omitBackground: true, clip: { x: r.x - m, y: r.y - m, width: r.width + 2 * m, height: r.height + 2 * m } });
  }
  await b.close();
  console.log('pictos capturés :', noms.join(', '));
})();
