USE bibliotheque;

START TRANSACTION;

-- =========================
-- ADMINS (2)
-- =========================
INSERT INTO admin (id_admin, nom, prenom, email, mot_de_passe) VALUES
(1, 'Biblio', 'Admin', 'admin@biblio.local', SHA2('Admin@12345', 256)),
(2, 'Biblio', 'Super', 'super@biblio.local', SHA2('Super@12345', 256));

-- =========================
-- SETTINGS (1)
-- =========================
INSERT INTO settings (id, library_name, contact_email, contact_phone, timezone, language, loan_days, late_fee, due_reminders, overdue_alerts, two_factor, session_timeout) VALUES
(1, 'Prime Lib Central', 'admin@primelib.com', '+1 (555) 248-9021', 'UTC-05:00', 'English', 14, 1.00, 'Email + SMS enabled', 'Escalation after 3 days', 'Required for staff accounts', '30 minutes of inactivity');

-- =========================
-- UTILISATEURS (15)
-- =========================
INSERT INTO utilisateur (id_utilisateur, nom, prenom, email, mot_de_passe, id_admin) VALUES
(1, 'Benali', 'Sami', 'sami.benali@mail.local', SHA2('User@1001',256), 1),
(2, 'Kaci', 'Nadia', 'nadia.kaci@mail.local', SHA2('User@1002',256), 1),
(3, 'Ait', 'Yacine', 'yacine.ait@mail.local', SHA2('User@1003',256), 1),
(4, 'Bouguerra', 'Ines', 'ines.bouguerra@mail.local', SHA2('User@1004',256), 2),
(5, 'Cherif', 'Amine', 'amine.cherif@mail.local', SHA2('User@1005',256), 2),
(6, 'Mansouri', 'Lina', 'lina.mansouri@mail.local', SHA2('User@1006',256), 1),
(7, 'Saidi', 'Hichem', 'hichem.saidi@mail.local', SHA2('User@1007',256), 1),
(8, 'Dahmani', 'Meriem', 'meriem.dahmani@mail.local', SHA2('User@1008',256), 2),
(9, 'Rahmani', 'Walid', 'walid.rahmani@mail.local', SHA2('User@1009',256), 1),
(10, 'Bensalem', 'Sara', 'sara.bensalem@mail.local', SHA2('User@1010',256), 2),
(11, 'Touati', 'Riad', 'riad.touati@mail.local', SHA2('User@1011',256), 1),
(12, 'Ouali', 'Aya', 'aya.ouali@mail.local', SHA2('User@1012',256), 2),
(13, 'Hamdi', 'Karim', 'karim.hamdi@mail.local', SHA2('User@1013',256), 1),
(14, 'Meziane', 'Imane', 'imane.meziane@mail.local', SHA2('User@1014',256), 2),
(15, 'Ziani', 'Nour', 'nour.ziani@mail.local', SHA2('User@1015',256), 1);

-- =========================
-- LIVRES (25)
-- etat_disponibilite mis "disponible" au départ
-- =========================
INSERT INTO livre (id_livre, titre, auteur, genre, annee_publication, etat_disponibilite, cover_url, id_admin) VALUES
(1, 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'Roman', 1943, 'disponible', NULL, 1),
(2, 'L’Étranger', 'Albert Camus', 'Roman', 1942, 'disponible', NULL, 1),
(3, 'Les Misérables', 'Victor Hugo', 'Classique', 1862, 'disponible', NULL, 2),
(4, 'Candide', 'Voltaire', 'Philosophie', 1759, 'disponible', NULL, 2),
(5, 'Madame Bovary', 'Gustave Flaubert', 'Roman', 1857, 'disponible', NULL, 1),
(6, 'Germinal', 'Émile Zola', 'Roman', 1885, 'disponible', NULL, 1),
(7, '1984', 'George Orwell', 'Dystopie', 1949, 'disponible', NULL, 2),
(8, 'La Peste', 'Albert Camus', 'Roman', 1947, 'disponible', NULL, 2),
(9, 'Fahrenheit 451', 'Ray Bradbury', 'Dystopie', 1953, 'disponible', NULL, 1),
(10, 'Les Fleurs du mal', 'Charles Baudelaire', 'Poésie', 1857, 'disponible', NULL, 1),
(11, 'Le Rouge et le Noir', 'Stendhal', 'Roman', 1830, 'disponible', NULL, 2),
(12, 'Bel-Ami', 'Guy de Maupassant', 'Roman', 1885, 'disponible', NULL, 2),
(13, 'Le Comte de Monte-Cristo', 'Alexandre Dumas', 'Aventure', 1844, 'disponible', NULL, 1),
(14, 'Notre-Dame de Paris', 'Victor Hugo', 'Classique', 1831, 'disponible', NULL, 1),
(15, 'Le Père Goriot', 'Honoré de Balzac', 'Roman', 1835, 'disponible', NULL, 2),
(16, 'L’Odyssée', 'Homère', 'Épopée', -800, 'disponible', NULL, 2),
(17, 'Crime et Châtiment', 'Fiodor Dostoïevski', 'Roman', 1866, 'disponible', NULL, 1),
(18, 'Le Procès', 'Franz Kafka', 'Roman', 1925, 'disponible', NULL, 1),
(19, 'Le Vieil Homme et la Mer', 'Ernest Hemingway', 'Roman', 1952, 'disponible', NULL, 2),
(20, 'Le Seigneur des Anneaux', 'J.R.R. Tolkien', 'Fantasy', 1954, 'disponible', NULL, 2),
(21, 'Harry Potter à l’école des sorciers', 'J.K. Rowling', 'Fantasy', 1997, 'disponible', NULL, 1),
(22, 'Dune', 'Frank Herbert', 'Science-fiction', 1965, 'disponible', NULL, 1),
(23, 'Sapiens', 'Yuval Noah Harari', 'Histoire', 2011, 'disponible', NULL, 2),
(24, 'Clean Code', 'Robert C. Martin', 'Informatique', 2008, 'disponible', NULL, 2),
(25, 'Introduction aux bases de données', 'Auteur L2', 'Informatique', 2020, 'disponible', NULL, 1);

