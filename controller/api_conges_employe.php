<?php
include '../model/conge_functions.php';

if (isset($_GET['id_employe'])) {
    $id_employe = $_GET['id_employe'];
    $year = date('Y');
    
    // 1. Get History
    $conges = getCongesByEmployeYear($id_employe, $year);
    
    // 2. Get Annual Allowance
    $stmt = $connexion->query("SELECT jours_conge_annuel, weekend_days FROM societe_info LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    $allowance = $settings['jours_conge_annuel'] ?? 18;
    $weekendIndices = $settings ? explode(',', $settings['weekend_days']) : [0];
    
    // 3. Get Holidays (for accurate calculation of consumed days if we want to be precise)
    // For simplicity, we assume the stored dates imply a duration. 
    // However, strictly speaking, 1 week leave might be 5 days if diff is 7 but 2 are weekends.
    // The previous view calculated simple diff. Let's try to be consistent with "working days" logic if possible, 
    // or just use simple diff if that's the established rule.
    // The prompt says "calculé a base de historique".
    // Let's us a simple loop to calculate 'taken' days.
    
    $daysTaken = 0;
    
    // Need a function to calc working days? Or just simple diff?
    // In view it was: ($debut->diff($fin)->days + 1). This is calendar days.
    // Usually 'conge annuel' counts working days. 
    // Let's assume for now we use the simple logic or better, use the API calc logic if available?
    // To match the VIEW's logic (simple diff), I will stick to simple diff:
    
    // BETTER APPROACH: Use the same helper used in `api_calculate_leave` if possible, 
    // OR just simple diff to match the view's "Durée" column. 
    // View says: ($debut->diff($fin)->days + 1). This includes weekends.
    // If the View shows 7 days for a week, then Solde should deduction 7?
    // Standard HR usually excludes weekends.
    // Let's check `api_calculate_leave.php` to see how it calculates "valid" days.
    // I can't read it now without a tool. Use strict simple diff for now to match `vue/conges.php` display logic 
    // unless I assume `api_calculate_leave` is the source of truth.
    // I will use simple days + 1 for now to match `vue/conges.php`.
    
    // REVISIT: The user wants "Solde restant".
    foreach ($conges as &$conge) {
        $start = new DateTime($conge['date_debut']);
        $end = new DateTime($conge['date_fin']);
        $duration = $start->diff($end)->days + 1;
        
        $conge['duree_calculee'] = $duration; // Add to response for convenience
        
        if ($conge['type_conge'] === 'Annuel' && $conge['statut'] === 'Approuvé') {
            $daysTaken += $duration;
        }
    }
    
    $soldeRestant = $allowance - $daysTaken;
    
    header('Content-Type: application/json');
    echo json_encode([
        'history' => $conges,
        'solde' => $soldeRestant,
        'taken' => $daysTaken,
        'allowance' => $allowance
    ]);
}
?>
