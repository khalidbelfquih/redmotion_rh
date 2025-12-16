<?php
require_once __DIR__ . '/../config/connexion.php';

function getHRStats() {
    global $connexion;
    
    $stats = [];
    
    // 1. Total Employees (Active)
    $sql = "SELECT COUNT(*) FROM employes WHERE statut = 'Actif'";
    $stats['total_active'] = $connexion->query($sql)->fetchColumn() ?: 0;
    
    // 2. On Leave Today
    $sql = "SELECT COUNT(*) FROM conges 
            WHERE statut = 'Approuvé' 
            AND CURDATE() BETWEEN date_debut AND date_fin";
    $stats['on_leave'] = $connexion->query($sql)->fetchColumn() ?: 0;
    
    // 3. Late Today
    $sql = "SELECT COUNT(*) FROM retards 
            WHERE date_retard = CURDATE()";
    $stats['late_today'] = $connexion->query($sql)->fetchColumn() ?: 0;
    
    // 4. Present Today (Estimate: Active - On Leave)
    $stats['present_today'] = max(0, $stats['total_active'] - $stats['on_leave']);
    
    return $stats;
}

function getPayrollStats() {
    global $connexion;
    
    $stats = [];
    
    // Total Payroll Cost for current month
    $month = date('n');
    $year = date('Y');
    
    try {
        $sql = "SELECT SUM(net_a_payer) FROM paiements WHERE mois = ? AND annee = ?";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$month, $year]);
        $stats['total_payroll'] = $stmt->fetchColumn() ?: 0;
    } catch (Exception $e) {
        $stats['total_payroll'] = 0;
    }
    
    // If 0, estimate from base salary
    if ($stats['total_payroll'] == 0) {
        $sql = "SELECT SUM(salaire) FROM employes WHERE statut = 'Actif'";
        $stats['estimated_payroll'] = $connexion->query($sql)->fetchColumn() ?: 0;
    }

    return $stats;
}

function getRecruitmentStats() {
    global $connexion;
    
    $stats = [];
    
    // Active Candidates (statut = 'Nouveau' or 'Entretien')
    $sql = "SELECT COUNT(*) FROM candidats WHERE statut IN ('Nouveau', 'Entretien')";
    $stats['active_candidates'] = $connexion->query($sql)->fetchColumn() ?: 0;
    
    return $stats;
}