-- =========================
-- EMPRUNTS (30)
-- Certains rendus (date_retour_effective), d'autres non (NULL)
-- =========================
INSERT INTO emprunt
(id_emprunt, date_emprunt, date_retour_prevue, date_retour_effective, id_utilisateur, id_livre, id_admin)
VALUES
(1,  '2026-01-02', '2026-01-09', '2026-01-08', 1,  1,  1),
(2,  '2026-01-03', '2026-01-10', NULL,        2,  2,  1),
(3,  '2026-01-03', '2026-01-10', '2026-01-10', 3,  3,  2),
(4,  '2026-01-04', '2026-01-11', NULL,        4,  4,  2),
(5,  '2026-01-04', '2026-01-11', '2026-01-09', 5,  5,  1),
(6,  '2026-01-05', '2026-01-12', NULL,        6,  6,  1),
(7,  '2026-01-05', '2026-01-12', '2026-01-12', 7,  7,  2),
(8,  '2026-01-06', '2026-01-13', NULL,        8,  8,  2),
(9,  '2026-01-06', '2026-01-13', '2026-01-11', 9,  9,  1),
(10, '2026-01-07', '2026-01-14', NULL,        10, 10, 2),
(11, '2026-01-07', '2026-01-14', '2026-01-14', 11, 11, 2),
(12, '2026-01-08', '2026-01-15', NULL,        12, 12, 2),
(13, '2026-01-08', '2026-01-15', '2026-01-13', 13, 13, 1),
(14, '2026-01-09', '2026-01-16', NULL,        14, 14, 1),
(15, '2026-01-09', '2026-01-16', '2026-01-16', 15, 15, 2),

(16, '2026-01-10', '2026-01-17', NULL,        1,  16, 2),
(17, '2026-01-10', '2026-01-17', '2026-01-15', 2,  17, 1),
(18, '2026-01-11', '2026-01-18', NULL,        3,  18, 1),
(19, '2026-01-11', '2026-01-18', '2026-01-18', 4,  19, 2),
(20, '2026-01-12', '2026-01-19', NULL,        5,  20, 2),
(21, '2026-01-12', '2026-01-19', '2026-01-19', 6,  21, 1),
(22, '2026-01-13', '2026-01-20', NULL,        7,  22, 1),
(23, '2026-01-13', '2026-01-20', '2026-01-17', 8,  23, 2),
(24, '2026-01-14', '2026-01-21', NULL,        9,  24, 2),
(25, '2026-01-14', '2026-01-21', '2026-01-21', 10, 25, 1),

-- 5 emprunts supplémentaires (re-emprunts sur des livres déjà rendus)
(26, '2026-01-15', '2026-01-22', NULL,        11, 1,  1),
(27, '2026-01-16', '2026-01-23', '2026-01-22', 12, 3,  2),
(28, '2026-01-17', '2026-01-24', NULL,        13, 5,  1),
(29, '2026-01-18', '2026-01-25', '2026-01-25', 14, 7,  2),
(30, '2026-01-19', '2026-01-26', NULL,        15, 9,  1);

-- =========================
-- Mise à jour des états des livres selon emprunts en cours
-- (si au moins un emprunt non rendu existe -> 'emprunte', sinon 'disponible')
-- =========================
UPDATE livre l
SET l.etat_disponibilite = 'disponible';

UPDATE livre l
SET l.etat_disponibilite = 'emprunte'
WHERE EXISTS (
  SELECT 1
  FROM emprunt e
  WHERE e.id_livre = l.id_livre
    AND e.date_retour_effective IS NULL
);

COMMIT;

-- Vérifications rapides
SELECT id_admin, nom, prenom, email FROM admin;
SELECT COUNT(*) AS nb_utilisateurs FROM utilisateur;
SELECT COUNT(*) AS nb_livres FROM livre;
SELECT COUNT(*) AS nb_emprunts FROM emprunt;
SELECT etat_disponibilite, COUNT(*) AS nb FROM livre GROUP BY etat_disponibilite;

