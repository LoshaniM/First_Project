/**
 * TRAVELMATE - Leaflet Map Integration & Location Utilities
 * Handles single-place, all-places, and itinerary maps with graceful fallback
 */

const TOWN_CENTRE_COORDS = [6.6828000, 80.4034000];
const TOWN_CENTRE_NAME = "Rathnapura Town Centre (Origin)";

/**
 * Renders a friendly fallback message if Leaflet or OSM tiles cannot be loaded
 */
function renderMapFallback(elementId, message, coords) {
    const container = document.getElementById(elementId);
    if (!container) return;

    let coordsHtml = '';
    if (coords && coords.length === 2) {
        const [lat, lng] = coords;
        coordsHtml = `
            <p style="font-size: 0.85rem; margin-top: 6px;">
                GPS Coordinates: <strong>${lat.toFixed(6)}, ${lng.toFixed(6)}</strong>
            </p>
            <p style="margin-top: 10px;">
                <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}" target="_blank" rel="noopener" class="btn btn-sm btn-outline">
                    Open in External Maps &rarr;
                </a>
            </p>
        `;
    }

    container.innerHTML = `
        <div class="map-fallback-msg">
            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                <line x1="8" y1="2" x2="8" y2="18"></line>
                <line x1="16" y1="6" x2="16" y2="22"></line>
            </svg>
            <h4 style="margin: 0.5rem 0 0.25rem; color: #1e293b;">Interactive Map Unavailable</h4>
            <p style="font-size: 0.9rem; max-width: 380px;">${message || 'Could not connect to map tile servers. Please check your internet connection.'}</p>
            ${coordsHtml}
        </div>
    `;
}

/**
 * Create standard Leaflet map with OpenStreetMap tiles
 */
function createBaseMap(elementId, centerCoords, zoomLevel) {
    if (typeof L === 'undefined') {
        throw new Error("Leaflet library not loaded");
    }

    const map = L.map(elementId, {
        scrollWheelZoom: false
    }).setView(centerCoords, zoomLevel);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Re-enable scroll zoom on map click
    map.on('focus', function() { map.scrollWheelZoom.enable(); });

    return map;
}

/**
 * Initialize all attractions overview map
 */
function initAllPlacesMap(elementId, places) {
    if (typeof L === 'undefined') {
        renderMapFallback(elementId, "Interactive attractions map could not load. You can still browse the attraction details below.");
        return;
    }

    try {
        const map = createBaseMap(elementId, TOWN_CENTRE_COORDS, 11);
        const markers = [];

        // 1. Add Rathnapura Town Centre Starting Marker
        const townIcon = L.divIcon({
            className: 'custom-town-pin',
            html: '<div style="background:#0f5132; color:#fff; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:3px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,0.3); font-size:14px;">📍</div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const townMarker = L.marker(TOWN_CENTRE_COORDS, { icon: townIcon }).addTo(map);
        townMarker.bindPopup(`
            <div style="font-family: sans-serif; font-size: 13px;">
                <strong style="color:#0f5132; font-size:14px;">${TOWN_CENTRE_NAME}</strong>
                <p style="margin: 4px 0 0; color:#555;">Official Day-Visit Starting Benchmark</p>
            </div>
        `);
        markers.push(townMarker);

        // 2. Add Attraction Markers
        if (Array.isArray(places)) {
            places.forEach(place => {
                const lat = parseFloat(place.latitude);
                const lng = parseFloat(place.longitude);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const placeMarker = L.marker([lat, lng]).addTo(map);
                    
                    const popupHtml = `
                        <div style="min-width: 180px; font-family: sans-serif;">
                            <span style="font-size:10px; font-weight:700; background:#e8f5e9; color:#0f5132; padding:2px 6px; border-radius:10px; text-transform:uppercase;">
                                ${place.category_name || 'Attraction'}
                            </span>
                            <h4 style="margin:6px 0 4px; font-size:14px; color:#1e293b;">${place.place_name}</h4>
                            <p style="margin:0 0 6px; font-size:12px; color:#64748b;">
                                📍 ~${place.distance_km} km from town centre
                            </p>
                            <a href="place-details.php?id=${place.place_id}" style="display:inline-block; font-size:12px; color:#198754; font-weight:600; text-decoration:none;">
                                View Full Details &rarr;
                            </a>
                        </div>
                    `;

                    placeMarker.bindPopup(popupHtml);
                    markers.push(placeMarker);
                }
            });
        }

        // Fit map bounds if markers exist
        if (markers.length > 1) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.12));
        }
    } catch (err) {
        console.error("Map render error:", err);
        renderMapFallback(elementId, "A problem occurred while rendering the interactive map.");
    }
}

