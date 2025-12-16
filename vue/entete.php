<?php
include_once '../model/function.php';

// Vérification de l'authentification
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    // Rediriger vers la page de connexion si non connecté
    header('Location: login');
    exit();
}

// Check if app is locked
if (isset($_SESSION['locked']) && $_SESSION['locked'] === true) {
    if (basename($_SERVER['PHP_SELF']) !== 'lock_screen.php') {
        header('Location: ../vue/lock_screen.php');
        exit();
    }
}

require_once '../model/dashboard_functions.php';

// Récupération des alertes
$alertes = [];

// Get leave alerts (Finished/Expiring leaves)
$leaveAlerts = getLeaveAlerts();
foreach ($leaveAlerts as $leave) {
    $alertes[] = [
        'matricule' => $leave['prenom'] . ' ' . $leave['nom'],
        'type_alerte' => "A terminé son congé (Fin: " . date('d/m/Y', strtotime($leave['date_fin'])) . ")",
        'niveau_alerte' => 'warning'
    ];
}

$nbAlertes = count($alertes);
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <title>
        <?php
        echo ucfirst(str_replace(".php", "", basename($_SERVER['PHP_SELF'])));
        ?>
    </title>
    <link rel="stylesheet" href="../public/css/style.css" />
    <!-- Boxicons CDN Link -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Red Motion Palette */
            --bg-white: #FFFFFF;
            --bg-light: #F8F9FA;
            --bg-card: #FFFFFF;
            
            --red-primary: #E63946;
            --red-dark: #B71C1C;
            --red-light: #FFCDD2;
            
            --text-dark: #1A1A1A;
            --text-medium: #4A4A4A;
            --text-light: #8C8C8C;
            --text-muted: #ADB5BD;
            
            --border-light: rgba(0, 0, 0, 0.05);
            --border-medium: rgba(0, 0, 0, 0.1);
            
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 8px 30px rgba(0, 0, 0, 0.08);
            --shadow-glow: 0 8px 32px rgba(230, 57, 70, 0.2);
            
            /* Mapping to old variables to maintain compatibility */
            --bg-cream: var(--bg-light);
            --bg-warm: var(--bg-white);
            --bg-soft: var(--bg-light);
            
            --caramel: var(--red-primary);
            --caramel-light: var(--red-light);
            --caramel-dark: var(--red-dark);
            
            --chocolate: var(--text-dark);
            --chocolate-light: var(--text-medium);
            
            --rose-petal: var(--red-light);
            --rose-dark: var(--red-primary);
            
            --pistachio: #E0E0E0;
            --pistachio-dark: #9E9E9E;
            
            --berry: var(--red-primary);
            
            /* Legacy variables for compatibility */
            --primary-color: var(--red-primary);
            --secondary-color: var(--text-dark);
            --accent-color: var(--red-primary);
            --light-bg: var(--bg-light);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
        }

        /* ================================================
           SIDEBAR
           ================================================ */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 295px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: width 0.45s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .sidebar.collapsed {
            width: 88px;
        }



        /* ================================================
           LOGO SECTION
           ================================================ */
        .logo-section {
            position: relative;
            z-index: 10;
            padding: 12px 24px;
            border-bottom: 1px solid var(--border-light);
            background: linear-gradient(180deg, var(--bg-warm) 0%, transparent 100%);
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-icon {
            position: relative;
            width: 56px;
            height: 56px;
            flex-shrink: 0;
        }

        .logo-icon-inner {
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, var(--red-primary) 0%, var(--red-dark) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-glow);
            position: relative;
            overflow: hidden;
        }



        .logo-icon i {
            font-size: 28px;
            color: white;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 2px 4px rgba(93, 64, 55, 0.3));
        }



        .logo-text {
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
        }

        .logo-title {
            font-family: 'DM Sans', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.1;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .logo-subtitle {
            font-size: 10px;
            color: var(--red-primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 3px;
            font-weight: 700;
        }

        /* ================================================
           NAVIGATION
           ================================================ */
        .nav-wrapper {
            position: relative;
            z-index: 10;
            flex: 1;
            padding: 24px 14px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-wrapper::-webkit-scrollbar {
            width: 4px;
        }

        .nav-wrapper::-webkit-scrollbar-track {
            background: var(--bg-soft);
            border-radius: 10px;
        }

        .nav-wrapper::-webkit-scrollbar-thumb {
            background: var(--caramel-light);
            border-radius: 10px;
        }

        .nav-group-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--text-muted);
            padding: 0 14px;
            margin-bottom: 12px;
            margin-top: 8px;
            font-weight: 700;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .nav-group-title {
            opacity: 0;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            text-decoration: none;
            color: var(--text-medium);
            border-radius: 16px;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 2px solid transparent;
        }

        .nav-item a:hover {
            color: var(--red-primary);
            background: rgba(230, 57, 70, 0.05);
            border-color: transparent;
        }

        .nav-item a:hover .nav-icon-wrapper {
            background: var(--red-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
        }

        /* Active State */
        .nav-item a.active {
            background: var(--red-primary);
            color: white;
            box-shadow: var(--shadow-glow);
            border-color: transparent;
        }

        .nav-item a.active .nav-icon-wrapper {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            box-shadow: none;
        }

        .nav-icon-wrapper {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            border-radius: 14px;
            background: var(--bg-soft);
            color: var(--caramel);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }

        .nav-text {
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.3s;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            transform: translateX(-10px);
        }

        /* Active indicator */
        .nav-item a.active::after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .sidebar.collapsed .nav-item a.active::after {
            display: none;
        }

        /* Tooltip */
        .nav-tooltip {
            position: absolute;
            left: calc(100% + 16px);
            top: 50%;
            transform: translateY(-50%);
            background: var(--chocolate);
            color: white;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            box-shadow: var(--shadow-medium);
            z-index: 999;
        }

        .nav-tooltip::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            border: 8px solid transparent;
            border-right-color: var(--chocolate);
        }

        .sidebar.collapsed .nav-item a:hover .nav-tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* Decorative divider */
        .nav-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--caramel-light), transparent);
            margin: 20px 20px;
            position: relative;
            border-radius: 2px;
        }

            padding: 0 12px;
        }

        .sidebar.collapsed .nav-divider::before {
            opacity: 0;
        }







        .user-dropdown-container {
            position: relative;
            margin-left: 20px;
        }

        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px;
            background: white;
            border: 1px solid var(--border-medium);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .user-dropdown-btn:hover, .user-dropdown-container.active .user-dropdown-btn {
            box-shadow: var(--shadow-medium);
            border-color: var(--red-light);
            transform: translateY(-1px);
        }

        .header-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--red-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .header-user-info {
            display: flex;
            flex-direction: column;
            margin-right: 8px;
            text-align: right;
        }

        .header-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .header-role {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .dropdown-chevron {
            color: var(--text-muted);
            font-size: 18px;
            transition: transform 0.3s;
            margin-right: 4px;
        }

        .user-dropdown-container.active .dropdown-chevron {
            transform: rotate(180deg);
        }

        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 240px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            z-index: 1000;
            border: 1px solid var(--border-light);
        }

        .user-dropdown-container.active .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 16px;
            border-bottom: 1px solid var(--border-light);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dropdown-avatar-large {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--red-primary), var(--red-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
        }

        .dropdown-user-details h4 {
            font-size: 15px;
            color: var(--text-dark);
            margin-bottom: 2px;
        }

        .dropdown-user-details p {
            font-size: 11px;
            color: var(--text-light);
            text-transform: uppercase;
            font-weight: 600;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: var(--text-medium);
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: var(--bg-light);
            color: var(--red-primary);
        }

        .dropdown-item i {
            font-size: 18px;
            color: var(--text-light);
            transition: color 0.2s;
        }

        .dropdown-item:hover i {
            color: var(--red-primary);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border-light);
            margin: 8px 0;
        }

        .logout-danger {
            color: var(--red-primary);
        }

        .logout-danger:hover {
            background: rgba(230, 57, 70, 0.08);
        }

        .notification-dropdown {
            /* Fix existing notification dropdown z-index/positioning if needed */
            right: -80px; /* Adjust as it was previously right: 0 */
        }


        .sidebar.collapsed .user-info {
            opacity: 0;
            width: 0;
        }





        /* ================================================
           TOGGLE BUTTON
           ================================================ */
        .toggle-btn {
            position: absolute;
            top: 32px;
            right: -16px;
            width: 32px;
            height: 32px;
            background: var(--red-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100;
            box-shadow: var(--shadow-glow);
            transition: all 0.3s;
            border: 3px solid var(--bg-card);
        }

        .toggle-btn:hover {
            transform: scale(1.15);
            box-shadow: 0 8px 28px rgba(200, 149, 108, 0.45);
        }

        /* ================================================
           GLOBAL MODAL STYLES (Red Motion Theme)
           ================================================ */
        .planning-modal, .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Unified opacity */
            backdrop-filter: blur(4px);
            z-index: 2000;
            overflow-y: auto;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        .planning-modal-content, .modal-content {
            position: relative;
            background: var(--bg-card);
            max-width: 500px;
            width: 95%;
            margin: 50px auto;
            border-radius: 16px; /* Unified radius */
            box-shadow: var(--shadow-medium);
            animation: slideUp 0.3s ease-out;
            border: 1px solid var(--border-light);
            overflow: hidden;
        }

        .planning-modal-header, .modal-header {
            background: var(--bg-soft);
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .planning-modal-title, .modal-title {
            color: var(--text-dark);
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .planning-modal-close, .modal-close {
            background: transparent;
            border: none;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            color: var(--text-medium);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .planning-modal-close:hover, .modal-close:hover {
            background: rgba(0,0,0,0.05);
            color: var(--text-dark);
            transform: rotate(90deg);
        }

        .planning-modal-body, .modal-body {
            padding: 24px;
        }

        .planning-modal-footer, .modal-footer {
            padding: 16px 24px;
            background: var(--bg-soft);
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .toggle-btn i {
            font-size: 18px;
            color: white;
            transition: transform 0.4s;
        }

        .sidebar.collapsed .toggle-btn i {
            transform: rotate(180deg);
        }

        /* ================================================
           RESPONSIVE & LAYOUT OVERRIDES
           ================================================ */
        .home-section {
            position: relative;
            background: #f0f2f5;
            min-height: 100vh;
            width: calc(100% - 295px);
            left: 295px;
            transition: all 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed ~ .home-section {
            width: calc(100% - 88px);
            left: 88px;
        }

        .home-section nav {
            width: calc(100% - 295px);
            left: 295px;
            transition: all 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed ~ .home-section nav {
            width: calc(100% - 88px);
            left: 88px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 88px;
            }
            .sidebar .logo-text,
            .sidebar .nav-group-title,
            .sidebar .nav-text,
            .sidebar .nav-divider::before {
                opacity: 0;
                display: none;
            }
            .toggle-btn {
                display: none;
            }
            .home-section {
                width: calc(100% - 88px);
                left: 88px;
            }
            .home-section nav {
                width: calc(100% - 88px);
                left: 88px;
            }
        }
        
        /* Notification Styles (Preserved) */
        .nav-actions { display: flex; align-items: center; gap: 20px; }
        .notification-box { position: relative; cursor: pointer; }
        .notification-icon { font-size: 24px; color: var(--text-color); transition: transform 0.3s ease; }
        .notification-box:hover .notification-icon { transform: rotate(15deg); color: var(--primary-color); }
        .notification-badge { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 50%; border: 2px solid #fff; animation: pulse 2s infinite; }
        .notification-dropdown { position: absolute; top: 60px; right: 0; width: 320px; background: #fff; border-radius: 10px; box-shadow: 0 5px 25px rgba(0,0,0,0.15); opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s; z-index: 1000; overflow: hidden; }
        .notification-dropdown.active { opacity: 1; visibility: visible; transform: translateY(0); }
        .notification-header { padding: 15px; border-bottom: 1px solid #eee; font-weight: 600; color: var(--primary-color); display: flex; justify-content: space-between; align-items: center; }
        .notification-list { max-height: 300px; overflow-y: auto; }
        .notification-item { padding: 15px; border-bottom: 1px solid #f5f5f5; display: flex; align-items: start; gap: 10px; transition: background 0.2s; }
        .notification-item:hover { background: #f8f9fa; }
        .notif-icon { min-width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; }
        .notif-danger { background: #ffebee; color: #dc3545; }
        .notif-warning { background: #fff3cd; color: #ffc107; }
        .notif-content h4 { margin: 0 0 5px; font-size: 14px; color: #333; }
        .notif-content p { margin: 0; font-size: 12px; color: #666; }

        /* ================================================
           GLOBAL TOOLTIP
           ================================================ */
        [data-tooltip] {
            position: relative;
        }
        
        [data-tooltip]:hover::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-8px);
            padding: 6px 12px;
            background: var(--text-dark);
            color: white;
            font-size: 12px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 1;
            visibility: visible;
            transition: all 0.2s ease;
            z-index: 1000;
            pointer-events: none;
            box-shadow: var(--shadow-medium);
            font-weight: 500;
            animation: fadeIntooltip 0.2s forwards;
        }

        [data-tooltip]:hover::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(4px); /* Arrow position */
            border: 6px solid transparent;
            border-top-color: var(--text-dark);
            opacity: 1;
            visibility: visible;
            z-index: 1000;
            pointer-events: none;
            animation: fadeIntooltip 0.2s forwards;
        }
        
        @keyframes fadeIntooltip {
            from { opacity: 0; transform: translateX(-50%) translateY(0); }
            to { opacity: 1; transform: translateX(-50%) translateY(-8px); }
        }
    </style>
</head>

<body>
    <aside class="sidebar" id="sidebar">
        <!-- Animated Background -->


        <!-- Toggle Button -->
        <div class="toggle-btn" onclick="document.getElementById('sidebar').classList.toggle('collapsed')">
            <i class='bx bx-chevron-left'></i>
        </div>

        <!-- Logo -->
        <div class="logo-section">
            <div class="logo-wrapper">
                <div class="logo-icon">

                    <div class="logo-icon-inner">
                        <i class='bx bxs-video'></i>
                    </div>
                </div>
                    <div class="logo-text">
                        <div class="logo-title">RED MOTION</div>
                        <div class="logo-subtitle">AUDIO VISUAL</div>
                    </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="nav-wrapper">
            <div class="nav-group-title">Menu</div>
            <ul class="nav-menu">
                <?php
                require_once '../model/menu_functions.php';
                
                $userRole = isset($_SESSION['user']) ? $_SESSION['user']['role'] : null;
                
                // Fetch only accessible items based on role permissions
                if ($userRole) {
                    $menuItems = getAccessibleMenuItemsForRole($userRole);
                } else {
                    $menuItems = [];
                }
                
                foreach ($menuItems as $item):
                    $isActive = (basename($_SERVER['PHP_SELF']) == $item['link']) ? 'active' : '';
                ?>
                <li class="nav-item">
                    <a href="<?= str_replace('.php', '', $item['link']) ?>" class="<?= $isActive ?>">
                        <div class="nav-icon-wrapper"><i class='<?= $item['icon'] ?>'></i></div>
                        <span class="nav-text"><?= $item['label'] ?></span>
                        <span class="nav-tooltip"><?= $item['label'] ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>



        <!-- User Section -->

    </aside>

    <section class="home-section" style="padding-left:20px;padding-right:20px;padding-bottom:20px" >
        <nav class="hidden-print">
            <div class="sidebar-button">
                <!-- Removed old sidebar toggle -->
                <span class="dashboard">
                    <?php
                    echo ucfirst(str_replace(".php", "", basename($_SERVER['PHP_SELF'])));
                    ?>
                </span>
            </div>
            
            <div class="nav-actions">
                <!-- Notification Bell -->
                <div class="notification-box" onclick="toggleNotifications(event)">
                    <i class='bx bx-bell notification-icon'></i>
                    <?php if ($nbAlertes > 0): ?>
                        <span class="notification-badge"><?= $nbAlertes ?></span>
                    <?php endif; ?>
                </div>

                <!-- Notification Dropdown Content -->
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <span>Notifications</span>
                        <span style="font-size: 12px; color: #666;"><?= $nbAlertes ?> alertes</span>
                    </div>
                    <div class="notification-list">
                        <?php if ($nbAlertes > 0): ?>
                            <?php foreach ($alertes as $alerte): ?>
                                <div class="notification-item">
                                    <div class="notif-icon <?= $alerte['niveau_alerte'] == 'danger' ? 'notif-danger' : 'notif-warning' ?>">
                                        <i class='bx bx-error-circle'></i>
                                    </div>
                                    <div class="notif-content">
                                        <h4><?= $alerte['matricule'] ?></h4>
                                        <p><?= $alerte['type_alerte'] ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="notification-item">
                                <div class="notif-content" style="width: 100%; text-align: center; color: #999;">
                                    <p>Aucune nouvelle notification</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Lock Button -->
                <a href="../controller/lock_app.php" class="notification-box" title="Verrouiller l'application" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-lock-alt notification-icon'></i>
                </a>

                <!-- User Profile Dropdown -->
                <div class="user-dropdown-container" id="userDropdownContainer">
                    <div class="user-dropdown-btn" onclick="toggleUserDropdown(event)">
                        <div class="header-avatar">
                            <?= strtoupper(substr(isset($_SESSION['user']) ? $_SESSION['user']['prenom'] : 'U', 0, 1) . substr(isset($_SESSION['user']) ? $_SESSION['user']['nom'] : 'S', 0, 1)) ?>
                        </div>
                        <div class="header-user-info">
                            <span class="header-name"><?php echo isset($_SESSION['user']) ? $_SESSION['user']['prenom'] : 'User'; ?></span>
                            <span class="header-role"><?php echo isset($_SESSION['user']) ? ucfirst($_SESSION['user']['role']) : 'Role'; ?></span>
                        </div>
                        <i class='bx bx-chevron-down dropdown-chevron'></i>
                    </div>

                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <div class="dropdown-header">
                            <div class="dropdown-avatar-large">
                                <?= strtoupper(substr(isset($_SESSION['user']) ? $_SESSION['user']['prenom'] : 'U', 0, 1) . substr(isset($_SESSION['user']) ? $_SESSION['user']['nom'] : 'S', 0, 1)) ?>
                            </div>
                            <div class="dropdown-user-details">
                                <h4><?php echo isset($_SESSION['user']) ? $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'] : 'Utilisateur'; ?></h4>
                                <p><?php echo isset($_SESSION['user']) ? ucfirst($_SESSION['user']['role']) : 'Invité'; ?></p>
                            </div>
                        </div>
                        
                        <a href="profile" class="dropdown-item">
                            <i class='bx bx-user'></i>
                            <span>Mon Profil</span>
                        </a>

                        <a href="setup_2fa.php" class="dropdown-item">
                            <i class='bx bx-shield-quarter'></i>
                            <span>Activer 2FA</span>
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <a href="../controller/deconnexion" class="dropdown-item logout-danger">
                            <i class='bx bx-log-out'></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <script>
            function toggleNotifications(e) {
                e.stopPropagation();
                const dropdown = document.getElementById('notificationDropdown');
                
                // Close other dropdowns
                document.getElementById('userDropdownContainer').classList.remove('active');
                
                dropdown.classList.toggle('active');
            }

            function toggleUserDropdown(e) {
                e.stopPropagation();
                const dropdownContainer = document.getElementById('userDropdownContainer');
                
                // Close other dropdowns
                document.getElementById('notificationDropdown').classList.remove('active');
                
                dropdownContainer.classList.toggle('active');
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                const notifDropdown = document.getElementById('notificationDropdown');
                const userDropdown = document.getElementById('userDropdownContainer');
                
                if (!e.target.closest('.notification-box') && !e.target.closest('.notification-dropdown')) {
                    notifDropdown.classList.remove('active');
                }
                
                if (!e.target.closest('.user-dropdown-container')) {
                    userDropdown.classList.remove('active');
                }
            });
            
            // Active link management (client-side backup)
            document.querySelectorAll('.nav-item a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // e.preventDefault(); // Don't prevent default, we need navigation
                    document.querySelectorAll('.nav-item a').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        </script>