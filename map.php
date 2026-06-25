<?php
// ============================================
// ໄຟລ໌: map.php
// ໜ້າສະແດງແຜນທີ່ເຮືອນມໍລະດົກຫຼວງພະບາງ
// ============================================
include_once 'config/database.php';

// ກວດສອບພາສາ
$lang = isset($_GET['lang']) ? trim($_GET['lang']) : 'lo';
if ($lang !== 'en') $lang = 'lo';

// ກວດສອບແຫຼ່ງທີ່ມາ: ມາຈາກໜ້າຈັດການ (?from=admin) → ກັບໄປ dashboard, ບໍ່ດັ່ງນັ້ນ → index
$fromAdmin = isset($_GET['from']) && $_GET['from'] === 'admin';
$backUrl = $fromAdmin ? 'admin/dashboard.php' : 'index.php?lang=' . urlencode($lang);

// ດຶງຂໍ້ມູນປະເພດທັງໝົດ
$cat_query = "SELECT * FROM heritage_categories";
$cat_result = mysqli_query($connect, $cat_query);
$categories = [];
while ($row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $row;
}

// ດຶงຂໍ້ມູນເຮືອນທີ່ມີພິກັດ
$house_query = "SELECT h.*, GROUP_CONCAT(hc.category_id) as category_ids 
                FROM heritage_houses h
                LEFT JOIN house_categories hc ON h.house_id = hc.house_id
                WHERE h.status = 'active' AND h.latitude IS NOT NULL AND h.longitude IS NOT NULL AND h.latitude != 0 AND h.longitude != 0
                GROUP BY h.house_id";
