INSERT INTO region (nom_region) VALUES
('Analamanga'),
('Atsinanana'),
('Boeny'),
('Haute Matsiatra'),
('Alaotra-Mangoro'),
('Sava');

INSERT INTO ville (id_region, nom_ville) VALUES
(1, 'Antananarivo'),
(2, 'Toamasina'),
(3, 'Mahajanga'),
(4, 'Fianarantsoa'),
(5, 'Ambatondrazaka'),
(6, 'Sambava');

INSERT INTO sinistre (id_ville) VALUES
(1),
(2),
(3),
(4),
(5),
(6);

INSERT INTO categorie_materiaux (nom) VALUES
('Nourriture'),
('Materiaux de construction');

INSERT INTO materiaux (nom_materiau, id_categorie) VALUES
('Riz', 1),
('Huile', 1),
('Tôle', 2),
('Clous', 2),
('Eau potable', 1),
('Bois de construction', 2),
('Couvertures', 1),
('Briques', 2),
('Farine', 1),
('Ciment', 2),
('Sucre', 1),
('Tentes', 2),
('Lait en poudre', 1),
('Groupes électrogènes', 2);

-- Sinistre 1 (Antananarivo)
INSERT INTO besoin_materiaux (id_sinistre, id_materiau, quantite, unite, prix_unitaire) VALUES
(1, 1, 1000, 'kg', 5000),
(1, 2, 500, 'litres', 12000),
(1, 3, 300, 'pieces', 25000),
(1, 4, 50, 'kg', 15000);

-- Sinistre 2 (Toamasina)
INSERT INTO besoin_materiaux (id_sinistre, id_materiau, quantite, unite, prix_unitaire) VALUES
(2, 5, 2000, 'litres', 3000),
(2, 6, 150, 'pieces', 40000);

-- Sinistre 3 (Mahajanga)
INSERT INTO besoin_materiaux (id_sinistre, id_materiau, quantite, unite, prix_unitaire) VALUES
(3, 7, 300, 'unites', 20000),
(3, 8, 1000, 'unites', 8000);

-- Sinistre 4 (Fianarantsoa)
INSERT INTO besoin_materiaux (id_sinistre, id_materiau, quantite, unite, prix_unitaire) VALUES
(4, 9, 800, 'kg', 8000),
(4, 10, 200, 'sacs', 35000);

-- Sinistre 5 (Ambatondrazaka)
INSERT INTO besoin_materiaux (id_sinistre, id_materiau, quantite, unite, prix_unitaire) VALUES
(5, 11, 400, 'kg', 10000),
(5, 12, 100, 'unites', 50000);

-- Sinistre 6 (Sambava)
INSERT INTO besoin_materiaux (id_sinistre, id_materiau, quantite, unite, prix_unitaire) VALUES
(6, 13, 300, 'kg', 25000),
(6, 14, 10, 'unites', 800000);


INSERT INTO besoin_argent (id_sinistre, montant_necessaire) VALUES
(1, 10000000),
(2, 25000000),
(3, 5000000),
(4, 8000000),
(5, 12000000),
(6, 20000000);

INSERT INTO don_materiaux (id_besoin, quantite_donnee) VALUES
(1, 400),
(3, 100),
(5, 500),
(8, 300),
(10, 50);

INSERT INTO don_argent (id_besoin_argent, montant_donne) VALUES
(1, 3000000),
(2, 10000000),
(3, 2000000),
(5, 5000000);

-- ============================================================================
-- Données inventaire_materiaux (stocks matériaux disponibles par sinistre)
-- ============================================================================
INSERT INTO inventaire_materiaux (id_sinistre, id_besoin, quantite_disponible) VALUES
-- Sinistre 1 (Antananarivo)
(1, 1, 500),       -- Riz: 500 disponible
(1, 2, 250),       -- Huile: 250 disponible
(1, 3, 100),       -- Tôle: 100 disponible
(1, 4, 30),        -- Clous: 30 disponible

-- Sinistre 2 (Toamasina)
(2, 5, 1000),      -- Eau: 1000 disponible
(2, 6, 80),        -- Bois: 80 disponible

-- Sinistre 3 (Mahajanga)
(3, 7, 170),       -- Couvertures: 170 disponible
(3, 8, 600),       -- Briques: 600 disponible

-- Sinistre 4 (Fianarantsoa)
(4, 9, 600),       -- Farine: 600 disponible
(4, 10, 120),      -- Ciment: 120 disponible

-- Sinistre 5 (Ambatondrazaka)
(5, 11, 300),      -- Sucre: 300 disponible
(5, 12, 60),       -- Tentes: 60 disponible

-- Sinistre 6 (Sambava)
(6, 13, 210),      -- Lait: 210 disponible
(6, 14, 6);        -- Groupes électrogènes: 6 disponible

-- ============================================================================
-- Données inventaire_argent (fonds disponibles par sinistre)
-- ============================================================================
INSERT INTO inventaire_argent (id_sinistre, montant_disponible) VALUES
(1, 4500000),        -- Sinistre 1: 4.5M disponible
(2, 13000000),       -- Sinistre 2: 13M disponible
(3, 2700000),        -- Sinistre 3: 2.7M disponible
(4, 3600000),        -- Sinistre 4: 3.6M disponible
(5, 5400000),        -- Sinistre 5: 5.4M disponible
(6, 9000000);        -- Sinistre 6: 9M disponible