/**
 * Initialize single attraction detail map
 */
function initSinglePlaceMap(elementId, place) {
    const lat = parseFloat(place.latitude);
    const lng = parseFloat(place.longitude);

    if (isNaN(lat) || isNaN(lng)) {
        renderMapFallback(elementId, "GPS coordinates are not available for this attraction.");
        return;
    }

    if (typeof L === 'undefined') {
        renderMapFallback(elementId, "Could not load the interactive map.", [lat, lng]);
        return;
    }

    try {
        const map = createBaseMap(elementId, [lat, lng], 13);

        // Place marker
        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(`
            <div style="font-family: sans-serif; font-size: 13px;">
                <strong style="color:#0f5132; font-size:14px;">${place.place_name}</strong>
                <p style="margin: 4px 0 0; color:#555;">${place.location_text}</p>
                <p style="margin: 4px 0 0; font-size:12px; color:#198754;">Approx. ${place.distance_km} km from Rathnapura Town Centre</p>
            </div>
        `).openPopup();

        // Also plot town centre reference
        const townMarker = L.circleMarker(TOWN_CENTRE_COORDS, {
            radius: 7,
            color: '#0f5132',
            fillColor: '#198754',
            fillOpacity: 0.9
        }).addTo(map);
        townMarker.bindPopup(`<strong>${TOWN_CENTRE_NAME}</strong><br>Starting point reference`);

        // Connect with a subtle guide line
        const line = L.polyline([TOWN_CENTRE_COORDS, [lat, lng]], {
            color: '#198754',
            weight: 3,
            dashArray: '6, 8',
            opacity: 0.7
        }).addTo(map);

        const group = new L.featureGroup([marker, townMarker]);
        map.fitBounds(group.getBounds().pad(0.2));
    } catch (err) {
        console.error("Single place map error:", err);
        renderMapFallback(elementId, "Map failed to initialize.", [lat, lng]);
    }
}

/**
 * Initialize 1-Day Itinerary Route Map
 */
function initItineraryMap(elementId, places) {
    if (!Array.isArray(places) || places.length === 0) {
        return;
    }

    if (typeof L === 'undefined') {
        renderMapFallback(elementId, "Interactive itinerary route map could not be loaded.");
        return;
    }

    try {
        const map = createBaseMap(elementId, TOWN_CENTRE_COORDS, 11);
        const routePoints = [TOWN_CENTRE_COORDS];
        const allMarkers = [];

        // Add Origin Marker (Start)
        const originMarker = L.circleMarker(TOWN_CENTRE_COORDS, {
            radius: 8,
            color: '#0f5132',
            fillColor: '#0f5132',
            fillOpacity: 1
        }).addTo(map);
        originMarker.bindPopup(`<strong>Start: ${TOWN_CENTRE_NAME}</strong>`);
        allMarkers.push(originMarker);

        // Add Numbered Markers for each Itinerary stop
        places.forEach((place, index) => {
            const lat = parseFloat(place.latitude);
            const lng = parseFloat(place.longitude);

            if (!isNaN(lat) && !isNaN(lng)) {
                routePoints.push([lat, lng]);

                const stopIcon = L.divIcon({
                    className: 'itinerary-stop-pin',
                    html: `<div style="background:#198754; color:#fff; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:13px; border:2px solid #fff; box-shadow:0 2px 5px rgba(0,0,0,0.3);">${index + 1}</div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                const stopMarker = L.marker([lat, lng], { icon: stopIcon }).addTo(map);
                stopMarker.bindPopup(`
                    <div style="font-family: sans-serif; font-size: 13px;">
                        <span style="font-weight:700; color:#198754;">Stop #${index + 1}</span>
                        <h4 style="margin:3px 0; font-size:14px;">${place.place_name}</h4>
                        <p style="margin:0; color:#64748b; font-size:12px;">${place.opening_time}</p>
                    </div>
                `);
                allMarkers.push(stopMarker);
            }
        });

        // Draw Route Line
        if (routePoints.length > 1) {
            L.polyline(routePoints, {
                color: '#20c997',
                weight: 4,
                opacity: 0.85,
                lineJoin: 'round'
            }).addTo(map);
        }

        const group = new L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.15));
    } catch (err) {
        console.error("Itinerary map error:", err);
        renderMapFallback(elementId, "Itinerary route map could not be displayed.");
    }
}
