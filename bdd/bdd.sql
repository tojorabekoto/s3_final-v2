CREATE DATABASE bngrc;
USE bngrc;

CREATE TABLE region (
    id_region INT AUTO_INCREMENT PRIMARY KEY,
    nom_region VARCHAR(100) NOT NULL
);

CREATE TABLE ville (
    id_ville INT AUTO_INCREMENT PRIMARY KEY,
    id_region INT NOT NULL,
    nom_ville VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_region) REFERENCES region(id_region)
    ON DELETE CASCADE
);

CREATE TABLE sinistre (
    id_sinistre INT AUTO_INCREMENT PRIMARY KEY,
    id_ville INT NOT NULL,
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville)
    ON DELETE CASCADE
);

CREATE TABLE categorie_materiaux (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL
);

CREATE TABLE materiaux (
    id_materiau INT AUTO_INCREMENT PRIMARY KEY,
    nom_materiau VARCHAR(100) NOT NULL UNIQUE,
    id_categorie INT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categorie) REFERENCES categorie_materiaux(id_categorie)
    ON DELETE CASCADE
);

CREATE TABLE besoin_materiaux (
    id_besoin INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    id_materiau INT NOT NULL,
    quantite DECIMAL(10,2),
    unite VARCHAR(50),
    prix_unitaire DECIMAL(10,2),
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE,
    FOREIGN KEY (id_materiau) REFERENCES materiaux(id_materiau)
    ON DELETE CASCADE
);

CREATE TABLE besoin_argent (
    id_besoin_argent INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    montant_necessaire DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE
);

CREATE TABLE don_materiaux (
    id_don INT AUTO_INCREMENT PRIMARY KEY,
    id_besoin INT NOT NULL,
    quantite_donnee DECIMAL(10,2),
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_besoin) REFERENCES besoin_materiaux(id_besoin)
    ON DELETE CASCADE
);

CREATE TABLE don_argent (
    id_don INT AUTO_INCREMENT PRIMARY KEY,
    id_besoin_argent INT NOT NULL,
    montant_donne DECIMAL(15,2),
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_besoin_argent) REFERENCES besoin_argent(id_besoin_argent)
    ON DELETE CASCADE
);

-- ============================================================================
-- TABLE : inventaire_materiaux
-- Description : Stock des dons matériaux reçus pour chaque sinistre
-- ============================================================================
CREATE TABLE inventaire_materiaux (
    id_inventaire INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    id_besoin INT NOT NULL,
    quantite_disponible DECIMAL(10,2),
    date_reception TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE,
    FOREIGN KEY (id_besoin) REFERENCES besoin_materiaux(id_besoin)
    ON DELETE CASCADE
);

-- ============================================================================
-- TABLE : inventaire_argent
-- Description : Stock d'argent des dons reçus pour chaque sinistre
-- ============================================================================
CREATE TABLE inventaire_argent (
    id_inventaire_argent INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    montant_disponible DECIMAL(15,2),
    date_reception TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE,
    UNIQUE KEY unique_sinistre (id_sinistre)
);

CREATE TABLE achat_materiaux (
    id_achat INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    id_besoin INT NOT NULL,
    quantite_achetee DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    prix_total DECIMAL(15,2) NOT NULL,
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE,
    FOREIGN KEY (id_besoin) REFERENCES besoin_materiaux(id_besoin)
    ON DELETE CASCADE
);

-- ============================================================================
-- TABLE : historique_dons_materiaux
-- Description : Historique complet des dons matériaux reçus
-- ============================================================================
CREATE TABLE historique_dons_materiaux (
    id_historique INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    id_ville INT NOT NULL,
    id_materiau INT NOT NULL,
    quantite_donnee DECIMAL(10,2) NOT NULL,
    unite VARCHAR(50),
    descriptif TEXT,
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE,
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville)
    ON DELETE CASCADE,
    FOREIGN KEY (id_materiau) REFERENCES materiaux(id_materiau)
    ON DELETE CASCADE,
    INDEX idx_sinistre (id_sinistre),
    INDEX idx_ville (id_ville),
    INDEX idx_materiau (id_materiau),
    INDEX idx_date_don (date_don)
);

-- ============================================================================
-- TABLE : historique_dons_argent
-- Description : Historique complet des dons argent reçus
-- ============================================================================
CREATE TABLE historique_dons_argent (
    id_historique INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    id_ville INT NOT NULL,
    montant_donne DECIMAL(15,2) NOT NULL,
    descriptif TEXT,
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE,
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville)
    ON DELETE CASCADE,
    INDEX idx_sinistre (id_sinistre),
    INDEX idx_ville (id_ville),
    INDEX idx_date_don (date_don)
);

-- ============================================================================
-- TABLE : historique_used_materiaux
-- Description : Historique des matériaux utilisés pour les achats
-- ============================================================================
-- CREATE TABLE historique_used_materiaux (
--     id_historique INT AUTO_INCREMENT PRIMARY KEY,
--     id_sinistre INT NOT NULL,
--     id_besoin INT NOT NULL,
--     id_materiau INT NOT NULL,
--     quantite_utilisee DECIMAL(10,2) NOT NULL,
--     prix_unitaire DECIMAL(10,2) NOT NULL,
--     prix_total DECIMAL(15,2),
--     date_utilisation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
--     ON DELETE CASCADE,
--     FOREIGN KEY (id_besoin) REFERENCES besoin_materiaux(id_besoin)
--     ON DELETE CASCADE,
--     FOREIGN KEY (id_materiau) REFERENCES materiaux(id_materiau)
--     ON DELETE CASCADE,
--     INDEX idx_sinistre (id_sinistre),
--     INDEX idx_materiau (id_materiau),
--     INDEX idx_date_utilisation (date_utilisation)
-- );

-- ============================================================================
-- TABLE : historique_used_argent
-- Description : Historique de l'argent utilisé pour les achats
-- ============================================================================
CREATE TABLE historique_used_argent (
    id_historique INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    id_besoin INT NOT NULL,
    montant_utilise DECIMAL(15,2) NOT NULL,
    quantite_achetee DECIMAL(10,2),
    descriptif TEXT,
    date_utilisation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE,
    FOREIGN KEY (id_besoin) REFERENCES besoin_materiaux(id_besoin)
    ON DELETE CASCADE,
    INDEX idx_sinistre (id_sinistre),
    INDEX idx_date_utilisation (date_utilisation)
);

-- ============================================================================
-- TABLE : besoin_total
-- Description : Résumé des besoins totaux par sinistre
-- ============================================================================
CREATE TABLE besoin_total (
    id_besoin_total INT AUTO_INCREMENT PRIMARY KEY,
    id_sinistre INT NOT NULL,
    id_ville INT NOT NULL,
    total_besoin_materiau DECIMAL(15,2) DEFAULT 0,
    total_besoin_argent DECIMAL(15,2) DEFAULT 0,
    date_calcul TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
    ON DELETE CASCADE,
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville)
    ON DELETE CASCADE,
    UNIQUE KEY unique_sinistre (id_sinistre),
    INDEX idx_ville (id_ville)
);

