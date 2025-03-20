@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Zones</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <form action="{{ route('admin.add_zone') }}" method="POST" class="card p-4 shadow-sm" id="addForm">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Zone Name:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <input type="hidden" name="boundary" id="boundary">
                    <input type="hidden" name="center_lat" id="center_lat">
                    <input type="hidden" name="center_lng" id="center_lng">
                    <input type="hidden" name="perimeter" id="perimeter">
                    <input type="hidden" name="area" id="area">
                    <input type="hidden" name="areas" id="areas">

                    <div class="mb-3">
                        <label for="place-search" class="form-label">Search Location:</label>
                        <input id="place-search" type="text" placeholder="Search a place...">
                    </div>

                    <div id="map" class="border rounded" style="width: 100%; height: 500px;"></div>
                    <div id="distance-info" class="mt-3 fw-bold"></div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Save Zone</button>
                        <button type="button" id="reset-zone" class="btn btn-secondary ms-2">Reset Zone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let map;
        let drawingManager;
        let selectedPolygon;
        let infoWindow;
        let tempLine;
        let lastClickedLatLng = null;
        let distanceUpdateTimeout;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 28.6139,
                    lng: 77.2090
                }, // Default: Delhi
                zoom: 10
            });

            // Place search
            const input = document.getElementById("place-search");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);

            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length === 0) return;

                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) return;
                    bounds.extend(place.geometry.location);
                    // If the place has a viewport (like a city or larger area), use it
                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    }
                });

                map.fitBounds(bounds);
                // map.setZoom(15);

                // Automatically draw polygon for the searched area
                if (selectedPolygon) {
                    selectedPolygon.setMap(null); // Remove previous polygon
                }

                const coords = getPolygonCoordinatesFromBounds(bounds);
                drawPolygon(coords);
            });

            // Function to get polygon coordinates from bounds (approximate box)
            function getPolygonCoordinatesFromBounds(bounds) {
                return [{
                        lat: bounds.getNorthEast().lat(),
                        lng: bounds.getNorthEast().lng()
                    }, // NE
                    {
                        lat: bounds.getNorthEast().lat(),
                        lng: bounds.getSouthWest().lng()
                    }, // NW
                    {
                        lat: bounds.getSouthWest().lat(),
                        lng: bounds.getSouthWest().lng()
                    }, // SW
                    {
                        lat: bounds.getSouthWest().lat(),
                        lng: bounds.getNorthEast().lng()
                    }, // SE
                    {
                        lat: bounds.getNorthEast().lat(),
                        lng: bounds.getNorthEast().lng()
                    } // Close the polygon
                ];
            }

            // Function to draw a polygon on map
            function drawPolygon(coordinates) {
                selectedPolygon = new google.maps.Polygon({
                    paths: coordinates,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                    editable: true,
                    draggable: true
                });

                selectedPolygon.setMap(map);
                updateBoundary(selectedPolygon);

                // Add event listeners to update polygon data on edit
                google.maps.event.addListener(selectedPolygon.getPath(), "set_at", () => updateBoundary(selectedPolygon));
                google.maps.event.addListener(selectedPolygon.getPath(), "insert_at", () => updateBoundary(
                selectedPolygon));
            }

            // Drawing manager
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                    editable: true,
                    draggable: true,
                    fillColor: "#FF0000",
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    fillOpacity: 0.35,
                    cursor: "crosshair"
                }
            });

            drawingManager.setMap(map);
            infoWindow = new google.maps.InfoWindow();

            // Polygon draw event
            google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
                if (event.type === google.maps.drawing.OverlayType.POLYGON) {
                    if (selectedPolygon) {
                        selectedPolygon.setMap(null); // Remove previous polygon
                    }
                    selectedPolygon = event.overlay;
                    updateBoundary(selectedPolygon);

                    // Live update on vertex drag or edit
                    google.maps.event.addListener(selectedPolygon.getPath(), "set_at", () => updateBoundary(
                        selectedPolygon));
                    google.maps.event.addListener(selectedPolygon.getPath(), "insert_at", () => updateBoundary(
                        selectedPolygon));
                }
            });

            // Capture last clicked point for distance calculation
            map.addListener("click", (event) => {
                lastClickedLatLng = event.latLng;
            });

            // Throttle mousemove event
            map.addListener("mousemove", (event) => {
                if (lastClickedLatLng) {
                    if (distanceUpdateTimeout) {
                        clearTimeout(distanceUpdateTimeout);
                    }
                    distanceUpdateTimeout = setTimeout(() => {
                        drawTempLine(lastClickedLatLng, event.latLng);
                        showRealTimeDistance(lastClickedLatLng, event.latLng);
                    }, 100); // Adjust the delay as needed
                }
            });

            // Reset button
            document.getElementById("reset-zone").addEventListener("click", resetZone);
        }

        // Calculate and update polygon details
        function updateBoundary(polygon) {
            const path = polygon.getPath();
            let coordinates = [];
            let latSum = 0,
                lngSum = 0;
            let totalDistance = 0;

            for (let i = 0; i < path.getLength(); i++) {
                let latLng = path.getAt(i);
                coordinates.push({
                    lat: latLng.lat(),
                    lng: latLng.lng()
                });
                latSum += latLng.lat();
                lngSum += latLng.lng();

                if (i > 0) {
                    totalDistance += haversineDistance(path.getAt(i - 1), latLng);
                }
            }

            // Close the polygon loop
            totalDistance += haversineDistance(path.getAt(path.getLength() - 1), path.getAt(0));

            let centerLat = latSum / coordinates.length;
            let centerLng = lngSum / coordinates.length;
            let area = google.maps.geometry.spherical.computeArea(path) / 1e6; // Convert to km²

            document.getElementById("boundary").value = JSON.stringify(coordinates);
            document.getElementById("center_lat").value = centerLat;
            document.getElementById("center_lng").value = centerLng;
            document.getElementById("perimeter").value = totalDistance.toFixed(2);
            document.getElementById("area").value = area.toFixed(2);

            getAreaNames(polygon); // 👈 Reverse geocode and populate areas

            document.getElementById("distance-info").innerHTML = `
            <strong>Perimeter:</strong> ${totalDistance.toFixed(2)} km &nbsp;&nbsp;
            <strong>Area:</strong> ${area.toFixed(2)} km²
        `;
        }

        // Draw a dynamic line for real-time distance
        function drawTempLine(startLatLng, endLatLng) {
            if (tempLine) {
                tempLine.setMap(null); // Remove previous line
            }

            tempLine = new google.maps.Polyline({
                path: [startLatLng, endLatLng],
                geodesic: true,
                strokeColor: "#0000FF",
                strokeOpacity: 1.0,
                strokeWeight: 2,
                map: map
            });
        }

        // Show live distance between last clicked point and current cursor
        function showRealTimeDistance(startLatLng, endLatLng) {
            const distance = haversineDistance(startLatLng, endLatLng);
            infoWindow.setContent(`<strong>Distance:</strong> ${distance.toFixed(2)} km`);
            infoWindow.setPosition(endLatLng);
            infoWindow.open(map);
        }

        // Haversine formula for distance calculation
        function haversineDistance(latlng1, latlng2) {
            const R = 6371; // Radius of Earth in km
            const dLat = toRad(latlng2.lat() - latlng1.lat());
            const dLng = toRad(latlng2.lng() - latlng1.lng());
            const lat1 = toRad(latlng1.lat());
            const lat2 = toRad(latlng2.lat());

            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.sin(dLng / 2) * Math.sin(dLng / 2) * Math.cos(lat1) * Math.cos(lat2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function toRad(deg) {
            return deg * (Math.PI / 180);
        }

        // Reset everything
        function resetZone() {
            if (selectedPolygon) selectedPolygon.setMap(null);
            if (tempLine) tempLine.setMap(null);
            selectedPolygon = null;
            lastClickedLatLng = null;

            document.getElementById("boundary").value = "";
            document.getElementById("center_lat").value = "";
            document.getElementById("center_lng").value = "";
            document.getElementById("perimeter").value = "";
            document.getElementById("area").value = "";
            document.getElementById("distance-info").innerHTML = "";
        }
        // Reverse geocode lat/lng to get area names
        function getAreaNames(polygon) {
            const path = polygon.getPath();
            let areaNames = [];
            const geocoder = new google.maps.Geocoder();

            let pendingRequests = path.getLength();

            path.forEach((latLng, index) => {
                geocoder.geocode({
                    location: latLng
                }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        areaNames.push(results[0].formatted_address);
                    } else {
                        console.error("Geocoder failed:", status);
                    }
                    pendingRequests--;

                    if (pendingRequests === 0) { // Ensure all requests are complete
                        document.getElementById("areas").value = JSON.stringify(areaNames);
                    }
                });
            });
        }
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeXtzgRc95cYuOaZD0fjyHsnqVg9Imf30&libraries=places,drawing,geometry&callback=initMap"
        async defer></script>
@endsection
