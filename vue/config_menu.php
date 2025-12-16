<?php
require_once '../model/menu_functions.php';

// Handle AJAX request for updating order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    // Clean output buffer to ensure no HTML is sent
    ob_clean();
    
    $order = json_decode($_POST['order'], true);
    if (updateMenuOrder($order)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

include 'entete.php';

$menuItems = getMenuItems();
?>

<div class="home-content">
    <div class="header-section">
        <h2 class="page-title">Configuration du Menu</h2>
    </div>

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header" style="padding-bottom: 15px; border-bottom: 1px solid #eee; margin-bottom: 20px;">
            <h3 style="margin: 0; color: var(--primary-color);"><i class='bx bx-list-ul'></i> Réorganiser le Menu</h3>
            <p style="color: #666; margin-top: 5px;">Glissez et déposez les éléments pour changer l'ordre d'affichage.</p>
        </div>

        <ul id="sortable-menu" class="menu-list">
            <?php foreach ($menuItems as $item): ?>
                <li class="menu-item-card" data-id="<?= $item['id'] ?>">
                    <div class="menu-item-content">
                        <i class='<?= $item['icon'] ?> menu-icon'></i>
                        <span class="menu-label"><?= $item['label'] ?></span>
                    </div>
                    <i class='bx bx-grid-vertical drag-handle'></i>
                </li>
            <?php endforeach; ?>
        </ul>

        <div style="margin-top: 20px; text-align: right;">
            <button id="btn-save-order" class="btn btn-primary">
                <i class='bx bx-save'></i> Enregistrer l'ordre
            </button>
        </div>
    </div>
</div>

<style>
    .menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu-item-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: grab;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .menu-item-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: var(--secondary-color);
    }

    .menu-item-card.sortable-ghost {
        opacity: 0.4;
        background: #f8f9fa;
        border: 2px dashed #ccc;
    }

    .menu-item-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .menu-icon {
        font-size: 24px;
        color: var(--primary-color);
        width: 40px;
        height: 40px;
        background: #f0f2f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .menu-label {
        font-weight: 600;
        font-size: 16px;
        color: #333;
    }

    .drag-handle {
        font-size: 24px;
        color: #ccc;
        cursor: grab;
    }
</style>

<!-- SortableJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>
    const el = document.getElementById('sortable-menu');
    const sortable = Sortable.create(el, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        handle: '.menu-item-card' // Make the whole card draggable
    });

    document.getElementById('btn-save-order').addEventListener('click', function() {
        const order = sortable.toArray();
        
        // Show loading state
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Enregistrement...";
        btn.disabled = true;

        // Send AJAX request
        const formData = new FormData();
        formData.append('action', 'update_order');
        formData.append('order', JSON.stringify(order));

        fetch('config_menu.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Ordre du menu mis à jour avec succès !');
                location.reload(); // Reload to see changes in sidebar
            } else {
                alert('Erreur lors de la mise à jour.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
</script>

<?php include 'pied.php'; ?>