$house_result = mysqli_query($connect, $house_query);
$houses = [];
while ($row = mysqli_fetch_assoc($house_result)) {
    // ກວດສອບຮູບພາບ
    $image_main = $row['image_main'];
    if ($image_main) {
        $row['image_src'] = 'uploads/' . str_replace('uploads/', '', $image_main);
    } else {
        $row['image_src'] = 'https://placehold.co/300x200/2d6a4f/white?text=Luang+Prabang';
    }
    $houses[] = $row;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo $lang === 'lo' ? 'ແຜນທີ່ມໍລະດົກ | Luang Prabang Heritage Map' : 'Heritage Map | Luang Prabang Heritage'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif; }
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
            background: #0d2b1f;
        }
        #map {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            bottom: 0;
            z-index: 1;
        }
        
        /* Glassmorphism Floating Panels */
        .glass-panel {
            background: rgba(21, 57, 34, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            color: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            z-index: 1000;
            position: absolute;
            transition: all 0.3s ease;
        }
        
        /* Title Card panel */
        .title-card {
            top: 20px;
            left: 20px;
            width: 350px;
            padding: 24px;
        }
        .title-card h4 {
            color: #dfb26a;
            font-weight: 800;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.4rem;
        }
        .title-card p {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.75);
            margin-bottom: 18px;
            line-height: 1.4;
        }
        
        /* Sidebar Filter Panel */
        .filter-panel {
            top: 20px;
            right: 20px;
            width: 290px;
            padding: 24px;
        }
        .filter-panel h5 {
            color: #dfb26a;
            font-weight: 800;
            margin-bottom: 16px;
            font-size: 1.1rem;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            padding-bottom: 8px;
        }
        .filter-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 0.9rem;
            user-select: none;
            transition: all 0.2s;
        }
        .filter-item:hover {
            color: #dfb26a;
        }
        .filter-item input {
            margin-right: 12px;
            cursor: pointer;
            width: 18px;
            height: 18px;
            accent-color: #dfb26a;
        }
        .filter-color-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
            border: 1.5px solid white;
        }
        
        /* Floating buttons */
        .action-buttons {
            bottom: 30px;
            left: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 1000;
            position: absolute;
        }
        .btn-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(255,255,255,0.95);
            color: #1a472a;
            border: 1px solid rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
        }
        .btn-circle:hover {
            background: #dfb26a;
            color: #1a472a;
            transform: scale(1.1) translateY(-2px);
        }
        
        .btn-back-home {
            background: linear-gradient(135deg, #d4a373, #b5835a);
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(212,163,115,0.4);
            transition: all 0.3s;
        }
        .btn-back-home:hover {
            background: linear-gradient(135deg, #b5835a, #996e49);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212,163,115,0.5);
        }
        
        /* Language switcher */
        .btn-lang {
            position: fixed;
            bottom: 30px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            border-radius: 50px;
            padding: 10px 22px;
            font-weight: bold;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            transition: all 0.3s;
            color: #1a472a;
            cursor: pointer;
        }
        .btn-lang:hover { transform: scale(1.05) translateY(-2px); background: white; box-shadow: 0 12px 40px rgba(0,0,0,0.2); }
        
        /* Leaflet popup customization */
        .leaflet-popup-content-wrapper {
            border-radius: 24px;
            background: rgba(255,255,255,0.98);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .leaflet-popup-content {
            margin: 0;
            width: 290px !important;
        }
        .popup-card {
            width: 290px;
        }
        .popup-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-bottom: 3.5px solid #dfb26a;
        }
        .popup-body {
            padding: 18px;
        }
        .popup-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1a472a;
            margin-bottom: 6px;
            line-height: 1.35;
        }
        .popup-meta {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        .popup-meta i {
            color: #b5835a;
            margin-right: 5px;
            width: 14px;
        }
        .popup-btn {
            background: linear-gradient(135deg, #2d6a4f, #1a472a);
            color: white !important;
            border: none;
            padding: 10px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 5px 12px rgba(45,106,79,0.3);
        }
        .popup-btn:hover {
            background: linear-gradient(135deg, #1a472a, #0d2b1f);
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(45,106,79,0.4);
        }
        
        /* Custom markers mapping */
        .custom-marker-wrapper {
            position: relative;
            width: 40px;
            height: 40px;
        }
        .custom-marker-pin {
            width: 36px;
            height: 36px;
            border-radius: 50% 50% 50% 0;
            position: absolute;
            transform: rotate(-45deg);
            left: 50%;
            top: 50%;
            margin: -18px 0 0 -18px;
            border: 2.5px solid #ffffff;
            box-shadow: 0 5px 12px rgba(0,0,0,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .custom-marker-pin i {
            transform: rotate(45deg);
            color: #ffffff;
            font-size: 13px;
        }
        .custom-marker-shadow {
            width: 14px;
            height: 5px;
            background: rgba(0,0,0,0.4);
            border-radius: 50%;
            position: absolute;
            bottom: -5px;
            left: 50%;
            margin-left: -7px;
            filter: blur(1.5px);
        }
        .custom-marker-wrapper:hover .custom-marker-pin {
            transform: rotate(-45deg) scale(1.15) translateY(-3px);
            box-shadow: 0 8px 18px rgba(0,0,0,0.45);
        }
        
        /* Pulse blue user position pin */
        .user-location-marker {
            width: 22px;
            height: 22px;
            background: #3b82f6;
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.8);
            position: relative;
        }
        .user-location-marker::after {
            content: '';
            width: 44px;
            height: 44px;
            background: rgba(59, 130, 246, 0.35);
            border-radius: 50%;
            position: absolute;
            top: -14px;
            left: -14px;
            animation: pulse 2.2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.5); opacity: 1; }
            100% { transform: scale(1.6); opacity: 0; }
        }
        
        /* Dark layout control adjustments */
        .leaflet-bar {
            border: 1px solid rgba(255,255,255,0.2) !important;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25) !important;
            border-radius: 12px !important;
            overflow: hidden;
        }
        .leaflet-bar a {
            background-color: rgba(21, 57, 34, 0.95) !important;
            color: white !important;
            border-bottom: 1px solid rgba(255,255,255,0.15) !important;
            transition: all 0.2s;
        }
        .leaflet-bar a:hover {
            background-color: #dfb26a !important;
            color: #1a472a !important;
        }
        .leaflet-popup-close-button {
            padding: 8px !important;
            color: #666 !important;
            font-size: 1.2rem !important;
        }
        
        /* Responsive design modifications */
        @media (max-width: 768px) {
            .title-card {
                top: 12px;
                left: 12px;
                right: 12px;
                width: auto;
                padding: 16px;
            }
            .title-card p {
                display: none;
            }
            .title-card h4 {
                font-size: 1.2rem;
                margin-bottom: 10px;
            }
            .filter-panel {
                top: auto;
                bottom: 100px;
                left: 12px;
                right: 12px;
                width: auto;
                padding: 16px;
            }
            .filter-panel h5 {
                font-size: 1rem;
                margin-bottom: 10px;
                padding-bottom: 6px;
            }
            .filter-items-wrapper {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            .filter-item {
                margin-bottom: 0;
                background: rgba(255,255,255,0.08);
                padding: 6px 12px;
                border-radius: 50px;
                font-size: 0.8rem;
            }
            .filter-item input {
                width: 15px;
                height: 15px;
                margin-right: 6px;
            }
            .action-buttons {
                bottom: 25px;
                left: 12px;
                flex-direction: row;
                gap: 8px;
            }
            .btn-circle {
                width: 48px;
                height: 48px;
                font-size: 1.15rem;
            }
            .btn-lang {
                bottom: 25px;
                right: 12px;
                padding: 10px 18px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

    <!-- ແຜນທີ່ Leaflet -->
    <div id="map"></div>
    
    <!-- Title overlay Card -->
    <div class="glass-panel title-card">
        <h4 id="title-main"><i class="fas fa-map-marked-alt"></i> ແຜນທີ່ເຮືອນມໍລະດົກ</h4>
        <p id="subtitle-main">ສຳຫຼວດ ແລະ ຄົ້ນຫາ ສະຖານທີ່ສຳຄັນທາງປະຫວັດສາດ ແລະ ເຮືອນມໍລະດົກຫຼວງພະບາງ</p>
        <a href="<?php echo htmlspecialchars($backUrl); ?>" class="btn-back-home" id="btn-back"><i class="fas fa-chevron-left"></i> <span id="btn-back-text">ກັບຄືນໜ້າຫຼັກ</span></a>
    </div>
    
    <!-- Sidebar Filter panel -->
    <div class="glass-panel filter-panel">
        <h5 id="filter-title">ປະເພດເຮືອນມໍລະດົກ</h5>
        <div class="filter-items-wrapper">
            <label class="filter-item">
                <input type="checkbox" id="filter-all" checked onchange="toggleAllFilters(this)">
                <span class="filter-color-dot" style="background: #2d6a4f;"></span>
                <span id="filter-all-text">ທັງໝົດ</span>
            </label>
            <?php foreach ($categories as $cat) { 
                $cat_name = ($lang === 'lo') ? $cat['category_name_lo'] : $cat['category_name_en'];
                $color = [
                    1 => '#b5835a', // traditional
                    2 => '#dfb26a', // temple
                    3 => '#22577a', // colonial
                    4 => '#38a3a5', // hybrid
                    5 => '#e07a5f'  // shop
                ][$cat['category_id']] ?? '#2d6a4f';
            ?>
                <label class="filter-item">
                    <input type="checkbox" class="category-filter" value="<?php echo $cat['category_id']; ?>" checked onchange="applyFilters()">
                    <span class="filter-color-dot" style="background: <?php echo $color; ?>;"></span>
                    <span><?php echo htmlspecialchars($cat_name); ?></span>
                </label>
            <?php } ?>
        </div>
    </div>
    
    <!-- Quick Actions Panel -->
    <div class="action-buttons">
        <button class="btn-circle" onclick="locateUser()" title="ຊອກຫາຕຳແໜ່ງຂອງຂ້ອຍ" id="btn-locate"><i class="fas fa-crosshairs"></i></button>
        <button class="btn-circle" onclick="zoomToLuangPrabang()" title="ກັບຄືນສູນກາງຫຼວງພະບາງ" id="btn-reset-zoom"><i class="fas fa-home"></i></button>
    </div>
    
    <!-- Switch Language Button -->
    <button class="btn-lang" onclick="toggleLanguage()"><i class="fas fa-globe"></i> <span id="lang-text">English</span></button>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const housesData = <?php echo json_encode($houses); ?>;
        const currentLang = '<?php echo $lang; ?>';
        
        const categoryColors = {
            1: '#b5835a', // Traditional House
            2: '#dfb26a', // Temple
            3: '#22577a', // French Colonial
            4: '#38a3a5', // Lao-French
            5: '#e07a5f'  // Shop House
        };
        
        const categoryIcons = {
            1: 'fas fa-home',
            2: 'fas fa-dharmachakra',
            3: 'fas fa-building',
            4: 'fas fa-landmark',
            5: 'fas fa-store'
        };
        
        const translations = {
            lo: {
                title: 'ແຜນທີ່ເຮືອນມໍລະດົກ',
                subtitle: 'ສຳຫຼວດ ແລະ ຄົ້ນຫາ ສະຖານທີ່ສຳຄັນທາງປະຫວັດສາດ ແລະ ເຮືອນມໍລະດົກຫຼວງພະບາງ',
                back: 'ກັບຄືນໜ້າຫຼັກ',
                filter_title: 'ປະເພດເຮືອນມໍລະດົກ',
                all: 'ທັງໝົດ',
                show_my_location: 'ຊອກຫາຕຳແໜ່ງຂອງຂ້ອຍ',
                no_gps: 'ບໍ່ສາມາດເຂົ້າເຖິງຕຳແໜ່ງຂອງທ່ານໄດ້',
                gps_searching: 'ກຳລັງຊອກຫາຕຳແໜ່ງຂອງທ່ານ...',
                view_detail: 'ເບິ່ງລາຍລະອຽດ',
                year_built: 'ປີກໍ່ສ້າງ',
                lang_btn: 'English'
            },
            en: {
                title: 'Heritage Map',
                subtitle: 'Explore and search historical landmarks and heritage houses in Luang Prabang',
                back: 'Back to Home',
                filter_title: 'Heritage Categories',
                all: 'All Categories',
                show_my_location: 'Find my location',
                no_gps: 'Cannot access your location',
                gps_searching: 'Searching for your location...',
                view_detail: 'View Details',
                year_built: 'Year Built',
                lang_btn: 'ພາສາລາວ'
            }
        };
        
        let map;
        let markerLayerGroup;
        let userMarker = null;
        const luangPrabangCenter = [19.8893, 102.1347];
        
        function toggleLanguage() {
            const newLang = currentLang === 'lo' ? 'en' : 'lo';
            window.location.href = `map.php?lang=${newLang}<?php echo $fromAdmin ? '&from=admin' : ''; ?>`;
        }
        
        function updateTexts() {
            const t = translations[currentLang];
            $('#title-main').html(`<i class="fas fa-map-marked-alt"></i> ${t.title}`);
            $('#subtitle-main').text(t.subtitle);
            $('#btn-back-text').text(t.back);
            $('#filter-title').text(t.filter_title);
            $('#filter-all-text').text(t.all);
            $('#lang-text').text(t.lang_btn);
        }
        
        function createCustomIcon(categoryId) {
            const color = categoryColors[categoryId] || '#2d6a4f';
            const iconClass = categoryIcons[categoryId] || 'fas fa-landmark';
            
            const htmlContent = `
                <div class="custom-marker-wrapper">
                    <div class="custom-marker-shadow"></div>
                    <div class="custom-marker-pin" style="background: ${color};">
                        <i class="${iconClass}"></i>
                    </div>
                </div>
            `;
            
            return L.divIcon({
                className: 'leaflet-custom-div-icon',
                html: htmlContent,
                iconSize: [40, 40],
                iconAnchor: [20, 36],
                popupAnchor: [0, -32]
            });
        }
        
        function initMap() {
            // ສ້າງແຜນທີ່
            map = L.map('map', { zoomControl: false }).setView(luangPrabangCenter, 15);
            
            // ໂຫຼດ OSM Tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // ຍ້າຍປຸ່ມຊູມໄປຂວາລຸ່ມ
            L.control.zoom({ position: 'bottomright' }).addTo(map);
            
            markerLayerGroup = L.layerGroup().addTo(map);
            
            // ສະແດງ Markers
            applyFilters();
            
            // ຖ້າມີຂໍ້ມູນພິກັດ, ປັບຂອບເຂດແຜນທີ່ໃຫ້ພໍດີກັບ Markers ທັງໝົດ
            fitMapBounds();
        }
        
        function toggleAllFilters(allCheckbox) {
            const isChecked = $(allCheckbox).is(':checked');
            $('.category-filter').prop('checked', isChecked);
            applyFilters();
        }
        
        function updateAllCheckboxState() {
            const totalFilters = $('.category-filter').length;
            const checkedFilters = $('.category-filter:checked').length;
            $('#filter-all').prop('checked', totalFilters === checkedFilters);
        }
        
        function applyFilters() {
            updateAllCheckboxState();
            
            const checkedFilters = [];
            $('.category-filter:checked').each(function() {
                checkedFilters.push(Number($(this).val()));
            });
            
            const allChecked = $('#filter-all').is(':checked');
            
            // ລ້າງ Markers ເກົ່າ
            markerLayerGroup.clearLayers();
            
            housesData.forEach(house => {
                const catIds = house.category_ids ? house.category_ids.split(',').map(Number) : [];
                
                let show = false;
                if (allChecked) {
                    show = true;
                } else {
                    show = catIds.some(id => checkedFilters.includes(id));
                }
                
                if (show) {
                    const lat = parseFloat(house.latitude);
                    const lng = parseFloat(house.longitude);
                    
                    const primaryCatId = catIds.length > 0 ? catIds[0] : 0;
                    const markerIcon = createCustomIcon(primaryCatId);
                    
                    const marker = L.marker([lat, lng], { icon: markerIcon });
                    
                    const houseName = currentLang === 'lo' ? house.house_name_lo : house.house_name_en;
                    const style = currentLang === 'lo' ? house.architectural_style_lo : house.architectural_style_en;
                    const detailUrl = `heritage_detail.php?id=${encodeURIComponent(house.qr_code)}&lang=${currentLang}`;
                    
                    const popupContent = `
                        <div class="popup-card">
                            <img class="popup-img" src="${house.image_src}" onerror="this.src='https://placehold.co/300x200/2d6a4f/white?text=Luang+Prabang'">
                            <div class="popup-body">
                                <div class="popup-title">${escapeHtml(houseName || house.house_number || 'Heritage Building')}</div>
                                <div class="popup-meta">
                                    ${house.construction_year ? `<span><i class="fas fa-calendar-alt"></i> ${translations[currentLang].year_built}: ${house.construction_year}</span>` : ''}
                                    ${style ? `<br><span><i class="fas fa-building"></i> ${escapeHtml(style)}</span>` : ''}
                                </div>
                                <a href="${detailUrl}" class="popup-btn">
                                    <i class="fas fa-info-circle"></i> ${translations[currentLang].view_detail}
                                </a>
                            </div>
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent);
                    markerLayerGroup.addLayer(marker);
                }
            });
        }
        
        function fitMapBounds() {
            if (housesData.length === 0) return;
            const bounds = [];
            housesData.forEach(h => {
                const lat = parseFloat(h.latitude);
                const lng = parseFloat(h.longitude);
                if (lat && lng) bounds.push([lat, lng]);
            });
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
        
        function zoomToLuangPrabang() {
            map.setView(luangPrabangCenter, 15);
            fitMapBounds();
        }
        
        function locateUser() {
            if (!navigator.geolocation) {
                Swal.fire({
                    icon: 'error',
                    title: translations[currentLang].no_gps,
                    text: 'Geolocation is not supported by your browser.'
                });
                return;
            }
            
            Swal.fire({
                title: translations[currentLang].gps_searching,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            navigator.geolocation.getCurrentPosition(position => {
                Swal.close();
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                map.setView([lat, lng], 16);
                
                if (userMarker) {
                    userMarker.setLatLng([lat, lng]);
                } else {
                    const userIcon = L.divIcon({
                        className: 'user-location-div-icon',
                        html: '<div class="user-location-marker"></div>',
                        iconSize: [22, 22],
                        iconAnchor: [11, 11]
                    });
                    userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);
                }
            }, err => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: translations[currentLang].no_gps,
                    text: err.message
                });
            }, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            });
        }
        
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
        
        $(document).ready(function() {
            updateTexts();
            initMap();
        });
    </script>
</body>
</html>
