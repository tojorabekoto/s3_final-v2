<?php

namespace app\model;

use PDO;

class BngrcModel {

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function getRegions() {
        $stmt = $this->db->query("SELECT * FROM region");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVilles() {
        $stmt = $this->db->query("SELECT * FROM ville");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVillesByRegion($id_region) {
        $stmt = $this->db->prepare("SELECT * FROM ville WHERE id_region = ?");
        $stmt->execute([$id_region]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSinistres() {
        $stmt = $this->db->query("SELECT * FROM sinistre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSinistresByVille($id_ville) {
        $stmt = $this->db->prepare("SELECT * FROM sinistre WHERE id_ville = ?");
        $stmt->execute([$id_ville]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinMateriaux() {
        $stmt = $this->db->query("SELECT * FROM besoin_materiaux");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinArgent() {
        $stmt = $this->db->query("SELECT * FROM besoin_argent");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonMateriauxByBesoin($id_besoin) {
        $stmt = $this->db->prepare("SELECT * FROM don_materiaux WHERE id_besoin = ?");
        $stmt->execute([$id_besoin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonArgentByBesoin($id_besoin_argent) {
        $stmt = $this->db->prepare("SELECT * FROM don_argent WHERE id_besoin_argent = ?");
        $stmt->execute([$id_besoin_argent]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesMateriau() {
        $stmt = $this->db->query("SELECT * FROM categorie_materiaux");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVilleById($id_ville) {
        $stmt = $this->db->prepare("SELECT * FROM ville WHERE id_ville = ?");
        $stmt->execute([$id_ville]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBesoinsMateriauxByVille($id_ville) {
        $stmt = $this->db->prepare(
            "SELECT bm.id_besoin, cm.nom AS categorie, m.nom_materiau, bm.quantite, bm.unite, bm.prix_unitaire
             FROM besoin_materiaux bm
             JOIN sinistre s ON s.id_sinistre = bm.id_sinistre
             JOIN materiaux m ON m.id_materiau = bm.id_materiau
             JOIN categorie_materiaux cm ON cm.id_categorie = m.id_categorie
             WHERE s.id_ville = ?
             ORDER BY bm.id_besoin"
        );
        $stmt->execute([$id_ville]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonsMateriauxByVille($id_ville) {
        $stmt = $this->db->prepare(
            "SELECT bm.id_besoin, cm.nom AS categorie, m.nom_materiau,
                    COALESCE(SUM(dm.quantite_donnee), 0) AS quantite_donnee, bm.unite
             FROM besoin_materiaux bm
             JOIN sinistre s ON s.id_sinistre = bm.id_sinistre
             JOIN materiaux m ON m.id_materiau = bm.id_materiau
             JOIN categorie_materiaux cm ON cm.id_categorie = m.id_categorie
             LEFT JOIN don_materiaux dm ON dm.id_besoin = bm.id_besoin
             WHERE s.id_ville = ?
             GROUP BY bm.id_besoin, cm.nom, m.nom_materiau, bm.unite
             ORDER BY bm.id_besoin"
        );
        $stmt->execute([$id_ville]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRestantMateriauxByVille($id_ville) {
        $stmt = $this->db->prepare(
            "SELECT bm.id_besoin, cm.nom AS categorie, m.nom_materiau,
                    (bm.quantite 
                     - COALESCE((SELECT SUM(dm.quantite_donnee) FROM don_materiaux dm WHERE dm.id_besoin = bm.id_besoin), 0)
                     - COALESCE((SELECT SUM(am.quantite_achetee) FROM achat_materiaux am WHERE am.id_besoin = bm.id_besoin AND am.id_sinistre = bm.id_sinistre), 0)
                    ) AS quantite_restante, bm.unite
             FROM besoin_materiaux bm
             JOIN sinistre s ON s.id_sinistre = bm.id_sinistre
             JOIN materiaux m ON m.id_materiau = bm.id_materiau
             JOIN categorie_materiaux cm ON cm.id_categorie = m.id_categorie
             WHERE s.id_ville = ?
             ORDER BY bm.id_besoin"
        );
        $stmt->execute([$id_ville]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinsArgentByVille($id_ville) {
        $stmt = $this->db->prepare(
            "SELECT ba.id_besoin_argent, ba.montant_necessaire
             FROM besoin_argent ba
             JOIN sinistre s ON s.id_sinistre = ba.id_sinistre
             WHERE s.id_ville = ?
             ORDER BY ba.id_besoin_argent"
        );
        $stmt->execute([$id_ville]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonsArgentByVille($id_ville) {
        $stmt = $this->db->prepare(
            "SELECT ba.id_besoin_argent,
                    COALESCE(SUM(da.montant_donne), 0) AS montant_donne
             FROM besoin_argent ba
             JOIN sinistre s ON s.id_sinistre = ba.id_sinistre
             LEFT JOIN don_argent da ON da.id_besoin_argent = ba.id_besoin_argent
             WHERE s.id_ville = ?
             GROUP BY ba.id_besoin_argent
             ORDER BY ba.id_besoin_argent"
        );
        $stmt->execute([$id_ville]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRestantArgentByVille($id_ville) {
        $stmt = $this->db->prepare(
            "SELECT ba.id_besoin_argent,
                    (ba.montant_necessaire - COALESCE(SUM(da.montant_donne), 0)) AS montant_restant
             FROM besoin_argent ba
             JOIN sinistre s ON s.id_sinistre = ba.id_sinistre
             LEFT JOIN don_argent da ON da.id_besoin_argent = ba.id_besoin_argent
             WHERE s.id_ville = ?
             GROUP BY ba.id_besoin_argent, ba.montant_necessaire
             ORDER BY ba.id_besoin_argent"
        );
        $stmt->execute([$id_ville]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinsMateriauxForForm() {
        $stmt = $this->db->query(
            "SELECT bm.id_besoin, m.nom_materiau, bm.quantite, bm.unite, s.id_ville, cm.nom AS categorie, bm.prix_unitaire
             FROM besoin_materiaux bm
             JOIN sinistre s ON s.id_sinistre = bm.id_sinistre
             JOIN materiaux m ON m.id_materiau = bm.id_materiau
             JOIN categorie_materiaux cm ON cm.id_categorie = m.id_categorie
             ORDER BY bm.id_besoin"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinsArgentForForm() {
        $stmt = $this->db->query(
            "SELECT ba.id_besoin_argent, ba.montant_necessaire, s.id_ville
             FROM besoin_argent ba
             JOIN sinistre s ON s.id_sinistre = ba.id_sinistre
             ORDER BY ba.id_besoin_argent"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertDonMateriaux($id_besoin, $quantite) {
        $stmt = $this->db->prepare(
            "INSERT INTO don_materiaux (id_besoin, quantite_donnee) VALUES (?, ?)"
        );
        return $stmt->execute([$id_besoin, $quantite]);
    }

    public function insertDonArgent($id_besoin_argent, $montant) {
        $stmt = $this->db->prepare(
            "INSERT INTO don_argent (id_besoin_argent, montant_donne) VALUES (?, ?)"
        );
        return $stmt->execute([$id_besoin_argent, $montant]);
    }

    public function getRestantMateriauxByBesoin($id_besoin) {
        $stmt = $this->db->prepare(
            "SELECT (bm.quantite - COALESCE(SUM(dm.quantite_donnee), 0)) AS quantite_restante
             FROM besoin_materiaux bm
             LEFT JOIN don_materiaux dm ON dm.id_besoin = bm.id_besoin
             WHERE bm.id_besoin = ?
             GROUP BY bm.quantite"
        );
        $stmt->execute([$id_besoin]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float)$row['quantite_restante'] : 0.0;
    }

    public function getRestantArgentByBesoin($id_besoin_argent) {
        $stmt = $this->db->prepare(
            "SELECT (ba.montant_necessaire - COALESCE(SUM(da.montant_donne), 0)) AS montant_restant
             FROM besoin_argent ba
             LEFT JOIN don_argent da ON da.id_besoin_argent = ba.id_besoin_argent
             WHERE ba.id_besoin_argent = ?
             GROUP BY ba.montant_necessaire"
        );
        $stmt->execute([$id_besoin_argent]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float)$row['montant_restant'] : 0.0;
    }

    // ────────────────────────────────────────────────
    // Inventaire et Achat de matériaux
    // ────────────────────────────────────────────────
    public function getTousMateriauxAvecCategorie() {
        $stmt = $this->db->query(
            "SELECT m.id_materiau, m.nom_materiau, cm.id_categorie, cm.nom AS categorie
             FROM materiaux m
             JOIN categorie_materiaux cm ON cm.id_categorie = m.id_categorie
             ORDER BY cm.nom, m.nom_materiau"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInventaireMateriauxBySinistre($id_sinistre) {
        $stmt = $this->db->prepare(
            "SELECT im.id_inventaire, im.id_besoin, m.nom_materiau, im.quantite_disponible, bm.unite, bm.prix_unitaire
             FROM inventaire_materiaux im
             JOIN besoin_materiaux bm ON bm.id_besoin = im.id_besoin
             JOIN materiaux m ON m.id_materiau = bm.id_materiau
             WHERE im.id_sinistre = ?
             ORDER BY m.nom_materiau"
        );
        $stmt->execute([$id_sinistre]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInventaireArgentBySinistre($id_sinistre) {
        $stmt = $this->db->prepare(
            "SELECT id_inventaire_argent, montant_disponible
             FROM inventaire_argent
             WHERE id_sinistre = ?"
        );
        $stmt->execute([$id_sinistre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertAchatMateriaux($id_sinistre, $id_besoin, $quantite_achetee, $prix_unitaire, $prix_total) {
        $stmt = $this->db->prepare(
            "INSERT INTO achat_materiaux (id_sinistre, id_besoin, quantite_achetee, prix_unitaire, prix_total)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$id_sinistre, $id_besoin, $quantite_achetee, $prix_unitaire, $prix_total]);
    }

    public function getAchatsBysinistre($id_sinistre) {
        $stmt = $this->db->prepare(
            "SELECT am.id_achat, am.quantite_achetee, am.prix_unitaire, am.prix_total, am.date_achat,
                    bm.id_materiau, m.nom_materiau, cm.nom AS categorie, bm.unite
             FROM achat_materiaux am
             JOIN besoin_materiaux bm ON bm.id_besoin = am.id_besoin
             JOIN materiaux m ON m.id_materiau = bm.id_materiau
             JOIN categorie_materiaux cm ON cm.id_categorie = m.id_categorie
             WHERE am.id_sinistre = ?
             ORDER BY am.date_achat DESC"
        );
        $stmt->execute([$id_sinistre]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reduceBesoinQuantite($id_besoin, $quantite_reduite) {
        $stmt = $this->db->prepare(
            "UPDATE besoin_materiaux
             SET quantite = quantite - ?
             WHERE id_besoin = ?"
        );
        return $stmt->execute([$quantite_reduite, $id_besoin]);
    }

    public function updateInventaireArgentApresAchat($id_sinistre, $montant_utilise) {
        $stmt = $this->db->prepare(
            "UPDATE inventaire_argent
             SET montant_disponible = montant_disponible - ?
             WHERE id_sinistre = ?"
        );
        return $stmt->execute([$montant_utilise, $id_sinistre]);
    }

    public function updateInventaireMateriauxApresAchat($id_inventaire, $quantite_utilisee) {
        $stmt = $this->db->prepare(
            "UPDATE inventaire_materiaux
             SET quantite_utilisee = quantite_utilisee + ?,
                 quantite_disponible = quantite_recue - (quantite_utilisee + ?)
             WHERE id_inventaire = ?"
        );
        return $stmt->execute([$quantite_utilisee, $quantite_utilisee, $id_inventaire]);
    }

    public function getSinistreByVille($id_ville) {
        $stmt = $this->db->prepare(
            "SELECT id_sinistre FROM sinistre WHERE id_ville = ? LIMIT 1"
        );
        $stmt->execute([$id_ville]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBesoinById($id_besoin) {
        $stmt = $this->db->prepare(
            "SELECT bm.id_besoin, bm.id_sinistre, bm.id_materiau, bm.quantite, bm.unite, bm.prix_unitaire,
                    m.nom_materiau, cm.nom AS categorie
             FROM besoin_materiaux bm
             JOIN materiaux m ON m.id_materiau = bm.id_materiau
             JOIN categorie_materiaux cm ON cm.id_categorie = m.id_categorie
             WHERE bm.id_besoin = ?"
        );
        $stmt->execute([$id_besoin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
