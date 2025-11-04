<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/interviewer-dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/application-form.css') ?>?v=<?= time() ?>">
    <!-- Leaflet CSS for map picker -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .field-error {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 6px;
        }
        .file-upload-wrapper {
            position: relative;
            width: 100%;
            margin-top: 8px;
        }

        .file-upload-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 2;
        }

        .file-upload-display {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px;
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            background: #f7fafc;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-wrapper:hover .file-upload-display {
            border-color: #f6ad55;
            background: #fffaf0;
        }

        .file-upload-display svg {
            color: #f6ad55;
            flex-shrink: 0;
        }

        .file-name {
            color: #4a5568;
            font-size: 14px;
            font-weight: 500;
        }

        .file-size {
            color: #a0aec0;
            font-size: 12px;
            margin-left: auto;
        }

        .file-preview {
            margin-top: 12px;
            padding: 16px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .preview-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .preview-header svg {
            color: #f56565;
            flex-shrink: 0;
        }

        .preview-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .preview-filename {
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }

        .preview-filesize {
            color: #718096;
            font-size: 12px;
        }

        .preview-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .btn-preview, .btn-remove {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-preview {
            background: #4299e1;
            color: white;
        }

        .btn-preview:hover {
            background: #3182ce;
        }

        .btn-remove {
            background: #fc8181;
            color: white;
        }

        .btn-remove:hover {
            background: #f56565;
        }
    </style>
</head>
<body>
    <div class="dashboard-container interviewer-dashboard">
        <?php if (session()->get('user_type') === 'interviewer'): ?>
            <?= view('components/interviewer_sidebar') ?>
        <?php else: ?>
            <?= view('components/admin_sidebar') ?>
        <?php endif; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Application Form</h1>
                <div class="user-info" style="gap:8px;">
                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
                </div>
            </header>

            <div class="dashboard-content">
                <?php 
                    // Cache flashdata to avoid multiple reads changing availability
                    $flashSuccess = session()->getFlashdata('success');
                    $flashError = session()->getFlashdata('error');
                ?>
                <div class="application-form-container">
                    <div class="form-card">
                        <div class="form-header">
                            <h2>Company Application Form</h2>
                            <p>Fill in the applicant details below</p>
                        </div>

                        <?php if (!empty($flashSuccess)): ?>
                            <div class="alert alert-success">
                                <?= esc($flashSuccess) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($flashError)): ?>
                            <div class="alert alert-error">
                                <?= esc($flashError) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Initial Interview Form -->
                        <?php 
                            // Collect validation errors passed from controller
                            $errors = session()->getFlashdata('errors') ?? [];
                            $birthdateFieldError = session()->getFlashdata('field_error_birthdate');
                            $resumeFieldError = session()->getFlashdata('field_error_resume');
                        ?>
                        <form action="<?= base_url('admin/application/save') ?>" method="POST" id="applicationForm" enctype="multipart/form-data" autocomplete="off">
                            
                            <!-- Company Selection at Top -->
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="company_name">Choose Company <span class="required">*</span></label>
                                    <select id="company_name" name="company_name" required>
                                        <option value="">-- Select Company --</option>
                                        <option value="Everise" <?= old('company_name') === 'Everise' ? 'selected' : '' ?>>Everise</option>
                                        <option value="IGT" <?= old('company_name') === 'IGT' ? 'selected' : '' ?>>IGT</option>
                                    </select>
                                    <?php if (!empty($errors['company_name'])): ?>
                                        <div class="field-error"><?= esc($errors['company_name']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="required">*</span></label>
                                    <input type="text" id="first_name" name="first_name" value="<?= old('first_name') ?>" placeholder="Enter first name" required>
                                    <?php if (!empty($errors['first_name'])): ?>
                                        <div class="field-error"><?= esc($errors['first_name']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="required">*</span></label>
                                    <input type="text" id="last_name" name="last_name" value="<?= old('last_name') ?>" placeholder="Enter last name" required>
                                    <?php if (!empty($errors['last_name'])): ?>
                                        <div class="field-error"><?= esc($errors['last_name']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email_address">Email Address <span class="required">*</span></label>
                                    <input type="email" id="email_address" name="email_address" value="<?= old('email_address') ?>" placeholder="applicant@email.com" required>
                                    <?php if (!empty($errors['email_address'])): ?>
                                        <div class="field-error"><?= esc($errors['email_address']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <div class="phone-input-wrapper">
                                        <span class="phone-prefix">+63</span>
                                        <input type="tel" id="phone_number" name="phone_number" value="<?= old('phone_number') ?>" placeholder="9123456789" maxlength="10" inputmode="numeric" oninput="sanitizeMobile(this)">
                                    </div>
                                    <small class="field-hint">Enter 10 digits starting with 9 (no spaces or dashes)</small>
                                    <?php if (!empty($errors['phone_number'])): ?>
                                        <div class="field-error"><?= esc($errors['phone_number']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="viber_number">Viber Number</label>
                                    <div class="phone-input-wrapper">
                                        <span class="phone-prefix">+63</span>
                                        <input type="tel" id="viber_number" name="viber_number" value="<?= old('viber_number') ?>" placeholder="9123456789" maxlength="10" inputmode="numeric" oninput="sanitizeMobile(this)">
                                    </div>
                                    <small class="field-hint">Enter 10 digits starting with 9 (no spaces or dashes)</small>
                                    <?php if (!empty($errors['viber_number'])): ?>
                                        <div class="field-error"><?= esc($errors['viber_number']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="street_address">Complete Street Address</label>
                                    <input type="text" id="street_address" name="street_address" value="<?= old('street_address') ?>" placeholder="House No., Street Name, Building, Zip Code">
                                    <div style="margin-top:8px;">
                                        <button type="button" class="btn-map" onclick="openMapModal()" title="Pick address on map">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 6l7-3 7 3 7-3v15l-7 3-7-3-7 3V6"/><path d="M8 3v15"/><path d="M15 6v15"/></svg>
                                            Pick on Map
                                        </button>
                                    </div>
                                    <?php if (!empty($errors['street_address'])): ?>
                                        <div class="field-error"><?= esc($errors['street_address']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Hidden fields for selected coordinates -->
                            <input type="hidden" id="latitude" name="latitude" value="<?= old('latitude') ?>">
                            <input type="hidden" id="longitude" name="longitude" value="<?= old('longitude') ?>">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="barangay">Barangay / Subdivision</label>
                                    <input type="text" id="barangay" name="barangay" value="<?= old('barangay') ?>" placeholder="Enter barangay or subdivision">
                                    <?php if (!empty($errors['barangay'])): ?>
                                        <div class="field-error"><?= esc($errors['barangay']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="municipality">Municipality / City</label>
                                    <input type="text" id="municipality" name="municipality" value="<?= old('municipality') ?>" placeholder="Enter city or municipality">
                                    <?php if (!empty($errors['municipality'])): ?>
                                        <div class="field-error"><?= esc($errors['municipality']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="province">Province / Region</label>
                                    <input type="text" id="province" name="province" value="<?= old('province') ?>" placeholder="Enter province or region">
                                    <?php if (!empty($errors['province'])): ?>
                                        <div class="field-error"><?= esc($errors['province']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="birthdate">Birthdate</label>
                                    <input type="date" id="birthdate" name="birthdate" value="<?= old('birthdate') ?>">
                                    <small class="field-hint">Must be at least 18 years old</small>
                                    <?php if (!empty($errors['birthdate'])): ?>
                                        <div class="field-error"><?= esc($errors['birthdate']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($birthdateFieldError)): ?>
                                        <div class="field-error"><?= esc($birthdateFieldError) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bpo_experience">Total BPO Experience</label>
                                    <input type="text" id="bpo_experience" name="bpo_experience" value="<?= old('bpo_experience') ?>" placeholder="e.g., 2 years 6 months">
                                    <?php if (!empty($errors['bpo_experience'])): ?>
                                        <div class="field-error"><?= esc($errors['bpo_experience']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="educational_attainment">Educational Attainment</label>
                                    <input type="text" id="educational_attainment" name="educational_attainment" value="<?= old('educational_attainment') ?>" placeholder="e.g., Bachelor's Degree">
                                    <?php if (!empty($errors['educational_attainment'])): ?>
                                        <div class="field-error"><?= esc($errors['educational_attainment']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label for="resume">Resume (PDF only)</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="resume" name="resume" accept=".pdf" onchange="updateFileName(this)">
                                    <div class="file-upload-display">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                            <line x1="12" y1="3" x2="12" y2="15"/>
                                        </svg>
                                        <span class="file-name">Choose PDF file or drag here</span>
                                        <span class="file-size"></span>
                                    </div>
                                </div>
                                <div class="file-preview" id="filePreview" style="display: none;">
                                    <div class="preview-header">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                            <polyline points="10 9 9 9 8 9"/>
                                        </svg>
                                        <div class="preview-info">
                                            <span class="preview-filename"></span>
                                            <span class="preview-filesize"></span>
                                        </div>
                                    </div>
                                    <div class="preview-actions">
                                        <button type="button" class="btn-preview" onclick="previewPDF()">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Preview
                                        </button>
                                        <button type="button" class="btn-remove" onclick="removeFile()">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                <?php if (!empty($resumeFieldError)): ?>
                                    <div class="field-error"><?= esc($resumeFieldError) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group full-width recruiter-box">
                                <label for="recruiter_email">Send Details To <span class="required">*</span></label>
                                <input type="email" id="recruiter_email" name="recruiter_email" value="<?= old('recruiter_email') ?>" placeholder="recruiter@company.com" required>
                                <?php if (!empty($errors['recruiter_email'])): ?>
                                    <div class="field-error"><?= esc($errors['recruiter_email']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Optional: Schedule another interview -->
                            <div class="form-section" style="margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0;">
                                <h3 style="margin:0 0 12px; font-size:16px; color:#2d3748;">Schedule another interview (optional)</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="next_interviewer_email">Interviewer Email</label>
                                        <input type="email" id="next_interviewer_email" name="next_interviewer_email" value="<?= old('next_interviewer_email') ?>" placeholder="colleague@gmail.com">
                                        <?php if (!empty($errors['next_interviewer_email'])): ?>
                                            <div class="field-error"><?= esc($errors['next_interviewer_email']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="next_interview_datetime">Interview Date & Time</label>
                                        <input type="datetime-local" id="next_interview_datetime" name="next_interview_datetime" value="<?= old('next_interview_datetime') ?>">
                                        <?php if (!empty($errors['next_interview_datetime'])): ?>
                                            <div class="field-error"><?= esc($errors['next_interview_datetime']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group full-width">
                                        <label for="next_interview_notes">Notes for the interviewer</label>
                                        <textarea id="next_interview_notes" name="next_interview_notes" rows="3" placeholder="Anything the interviewer should know..."><?= old('next_interview_notes') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Entry</button>
                                <button type="button" class="btn btn-outline" onclick="clearFormData()">Clear Form</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="<?= base_url('assets/js/interviewer-dashboard.js') ?>?v=<?= time() ?>"></script>
    <!-- Leaflet JS for map picker -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        // Map Picker Modal
        let map, marker, mapInitialized = false;
        let selectedLatLng = null;
        let selectedDisplayName = '';

        function openMapModal() {
            let modal = document.getElementById('mapModal');
            if (!modal) {
                // Build modal markup once and append to body
                modal = document.createElement('div');
                modal.className = 'modal';
                modal.id = 'mapModal';
                modal.innerHTML = `
                    <div class="modal-content map-modal-content">
                        <div class="modal-header">
                            <h3>Pick Address on Map</h3>
                            <button type="button" class="modal-close" onclick="closeMapModal()" aria-label="Close">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div class="modal-body map-modal-body">
                            <div class="map-pane">
                                <div class="map-toolbar">
                                    <div class="search-box">
                                        <input type="text" id="mapSearch" placeholder="Search a place, street, or city..." style="width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:6px;">
                                        <div class="address-suggestions" id="addressSuggestions"></div>
                                    </div>
                                    <button type="button" class="btn btn-outline" onclick="useCurrentLocation()">Use My Location</button>
                                </div>
                                <div id="mapPicker"></div>
                                <div class="modal-footer">
                                    <div class="selected-location-info">
                                        <span id="selectedLocationText">Drag the marker or search to choose a location.</span>
                                    </div>
                                    <button type="button" class="btn btn-secondary" onclick="closeMapModal()">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="applySelectedLocation()">Use this location</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                document.body.appendChild(modal);
            }
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            setTimeout(initMap, 0);
        }

        function closeMapModal() {
            const modal = document.getElementById('mapModal');
            if (modal) modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        function initMap() {
            if (mapInitialized) {
                setTimeout(() => map.invalidateSize(), 50);
                return;
            }
            const defaultCenter = [14.5995, 120.9842]; // Manila
            map = L.map('mapPicker').setView(defaultCenter, 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            marker = L.marker(defaultCenter, { draggable: true }).addTo(map);
            marker.on('dragend', () => {
                const { lat, lng } = marker.getLatLng();
                selectedLatLng = { lat, lng };
                updateSelectedLocationText(lat, lng);
                reverseGeocode(lat, lng);
            });
            map.on('click', (e) => {
                marker.setLatLng(e.latlng);
                const { lat, lng } = e.latlng;
                selectedLatLng = { lat, lng };
                updateSelectedLocationText(lat, lng);
                reverseGeocode(lat, lng);
            });
            attachSearchHandler();
            mapInitialized = true;
            setTimeout(() => map.invalidateSize(), 100);
        }

        function updateSelectedLocationText(lat, lng, name) {
            const el = document.getElementById('selectedLocationText');
            const label = name || selectedDisplayName || 'Selected location';
            el.textContent = `${label} (lat: ${lat.toFixed(5)}, lng: ${lng.toFixed(5)})`;
        }

        async function applySelectedLocation() {
            if (!selectedLatLng) {
                // Use marker's current position if user didn't move anything yet
                const { lat, lng } = marker.getLatLng();
                selectedLatLng = { lat, lng };
            }
            // Ensure we have human-readable address before closing
            try {
                await reverseGeocode(selectedLatLng.lat, selectedLatLng.lng);
            } catch (e) { /* ignore and fallback below */ }

            // Fill coordinates
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            if (latInput && lngInput) {
                latInput.value = selectedLatLng.lat;
                lngInput.value = selectedLatLng.lng;
            }

            // Fallback: if address fields are still empty (e.g., network issue),
            // at least place a readable string in street address
            const streetInput = document.getElementById('street_address');
            if (streetInput && !streetInput.value) {
                const label = selectedDisplayName || 'Selected location';
                streetInput.value = `${label} (${selectedLatLng.lat.toFixed(5)}, ${selectedLatLng.lng.toFixed(5)})`;
            }

            saveFormData();
            closeMapModal();
        }

        function useCurrentLocation() {
            if (!navigator.geolocation) return alert('Geolocation not supported in this browser');
            navigator.geolocation.getCurrentPosition(pos => {
                const { latitude: lat, longitude: lng } = pos.coords;
                selectedLatLng = { lat, lng };
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 16);
                updateSelectedLocationText(lat, lng);
                reverseGeocode(lat, lng);
            }, () => alert('Unable to retrieve your location'));
        }

        function attachSearchHandler() {
            const input = document.getElementById('mapSearch');
            const suggestions = document.getElementById('addressSuggestions');
            let debounceTimer;
            input.addEventListener('input', () => {
                const q = input.value.trim();
                clearTimeout(debounceTimer);
                if (!q) { suggestions.style.display = 'none'; suggestions.innerHTML=''; return; }
                debounceTimer = setTimeout(async () => {
                    try {
                        const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&addressdetails=1&limit=8&countrycodes=ph&q=${encodeURIComponent(q)}`;
                        const res = await fetch(url, { headers: { 'Accept-Language': 'en' } });
                        const data = await res.json();
                        renderSuggestions(data, suggestions);
                    } catch (e) {
                        suggestions.innerHTML = `<div class='suggestion-item no-results'>Search unavailable</div>`;
                        suggestions.style.display = 'block';
                    }
                }, 300);
            });
            document.addEventListener('click', (e) => {
                if (!suggestions.contains(e.target) && e.target !== input) {
                    suggestions.style.display = 'none';
                }
            });
        }

        function renderSuggestions(results, container) {
            if (!results || !results.length) {
                container.innerHTML = `<div class='suggestion-item no-results'>No results</div>`;
                container.style.display = 'block';
                return;
            }
            container.innerHTML = '';
            results.forEach(item => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>${item.display_name}</span>`;
                div.addEventListener('click', () => {
                    container.style.display = 'none';
                    const lat = parseFloat(item.lat), lng = parseFloat(item.lon);
                    selectedLatLng = { lat, lng };
                    selectedDisplayName = item.display_name;
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 17);
                    updateSelectedLocationText(lat, lng, item.display_name);
                    fillAddressFieldsFromNominatim(item.address);
                });
                container.appendChild(div);
            });
            container.style.display = 'block';
        }

        async function reverseGeocode(lat, lng) {
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&lat=${lat}&lon=${lng}`;
                const res = await fetch(url); // avoid custom headers that may trigger CORS preflight
                if (!res.ok) throw new Error('Reverse geocode failed');
                const data = await res.json();
                selectedDisplayName = data.display_name || selectedDisplayName;
                updateSelectedLocationText(lat, lng, selectedDisplayName);
                if (data && data.address) {
                    fillAddressFieldsFromNominatim(data.address);
                    return true;
                }
                // Try fallback if no address
                return await reverseGeocodeFallbackPhoton(lat, lng);
            } catch (e) {
                // Fallback to Photon (CORS-friendly) if Nominatim blocked or unavailable
                return await reverseGeocodeFallbackPhoton(lat, lng);
            }
        }

        async function reverseGeocodeFallbackPhoton(lat, lng) {
            try {
                const url = `https://photon.komoot.io/reverse?lon=${lng}&lat=${lat}`;
                const res = await fetch(url);
                if (!res.ok) throw new Error('Photon reverse failed');
                const data = await res.json();
                const feature = data && data.features && data.features[0];
                if (!feature) return false;
                const props = feature.properties || {};
                const label = [props.name, props.city || props.town || props.state || props.country].filter(Boolean).join(', ');
                if (label) {
                    selectedDisplayName = label;
                    updateSelectedLocationText(lat, lng, selectedDisplayName);
                }
                fillAddressFieldsFromPhoton(props);
                return true;
            } catch (e) {
                return false;
            }
        }

        function fillAddressFieldsFromNominatim(addr) {
            const street = [addr.house_number, addr.road || addr.residential, addr.neighbourhood || addr.suburb || addr.quarter, addr.postcode]
                .filter(Boolean)
                .join(', ');
            const brgy = addr.barangay || addr.village || addr.neighbourhood || addr.suburb || addr.hamlet || addr.city_district || '';
            const city = addr.city || addr.town || addr.municipality || addr.city_district || addr.county || '';
            const province = addr.province || addr.state || addr.region || addr.state_district || '';

            const streetInput = document.getElementById('street_address');
            const barangayInput = document.getElementById('barangay');
            const municipalityInput = document.getElementById('municipality');
            const provinceInput = document.getElementById('province');

            if (streetInput) streetInput.value = street;
            if (barangayInput) barangayInput.value = brgy;
            if (municipalityInput) municipalityInput.value = city;
            if (provinceInput) provinceInput.value = province;
            saveFormData();
        }

        function fillAddressFieldsFromPhoton(props) {
            const street = [props.housenumber, props.street || props.name, props.district, props.postcode]
                .filter(Boolean)
                .join(', ');
            const brgy = props.district || props.suburb || props.neighbourhood || props.locality || '';
            const city = props.city || props.town || props.municipality || props.county || '';
            const province = props.state || props.region || props.province || '';

            const streetInput = document.getElementById('street_address');
            const barangayInput = document.getElementById('barangay');
            const municipalityInput = document.getElementById('municipality');
            const provinceInput = document.getElementById('province');

            if (streetInput && street && !streetInput.value) streetInput.value = street;
            if (barangayInput && brgy && !barangayInput.value) barangayInput.value = brgy;
            if (municipalityInput && city && !municipalityInput.value) municipalityInput.value = city;
            if (provinceInput && province && !provinceInput.value) provinceInput.value = province;
            saveFormData();
        }
        // Auto-save and restore form data
        // If previous request succeeded, clear saved data BEFORE attempting to load
        const submissionSuccess = <?= !empty($flashSuccess) ? 'true' : 'false' ?>;
        if (submissionSuccess) {
            localStorage.removeItem('applicationFormData');
        }
        const formInputs = document.querySelectorAll('#applicationForm input:not([type="file"]), #applicationForm select, #applicationForm textarea');
        
        // Save form data to localStorage on input
        function saveFormData() {
            const formData = {};
            formInputs.forEach(input => {
                if (input.type !== 'file') {
                    formData[input.id || input.name] = input.value;
                }
            });
            localStorage.setItem('applicationFormData', JSON.stringify(formData));
        }

        // Load saved form data on page load
        function loadFormData() {
            const savedData = localStorage.getItem('applicationFormData');
            if (savedData) {
                const formData = JSON.parse(savedData);
                formInputs.forEach(input => {
                    const key = input.id || input.name;
                    if (formData[key] && formData[key] !== '') {
                        input.value = formData[key];
                    }
                });
            }
        }

        // Add event listeners to all form inputs
        formInputs.forEach(input => {
            input.addEventListener('input', saveFormData);
            input.addEventListener('change', saveFormData);
        });

        // Load form data when page loads
        window.addEventListener('DOMContentLoaded', function() {
            loadFormData();
            // Normalize mobile numbers restored from localStorage
            const pn = document.getElementById('phone_number');
            const vn = document.getElementById('viber_number');
            if (pn) sanitizeMobile(pn);
            if (vn) sanitizeMobile(vn);
        });

        // Note: localStorage is cleared on the next page load if the
        // server sets a success flash message (handled at top of script).

        // Clear form function
        function clearFormData() {
            localStorage.removeItem('applicationFormData');
            document.getElementById('applicationForm').reset();
        }

        function updateFileName(input) {
            const filePreview = document.getElementById('filePreview');
            const fileName = document.querySelector('.file-name');
            const fileSize = document.querySelector('.file-size');
            const previewFilename = document.querySelector('.preview-filename');
            const previewFilesize = document.querySelector('.preview-filesize');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileSizeKB = (file.size / 1024).toFixed(2);
                
                fileName.textContent = file.name;
                fileSize.textContent = `${fileSizeKB} KB`;
                
                previewFilename.textContent = file.name;
                previewFilesize.textContent = `${fileSizeKB} KB`;
                
                filePreview.style.display = 'block';
            }
        }

        function previewPDF() {
            const fileInput = document.getElementById('resume');
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const fileURL = URL.createObjectURL(file);
                window.open(fileURL, '_blank');
            }
        }

        function removeFile() {
            const fileInput = document.getElementById('resume');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.querySelector('.file-name');
            const fileSize = document.querySelector('.file-size');
            
            fileInput.value = '';
            fileName.textContent = 'Choose PDF file or drag here';
            fileSize.textContent = '';
            filePreview.style.display = 'none';
        }

        // (downloadCSV and emailInfo removed — no longer used)

        // Sanitize mobile numbers: keep digits only, drop leading zero, enforce max 10, must start with 9
        function sanitizeMobile(el) {
            let digits = (el.value || '').replace(/\D/g, '');
            if (digits.startsWith('0')) digits = digits.slice(1);
            if (digits.length > 10) digits = digits.slice(0, 10);
            el.value = digits;
            saveFormData();
        }
    </script>
</body>
</html>
