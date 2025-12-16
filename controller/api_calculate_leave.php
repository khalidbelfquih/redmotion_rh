<?php
include '../config/connexion.php';

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$startDate = $data['start'] ?? null;
$endDate = $data['end'] ?? null;

if (!$startDate || !$endDate) {
    echo json_encode(['error' => 'Dates missing']);
    exit;
}

try {
    // 1. Get Settings (Weekends)
    $stmt = $connexion->query("SELECT weekend_days FROM societe_info LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    $weekendIndices = $settings ? explode(',', $settings['weekend_days']) : [0]; // Default Sunday

    // 2. Get Holidays
    $stmt = $connexion->query("SELECT * FROM jours_feries");
    $holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Calculate
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    
    // Check if end < start
    if ($end < $start) {
        echo json_encode(['days' => 0, 'valid' => false, 'message' => 'Date de fin antérieure à la date de début']);
        exit;
    }

    $daysCount = 0;
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end->modify('+1 day')); // Include end date

    foreach ($period as $dt) {
        $currentDate = $dt->format('Y-m-d');
        $dayOfWeek = $dt->format('w'); // 0 (Sun) to 6 (Sat)
        $monthDay = $dt->format('m-d');

        // Check Weekend
        if (in_array($dayOfWeek, $weekendIndices)) {
            continue; // Skip weekend
        }

        // Check Holidays
        $isHoliday = false;
        foreach ($holidays as $h) {
            if ($h['recurrent']) {
                // Compare Month-Day
                if (date('m-d', strtotime($h['date_ferie'])) === $monthDay) {
                    $isHoliday = true;
                    break;
                }
            } else {
                // Compare Exact Date
                if ($h['date_ferie'] === $currentDate) {
                    $isHoliday = true;
                    break;
                }
            }
        }

        if ($isHoliday) {
            continue; // Skip holiday
        }

        $daysCount++;
    }

    echo json_encode(['days' => $daysCount, 'valid' => true]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
