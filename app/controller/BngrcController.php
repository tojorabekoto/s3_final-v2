<?php

namespace app\controller;

use Flight;
use app\model\BngrcModel;

class BngrcController
{
    private $model;

    public function __construct()
    {
        // Instanciation du modèle une seule fois par requête
        $this->model = new BngrcModel(Flight::db());
    }

    // ────────────────────────────────────────────────
    // Régions
    // ────────────────────────────────────────────────
    public function getRegions()
    {
        $regions = $this->model->getRegions();
        Flight::render('regions', ['regions' => $regions]);
    }

    // ────────────────────────────────────────────────
    // Villes
    // ────────────────────────────────────────────────
    public function getVilles()
    {
        $villes = $this->model->getVilles();
        Flight::render('villes', ['villes' => $villes]);
    }

    public function getVillesByRegion($id_region)
    {
        $id_region = (int)$id_region;
        $villes = $this->model->getVillesByRegion($id_region);
        Flight::render('villes', [
            'villes' => $villes,
            'id_region' => $id_region
        ]);
    }

    // ────────────────────────────────────────────────
    // Sinistres
    // ────────────────────────────────────────────────
    public function getSinistres()
    {
        $sinistres = $this->model->getSinistres();
        Flight::render('sinistres', ['sinistres' => $sinistres]);
    }

    public function getSinistresByVille($id_ville)
    {
        $id_ville = (int)$id_ville;
        $sinistres = $this->model->getSinistresByVille($id_ville);
        Flight::render('sinistres', [
            'sinistres' => $sinistres,
            'id_ville' => $id_ville
        ]);
    }

    public function villeDetail($id_ville)
    {
        $id_ville = (int)$id_ville;
        $ville = $this->model->getVilleById($id_ville);
        $sinistre = $this->model->getSinistreByVille($id_ville);
        $id_sinistre = $sinistre ? $sinistre['id_sinistre'] : null;

        $data = [
            'ville' => $ville,
            'besoins_materiaux' => $this->model->getBesoinsMateriauxByVille($id_ville),
            'dons_materiaux' => $this->model->getDonsMateriauxByVille($id_ville),
            'restant_materiaux' => $this->model->getRestantMateriauxByVille($id_ville),
            'besoins_argent' => $this->model->getBesoinsArgentByVille($id_ville),
            'dons_argent' => $this->model->getDonsArgentByVille($id_ville),
            'restant_argent' => $this->model->getRestantArgentByVille($id_ville),
            'achats_materiaux' => $id_sinistre ? $this->model->getAchatsBysinistre($id_sinistre) : [],
            'inventaire_materiaux' => $id_sinistre ? $this->model->getInventaireMateriauxBySinistre($id_sinistre) : [],
            'inventaire_argent' => $id_sinistre ? $this->model->getInventaireArgentBySinistre($id_sinistre) : []
        ];

        Flight::render('ville_detail', $data);
    }

    // ────────────────────────────────────────────────
    // Besoins Matériaux
    // ────────────────────────────────────────────────
    public function getBesoinsMateriaux()
    {
        $besoins = $this->model->getBesoinMateriaux();
        Flight::render('besoins_materiaux', ['besoins' => $besoins]);
    }

    public function getDonsMateriaux($id_besoin)
    {
        $id_besoin = (int)$id_besoin;
        $dons = $this->model->getDonMateriauxByBesoin($id_besoin);
        Flight::render('dons_materiaux', [
            'dons' => $dons,
            'id_besoin' => $id_besoin
        ]);
    }

    // ────────────────────────────────────────────────
    // Besoins Argent
    // ────────────────────────────────────────────────
    public function getBesoinsArgent()
    {
        $besoins = $this->model->getBesoinArgent();
        Flight::render('besoins_argent', ['besoins' => $besoins]);
    }

    public function getDonsArgent($id_besoin_argent)
    {
        $id_besoin_argent = (int)$id_besoin_argent;
        $dons = $this->model->getDonArgentByBesoin($id_besoin_argent);
        Flight::render('dons_argent', [
            'dons' => $dons,
            'id_besoin_argent' => $id_besoin_argent
        ]);
    }

