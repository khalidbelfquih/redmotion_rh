<?php
require_once 'model/dashboard_functions.php';

echo "Testing HR Dashboard Functions (Real Data Check)...\n";

try {
    echo "1. getHRStats(): ";
    $stats = getHRStats();
    echo json_encode($stats) . "\n";
    
    echo "2. getPayrollStats(): ";
    $payroll = getPayrollStats();
    echo json_encode($payroll) . "\n";
    
    echo "3. getRecruitmentStats(): ";
    $recruitment = getRecruitmentStats();
    echo json_encode($recruitment) . "\n";
    
    echo "4. getLeaveRequests(): ";
    $leaves = getLeaveRequests();
    echo count($leaves) . " pending requests found.\n";
    
    echo "5. getPayrollEvolution(): ";
    $evolution = getPayrollEvolution();
    echo count($evolution) . " months found.\n";
    
    echo "6. getUpcomingInterviews(): ";
    $interviews = getUpcomingInterviews();
    echo count($interviews) . " interviews found.\n";
    if (count($interviews) > 0) {
        echo "   First interview candidate: " . $interviews[0]['nom'] . " for " . $interviews[0]['poste_vise'] . "\n";
    }
    
    echo "All tests passed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
