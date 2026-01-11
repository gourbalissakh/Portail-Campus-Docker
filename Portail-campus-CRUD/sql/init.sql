-- Script d'initialisation de la base de données Portail Campus

-- Table des étudiants
CREATE TABLE IF NOT EXISTS etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telephone VARCHAR(20),
    filiere VARCHAR(50),
    niveau ENUM('L1', 'L2', 'L3', 'M1', 'M2') DEFAULT 'L1',
    date_naissance DATE,
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nom (nom),
    INDEX idx_filiere (filiere),
    INDEX idx_niveau (niveau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des administrateurs
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin par défaut (mot de passe: admin123)
INSERT INTO admins (username, password, nom, prenom, email) VALUES
('admin', '$2y$10$xAr1voLV3/9EtHylCwYBceTNRpSc3psFWsGw.kQC3Mb8WocxeeF8u', 'GOURBAL', 'MAHAMAT', 'admin@campus.sn')
ON DUPLICATE KEY UPDATE username = username;

-- Données de test
INSERT INTO etudiants (matricule, nom, prenom, email, telephone, filiere, niveau, date_naissance, adresse) VALUES
('L3GLAR2026001', 'Diop', 'Amadou', 'amadou.diop@campus.sn', '771234567', 'GLAR', 'L3', '2003-05-15', 'Dakar'),
('L3GLAR2026002', 'Ndiaye', 'Fatou', 'fatou.ndiaye@campus.sn', '771234568', 'GLAR', 'L3', '2002-11-20', 'Thiès'),
('L3INFO2026001', 'Sow', 'Moussa', 'moussa.sow@campus.sn', '771234569', 'Informatique', 'L3', '2003-08-10', 'Saint-Louis'),
('L2MATH2026001', 'Kane', 'Aissatou', 'aissatou.kane@campus.sn', '771234570', 'Mathématiques', 'L2', '2004-03-25', 'Ziguinchor'),
('M1GLAR2026001', 'Fall', 'Ibrahim', 'ibrahim.fall@campus.sn', '771234571', 'GLAR', 'M1', '2001-07-18', 'Kaolack'),
('L1INFO2026001', 'Ba', 'Mariama', 'mariama.ba@campus.sn', '771234572', 'Informatique', 'L1', '2005-01-12', 'Dakar'),
('L2GLAR2026001', 'Sy', 'Ousmane', 'ousmane.sy@campus.sn', '771234573', 'GLAR', 'L2', '2004-09-08', 'Rufisque'),
('M2INFO2026001', 'Diallo', 'Aminata', 'aminata.diallo@campus.sn', '771234574', 'Informatique', 'M2', '2000-06-22', 'Mbour'),
('L3MATH2026001', 'Gueye', 'Cheikh', 'cheikh.gueye@campus.sn', '771234575', 'Mathématiques', 'L3', '2002-12-30', 'Louga'),
('L1GLAR2026001', 'Sarr', 'Khady', 'khady.sarr@campus.sn', '771234576', 'GLAR', 'L1', '2005-04-17', 'Tambacounda')
ON DUPLICATE KEY UPDATE matricule = matricule;