    // ────────────────────────────────────────────────
    // Catégories de besoins
    // ────────────────────────────────────────────────
    public function getCategoriesMateriau()
    {
        $categories = $this->model->getCategoriesMateriau();
        Flight::render('categories_materiau', ['categories' => $categories]);
    }

    // ────────────────────────────────────────────────
    // API JSON endpoints used by AJAX views
    // ────────────────────────────────────────────────
    public function apiGetRegions()
    {
        $regions = $this->model->getRegions();
        Flight::json($regions, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    public function apiGetVilles()
    {
        $villes = $this->model->getVilles();
        Flight::json($villes, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    public function apiGetVillesByRegion($id_region)
    {
        $id_region = (int)$id_region;
        $villes = $this->model->getVillesByRegion($id_region);
        Flight::json($villes, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    public function apiGetBesoinsByVille($id_ville)
    {
        $id_ville = (int)$id_ville;
        $materiaux = $this->model->getBesoinsMateriauxByVille($id_ville);
        $argent = $this->model->getBesoinsArgentByVille($id_ville);
        Flight::json(['materiaux' => $materiaux, 'argent' => $argent], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    // ────────────────────────────────────────────────
    // Insertion de don
    // ────────────────────────────────────────────────
    public function showInsertionDon()
    {
        $data = [
            'regions' => $this->model->getRegions(),
            'villes' => $this->model->getVilles(),
            'besoins_materiaux' => $this->model->getBesoinsMateriauxForForm(),
            'besoins_argent' => $this->model->getBesoinsArgentForForm(),
            'message' => null,
            'error' => null
        ];

        Flight::render('insertion_don', $data);
    }

    public function submitInsertionDon()
    {
        $type = $_POST['type_don'] ?? '';
        $quantite = isset($_POST['quantite']) ? (float)$_POST['quantite'] : 0;
        $error = null;
        $message = null;

        if ($quantite <= 0) {
            $error = 'La quantite doit etre superieure a 0.';
        } elseif ($type === 'materiaux') {
            $id_besoin = (int)($_POST['id_besoin'] ?? 0);
            if ($id_besoin <= 0) {
                $error = 'Veuillez selectionner un besoin materiel.';
            } else {
                $this->model->insertDonMateriaux($id_besoin, $quantite);
                $message = 'Don materiel enregistre avec succes.';
            }
        } elseif ($type === 'argent') {
            $id_besoin_argent = (int)($_POST['id_besoin_argent'] ?? 0);
            if ($id_besoin_argent <= 0) {
                $error = 'Veuillez selectionner un besoin en argent.';
            } else {
                $this->model->insertDonArgent($id_besoin_argent, $quantite);
                $message = 'Don en argent enregistre avec succes.';
            }
        } else {
            $error = 'Type de don invalide.';
        }

        $data = [
            'regions' => $this->model->getRegions(),
            'villes' => $this->model->getVilles(),
            'besoins_materiaux' => $this->model->getBesoinsMateriauxForForm(),
            'besoins_argent' => $this->model->getBesoinsArgentForForm(),
            'message' => $message,
            'error' => $error
        ];

        Flight::render('insertion_don', $data);
    }

    // ────────────────────────────────────────────────
    // Attribution de don
    // ────────────────────────────────────────────────
    public function showAttribution()
    {
        $data = [
            'regions' => $this->model->getRegions(),
            'villes' => $this->model->getVilles(),
            'besoins_materiaux' => $this->model->getBesoinsMateriauxForForm(),
            'besoins_argent' => $this->model->getBesoinsArgentForForm(),
            'restant_materiaux' => [],
            'restant_argent' => [],
            'message' => null,
            'error' => null
        ];

        Flight::render('attribution', $data);
    }

    public function submitAttribution()
    {
        $type = $_POST['type_don'] ?? '';
        $quantite = isset($_POST['quantite']) ? (float)$_POST['quantite'] : 0;
        $error = null;
        $message = null;

        if ($quantite <= 0) {
            $error = 'La quantite doit etre superieure a 0.';
        } elseif ($type === 'materiaux') {
            $id_besoin = (int)($_POST['id_besoin'] ?? 0);
            if ($id_besoin <= 0) {
                $error = 'Veuillez selectionner un besoin materiel.';
            } else {
                $restant = $this->model->getRestantMateriauxByBesoin($id_besoin);
                if ($quantite > $restant) {
                    $error = 'Quantite superieure au restant disponible.';
                } else {
                    $this->model->insertDonMateriaux($id_besoin, $quantite);
                    $message = 'Attribution materielle enregistree avec succes.';
                }
            }
        } elseif ($type === 'argent') {
            $id_besoin_argent = (int)($_POST['id_besoin_argent'] ?? 0);
            if ($id_besoin_argent <= 0) {
                $error = 'Veuillez selectionner un besoin en argent.';
            } else {
                $restant = $this->model->getRestantArgentByBesoin($id_besoin_argent);
                if ($quantite > $restant) {
                    $error = 'Montant superieur au restant disponible.';
                } else {
                    $this->model->insertDonArgent($id_besoin_argent, $quantite);
                    $message = 'Attribution en argent enregistree avec succes.';
                }
            }
        } else {
            $error = 'Type de don invalide.';
        }

        $data = [
            'regions' => $this->model->getRegions(),
            'villes' => $this->model->getVilles(),
            'besoins_materiaux' => $this->model->getBesoinsMateriauxForForm(),
            'besoins_argent' => $this->model->getBesoinsArgentForForm(),
            'restant_materiaux' => [],
            'restant_argent' => [],
            'message' => $message,
            'error' => $error
        ];

        Flight::render('attribution', $data);
    }

    // ────────────────────────────────────────────────
    // Bonus : page d'accueil / dashboard (exemple)
    // ────────────────────────────────────────────────
    public function dashboard()
    {
        $data = [
            'nb_regions'         => count($this->model->getRegions()),
            'nb_villes'          => count($this->model->getVilles()),
            'nb_sinistres'       => count($this->model->getSinistres()),
            'nb_besoins_mat'     => count($this->model->getBesoinMateriaux()),
            'nb_besoins_argent'  => count($this->model->getBesoinArgent()),
            'categories'         => $this->model->getCategoriesMateriau(),
            'villes'             => $this->model->getVilles()
        ];

        Flight::render('accueil', $data);
    }

    // ────────────────────────────────────────────────
    // Achat de matériaux
    // ────────────────────────────────────────────────
    public function showAchat()
    {
        $data = [
            'regions' => $this->model->getRegions(),
            'villes' => $this->model->getVilles(),
            'materiaux' => $this->model->getTousMateriauxAvecCategorie(),
            'inventaire_materiaux' => [],
            'inventaire_argent' => [],
            'message' => null,
            'error' => null,
            'show_success_modal' => false,
            'achats_details' => [],
            'prix_total' => 0,
            'id_ville' => 0
        ];

        Flight::render('achat', $data);
    }

    public function submitAchat()
    {
        $id_ville = (int)($_POST['id_ville'] ?? 0);
        $mode_paiement = $_POST['mode_paiement'] ?? 'argent';
        $error = null;
        $message = null;
        $show_success_modal = false;
        $achats_details = [];
        $prix_total = 0;

        if ($id_ville <= 0) {
            $error = 'Veuillez sélectionner une ville.';
        } else {
            $sinistre = $this->model->getSinistreByVille($id_ville);
            if (!$sinistre) {
                $error = 'Aucun sinistre trouvé pour cette ville.';
            } else {
                $id_sinistre = $sinistre['id_sinistre'];
                $materiaux_selectionnes = $_POST['materiaux'] ?? [];
                
                if (empty($materiaux_selectionnes)) {
                    $error = 'Veuillez sélectionner au moins un matériau.';
                } else {
                    // Calcul du coût total
                    $prix_total = 0;
                    foreach ($materiaux_selectionnes as $id_besoin => $quantite) {
                        $quantite = (float)$quantite;
                        if ($quantite > 0) {
                            $besoin = $this->model->getBesoinById($id_besoin);
                            if ($besoin) {
                                $prix_total += $quantite * $besoin['prix_unitaire'];
                            }
                        }
                    }

                    // Vérifier disponibilité selon mode de paiement
                    $inventaire_argent = $this->model->getInventaireArgentBySinistre($id_sinistre);
                    $argent_disponible = $inventaire_argent ? (float)$inventaire_argent['montant_disponible'] : 0;

                    if ($mode_paiement === 'argent') {
                        if ($argent_disponible < $prix_total) {
                            $error = 'Argent insuffisant. Disponible: ' . number_format($argent_disponible, 2) . ' Ar';
                        }
                    }

                    if (!$error) {
                        // Enregistrer les achats et collecter les infos
                        $achats_details = [];
                        foreach ($materiaux_selectionnes as $id_besoin => $quantite) {
                            $quantite = (float)$quantite;
                            if ($quantite > 0) {
                                $besoin = $this->model->getBesoinById($id_besoin);
                                if ($besoin) {
                                    $prix_unitaire = $besoin['prix_unitaire'];
                                    $prix_item = $quantite * $prix_unitaire;
                                    
                                    // Enregistrer l'achat
                                    $this->model->insertAchatMateriaux($id_sinistre, $id_besoin, $quantite, $prix_unitaire, $prix_item);
                                    
                                    // Réduire la quantité du besoin
                                    $quantite_avant = $besoin['quantite'];
                                    $quantite_apres = $quantite_avant - $quantite;
                                    $this->model->reduceBesoinQuantite($id_besoin, $quantite);
                                    
                                    // Ajouter aux détails pour la popup
                                    $achats_details[] = [
                                        'nom_materiau' => $besoin['nom_materiau'],
                                        'categorie' => $besoin['categorie'],
                                        'unite' => $besoin['unite'],
                                        'quantite_achetee' => $quantite,
                                        'quantite_avant' => $quantite_avant,
                                        'quantite_apres' => $quantite_apres,
                                        'prix_unitaire' => $prix_unitaire,
                                        'prix_total' => $prix_item
                                    ];
                                }
                            }
                        }

                        // Mettre à jour inventaire selon mode de paiement
                        if ($mode_paiement === 'argent' || $mode_paiement === 'mixte') {
                            $this->model->updateInventaireArgentApresAchat($id_sinistre, $prix_total);
                        }

                        $message = 'Achat enregistré avec succès!';
                        $show_success_modal = true;
                    }
                }
            }
        }

        $data = [
            'regions' => $this->model->getRegions(),
            'villes' => $this->model->getVilles(),
            'materiaux' => $this->model->getTousMateriauxAvecCategorie(),
            'inventaire_materiaux' => [],
            'inventaire_argent' => [],
            'message' => $message,
            'error' => $error,
            'show_success_modal' => $show_success_modal ?? false,
            'achats_details' => $achats_details ?? [],
            'prix_total' => $prix_total ?? 0,
            'id_ville' => $id_ville
        ];

        Flight::render('achat', $data);
    }

    public function apiGetInventaireByVille($id_ville)
    {
        $id_ville = (int)$id_ville;
        $sinistre = $this->model->getSinistreByVille($id_ville);
        
        if (!$sinistre) {
            Flight::json(['materiaux' => [], 'argent' => null], 404);
            return;
        }

        $id_sinistre = $sinistre['id_sinistre'];
        $materiaux = $this->model->getInventaireMateriauxBySinistre($id_sinistre);
        $argent = $this->model->getInventaireArgentBySinistre($id_sinistre);

        Flight::json(['materiaux' => $materiaux, 'argent' => $argent], 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }

    public function apiGetTousMateriau()
    {
        $materiaux = $this->model->getTousMateriauxAvecCategorie();
        Flight::json($materiaux, 200, true, 'utf-8', JSON_PRETTY_PRINT);
    }
}
