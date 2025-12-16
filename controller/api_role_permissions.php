<?php
include '../config/connexion.php';
require_once '../model/menu_functions.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Only Admin can manage permissions
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get permissions for a role
    $roleId = $_GET['role_id'] ?? null;
    
    if (!$roleId) {
        echo json_encode(['error' => 'Role ID required']);
        exit;
    }
    
    $allMenuItems = getMenuItems(); // Get all available items
    $permissions = getRoleMenuPermissions($roleId); // Get IDs of allowed items
    
    // Map data
    $result = [];
    foreach ($allMenuItems as $item) {
        $result[] = [
            'id' => $item['id'],
            'label' => $item['label'],
            'icon' => $item['icon'],
            'allowed' => in_array($item['id'], $permissions)
        ];
    }
    
    echo json_encode($result);
} 
elseif ($method === 'POST') {
    // Update permissions
    $input = json_decode(file_get_contents('php://input'), true);
    
    $roleId = $input['role_id'] ?? null;
    $menuItemIds = $input['menu_items'] ?? []; // Array of IDs
    
    if (!$roleId) {
        echo json_encode(['success' => false, 'message' => 'Role ID required']);
        exit;
    }
    
    // If admin, we should be careful not to lock ourselves out, but typically admin manages admin.
    // Logic is handled in model function.
    
    if (updateRoleMenuPermissions($roleId, $menuItemIds)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>
