-- Base de donnees : Gestion de Bibliotheque (simple niveau L2)
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
  email  VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL
);

-- =========================
-- TABLE : UTILISATEUR
-- =========================
CREATE TABLE utilisateur (
  id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
  nom    VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email  VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  id_admin INT,  -- (optionnel) admin qui gere cet utilisateur

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

  id_admin INT, -- admin qui gere ce livre (0,1 cote livre ; 0,n cote admin)
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
  id_admin INT NULL, -- admin qui supervise l'emprunt (0,1 cote emprunt ; 0,n cote admin)

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
-- (Optionnel mais utile) Index pour accelerer les recherches
-- =========================
CREATE INDEX idx_emprunt_utilisateur ON emprunt(id_utilisateur);
CREATE INDEX idx_emprunt_livre ON emprunt(id_livre);
CREATE INDEX idx_emprunt_admin ON emprunt(id_admin);