<?php
require_once 'model/dashboard_functions.php';

echo "Testing Dashboard Functions...\n";

try {
    echo "1. getGlobalStats(): ";
    $stats = getGlobalStats();
    echo json_encode($stats) . "\n";
    
    echo "2. getSalesChartData(): ";
    $chart = getSalesChartData();
    echo count($chart) . " records found.\n";
    
    echo "3. getTopProducts(): ";
    $top = getTopProducts();
    echo count($top) . " products found.\n";
    
    echo "4. getStockAlerts(): ";
    $alerts = getStockAlerts();
    echo count($alerts) . " alerts found.\n";
    
    echo "5. getRecentOrders(): ";
    $orders = getRecentOrders();
    echo count($orders) . " orders found.\n";
    
    echo "All tests passed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
