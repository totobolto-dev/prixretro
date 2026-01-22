@extends('layout')

@section('title')
Comment tester une console d'occasion avant achat - Checklist 2026 | PrixRetro
@endsection

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <a href="/guides">Guides</a>
        <span>›</span>
        <span>Tester une console d'occasion</span>
    </div>

    <h1>Comment tester une console d'occasion avant achat</h1>

    <div style="background: var(--bg-card); border-left: 4px solid var(--accent-primary); padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
        <strong>📌 L'essentiel</strong>
        <p style="margin-top: 0.5rem;">Acheter une console d'occasion sans la tester = risque de panne. Suivez cette checklist en 10 minutes chrono : <strong>(1) État physique, (2) Lecture jeux, (3) Contrôles, (4) Connectique, (5) Son/Image</strong>. Emportez un jeu de test + câbles. Si vendeur refuse test, fuyez. Sur eBay/Leboncoin, exigez vidéo de test complet.</p>
    </div>

    <h2>🧰 Matériel à apporter</h2>

    <ul>
        <li><strong>Jeu de test :</strong> Cartouche/disque compatible (emprunter si besoin)</li>
        <li><strong>Smartphone :</strong> Lampe torche + photos état console</li>
        <li><strong>Câbles HDMI/AV :</strong> Si vendeur n'a pas (consoles salon)</li>
        <li><strong>Chargeur compatible :</strong> Portable (GB, DS, PSP) si vendeur n'a pas</li>
        <li><strong>Coton-tige + alcool isopropylique :</strong> Nettoyer connecteurs si besoin</li>
    </ul>

    <h2>✅ Checklist universelle (toutes consoles)</h2>

    <h3>1️⃣ Inspection visuelle (2 min)</h3>

    <div style="background: var(--bg-card); padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius);">
        <p><strong>❌ RED FLAGS - N'achetez pas si :</strong></p>
        <ul>
            <li>Fissures/casse du châssis</li>
            <li>Traces de liquide/corrosion (ports, vis)</li>
            <li>Batterie gonflée (portables)</li>
            <li>Odeur de brûlé/plastique fondu</li>
            <li>Vis manquantes/dépouillées (ouverte par amateur)</li>
        </ul>

        <p style="margin-top: 1rem;"><strong>✅ Acceptable :</strong></p>
        <ul>
            <li>Rayures superficielles coque</li>
            <li>Jaunissement plastique (normal sur anciennes consoles)</li>
            <li>Autocollants/résidus colle (nettoyable)</li>
            <li>Léger jeu charnières (DS/3DS)</li>
        </ul>
    </div>

    <h3>2️⃣ Test de démarrage (1 min)</h3>

    <ol>
        <li>Insérer jeu de test (cartouche/disque propre)</li>
        <li>Allumer console</li>
        <li>Chronométrer démarrage (< 30 sec normal)</li>
        <li>Vérifier logo constructeur affiché</li>
        <li>Accéder menu principal</li>
    </ol>

    <p><strong>❌ Problème si :</strong> Écran noir, redémarrages, freeze, bruits anormaux lecteur</p>

    <h3>3️⃣ Test image (2 min)</h3>

    <div style="background: var(--bg-card); padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius);">
        <p><strong>À vérifier :</strong></p>
        <ul>
            <li><strong>Pixels morts :</strong> Lancer jeu, pause sur fond uni (blanc/noir). Compter pixels morts (< 5 acceptable)</li>
            <li><strong>Couleurs :</strong> Tester rouge/vert/bleu vifs. Pas de dominante anormale</li>
            <li><strong>Luminosité :</strong> Réglages min/max fonctionnels</li>
            <li><strong>Ghosting :</strong> Scène rapide = pas de traînée excessive</li>
            <li><strong>Burn-in (OLED) :</strong> PS Vita OLED = fond gris 50% pendant 30 sec</li>
        </ul>
    </div>

    <h3>4️⃣ Test contrôles (3 min)</h3>

    <table style="width: 100%; border-collapse: collapse; margin: 2rem 0;">
        <thead style="background: var(--bg-darker);">
            <tr>
                <th style="padding: 1rem; text-align: left; border: 1px solid var(--border-color);">Contrôle</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid var(--border-color);">Test</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid var(--border-color);">RED FLAG</th>
            </tr>
        </thead>
        <tbody style="background: var(--bg-card);">
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Croix directionnelle</strong></td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Tester 8 directions (haut, bas, gauche, droite + diagonales)</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Inputs manqués, directions bloquées</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Boutons A/B/X/Y</strong></td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Spam rapide 20x chaque bouton</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Clics mous, inputs doubles</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Gâchettes L/R</strong></td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Appui complet + relâcher. Répéter 10x</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Pas de clic, course incomplète</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Sticks analogiques</strong></td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Cercles complets lents, puis rapides. Relâcher = recentrage</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Dead zones, dérives, stick drift</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Start/Select</strong></td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Ouvrir/fermer menu 5x</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Bouton enfoncé en permanence</td>
            </tr>
        </tbody>
    </table>

    <h3>5️⃣ Test audio (1 min)</h3>

    <ul>
        <li><strong>Haut-parleurs :</strong> Volume 50% → Musique claire, pas de grésillement</li>
        <li><strong>Prise casque :</strong> Brancher casque → Son dans les 2 oreilles</li>
        <li><strong>Silence complet :</strong> Éteindre son → Pas de sifflement/bourdonnement</li>
    </ul>

    <h3>6️⃣ Test lecteur (2 min - consoles à disques/cartouches)</h3>

    <div style="background: var(--bg-card); padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius);">
        <p><strong>Cartouches (GB, GBA, DS, N64, SNES) :</strong></p>
        <ul>
            <li>Insérer/retirer cartouche 3x → Pas de blocage</li>
            <li>Souffler sur port + cartouche (enlever poussière)</li>
            <li>Si non-lecture : Nettoyer connecteurs (coton-tige + alcool)</li>
            <li>Retry après nettoyage</li>
        </ul>

        <p style="margin-top: 1rem;"><strong>Disques (PS1, PS2, PSP, GameCube, Wii) :</strong></p>
        <ul>
            <li>Éjecter/insérer disque 3x → Mécanisme fluide</li>
            <li>Écouter rotation disque → Ronronnement régulier (pas de cliquetis)</li>
            <li>Temps de chargement < 1 min (jeu vers gameplay)</li>
            <li>Tester sauvegarde (créer fichier de save)</li>
        </ul>
    </div>

    <h2>🎯 Tests spécifiques par type de console</h2>

    <h3>Consoles portables (Game Boy, DS, PSP, PS Vita)</h3>

    <ul>
        <li><strong>Batterie :</strong> Vérifier autonomie restante (menu système si disponible). Accepter 50% capacité d'origine.</li>
        <li><strong>Charnières (DS/3DS) :</strong> Ouvrir/fermer 5x. Craquements légers OK, fissures = NON.</li>
        <li><strong>Charge :</strong> Brancher chargeur → LED charge allumée. Laisser charger 2 min.</li>
        <li><strong>Écran tactile (DS/3DS/Vita) :</strong> Dessiner croix complète dans 4 coins. Pas de zones mortes.</li>
    </ul>

    <h3>Consoles salon (Nintendo, PlayStation, Xbox, Sega)</h3>

    <ul>
        <li><strong>Ventilation :</strong> Après 10 min de jeu, vérifier chaleur (tiède OK, brûlant = NON).</li>
        <li><strong>Ports :</strong> Tester tous ports manettes (changer port en jeu).</li>
        <li><strong>Memory card :</strong> Créer sauvegarde → Éteindre → Rallumer → Vérifier présence save.</li>
        <li><strong>HDMI/AV :</strong> Changer source TV → Image stable.</li>
    </ul>

    <h2>💬 Questions à poser au vendeur</h2>

    <ol>
        <li>"Depuis combien de temps vous l'avez ?" (Achat neuf/occasion ?)</li>
        <li>"Quand l'avez-vous utilisée pour la dernière fois ?" (Stockage longue durée = risques)</li>
        <li>"Pourquoi vous la vendez ?" (Upgrade normal vs problème caché)</li>
        <li>"Y a-t-il eu des réparations ?" (Bricolage amateur = red flag)</li>
        <li>"Avez-vous la boîte/facture ?" (Authenticité, valeur revente)</li>
        <li>"Acceptez-vous un test complet avant paiement ?" (Refus = suspect)</li>
    </ol>

    <h2>🌐 Achats en ligne (eBay, Leboncoin, Vinted)</h2>

    <div style="background: var(--bg-card); border-left: 4px solid #f59e0b; padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
        <strong>⚠️ Exigences minimales :</strong>
        <ul style="margin-top: 0.5rem;">
            <li><strong>Photos :</strong> Minimum 8-10 photos HD (tous angles, ports, écran allumé, série console)</li>
            <li><strong>Vidéo de test :</strong> 2-3 min montrant démarrage complet + gameplay + tous boutons testés</li>
            <li><strong>Description honnête :</strong> Défauts mentionnés (rayures, bouton qui colle, etc.)</li>
            <li><strong>Retours acceptés :</strong> eBay = "Retours sous 30 jours" (protection acheteur)</li>
            <li><strong>Évaluations vendeur :</strong> > 95% positives + historique vente consoles</li>
        </ul>
        <p style="margin-top: 1rem;"><strong>🚫 N'achetez JAMAIS :</strong> Annonces "non testée", "pour pièces", "vendue en l'état", photos floues, vendeur 0 évaluation.</p>
    </div>

    <h2>📝 Checklist imprimable (récapitulatif)</h2>

    <div style="background: var(--bg-darker); padding: 2rem; margin: 2rem 0; border-radius: var(--radius); font-family: monospace;">
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Inspection visuelle (fissures, liquide, batterie)</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Démarrage < 30 sec + menu accessible</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Écran : pixels morts, couleurs, luminosité</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Croix directionnelle (8 directions)</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Boutons A/B/X/Y (spam 20x chacun)</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Gâchettes L/R (10 appuis complets)</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Sticks analogiques (cercles, recentrage)</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Audio (haut-parleurs + casque)</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Lecteur (insertion, lecture, éjection)</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Sauvegarde (créer + vérifier après reboot)</p>
        <p style="font-weight: 700; margin-bottom: 1rem;">☐ Charge/Batterie (portables)</p>
        <p style="font-weight: 700;">☐ Questions vendeur (historique, réparations)</p>
    </div>

    <h2>💡 Négociation post-test</h2>

    <p>Si défauts mineurs détectés :</p>
    <ul>
        <li><strong>1-2 pixels morts :</strong> -5 à -10€</li>
        <li><strong>Rayures écran légères :</strong> -5€</li>
        <li><strong>Bouton L/R mou (réparable) :</strong> -10 à -15€</li>
        <li><strong>Autonomie batterie 50% :</strong> -10€ (coût remplacement)</li>
        <li><strong>Jaunissement plastique :</strong> -0€ (normal sur vieilles consoles)</li>
    </ul>

    <div style="background: var(--bg-card); border-left: 4px solid var(--accent-primary); padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
        <strong>💡 Conseil final</strong>
        <p style="margin-top: 0.5rem;">Un vendeur honnête acceptera TOUJOURS un test complet. Si refus ou pression ("quelqu'un d'autre est intéressé"), partez. Mieux vaut rater une "bonne affaire" que d'acheter une console HS. Consultez <a href="/tendances" style="color: var(--accent-primary);">nos prix de marché</a> pour éviter les arnaques.</p>
    </div>

    <div style="text-align: center; margin: 3rem 0;">
        <a href="/" style="display: inline-block; background: var(--accent-primary); color: white; padding: 1rem 2rem; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
            📊 Voir les prix des consoles d'occasion
        </a>
    </div>
</div>
@endsection
