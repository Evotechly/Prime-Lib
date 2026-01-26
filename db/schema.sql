-- Base de données : Gestion de Bibliothèque (simple niveau L2)
-- MySQL / MariaDB compatible

CREATE DATABASE IF NOT EXISTS bibliotheque;
USE bibliotheque;

-- =========================
-- TABLE : ADMIN
-- =========================
CREATE TABLE admin (
  id_admin INT AUTO_INCREMENT PRIMARY KEY,
  nom    VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email  VARCHAR(150) NOT NULL UNIQUE
);

-- =========================
-- TABLE : UTILISATEUR
-- =========================
CREATE TABLE utilisateur (
  id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
  nom    VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email  VARCHAR(150) NOT NULL UNIQUE,
  id_admin INT,  -- (optionnel) admin qui gère cet utilisateur

  CONSTRAINT fk_utilisateur_admin
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);

-- =========================
-- TABLE : LIVRE
-- =========================
CREATE TABLE livre (
  id_livre INT AUTO_INCREMENT PRIMARY KEY,
  titre  VARCHAR(200) NOT NULL,
  auteur VARCHAR(150) NOT NULL,
  genre  VARCHAR(80),
  annee_publication INT,
  etat_disponibilite VARCHAR(30) NOT NULL, -- ex: 'disponible', 'emprunte', 'perdu'

  id_admin INT, -- admin qui gère ce livre (0,1 côté livre ; 0,n côté admin)
  CONSTRAINT fk_livre_admin
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);

-- =========================
-- TABLE : EMPRUNT
-- (relation entre UTILISATEUR et LIVRE)
-- =========================
CREATE TABLE emprunt (
  id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
  date_emprunt DATE NOT NULL,
  date_retour_prevue DATE NOT NULL,
  date_retour_effective DATE NULL,

  id_utilisateur INT NOT NULL,
  id_livre INT NOT NULL,
  id_admin INT NULL, -- admin qui supervise l’emprunt (0,1 côté emprunt ; 0,n côté admin)

  CONSTRAINT fk_emprunt_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

  CONSTRAINT fk_emprunt_livre
    FOREIGN KEY (id_livre) REFERENCES livre(id_livre)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

  CONSTRAINT fk_emprunt_admin
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);

-- =========================
-- (Optionnel mais utile) Index pour accélérer les recherches
-- =========================
CREATE INDEX idx_emprunt_utilisateur ON emprunt(id_utilisateur);
CREATE INDEX idx_emprunt_livre ON emprunt(id_livre);
CREATE INDEX idx_emprunt_admin ON emprunt(id_admin);
