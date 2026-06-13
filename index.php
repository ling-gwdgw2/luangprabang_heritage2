<?php
// ============================================
// ໜ້າຫຼັກສຳລັບສະແກນ QR Code ເຮືອນມໍລະດົກ
// ============================================
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>ເຮືອນມໍລະດົກຫຼວງພະບາງ | Luang Prabang Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * { font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif; }
        body { 
            background: radial-gradient(circle at 10% 20%, #153922 0%, #1a472a 40%, #0d2b1f 100%); 
            min-height: 100vh; 
            color: #2b2b2b;
        }
        .heritage-card { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(12px); 
            -webkit-backdrop-filter: blur(12px);
            border-radius: 30px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); 
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
        }
        .heritage-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
        }
        #qr-reader { width: 100%; max-width: 500px; margin: 0 auto; overflow: hidden; border-radius: 20px; }
        #qr-reader__scan_region { background: #000; border-radius: 20px; }
        .btn-lang { 
            position: fixed; 
            top: 20px; 
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
        }
        .btn-lang:hover { transform: scale(1.05); background: white; box-shadow: 0 12px 40px rgba(0,0,0,0.2); }
        .heritage-title { 
            color: #dfb26a; 
            font-size: 2.6rem; 
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 2px 4px 10px rgba(0,0,0,0.4); 
        }
        .footer { background: rgba(13, 43, 31, 0.95); color: #c4c4c4; text-align: center; padding: 25px; margin-top: 50px; font-size: 0.9rem; border-top: 1px solid rgba(255,255,255,0.05); }
        .btn-scan { 
            background: linear-gradient(135deg, #d4a373, #b5835a); 
            color: white; 
            border: none; 
            padding: 14px 35px; 
            border-radius: 50px; 
            font-weight: bold; 
            box-shadow: 0 4px 15px rgba(212,163,115,0.4);
            transition: all 0.3s; 
        }
        .btn-scan:hover { 
            transform: scale(1.05) translateY(-2px); 
            background: linear-gradient(135deg, #b5835a, #996e49); 
            box-shadow: 0 8px 25px rgba(212,163,115,0.6);
            color: white; 
        }
        .btn-upload { background: linear-gradient(135deg, #6c757d, #495057); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: bold; transition: all 0.3s; margin: 5px; }
        .mode-switch { display: flex; gap: 10px; justify-content: center; margin-bottom: 25px; flex-wrap: wrap; }
        .mode-btn { 
            background: rgba(0,0,0,0.05); 
            border: 1px solid rgba(0,0,0,0.05);
            padding: 12px 30px; 
            border-radius: 50px; 
            font-weight: bold; 
            color: #2d6a4f;
            transition: all 0.3s; 
            cursor: pointer; 
        }
        .mode-btn.active { background: #2d6a4f; color: white; box-shadow: 0 4px 15px rgba(45,106,79,0.3); }
        .upload-area { border: 2px dashed #d4a373; border-radius: 20px; padding: 40px 20px; text-align: center; margin: 15px 0; cursor: pointer; transition: all 0.3s; background: rgba(0,0,0,0.02); }
        .upload-area:hover { background: rgba(212,163,115,0.08); border-color: #b5835a; }
        .upload-area.drag-over { background: rgba(212,163,115,0.15); border-color: #b5835a; }
        #preview-image { max-width: 100%; max-height: 200px; border-radius: 15px; margin-top: 15px; display: none; box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .hidden { display: none; }
        .search-result-item { transition: all 0.3s; border-radius: 15px; margin-bottom: 12px; border: 1px solid rgba(0,0,0,0.05) !important; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
        .search-result-item:hover { background: rgba(45,106,79,0.05) !important; transform: translateX(8px); border-color: rgba(45,106,79,0.2) !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.5s ease-out; }
    </style>
</head>
<body>
    <button class="btn-lang" onclick="toggleLanguage()"><i class="fas fa-globe"></i> <span id="lang-text">English</span></button>
    
    <div class="container py-4">
        <div class="text-center mb-4 fade-in">
            <i class="fas fa-landmark fa-4x text-white mb-3"></i>
            <h1 class="heritage-title" id="title-main">ມໍລະດົກຫຼວງພະບາງ</h1>
            <p id="subtitle-main" class="text-white">ສະແກນ QR Code ເພື່ອຮຽນຮູ້ເລື່ອງລາວຂອງເຮືອນມໍລະດົກແຕ່ລະຫຼັງ</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="heritage-card p-4 fade-in">
                    <div class="text-center mb-3">
                        <i class="fas fa-qrcode fa-4x" style="color: #2d6a4f;"></i>
                        <h3 id="scanner-title" class="mt-2">ສະແກນ QR Code</h3>
                        <p id="scanner-desc" class="text-muted">ສະແກນດ້ວຍກ້ອງ ຫຼື ອັບໂຫຼດຮູບ QR Code</p>
                    </div>
                    
                    <div class="mode-switch">
                        <button class="mode-btn" id="mode-upload" onclick="switchMode('upload')"><i class="fas fa-image"></i> <span id="mode-upload-text">ອັບໂຫຼດຮູບພາບ</span></button>
                    </div>
                    
                    <div id="camera-mode" class="scanner-mode">
                        <div id="qr-reader" class="qr-scanner-container"></div>
                        <div class="text-center mt-3">
                            <button class="btn-scan" onclick="startScanner()" id="scan-btn"><i class="fas fa-camera"></i> <span id="scan-btn-text">ເປີດກ້ອງສະແກນ</span></button>
                        </div>
                    </div>
                    
                    <div id="upload-mode" class="upload-mode hidden">
                        <div class="upload-area" id="upload-area" onclick="document.getElementById('file-input').click()">
                            <i class="fas fa-cloud-upload-alt fa-3x" style="color: #2d6a4f;"></i>
                            <p id="upload-text" class="mt-2 mb-1">ກົດເພື່ອເລືອກຮູບ ຫຼື ລາກຮູບມາວາງທີ່ນີ້</p>
                            <p class="text-muted small" id="upload-supported">ຮອງຮັບ PNG, JPG, JPEG, GIF, WEBP</p>
                            <input type="file" id="file-input" accept="image/*" style="display: none;" onchange="handleImageUpload(this)">
                        </div>
                        <div class="text-center"><img id="preview-image" alt="Preview"></div>
                        <div id="upload-result" class="upload-result"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 col-lg-6">
                <div class="heritage-card p-4 fade-in">
                    <h4 id="search-title" class="text-center"><i class="fas fa-search"></i> ຄົ້ນຫາເຮືອນມໍລະດົກ</h4>
                    <div class="input-group mt-3">
                        <input type="text" id="search-input" class="form-control form-control-lg" placeholder="ປ້ອນຊື່ເຮືອນ ຫຼື ເລກທີ່ຢູ່...">
                        <button class="btn btn-scan" onclick="searchHeritage()"><i class="fas fa-search"></i> <span id="search-btn-text">ຄົ້ນຫາ</span></button>
                    </div>
                    <div id="search-results" class="mt-3"></div>
                </div>
            </div>
        </div>
        
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 col-lg-6">
                <div class="heritage-card p-4 fade-in">
                    <div class="text-center">
                        <i class="fas fa-info-circle fa-3x" style="color: #2d6a4f;"></i>
                        <h5 id="info-title">ວິທີການໃຊ້ງານ</h5>
                        <p id="info-desc" class="text-muted">1. ເລືອກ "ສະແກນດ້ວຍກ້ອງ" ເພື່ອໃຊ້ກ້ອງຖ່າຍຮູບ<br>2. ຫຼື ເລືອກ "ອັບໂຫຼດຮູບພາບ" ເພື່ອເລືອກຮູບທີ່ມີ QR Code<br>3. ລະບົບຈະສະແດງຂໍ້ມູນລາຍລະອຽດຂອງເຮືອນຫຼັງນັ້ນ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; nadin DUANGTHACHIT ນັກສຶກສາ ວິສະວະກຳຄອມພິວເຕີ</p>
    </div>
    
    <script>
        let html5QrCode = null;
        let currentLang = 'lo';
        let isScanning = false;
        let currentMode = 'camera';
        
        const translations = {
            lo: {
                title_main: 'ເຮືອນມໍລະດົກຫຼວງພະບາງ',
                subtitle_main: 'ສະແກນ QR Code ເພື່ອຮຽນຮູ້ເລື່ອງລາວຂອງເຮືອນມໍລະດົກແຕ່ລະຫຼັງ',
                scanner_title: 'ສະແກນ QR Code',
                scanner_desc: 'ສະແກນດ້ວຍກ້ອງ ຫຼື ອັບໂຫຼດຮູບ QR Code',
                loading_text: 'ກຳລັງໂຫຼດຂໍ້ມູນ...',
                scan_btn: 'ເປີດກ້ອງສະແກນ',
                stop_btn: 'ຢຸດສະແກນ',
                mode_camera: 'ສະແກນດ້ວຍກ້ອງ',
                mode_upload: 'ອັບໂຫຼດຮູບພາບ',
                upload_text: 'ກົດເພື່ອເລືອກຮູບ ຫຼື ລາກຮູບມາວາງທີ່ນີ້',
                upload_supported: 'ຮອງຮັບ PNG, JPG, JPEG, GIF, WEBP',
                scanning_qr: 'ກຳລັງສະແກນ QR Code...',
                scan_success: 'ສະແກນສຳເລັດ! ກຳລັງໂຫຼດ...',
                no_qr_found: 'ບໍ່ພົບ QR Code ໃນຮູບພາບ',
                search_title: 'ຄົ້ນຫາເຮືອນມໍລະດົກ',
                search_btn: 'ຄົ້ນຫາ',
                search_placeholder: 'ປ້ອນຊື່ເຮືອນ ຫຼື ເລກທີ່ຢູ່...',
                no_results: 'ບໍ່ພົບຂໍ້ມູນ',
                error_scan: 'ການສະແກນລົ້ມເຫຼວ, ກະລຸນາລອງໃໝ່',
                info_title: 'ວິທີການໃຊ້ງານ',
                info_desc: '1. ເລືອກ "ສະແກນດ້ວຍກ້ອງ" ເພື່ອໃຊ້ກ້ອງຖ່າຍຮູບ\n2. ຫຼື ເລືອກ "ອັບໂຫຼດຮູບພາບ" ເພື່ອເລືອກຮູບທີ່ມີ QR Code\n3. ລະບົບຈະສະແດງຂໍ້ມູນລາຍລະອຽດຂອງເຮືອນຫຼັງນັ້ນ'
            },
            en: {
                title_main: 'Luang Prabang Heritage',
                subtitle_main: 'Scan QR Code to learn the stories of each heritage house',
                scanner_title: 'Scan QR Code',
                scanner_desc: 'Scan with camera or upload QR code image',
                loading_text: 'Loading data...',
                scan_btn: 'Start Scanner',
                stop_btn: 'Stop Scanner',
                mode_camera: 'Scan with Camera',
                mode_upload: 'Upload Image',
                upload_text: 'Click to select image or drag & drop image here',
                upload_supported: 'Supports PNG, JPG, JPEG, GIF, WEBP',
                scanning_qr: 'Scanning QR Code...',
                scan_success: 'Scan successful! Loading...',
                no_qr_found: 'No QR Code found in the image',
                search_title: 'Search Heritage House',
                search_btn: 'Search',
                search_placeholder: 'Enter house name or address...',
                no_results: 'No results found',
                error_scan: 'Scan failed, please try again',
                info_title: 'How to use',
                info_desc: '1. Select "Scan with Camera" to use camera\n2. Or select "Upload Image" to choose an image with QR Code\n3. System will display detailed information'
            }
        };
        
        function toggleLanguage() { currentLang = currentLang === 'lo' ? 'en' : 'lo'; updateLanguage(); }
        
        function updateLanguage() {
            $('#title-main').text(translations[currentLang].title_main);
            $('#subtitle-main').text(translations[currentLang].subtitle_main);
            $('#scanner-title').text(translations[currentLang].scanner_title);
            $('#scanner-desc').text(translations[currentLang].scanner_desc);
            const btnText = isScanning ? translations[currentLang].stop_btn : translations[currentLang].scan_btn;
            $('#scan-btn-text').text(btnText);
            $('#mode-camera-text').text(translations[currentLang].mode_camera);
            $('#mode-upload-text').text(translations[currentLang].mode_upload);
            $('#upload-text').text(translations[currentLang].upload_text);
            $('#upload-supported').text(translations[currentLang].upload_supported);
            $('#search-title').html('<i class="fas fa-search"></i> ' + translations[currentLang].search_title);
            $('#search-btn-text').text(translations[currentLang].search_btn);
            $('#search-input').attr('placeholder', translations[currentLang].search_placeholder);
            $('#info-title').text(translations[currentLang].info_title);
            $('#info-desc').text(translations[currentLang].info_desc);
            $('#lang-text').text(currentLang === 'lo' ? 'English' : 'ພາສາລາວ');
        }
        
        function switchMode(mode) {
            currentMode = mode;
            if (mode === 'camera') {
                $('#camera-mode').removeClass('hidden');
                $('#upload-mode').addClass('hidden');
                $('#mode-camera').addClass('active');
                $('#mode-upload').removeClass('active');
                if (html5QrCode && isScanning) { html5QrCode.stop(); isScanning = false; $('#scan-btn-text').text(translations[currentLang].scan_btn); }
            } else {
                $('#camera-mode').addClass('hidden');
                $('#upload-mode').removeClass('hidden');
                $('#mode-camera').removeClass('active');
                $('#mode-upload').addClass('active');
                if (html5QrCode && isScanning) { html5QrCode.stop(); isScanning = false; $('#scan-btn-text').text(translations[currentLang].scan_btn); }
            }
        }
        
        async function startScanner() {
            if (html5QrCode && isScanning) {
                await html5QrCode.stop();
                isScanning = false;
                $('#scan-btn-text').text(translations[currentLang].scan_btn);
                return;
            }
            try {
                html5QrCode = new Html5Qrcode("qr-reader");
                await html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } }, onScanSuccess, onScanError);
                isScanning = true;
                $('#scan-btn-text').text(translations[currentLang].stop_btn);
            } catch (err) {
                Swal.fire({ icon: 'error', title: translations[currentLang].error_scan, text: err.message });
            }
        }
        
        function onScanSuccess(decodedText) {
            if (html5QrCode && isScanning) { html5QrCode.stop(); isScanning = false; $('#scan-btn-text').text(translations[currentLang].scan_btn); }
            window.location.href = `heritage_detail.php?id=${encodeURIComponent(decodedText)}&lang=${currentLang}`;
        }
        
        function onScanError(errorMessage) { console.log(errorMessage); }
        
        async function handleImageUpload(input) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) { $('#preview-image').attr('src', e.target.result).show(); };
            reader.readAsDataURL(file);
            $('#upload-result').html(`<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> ${translations[currentLang].scanning_qr}</div>`);
            try {
                const html5QrCodeScanner = new Html5Qrcode("upload-result");
                const result = await html5QrCodeScanner.scanFile(file, false);
                $('#upload-result').html(`<div class="alert alert-success"><i class="fas fa-check-circle"></i> ${translations[currentLang].scan_success}</div>`);
                setTimeout(() => { window.location.href = `heritage_detail.php?id=${encodeURIComponent(result)}&lang=${currentLang}`; }, 1000);
            } catch (err) {
                $('#upload-result').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${translations[currentLang].no_qr_found}</div>`);
                setTimeout(() => { $('#upload-result').html(''); }, 3000);
            }
        }
        
        const uploadArea = document.getElementById('upload-area');
        if (uploadArea) {
            uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.classList.add('drag-over'); });
            uploadArea.addEventListener('dragleave', () => { uploadArea.classList.remove('drag-over'); });
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('drag-over');
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    const input = document.getElementById('file-input');
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    handleImageUpload(input);
                } else { Swal.fire('Error', 'ກະລຸນາລາກຮູບພາບເທົ່ານັ້ນ', 'error'); }
            });
        }
        
        function searchHeritage() {
            const keyword = $('#search-input').val();
            if (keyword.trim() === '') { Swal.fire({ icon: 'warning', title: translations[currentLang].search_title, text: 'ກະລຸນາປ້ອນຄຳຄົ້ນຫາ' }); return; }
            $('#search-results').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> ກຳລັງຄົ້ນຫາ...</div>');
            $.ajax({
                url: 'search_heritage.php',
                method: 'POST',
                data: { keyword: keyword, lang: currentLang },
                dataType: 'json',
                success: function(response) { displaySearchResults(response); },
                error: function() { $('#search-results').html('<div class="alert alert-danger">' + translations[currentLang].no_results + '</div>'); }
            });
        }
        
        function displaySearchResults(results) {
            const container = $('#search-results');
            if (results.length === 0) { container.html('<div class="alert alert-warning text-center">' + translations[currentLang].no_results + '</div>'); return; }
            let html = '<div class="list-group">';
            results.forEach(house => {
                const name = currentLang === 'lo' ? house.house_name_lo : house.house_name_en;
                html += `<a href="heritage_detail.php?id=${house.qr_code}&lang=${currentLang}" class="list-group-item list-group-item-action search-result-item"><div class="d-flex justify-content-between align-items-center"><div><h6 class="mb-1"><i class="fas fa-home"></i> ${name || house.house_number}</h6><small class="text-muted">${currentLang === 'lo' ? house.architectural_style_lo : house.architectural_style_en || ''}</small></div><i class="fas fa-chevron-right text-success"></i></div></a>`;
            });
            html += '</div>';
            container.html(html);
        }
        
        $(document).ready(function() { updateLanguage(); switchMode('camera'); });
    </script>
</body>
</html>