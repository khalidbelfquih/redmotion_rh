ALTER TABLE employes
ADD COLUMN situation_familiale VARCHAR(50) DEFAULT 'Célibataire',
ADD COLUMN nombre_enfants INT DEFAULT 0,
ADD COLUMN type_contrat VARCHAR(50) DEFAULT 'CDI',
ADD COLUMN rib VARCHAR(50);
