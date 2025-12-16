CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL,
    link VARCHAR(100) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    position INT NOT NULL,
    role_required VARCHAR(20) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Insert default menu items if table is empty
INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Utilisateurs', 'utilisateur.php', 'bx bx-user', 1, 'admin') AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'utilisateur.php') LIMIT 1;

INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Dashboard', 'dashboard.php', 'bx bx-grid-alt', 2, NULL) AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'dashboard.php') LIMIT 1;

INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Locations', 'location.php', 'bx bx-key', 3, NULL) AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'location.php') LIMIT 1;

INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Voitures', 'voiture.php', 'bx bx-car', 4, NULL) AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'voiture.php') LIMIT 1;

INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Clients', 'client.php', 'bx bx-user', 5, NULL) AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'client.php') LIMIT 1;

INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Chauffeurs', 'chauffeur.php', 'bx bx-id-card', 6, NULL) AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'chauffeur.php') LIMIT 1;

INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Missions', 'mission.php', 'bx bx-task', 7, NULL) AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'mission.php') LIMIT 1;

INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Paiements', 'paiements.php', 'bx bx-money', 8, NULL) AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'paiements.php') LIMIT 1;

INSERT INTO menu_items (label, link, icon, position, role_required)
SELECT * FROM (SELECT 'Dépenses', 'depenses.php', 'bx bx-receipt', 9, NULL) AS tmp
WHERE NOT EXISTS (SELECT * FROM menu_items WHERE link = 'depenses.php') LIMIT 1;
