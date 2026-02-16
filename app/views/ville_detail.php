<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="/accueil" class="btn btn-secondary mb-3">← Retour a l'accueil</a>
            <h1 class="mb-1">Ville: <?php echo htmlspecialchars($ville['nom_ville'] ?? 'Inconnue'); ?></h1>
        </div>
    </div>

    <!-- Inventaire courant -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="alert alert-info">
                <h4 class="mb-3"><i class="fas fa-warehouse"></i> Inventaire courant</h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Argent disponible:</strong>
                        <span class="badge bg-success" style="font-size: 1rem;">
                            <?php 
                                $argent = $inventaire_argent ?? [];
                                echo $argent ? number_format($argent['montant_disponible'], 0, ',', ' ') . ' Ar' : 'N/A';
                            ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Matériaux en stock:</strong>
                        <div class="mt-2">
                            <?php if (!empty($inventaire_materiaux)): ?>
                                <?php foreach ($inventaire_materiaux as $inv): ?>
                                    <span class="badge bg-info me-2 mb-2">
                                        <?php echo htmlspecialchars($inv['nom_materiau']); ?>: 
                                        <strong><?php echo $inv['quantite_disponible']; ?> <?php echo htmlspecialchars($inv['unite']); ?></strong>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Aucun matériau en stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-3">Besoins initiaux</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Categorie</th>
                            <th>Besoin</th>
                            <th>Quantite</th>
                            <th>Unite</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($besoins_materiaux)): ?>
                            <?php foreach ($besoins_materiaux as $besoin): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($besoin['categorie']); ?></td>
                                    <td><?php echo htmlspecialchars($besoin['nom_materiau']); ?></td>
                                    <td><?php echo $besoin['quantite']; ?></td>
                                    <td><?php echo htmlspecialchars($besoin['unite'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($besoins_argent)): ?>
                            <?php foreach ($besoins_argent as $besoin_argent): ?>
                                <tr>
                                    <td>Argent</td>
                                    <td>Montant necessaire</td>
                                    <td><?php echo number_format($besoin_argent['montant_necessaire'], 2, ',', ' '); ?></td>
                                    <td>Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (empty($besoins_materiaux) && empty($besoins_argent)): ?>
                            <tr>
                                <td colspan="4" class="text-muted">Aucun besoin enregistre.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-3">Dons attribués</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Categorie</th>
                            <th>Besoin</th>
                            <th>Quantite donnee</th>
                            <th>Unite</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dons_materiaux)): ?>
                            <?php foreach ($dons_materiaux as $don): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($don['categorie']); ?></td>
                                    <td><?php echo htmlspecialchars($don['nom_materiau']); ?></td>
                                    <td><?php echo $don['quantite_donnee']; ?></td>
                                    <td><?php echo htmlspecialchars($don['unite'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($dons_argent)): ?>
                            <?php foreach ($dons_argent as $don_argent): ?>
                                <tr>
                                    <td>Argent</td>
                                    <td>Montant attribue</td>
                                    <td><?php echo number_format($don_argent['montant_donne'], 2, ',', ' '); ?></td>
                                    <td>Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (empty($dons_materiaux) && empty($dons_argent)): ?>
                            <tr>
                                <td colspan="4" class="text-muted">Aucun don attribue.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-3">Restant à attribuer</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Categorie</th>
                            <th>Besoin</th>
                            <th>Quantite restante</th>
                            <th>Unite</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($restant_materiaux)): ?>
                            <?php foreach ($restant_materiaux as $reste): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reste['categorie']); ?></td>
                                    <td><?php echo htmlspecialchars($reste['nom_materiau']); ?></td>
                                    <td><?php echo $reste['quantite_restante']; ?></td>
                                    <td><?php echo htmlspecialchars($reste['unite'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($restant_argent)): ?>
                            <?php foreach ($restant_argent as $reste_argent): ?>
                                <tr>
                                    <td>Argent</td>
                                    <td>Montant restant</td>
                                    <td><?php echo number_format($reste_argent['montant_restant'], 2, ',', ' '); ?></td>
                                    <td>Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (empty($restant_materiaux) && empty($restant_argent)): ?>
                            <tr>
                                <td colspan="4" class="text-muted">Aucun restant a afficher.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-3">Achats effectués</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Categorie</th>
                            <th>Materiau</th>
                            <th>Quantite achetee</th>
                            <th>Unite</th>
                            <th>Prix unitaire</th>
                            <th>Prix total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($achats_materiaux)): ?>
                            <?php foreach ($achats_materiaux as $achat): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($achat['categorie']); ?></td>
                                    <td><?php echo htmlspecialchars($achat['nom_materiau']); ?></td>
                                    <td><?php echo $achat['quantite_achetee']; ?></td>
                                    <td><?php echo htmlspecialchars($achat['unite'] ?? ''); ?></td>
                                    <td><?php echo number_format($achat['prix_unitaire'], 2, ',', ' '); ?> Ar</td>
                                    <td><?php echo number_format($achat['prix_total'], 2, ',', ' '); ?> Ar</td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($achat['date_achat'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (empty($achats_materiaux)): ?>
                            <tr>
                                <td colspan="7" class="text-muted">Aucun achat effectue.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
