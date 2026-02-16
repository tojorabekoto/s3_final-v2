<?php include 'header.php'; ?>

<style>
    .achat-card { margin-top: 2rem; }
    .materiau-item { margin-bottom: 0.5rem; }
    .inventaire-info { padding: 1rem; background-color: #f8f9fa; border-radius: 0.25rem; margin-bottom: 1.5rem; }
    .inventaire-info h5 { margin-bottom: 0.5rem; font-weight: 600; }
    .stock-badge { display: inline-block; margin-right: 1rem; }
    .total-paiement { padding: 1.5rem; background-color: #e7f3ff; border: 2px solid #0d6efd; border-radius: 0.5rem; }
    .total-paiement h4 { color: #0d6efd; margin-bottom: 1rem; }
    .mode-paiement { margin-top: 1.5rem; padding: 1rem; background-color: #fff3cd; border-radius: 0.25rem; }
    .categorie-header { background-color: #e9ecef; padding: 0.8rem; margin-top: 1.5rem; margin-bottom: 0.5rem; font-weight: 600; border-radius: 0.25rem; }
</style>

<main class="container achat-card">
    <div class="row mb-4">
        <div class="col-12">
            <a href="/accueil" class="btn btn-secondary mb-3">← Retour a l'accueil</a>
            <h2 class="mt-2">Acheter des matériaux</h2>
            <p class="text-muted">Sélectionnez les matériaux à acheter avec l'argent disponible</p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form id="achatForm" method="POST" action="/achat" class="row g-3">
        <!-- Sélection région/ville -->
        <div class="col-md-4">
            <label class="form-label">Région</label>
            <select id="region" class="form-select" required>
                <option value="">-- Choisir une région --</option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?php echo $region['id_region']; ?>">
                        <?php echo htmlspecialchars($region['nom_region']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Ville</label>
            <select id="ville" name="id_ville" class="form-select" required disabled>
                <option value="">-- Choisir une ville --</option>
                <?php foreach ($villes as $ville): ?>
                    <option value="<?php echo $ville['id_ville']; ?>" data-region="<?php echo $ville['id_region']; ?>">
                        <?php echo htmlspecialchars($ville['nom_ville']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4 align-self-end">
            <button type="button" id="loadInventaire" class="btn btn-info w-100">Charger inventaire</button>
        </div>

        <!-- Affichage de l'inventaire disponible -->
        <div class="col-12">
            <div id="inventaireDisplay" style="display: none;">
                <div class="inventaire-info">
                    <h5>Ressources disponibles</h5>
                    <div id="inventaireContent">
                        <span class="text-muted">Aucune ressource n'a pu être chargée</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des matériaux pour sélection -->
        <div class="col-12">
            <h5 class="mb-3">Sélectionner les matériaux à acheter</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px">Sel</th>
                            <th>Catégorie</th>
                            <th>Matériau (Besoin / Disponible)</th>
                            <th style="width: 120px">Quantité à acheter</th>
                            <th>Prix unitaire</th>
                            <th>Sous-total</th>
                        </tr>
                    </thead>
                    <tbody id="materiauxTable">
                        <tr><td colspan="6" class="text-muted text-center">Sélectionnez une ville pour afficher les matériaux</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Résumé du paiement -->
        <div class="col-12">
            <div class="total-paiement">
                <h4>Résumé du paiement</h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nombre d'articles sélectionnés:</strong>
                        <span id="countArticles">0</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Coût total:</strong>
                        <span id="totalCost" style="font-size: 1.2rem; color: #0d6efd;">0 Ar</span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong>Argent disponible:</strong>
                        <span id="argent_dispo" class="badge bg-success" style="font-size: 1rem;">0 Ar</span>
                    </div>
                </div>
            </div>
            <input type="hidden" name="mode_paiement" value="argent">
        </div>

        <!-- Boutons d'action -->
        <div class="col-12">
            <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                <i class="fas fa-shopping-cart"></i> Confirmer l'achat
            </button>
            <a href="/accueil" class="btn btn-secondary btn-lg">Annuler</a>
        </div>

        <!-- Champs cachés pour les matériaux sélectionnés -->
        <div id="materiauxInputs"></div>
    </form>

    <!-- Modal de succès d'achat -->
    <?php if ($show_success_modal): ?>
    <div class="modal fade show" id="successModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Achat Confirmé!</h5>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <h6 class="mb-3">Détails de l'achat:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Matériau</th>
                                    <th>Quantité achetée</th>
                                    <th>Besoin avant</th>
                                    <th>Besoin après</th>
                                    <th>Prix total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($achats_details as $achat): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($achat['nom_materiau']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($achat['categorie']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo $achat['quantite_achetee']; ?> <?php echo htmlspecialchars($achat['unite']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo $achat['quantite_avant']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $achat['quantite_apres']; ?></span>
                                    </td>
                                    <td>
                                        <?php echo number_format($achat['prix_total'], 0, ',', ' '); ?> Ar
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-success fw-bold">
                                    <td colspan="4" class="text-end">Coût total de l'achat:</td>
                                    <td><?php echo number_format($prix_total, 0, ',', ' '); ?> Ar</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="/achat" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Continuer l'achat
                    </a>
                    <a href="/ville-detail/<?php echo $id_ville; ?>" class="btn btn-success">
                        <i class="fas fa-city"></i> Voir la ville
                    </a>
                    <a href="/accueil" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Aller vers l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<?php include 'footer.php'; ?>

<script>
    const regionEl = document.getElementById('region');
    const villeEl = document.getElementById('ville');
    const loadBtn = document.getElementById('loadInventaire');
    const materiauxTable = document.getElementById('materiauxTable');
    const countArticles = document.getElementById('countArticles');
    const totalCost = document.getElementById('totalCost');
    const submitBtn = document.getElementById('submitBtn');
    const inventaireDisplay = document.getElementById('inventaireDisplay');
    const inventaireContent = document.getElementById('inventaireContent');
    const argent_dispo = document.getElementById('argent_dispo');

    let inventaireData = {};
    let materiauxData = {};

    // Filtrer les villes selon région via API
    regionEl.addEventListener('change', () => {
        const regionId = regionEl.value;
        villeEl.innerHTML = '<option value="">-- Choisir une ville --</option>';
        villeEl.disabled = true;
        materiauxTable.innerHTML = '<tr><td colspan="6" class="text-muted text-center">Sélectionnez une ville</td></tr>';
        inventaireDisplay.style.display = 'none';

        if (regionId) {
            fetch('/api/villes/' + encodeURIComponent(regionId))
                .then(r => r.json())
                .then(villes => {
                    villes.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.id_ville;
                        opt.textContent = v.nom_ville;
                        villeEl.appendChild(opt);
                    });
                    villeEl.disabled = false;
                })
                .catch(err => {
                    console.error('Erreur chargement villes:', err);
                    villeEl.disabled = true;
                });
        }
    });

    // Charger le groupEsInventaire quand on sélectionne une ville
    loadBtn.addEventListener('click', () => {
        const villeId = villeEl.value;
        if (!villeId) {
            alert('Veuillez sélectionner une ville');
            return;
        }

        // Récupérer inventaire
        fetch('/api/inventaire/' + encodeURIComponent(villeId))
            .then(r => r.json())
            .then(data => {
                inventaireData = data;
                updateInventaireDisplay();
                updateMateriauxTable();
                submitBtn.disabled = false;
            })
            .catch(err => {
                console.error('Erreur', err);
                alert('Erreur lors du chargement de l\'inventaire');
            });
    });

    function updateInventaireDisplay() {
        const argent = inventaireData.argent;
        const materiauxList = inventaireData.materiaux || [];

        let html = '';
        if (argent) {
            html += `<div class="stock-badge">
                        <strong>Argent disponible:</strong>
                        <span class="badge bg-success">${Number(argent.montant_disponible).toLocaleString('fr-FR')} Ar</span>
                    </div>`;
            argent_dispo.textContent = Number(argent.montant_disponible).toLocaleString('fr-FR') + ' Ar';
        }
        if (materiauxList.length > 0) {
            html += '<div><strong>Matériaux en stock:</strong></div>';
            materiauxList.forEach(m => {
                html += `<div class="badge bg-info me-2">${m.nom_materiau}: ${m.quantite_disponible} ${m.unite}</div>`;
            });
        }

        inventaireContent.innerHTML = html;
        inventaireDisplay.style.display = 'block';
    }

    function updateMateriauxTable() {
        const argent = inventaireData.argent;
        if (!argent) {
            materiauxTable.innerHTML = '<tr><td colspan="7" class="alert alert-warning text-center">Aucun inventaire trouvé pour cette ville</td></tr>';
            return;
        }

        materiauxTable.innerHTML = '';

        // Afficher directement les besoins disponibles pour cette ville
        fetch('/api/besoins-by-ville/' + villeEl.value)
            .then(r => r.json())
            .then(data => {
                const besoins = data.materiaux || [];
                
                if (besoins.length === 0) {
                    materiauxTable.innerHTML = '<tr><td colspan="7" class="text-muted text-center">Aucun besoin materiel pour cette ville</td></tr>';
                    return;
                }

                // Créer une map des quantités disponibles en inventaire
                const inventaireMap = {};
                inventaireData.materiaux.forEach(m => {
                    inventaireMap[m.id_besoin] = m.quantite_disponible;
                });

                let html = '';
                let currentCategorie = '';

                besoins.forEach(besoin => {
                    if (besoin.categorie !== currentCategorie) {
                        currentCategorie = besoin.categorie;
                    }

                    const id_besoin = besoin.id_besoin;
                    const quantiteDisponible = inventaireMap[id_besoin] || 0;

                    html += `
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input materiau-check" 
                                       data-id-besoin="${id_besoin}" 
                                       data-prix="${besoin.prix_unitaire || 0}"
                                       data-nom="${besoin.nom_materiau || ''}">
                            </td>
                            <td>${besoin.categorie || ''}</td>
                            <td>
                                <strong>${besoin.nom_materiau || ''}</strong>
                                <br><small class="text-muted">Besoin: ${besoin.quantite} ${besoin.unite}</small>
                                <br><small class="text-success">Dispo: ${quantiteDisponible} ${besoin.unite}</small>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm quantite-input" 
                                       data-id-besoin="${id_besoin}"
                                       data-dispo="${quantiteDisponible}"
                                       min="0" step="0.1" value="0"
                                       style="width: 100px;" disabled>
                            </td>
                            <td>${Number(besoin.prix_unitaire || 0).toLocaleString('fr-FR')} Ar</td>
                            <td class="sous-total" data-id-besoin="${id_besoin}">0 Ar</td>
                        </tr>
                    `;
                });

                materiauxTable.innerHTML = html;

                // Event listeners
                document.querySelectorAll('.materiau-check').forEach(cb => {
                    cb.addEventListener('change', e => {
                        const input = document.querySelector(`.quantite-input[data-id-besoin="${e.target.dataset.idBesoin}"]`);
                        input.disabled = !e.target.checked;
                        if (!e.target.checked) input.value = 0;
                        updateTotals();
                    });
                });

                document.querySelectorAll('.quantite-input').forEach(input => {
                    input.addEventListener('change', updateTotals);
                    input.addEventListener('input', updateTotals);
                });
            });
    }

    function updateTotals() {
        let count = 0;
        let total = 0;

        document.querySelectorAll('.materiau-check:checked').forEach(cb => {
            const idBesoin = cb.dataset.idBesoin;
            const input = document.querySelector(`.quantite-input[data-id-besoin="${idBesoin}"]`);
            const quantite = parseFloat(input.value) || 0;
            const prix = parseFloat(cb.dataset.prix) || 0;

            if (quantite > 0) {
                count++;
                const sousTotal = quantite * prix;
                total += sousTotal;

                // Mettre à jour sous-total
                document.querySelector(`.sous-total[data-id-besoin="${idBesoin}"]`).textContent = 
                    Number(sousTotal).toLocaleString('fr-FR') + ' Ar';
            } else {
                document.querySelector(`.sous-total[data-id-besoin="${idBesoin}"]`).textContent = '0 Ar';
            }
        });

        countArticles.textContent = count;
        totalCost.textContent = Number(total).toLocaleString('fr-FR') + ' Ar';

        // Vérifier si le bouton doit être activé
        const argentDisponible = inventaireData.argent ? parseFloat(inventaireData.argent.montant_disponible) : 0;
        const canSubmit = count > 0 && total <= argentDisponible;
        submitBtn.disabled = !canSubmit;

        if (canSubmit) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-success');
        } else {
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-secondary');
        }

        // Créer les inputs cachés pour le formulaire
        updateMateriauxInputs();
    }

    function updateMateriauxInputs() {
        const container = document.getElementById('materiauxInputs');
        container.innerHTML = '';

        document.querySelectorAll('.materiau-check:checked').forEach(cb => {
            const idBesoin = cb.dataset.idBesoin;
            const input = document.querySelector(`.quantite-input[data-id-besoin="${idBesoin}"]`);
            const quantite = parseFloat(input.value) || 0;

            if (quantite > 0) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `materiaux[${idBesoin}]`;
                hidden.value = quantite;
                container.appendChild(hidden);
            }
        });
    }
</script>
