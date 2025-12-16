<?php
require_once __DIR__ . '/function.php';

function getMenuItems() {
    $pdo = getPDO();
    $sql = "SELECT * FROM menu_items WHERE is_active = 1 ORDER BY position ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateMenuOrder($items) {
    $pdo = getPDO();
    try {
        $pdo->beginTransaction();
        
        $sql = "UPDATE menu_items SET position = :position WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        
        foreach ($items as $index => $id) {
            $stmt->execute([
                ':position' => $index + 1,
                ':id' => $id
            ]);
        }
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Erreur lors de la mise à jour de l'ordre du menu : " . $e->getMessage());
        return false;
    }
}
// --- Role Based Menu Access ---

function checkMenuAccessTable() {
    $pdo = getPDO();
    // Table associating roles (by ID) to menu_items (by ID)
    $sql = "CREATE TABLE IF NOT EXISTS role_menu_access (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role_id INT NOT NULL,
        menu_item_id INT NOT NULL,
        UNIQUE KEY unique_role_menu (role_id, menu_item_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
}

function getRoleMenuPermissions($roleId) {
    $pdo = getPDO();
    checkMenuAccessTable();
    
    $stmt = $pdo->prepare("SELECT menu_item_id FROM role_menu_access WHERE role_id = ?");
    $stmt->execute([$roleId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function updateRoleMenuPermissions($roleId, $menuItemIds) {
    $pdo = getPDO();
    checkMenuAccessTable();
    
    try {
        $pdo->beginTransaction();
        
        // Clear existing permissions for this role
        $stmt = $pdo->prepare("DELETE FROM role_menu_access WHERE role_id = ?");
        $stmt->execute([$roleId]);
        
        // Insert new permissions
        if (!empty($menuItemIds) && is_array($menuItemIds)) {
            $sql = "INSERT INTO role_menu_access (role_id, menu_item_id) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            foreach ($menuItemIds as $mid) {
                $stmt->execute([$roleId, $mid]);
            }
        }
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function getAccessibleMenuItemsForRole($roleName) {
    $pdo = getPDO();
    checkMenuAccessTable();
    
    // 1. Get Role ID from Name
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE nom = ?");
    $stmt->execute([$roleName]);
    $roleId = $stmt->fetchColumn();
    
    // If role not found in DB (accidental mismatch), fallback or return empty
    // HOWEVER: 'admin' is special. We might want to give 'admin' full access always?
    // Let's adhere to the table unless it's empty, then maybe default to all for admin?
    // User requested "role menu access", so explicit is better.
    
    if ($roleName === 'admin') {
         // Check if admin has ANY entries. If 0, assume full access (migration)
         if (!$roleId) return getMenuItems(); // Admin role not found in table? Return all.
         
         $count = $pdo->prepare("SELECT COUNT(*) FROM role_menu_access WHERE role_id = ?");
         $count->execute([$roleId]);
         if ($count->fetchColumn() == 0) {
             return getMenuItems(); // No permissions set yet -> All Access
         }
    }

    if (!$roleId) return [];

    // 2. Get Menu Items
    $sql = "SELECT m.* 
            FROM menu_items m 
            JOIN role_menu_access rma ON m.id = rma.menu_item_id 
            WHERE rma.role_id = ? AND m.is_active = 1 
            ORDER BY m.position ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$roleId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
