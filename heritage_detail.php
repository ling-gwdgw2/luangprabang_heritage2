<?php
// ============================================
// ໜ້າສະແດງລາຍລະອຽດເຮືອນມໍລະດົກ
// ============================================
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>ລາຍລະອຽດເຮືອນມໍລະດົກ | Heritage Detail</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        * { font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif; }
        body { 
            background: radial-gradient(circle at 10% 20%, #153922 0%, #1a472a 40%, #0d2b1f 100%); 
            min-height: 100vh;
            color: #333;
        }
        .hero-section { 
            position: relative; 
            background: linear-gradient(135deg, #153922, #2d6a4f); 
            color: white; 
            padding: 70px 20px; 
            text-align: center; 
            margin-bottom: -50px; 
            border-radius: 0 0 50px 50px; 
            overflow: hidden; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .hero-section::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.45); z-index: 1; }
        .hero-section h1, .hero-section .subtitle, .hero-section .qr-badge { position: relative; z-index: 2; }
        .hero-section h1 { font-size: 2.8rem; font-weight: 800; text-shadow: 2px 4px 10px rgba(0,0,0,0.4); margin-bottom: 15px; color: #dfb26a; }
        .hero-section .subtitle { font-size: 1.1rem; color: rgba(255,255,255,0.85); margin-bottom: 10px; }
        .hero-section .qr-badge { 
            display: inline-block;
            background: rgba(255, 255, 255, 0.95); 
            color: #1a472a; 
            padding: 8px 25px; 
            border-radius: 50px; 
            font-size: 0.9rem; 
            font-weight: bold; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.3); 
            margin-top: 15px;
        }
        .heritage-card { 
            background: rgba(255, 255, 255, 0.95); 
            border-radius: 35px; 
            overflow: hidden; 
            box-shadow: 0 30px 60px rgba(0,0,0,0.3); 
            margin-top: 70px; 
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Slider */
        .main-slider-container { position: relative; width: 100%; height: 520px; overflow: hidden; background: #0c0d0c; }
        .slide { display: none; width: 100%; height: 100%; }
        .slide.active { display: block; }
        .slide img { width: 100%; height: 100%; object-fit: cover; object-position: center; transition: transform 0.5s; }
        .slide img:hover { transform: scale(1.02); }
        .slider-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; transition: all 0.3s; z-index: 10; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; backdrop-filter: blur(2px); }
        .slider-btn:hover { background: #dfb26a; color: #1a472a; transform: translateY(-50%) scale(1.1); }
        .slider-prev { left: 20px; }
        .slider-next { right: 20px; }
        .slide-dots { position: absolute; bottom: 20px; left: 0; right: 0; text-align: center; z-index: 10; }
        .dot { display: inline-block; width: 10px; height: 10px; margin: 0 6px; background: rgba(255,255,255,0.45); border-radius: 50%; cursor: pointer; transition: all 0.3s; }
        .dot.active { background: #dfb26a; width: 28px; border-radius: 10px; }
        .fullscreen-btn { position: absolute; bottom: 20px; right: 20px; background: rgba(0,0,0,0.5); color: white; border: none; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; transition: all 0.3s; z-index: 10; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; backdrop-filter: blur(2px); }
        .fullscreen-btn:hover { background: #dfb26a; color: #1a472a; transform: scale(1.1); }
        
        .thumbnail-gallery { display: flex; gap: 15px; overflow-x: auto; padding: 20px; background: rgba(0,0,0,0.02); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .thumbnail-gallery::-webkit-scrollbar { height: 6px; }
        .thumbnail-gallery::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); border-radius: 10px; }
        .thumbnail-gallery::-webkit-scrollbar-thumb { background: #2d6a4f; border-radius: 10px; }
        .thumbnail { width: 110px; height: 85px; object-fit: cover; border-radius: 16px; cursor: pointer; transition: all 0.3s; border: 3px solid transparent; flex-shrink: 0; }
        .thumbnail:hover { transform: scale(1.08) translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .thumbnail.active { border-color: #2d6a4f; box-shadow: 0 0 0 4px rgba(45,106,79,0.25); }
        
        /* Modal Fullscreen */
        .image-modal-full { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.98); z-index: 9999; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0; visibility: hidden; transition: all 0.3s; }
        .image-modal-full.active { opacity: 1; visibility: visible; }
        .image-modal-full img { max-width: 95%; max-height: 85%; object-fit: contain; cursor: default; border-radius: 8px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .image-modal-full .close-modal { position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; cursor: pointer; z-index: 10000; background: rgba(0,0,0,0.5); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
        .image-modal-full .close-modal:hover { background: #dfb26a; color: #1a472a; transform: scale(1.1); }
        
        .info-section { padding: 30px 40px; border-bottom: 1px solid rgba(0,0,0,0.06); transition: all 0.3s; }
        .info-section:hover { background: rgba(45,106,79,0.02); }
        .info-icon { width: 50px; height: 50px; background: linear-gradient(135deg, #2d6a4f, #1a472a); border-radius: 18px; display: inline-flex; align-items: center; justify-content: center; margin-right: 20px; color: white; font-size: 1.3rem; box-shadow: 0 5px 15px rgba(26,71,42,0.25); }
        .info-title { font-weight: 800; color: #1a472a; font-size: 1.25rem; margin-bottom: 12px; }
        .info-content { color: #444; line-height: 1.8; font-size: 1.05rem; }
        .style-badge { display: inline-block; background: linear-gradient(135deg, #dfb26a, #b5835a); color: white; padding: 10px 25px; border-radius: 50px; font-size: 0.95rem; font-weight: 700; margin: 20px 0; box-shadow: 0 5px 15px rgba(223,178,106,0.3); }
        .map-container { border-radius: 24px; overflow: hidden; margin-top: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.05); }
        .share-section { padding: 30px; text-align: center; background: rgba(0,0,0,0.02); }
        .share-btn { width: 50px; height: 50px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 10px; transition: all 0.3s; text-decoration: none; font-size: 1.3rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .share-btn.copy { background: #6c757d; color: white; }
        .share-btn:hover { transform: translateY(-4px) scale(1.05); filter: brightness(1.05); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .back-button { display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #d4a373, #b5835a); color: white; padding: 14px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; box-shadow: 0 8px 25px rgba(212,163,115,0.4); transition: all 0.3s; margin: 30px 0; }
        .back-button:hover { background: linear-gradient(135deg, #b5835a, #996e49); color: white; transform: translateX(-5px); box-shadow: 0 12px 35px rgba(212,163,115,0.6); }
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
        
        .loading-container { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 450px; }
        .loading-spinner { width: 65px; height: 65px; border: 5px solid rgba(255,255,255,0.1); border-top-color: #dfb26a; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        @media (max-width: 768px) {
            .hero-section h1 { font-size: 2rem; }
            .main-slider-container { height: 320px; }
            .thumbnail { width: 90px; height: 70px; }
            .info-section { padding: 20px 25px; }
            .info-icon { width: 40px; height: 40px; font-size: 1rem; margin-right: 15px; }
        }
    </style>
</head>
<body>
    <button class="btn-lang" onclick="toggleLanguage()"><i class="fas fa-globe"></i> <span id="lang-text">English</span></button>

    <div class="container py-4">
        <div id="detail-content" class="fade-in-up"><div class="loading-container"><div class="loading-spinner"></div><p class="mt-3 text-muted" id="loading-text">ກຳລັງໂຫຼດຂໍ້ມູນ...</p></div></div>
        <div class="text-center"><a href="index.php" class="back-button" id="back-btn"><i class="fas fa-arrow-left"></i> <span id="back-text">ກັບຄືນ</span></a></div>
    </div>
    
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        let qrId = urlParams.get('id');
        const lang = urlParams.get('lang') || 'lo';
        
        function extractIdFromUrl(input) {
            if (input && input.includes('heritage_detail.php')) {
                const match = input.match(/[?&]id=([^&]+)/);
                return match ? match[1] : input;
            }
            return input;
        }
        qrId = extractIdFromUrl(qrId);
        
        function toggleLanguage() {
            const newLang = lang === 'lo' ? 'en' : 'lo';
            window.location.href = `heritage_detail.php?id=${encodeURIComponent(qrId)}&lang=${newLang}`;
        }
        
        function updateLangButton() {
            $('#lang-text').text(lang === 'lo' ? 'English' : 'ພາສາລາວ');
            $('#loading-text').text(lang === 'lo' ? 'ກຳລັງໂຫຼດຂໍ້ມູນ...' : 'Loading...');
            $('#back-text').text(lang === 'lo' ? 'ກັບຄືນ' : 'Back');
        }
        
        let currentSlideIndex = 0;
        let allImages = [];
        let slideInterval;
        
        $(document).ready(function() { updateLangButton(); loadHeritageDetail(); });
        
        function loadHeritageDetail() {
            $.ajax({
                url: 'api/get_heritage.php',
                method: 'POST',
                data: { qr_code: qrId, lang: lang },
                dataType: 'json',
                success: function(response) {
                    if (response.success) { displayDetail(response.data); logVisit(response.data.house_id); }
                    else { showError(response.message); }
                },
                error: function() { showError('ບໍ່ສາມາດໂຫຼດຂໍ້ມູນໄດ້ | Cannot load data'); }
            });
        }
        
        function displayDetail(data) {
            const isLao = lang === 'lo';
            const houseName = isLao ? data.house_name_lo : data.house_name_en;
            const ownerName = isLao ? data.owner_name_lo : data.owner_name_en;
            const architecturalStyle = isLao ? data.architectural_style_lo : data.architectural_style_en;
            const historicalSignificance = isLao ? data.historical_significance_lo : data.historical_significance_en;
            const description = isLao ? data.description_lo : data.description_en;
            
            function toImageSrc(path) {
                if (!path) return null;
                const clean = path.replace(/^uploads\//, '');
                return `uploads/${clean}`;
            }

            allImages = [];
            if (data.image_main && data.image_main !== '') {
                allImages.push({ src: toImageSrc(data.image_main), caption: isLao ? 'ຮູບຫຼັກ' : 'Main Image' });
            }
            if (data.images && data.images.length > 0) {
                data.images.forEach(img => {
                    allImages.push({ src: toImageSrc(img.image_path), caption: isLao ? img.image_caption_lo : img.image_caption_en });
                });
            }
            if (allImages.length === 0) {
                allImages.push({ src: 'https://placehold.co/800x500/2d6a4f/white?text=ມໍລະດົກຫຼວງພະບາງ', caption: '' });
            }
            
            let html = `<div class="hero-section"><h1>${escapeHtml(houseName || data.house_number || (isLao ? 'ເຮືອນມໍລະດົກຫຼວງພະບາງ' : 'Luang Prabang Heritage House'))}</h1>${data.house_number ? `<p class="subtitle"><i class="fas fa-map-pin"></i> ${isLao ? 'ເລກທີ່' : 'No.'} ${data.house_number}</p>` : ''}<div class="qr-badge"><i class="fas fa-qrcode"></i> ${data.qr_code}</div></div>
                        <div class="heritage-card">
                        <div class="main-slider-container" id="mainSlider">`;
            
            for (let i = 0; i < allImages.length; i++) {
                html += `<div class="slide ${i === 0 ? 'active' : ''}" data-index="${i}">
                            <img src="${allImages[i].src}" alt="Slide ${i+1}" onclick="openFullscreen()">
                         </div>`;
            }
            if (allImages.length > 1) {
                html += `<button class="slider-btn slider-prev" onclick="changeSlide(-1)"><i class="fas fa-chevron-left"></i></button>
                         <button class="slider-btn slider-next" onclick="changeSlide(1)"><i class="fas fa-chevron-right"></i></button>
                         <div class="slide-dots" id="slideDots">`;
                for (let i = 0; i < allImages.length; i++) {
                    html += `<span class="dot ${i === 0 ? 'active' : ''}" onclick="goToSlide(${i})"></span>`;
                }
                html += `</div>`;
            }
            html += `<button class="fullscreen-btn" onclick="openFullscreen()"><i class="fas fa-expand"></i></button>
                    </div>`;
            
            if (allImages.length > 1) {
                html += `<div class="thumbnail-gallery" id="thumbnailGallery">`;
                for (let i = 0; i < allImages.length; i++) {
                    html += `<img src="${allImages[i].src}" class="thumbnail ${i === 0 ? 'active' : ''}" onclick="goToSlide(${i})" data-index="${i}">`;
                }
                html += `</div>`;
            }
            
            if (architecturalStyle) html += `<div class="text-center"><span class="style-badge"><i class="fas fa-building"></i> ${escapeHtml(architecturalStyle)}</span></div>`;
            if (data.house_type) html += `<div class="info-section"><div class="d-flex align-items-center"><div class="info-icon"><i class="fas fa-home"></i></div><div><div class="info-title">${isLao ? 'ປະເພດເຮືອນ' : 'House Type'}</div><div class="info-content">${escapeHtml(data.house_type)}</div></div></div></div>`;
            if (data.building_material) html += `<div class="info-section"><div class="d-flex align-items-center"><div class="info-icon"><i class="fas fa-cubes"></i></div><div><div class="info-title">${isLao ? 'ວັດສະດຸກໍ່ສ້າງ' : 'Building Material'}</div><div class="info-content">${escapeHtml(data.building_material)}</div></div></div></div>`;
            if (ownerName) html += `<div class="info-section"><div class="d-flex align-items-center"><div class="info-icon"><i class="fas fa-user"></i></div><div><div class="info-title">${isLao ? 'ເຈົ້າຂອງ' : 'Owner'}</div><div class="info-content">${escapeHtml(ownerName)}</div></div></div></div>`;
            if (data.construction_year) html += `<div class="info-section"><div class="d-flex align-items-center"><div class="info-icon"><i class="fas fa-calendar-alt"></i></div><div><div class="info-title">${isLao ? 'ປີກໍ່ສ້າງ' : 'Year Built'}</div><div class="info-content">${data.construction_year} ${isLao ? 'ຄ.ສ.' : 'CE'}</div></div></div></div>`;
            if (historicalSignificance) html += `<div class="info-section"><div class="d-flex align-items-start"><div class="info-icon"><i class="fas fa-history"></i></div><div><div class="info-title">${isLao ? 'ຄວາມສຳຄັນທາງປະຫວັດສາດ' : 'Historical Significance'}</div><div class="info-content">${escapeHtml(historicalSignificance)}</div></div></div></div>`;
            if (description) html += `<div class="info-section"><div class="d-flex align-items-start"><div class="info-icon"><i class="fas fa-align-left"></i></div><div><div class="info-title">${isLao ? 'ລາຍລະອຽດ' : 'Description'}</div><div class="info-content">${escapeHtml(description)}</div></div></div></div>`;
            
            if (data.latitude && data.longitude && data.latitude != 0 && data.longitude != 0) {
                html += `<div class="info-section"><div class="d-flex align-items-start"><div class="info-icon"><i class="fas fa-map-marker-alt"></i></div><div style="flex:1"><div class="info-title">${isLao ? 'ທີ່ຕັ້ງ' : 'Location'}</div><div class="map-container"><iframe width="100%" height="250" frameborder="0" style="border:0; border-radius: 15px;" src="https://www.openstreetmap.org/export/embed.html?bbox=${data.longitude-0.005},${data.latitude-0.005},${data.longitude+0.005},${data.latitude+0.005}&layer=mapnik&marker=${data.latitude},${data.longitude}" allowfullscreen></iframe></div><div class="mt-2"><a href="https://maps.google.com/?q=${data.latitude},${data.longitude}" target="_blank" class="btn btn-outline-success btn-sm"><i class="fas fa-external-link-alt"></i> ${isLao ? 'ເບິ່ງໃນ Google Maps' : 'View on Google Maps'}</a><div id="distance-display" class="mt-2 text-success" style="font-weight: bold; display: none;"></div></div></div></div></div>`;
                
                setTimeout(() => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(position => {
                            const userLat = position.coords.latitude;
                            const userLon = position.coords.longitude;
                            const R = 6371; // km
                            const dLat = (data.latitude - userLat) * Math.PI / 180;
                            const dLon = (data.longitude - userLon) * Math.PI / 180;
                            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                      Math.cos(userLat * Math.PI/180) * Math.cos(data.latitude * Math.PI/180) *
                                      Math.sin(dLon/2) * Math.sin(dLon/2);
                            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                            const d = R * c;
                            
                            let distText = '';
                            if (d < 1) {
                                distText = `${Math.round(d * 1000)} ${isLao ? 'ແມັດ' : 'meters'}`;
                            } else {
                                distText = `${d.toFixed(2)} ${isLao ? 'ກິໂລແມັດ' : 'km'}`;
                            }
                            $('#distance-display').html(`<i class="fas fa-route"></i> ${isLao ? 'ໄລຍະຫ່າງຈາກທ່ານ' : 'Distance from you'}: <strong>${distText}</strong>`).show();
                        }, err => {
                            console.log("Geolocation error:", err);
                        }, {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        });
                    }
                }, 300);
            }
            
            html += `<div class="share-section"><div class="info-title mb-3"><i class="fas fa-share-alt"></i> ${isLao ? 'ແບ່ງປັນ' : 'Share'}</div>
                   
                    <a href="#" class="share-btn copy" onclick="copyToClipboard(); return false;"><i class="fas fa-link"></i></a>
                    </div></div>`;
            
            $('#detail-content').html(html);
            document.title = `${houseName || (isLao ? 'ມໍລະດົກ' : 'Heritage')} - ${isLao ? 'ມໍລະດົກຫຼວງພະບາງ' : 'Luang Prabang Heritage'}`;
            
            if (allImages.length > 1) {
                startAutoSlide();
            }
        }
        
        function changeSlide(direction) {
            stopAutoSlide();
            let newIndex = currentSlideIndex + direction;
            if (newIndex < 0) newIndex = allImages.length - 1;
            if (newIndex >= allImages.length) newIndex = 0;
            goToSlide(newIndex);
            startAutoSlide();
        }
        
        function goToSlide(index) {
            if (index === currentSlideIndex) return;
            
            $('.slide').removeClass('active');
            $(`.slide[data-index="${index}"]`).addClass('active');
            $('.dot').removeClass('active');
            $('.dot').eq(index).addClass('active');
            $('.thumbnail').removeClass('active');
            $('.thumbnail').eq(index).addClass('active');
            currentSlideIndex = index;
        }
        
        function startAutoSlide() {
            if (slideInterval) clearInterval(slideInterval);
            if (allImages.length > 1) {
                slideInterval = setInterval(() => {
                    let nextIndex = currentSlideIndex + 1;
                    if (nextIndex >= allImages.length) nextIndex = 0;
                    goToSlide(nextIndex);
                }, 4000);
            }
        }
        
        function stopAutoSlide() {
            if (slideInterval) {
                clearInterval(slideInterval);
                slideInterval = null;
            }
        }
        
        function openFullscreen() {
            stopAutoSlide();
            let imageSrc = allImages[currentSlideIndex].src;
            let fullscreenModal = document.getElementById('fullscreenModal');
            
            if (!fullscreenModal) {
                fullscreenModal = document.createElement('div');
                fullscreenModal.id = 'fullscreenModal';
                fullscreenModal.className = 'image-modal-full';
                fullscreenModal.innerHTML = `
                    <span class="close-modal">&times;</span>
                    <img id="fullscreenImage" src="">
                    <div class="modal-caption" id="modalCaptionFull"></div>
                    <button class="slider-btn fullscreen-prev" id="fullscreenPrev"><i class="fas fa-chevron-left"></i></button>
                    <button class="slider-btn fullscreen-next" id="fullscreenNext"><i class="fas fa-chevron-right"></i></button>
                `;
                document.body.appendChild(fullscreenModal);
                
                fullscreenModal.addEventListener('click', function(e) { 
                    if (e.target === fullscreenModal || e.target.classList.contains('close-modal')) {
                        fullscreenModal.classList.remove('active');
                        startAutoSlide();
                    }
                });
                
                document.getElementById('fullscreenPrev').addEventListener('click', function(e) {
                    e.stopPropagation();
                    let newIndex = currentSlideIndex - 1;
                    if (newIndex < 0) newIndex = allImages.length - 1;
                    goToSlide(newIndex);
                    document.getElementById('fullscreenImage').src = allImages[currentSlideIndex].src;
                    document.getElementById('modalCaptionFull').textContent = allImages[currentSlideIndex].caption || '';
                });
                
                document.getElementById('fullscreenNext').addEventListener('click', function(e) {
                    e.stopPropagation();
                    let newIndex = currentSlideIndex + 1;
                    if (newIndex >= allImages.length) newIndex = 0;
                    goToSlide(newIndex);
                    document.getElementById('fullscreenImage').src = allImages[currentSlideIndex].src;
                    document.getElementById('modalCaptionFull').textContent = allImages[currentSlideIndex].caption || '';
                });
                
                document.addEventListener('keydown', function(e) { 
                    if (fullscreenModal.classList.contains('active')) {
                        if (e.key === 'Escape') {
                            fullscreenModal.classList.remove('active');
                            startAutoSlide();
                        } else if (e.key === 'ArrowLeft') {
                            let newIndex = currentSlideIndex - 1;
                            if (newIndex < 0) newIndex = allImages.length - 1;
                            goToSlide(newIndex);
                            document.getElementById('fullscreenImage').src = allImages[currentSlideIndex].src;
                            document.getElementById('modalCaptionFull').textContent = allImages[currentSlideIndex].caption || '';
                        } else if (e.key === 'ArrowRight') {
                            let newIndex = currentSlideIndex + 1;
                            if (newIndex >= allImages.length) newIndex = 0;
                            goToSlide(newIndex);
                            document.getElementById('fullscreenImage').src = allImages[currentSlideIndex].src;
                            document.getElementById('modalCaptionFull').textContent = allImages[currentSlideIndex].caption || '';
                        }
                    }
                });
            }
            
            document.getElementById('fullscreenImage').src = imageSrc;
            document.getElementById('modalCaptionFull').textContent = allImages[currentSlideIndex].caption || '';
            fullscreenModal.classList.add('active');
        }
        
        function showError(message) { 
            const isLao = lang === 'lo'; 
            $('#detail-content').html(`<div class="error-section">
            <div class="error-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h3 class="text-danger">${isLao ? 'ບໍ່ພົບຂໍ້ມູນ' : 'Data Not Found'}</h3>
            <p class="text-muted">${escapeHtml(message)}</p>
            <p class="mt-3">${isLao ? 'ກະລຸນາກວດສອບ QR Code ແລະລອງໃໝ່ອີກຄັ້ງ' : 'Please check the QR Code and try again'}</p>
            <div class="custom-divider"></div>
            <a href="index.php" class="back-button mt-3" style="display: inline-flex;"><i class="fas fa-home"></i> ${isLao ? 'ໜ້າຫຼັກ' : 'Home'}</a></div>`); 
        }
        
        function logVisit(houseId) { $.ajax({ url: 'api/log_visit.php', method: 'POST', data: { house_id: houseId } }); }
        function copyToClipboard() { navigator.clipboard.writeText(window.location.href).then(() => { Swal.fire({ icon: 'success', title: lang === 'lo' ? 'ສຳເນົາສຳເລັດ' : 'Copied!', text: lang === 'lo' ? 'ລິ້ງຖືກສຳເນົາໃສ່ Clipboard ແລ້ວ' : 'Link copied to clipboard', timer: 2000, showConfirmButton: false }); }); }
       
        function escapeHtml(str) { if (!str) return ''; return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/\n/g, '<br>'); }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>