function getLeaveRequests() {
    global $connexion;
    
    // Pending leave requests
    $sql = "SELECT c.*, e.nom, e.prenom, e.photo 
            FROM conges c
            JOIN employes e ON c.id_employe = e.id
            WHERE c.statut = 'En attente'
            ORDER BY c.date_demande DESC
            LIMIT 5";
            
    $stmt = $connexion->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPayrollEvolution() {
    global $connexion;
    
    // Last 6 months payroll
    $sql = "SELECT mois, annee, SUM(net_a_payer) as total
            FROM paiements
            WHERE CONCAT(annee, '-', LPAD(mois, 2, '0')) >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 6 MONTH), '%Y-%m')
            GROUP BY annee, mois
            ORDER BY annee ASC, mois ASC";
            
    try {
        $stmt = $connexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getUpcomingInterviews() {
    global $connexion;
    
    // Candidates with status 'Entretien'
    $sql = "SELECT * FROM candidats 
            WHERE statut = 'Entretien' 
            ORDER BY date_candidature DESC 
            LIMIT 5";
            
    $stmt = $connexion->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- NEW FUNCTIONS FOR REDESIGNED DASHBOARD ---

function getUpcomingBirthdays() {
    global $connexion;
    
    // Get birthdays in the next 30 days
    // Logic: Look for dates where (Month/Day) is between Today and Today+30days
    // Simplified SQL for portability (Works nicely if near end of year too)
    
    $sql = "SELECT e.id, e.nom, e.prenom, e.photo, e.date_naissance, 
            DATE_FORMAT(e.date_naissance, '%d/%m') as date_fmt,
            DATEDIFF(
                DATE_ADD(e.date_naissance, INTERVAL YEAR(CURDATE())-YEAR(e.date_naissance) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(e.date_naissance), 1, 0) YEAR),
                CURDATE()
            ) as days_remaining
            FROM employes e 
            WHERE e.statut = 'Actif'
            HAVING days_remaining BETWEEN 0 AND 30
            ORDER BY days_remaining ASC
            LIMIT 5";
            
    try {
        $stmt = $connexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getDepartmentStats() {
    global $connexion;
    
    $sql = "SELECT d.nom, COUNT(e.id) as count 
            FROM departements d
            LEFT JOIN postes p ON d.id = p.id_departement
            LEFT JOIN employes e ON p.id = e.id_poste AND e.statut = 'Actif'
            GROUP BY d.id, d.nom
            HAVING count > 0
            ORDER BY count DESC";
            
    try {
        $stmt = $connexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getWorkAnniversaries() {
    global $connexion;
    
    // Upcoming work anniversaries in next 30 days
    $sql = "SELECT e.id, e.nom, e.prenom, e.photo, e.date_embauche,
            DATE_FORMAT(e.date_embauche, '%d/%m/%Y') as date_embauche_fmt,
            YEAR(CURDATE()) - YEAR(e.date_embauche) as years,
            DATEDIFF(
                DATE_ADD(e.date_embauche, INTERVAL YEAR(CURDATE())-YEAR(e.date_embauche) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(e.date_embauche), 1, 0) YEAR),
                CURDATE()
            ) as days_remaining
            FROM employes e 
            WHERE e.statut = 'Actif'
            HAVING days_remaining BETWEEN 0 AND 60 AND years > 0
            ORDER BY days_remaining ASC
            LIMIT 5";
            
    try {
        $stmt = $connexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    } catch (Exception $e) {
        return [];
    }
}

function getLeaveAlerts() {
    global $connexion;
    
    // Check for leaves that ended yesterday or today
    // We want to notify if a leave end_date matches specific criteria
    // Assuming "Terminé" or simply checking date_fin < CURDATE() and notification hasn't been dismissed (if we had a dismissed flag)
    // For now, simpler approach: alerts for leaves ending yesterday or today
    
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    $sql = "SELECT e.nom, e.prenom, c.date_fin 
            FROM conges c
            JOIN employes e ON c.id_employe = e.id
            WHERE c.statut = 'Approuvé' 
            AND (c.date_fin = ? OR c.date_fin = ?)";
            
    try {
        $stmt = $connexion->prepare($sql);
        $stmt->execute([$yesterday, $today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}
// ... (existing code) ...

function getRecruitmentFunnel() {
    global $connexion;
    
    $stats = [
        'Nouveau' => 0,
        'Entretien' => 0,
        'Embauché' => 0,
        'Rejeté' => 0
    ];
    
    $sql = "SELECT statut, COUNT(*) as count FROM candidats GROUP BY statut";
    $stmt = $connexion->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results as $row) {
        if (isset($stats[$row['statut']])) {
            $stats[$row['statut']] = $row['count'];
        }
    }
    
    return $stats;
}

function getSeniorityStats() {
    global $connexion;
    
    // Categories: < 1 an, 1-3 ans, 3-5 ans, > 5 ans
    $stats = [
        'Moins de 1 an' => 0,
        '1 à 3 ans' => 0,
        '3 à 5 ans' => 0,
        'Plus de 5 ans' => 0
    ];
    
    $sql = "SELECT date_embauche FROM employes WHERE statut = 'Actif'";
    $stmt = $connexion->query($sql);
    $employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($employes as $emp) {
        if (!$emp['date_embauche']) continue;
        
        $start = new DateTime($emp['date_embauche']);
        $now = new DateTime();
        $diff = $now->diff($start);
        $years = $diff->y;
        
        if ($years < 1) {
            $stats['Moins de 1 an']++;
        } elseif ($years < 3) {
            $stats['1 à 3 ans']++;
        } elseif ($years < 5) {
            $stats['3 à 5 ans']++;
        } else {
            $stats['Plus de 5 ans']++;
        }
    }
    
    return $stats;
}
?>
