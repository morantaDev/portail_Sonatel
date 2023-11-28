-- Création de la base de données
DROP DATABASE IF EXISTS facturations;
CREATE DATABASE facturations;
\c facturations; -- Changer de base de données vers "facturation"

-- Création de la table Client
CREATE TABLE Client (
    id_client SERIAL PRIMARY KEY,
    NomClient VARCHAR(200),
    Prenom_client VARCHAR(200)
);

-- Création de la table Catalogue_aggregateur
CREATE TABLE Catalogue_aggregateur (
    id_catalogue_aggregat SERIAL PRIMARY KEY,
    Paliers VARCHAR(200),
    Tarif_on_net VARCHAR(200),
    Tarif_off_net VARCHAR(200),
    Tarif_moyene VARCHAR(200)
);

-- Création de la table Type_Client
CREATE TABLE Type_Client (
    id_Type_Client SERIAL PRIMARY KEY,
    describ VARCHAR(200)
);

-- Création de la table Utilisateur
CREATE TABLE Utilisateur (
    id_utilisateur SERIAL PRIMARY KEY,
    email VARCHAR(250),
    mot_passe VARCHAR(250)
);



-- Création de la table Offre_Sur_Mesure
CREATE TABLE Offre_Sur_Mesure (
    id_OMS SERIAL PRIMARY KEY,
    description VARCHAR(200),
    Tarif_associe INTEGER,
    montantTotal INTEGER,
    bagageInclus BOOLEAN,
    nombre_SMS INTEGER,
    id_catalogue INTEGER,
    FOREIGN KEY (id_catalogue) REFERENCES Catalogue(id_catalogue)
);

-- Création de la table Catalogue
CREATE TABLE Catalogue (
    id_catalogue SERIAL PRIMARY KEY,
    indicateur_OSM BOOLEAN,
    code VARCHAR(200),
    Tarif REAL,
    KTCK VARCHAR(200),
    id_Type_Client INTEGER,
    FOREIGN KEY (id_Type_Client) REFERENCES Type_Client(id_Type_Client)
);

-- Création de la table Biling_Aggregateur
CREATE TABLE Biling_Aggregateur (
    id_Biling_Aggregat SERIAL PRIMARY KEY,
    id_utilisateur INTEGER,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Création de la table Biling
CREATE TABLE Biling (
    id_Biling SERIAL PRIMARY KEY,
    Nombre_SMS_Mois INTEGER,
    libelle VARCHAR(200),
    destination VARCHAR(200),
    mois_fac DATE,
    id_utilisateur INTEGER,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Création de la table Client_Biling
CREATE TABLE Client_Biling (
    id_Client_Biling SERIAL PRIMARY KEY,
    id_client INTEGER,
    id_Biling INTEGER,
    FOREIGN KEY (id_client) REFERENCES Client(id_client),
    FOREIGN KEY (id_Biling) REFERENCES Biling(id_Biling)
);


-- Création de la table Biling_Aggregateur_Client
CREATE TABLE Biling_Aggregateur_Client (
    id_BAC SERIAL PRIMARY KEY,
    id_Biling_Aggregat INTEGER,
    id_client INTEGER,
    FOREIGN KEY (id_Biling_Aggregat) REFERENCES Biling_Aggregateur(id_Biling_Aggregat),
    FOREIGN KEY (id_client) REFERENCES Client(id_client)
);

-- Création de la table ND_Numero
CREATE TABLE ND_Numero (
    id_numero SERIAL PRIMARY KEY,
    id_client INTEGER,
    id_OMS INTEGER,
    id_catalogue_aggregat INTEGER,
    FOREIGN KEY (id_client) REFERENCES Client(id_client),
    FOREIGN KEY (id_OMS) REFERENCES Offre_Sur_Mesure(id_OMS),
    FOREIGN KEY (id_catalogue_aggregat) REFERENCES Catalogue_aggregateur(id_catalogue_aggregat)
);


