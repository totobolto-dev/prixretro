<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quick Completeness Classifier - PrixRetro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 2rem;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .counter {
            font-size: 1.25rem;
            color: #94a3b8;
        }

        .done-message {
            display: none;
            text-align: center;
            padding: 4rem;
            font-size: 2rem;
        }

        .done-message.show {
            display: block;
        }

        .classifier {
            background: #1e293b;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .listing-info {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .listing-info:has(img[style*="display: none"]) {
            grid-template-columns: 1fr;
        }

        .listing-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            background: #0f172a;
            border-radius: 0.5rem;
        }

        .listing-details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .listing-title {
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .listing-meta {
            display: flex;
            gap: 2rem;
            font-size: 1.25rem;
        }

        .price {
            font-weight: 700;
            color: #22c55e;
            font-size: 2rem;
        }

        .variant-name {
            color: #94a3b8;
            font-size: 1.1rem;
        }

        .ebay-link {
            color: #60a5fa;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .ebay-link:hover {
            text-decoration: underline;
        }

        .buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .btn {
            padding: 2rem;
            font-size: 1.5rem;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-loose {
            background: #475569;
            color: #fff;
        }

        .btn-loose:hover {
            background: #64748b;
        }

        .btn-cib {
            background: #3b82f6;
            color: #fff;
        }

        .btn-cib:hover {
            background: #2563eb;
        }

        .btn-sealed {
            background: #f59e0b;
            color: #fff;
        }

        .btn-sealed:hover {
            background: #d97706;
        }

        .btn-emoji {
            font-size: 3rem;
        }

        .btn-label {
            font-size: 1.25rem;
        }

        .btn-desc {
            font-size: 0.85rem;
            opacity: 0.8;
            font-weight: 400;
        }

        .loading {
            text-align: center;
            padding: 4rem;
            font-size: 1.5rem;
            color: #94a3b8;
        }

        .keyboard-hint {
            text-align: center;
            margin-top: 1rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .listing-info {
                grid-template-columns: 1fr;
            }

            .buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ Quick Completeness Classifier</h1>
            <div class="counter" id="counter">Loading...</div>
        </div>

        <div class="loading" id="loading">Chargement de la première annonce...</div>

        <div class="done-message" id="done">
            🎉 Terminé ! Toutes les annonces ont été classifiées.
            <br><br>
            <a href="/admin/listings" style="color: #60a5fa; text-decoration: none;">← Retour au panneau admin</a>
        </div>

        <div class="classifier" id="classifier" style="display: none;">
            <div class="listing-info">
                <img id="image" src="" alt="Listing image" class="listing-image">
                <div class="listing-details">
                    <div class="listing-title" id="title"></div>
                    <div class="price" id="price"></div>
                    <div class="variant-name" id="variant"></div>
                    <a id="ebay-link" href="" target="_blank" class="ebay-link">Voir sur eBay →</a>
                </div>
            </div>

            <div class="buttons">
                <button class="btn btn-loose" onclick="classify('loose')">
                    <span class="btn-emoji">⚪</span>
                    <span class="btn-label">Loose</span>
                    <span class="btn-desc">Console seule</span>
                </button>
                <button class="btn btn-cib" onclick="classify('cib')">
                    <span class="btn-emoji">📦</span>
                    <span class="btn-label">CIB</span>
                    <span class="btn-desc">Complet en boîte</span>
                </button>
                <button class="btn btn-sealed" onclick="classify('sealed')">
                    <span class="btn-emoji">🔒</span>
                    <span class="btn-label">Sealed</span>
                    <span class="btn-desc">Neuf scellé</span>
                </button>
            </div>

            <div class="keyboard-hint">
                Raccourcis clavier : 1 = Loose • 2 = CIB • 3 = Sealed
            </div>
        </div>
    </div>

    <script>
        let currentListing = null;

        // Fetch next listing
        async function loadNext() {
            try {
                const response = await fetch('/admin/quick-classify/next', {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.done) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('classifier').style.display = 'none';
                    document.getElementById('done').classList.add('show');
                    return;
                }

                currentListing = data;
                displayListing(data);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('classifier').style.display = 'block';
            } catch (error) {
                console.error('Error loading listing:', error);
                alert('Erreur lors du chargement. Rechargez la page.');
            }
        }

        // Display listing
        function displayListing(data) {
            console.log('Listing data:', data);

            document.getElementById('title').textContent = data.title;
            document.getElementById('price').textContent = data.price + '€';
            document.getElementById('variant').textContent = data.variant;

            const img = document.getElementById('image');
            const imgUrl = data.image_url || data.thumbnail_url;

            console.log('Image URL:', imgUrl);

            if (imgUrl && imgUrl !== '' && imgUrl !== 'null') {
                img.src = imgUrl;
                img.style.display = 'block';
                img.style.opacity = '1';
                console.log('Image src set to:', img.src);
            } else {
                console.log('No image URL, hiding image');
                img.style.display = 'none';
            }

            document.getElementById('ebay-link').href = data.url;
            document.getElementById('counter').textContent = `${data.remaining} annonces restantes`;
        }

        // Classify and move to next
        async function classify(completeness) {
            if (!currentListing) return;

            // Disable buttons during save
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => btn.disabled = true);

            try {
                const response = await fetch(`/admin/quick-classify/${currentListing.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ completeness }),
                });

                if (response.ok) {
                    // Load next immediately
                    loadNext();
                } else {
                    alert('Erreur lors de la sauvegarde');
                    buttons.forEach(btn => btn.disabled = false);
                }
            } catch (error) {
                console.error('Error saving:', error);
                alert('Erreur réseau');
                buttons.forEach(btn => btn.disabled = false);
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === '1') classify('loose');
            if (e.key === '2') classify('cib');
            if (e.key === '3') classify('sealed');
        });

        // Load first listing on page load
        loadNext();
    </script>
</body>
</html>
