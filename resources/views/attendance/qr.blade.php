@extends('layouts.app')

@section('title', 'Rolling Attendance QR - ' . $session->title)

@section('content')
<div class="container py-5" 
     x-data="{
        sessionId: {{ $session->id }},
        sessionSecret: '{{ $session->qr_secret }}',
        tokenTimestamp: Math.floor(Date.now() / 15000),
        countdown: 15,
        latBoundary: {{ $session->lat_boundary ?? 0 }},
        lngBoundary: {{ $session->lng_boundary ?? 0 }},
        maxRadiusMeters: {{ $session->radius_meters ?? 50 }},
        userLat: null,
        userLng: null,
        distanceMeters: null,
        withinGeofence: true,
        checkinStatus: '',
        qrUrl: '',

        init() {
            this.generateQrUrl();
            this.trackGeolocation();

            // Refresh rolling token every 15 seconds
            setInterval(() => {
                if (this.countdown > 1) {
                    this.countdown--;
                } else {
                    this.countdown = 15;
                    this.tokenTimestamp = Math.floor(Date.now() / 15000);
                    this.generateQrUrl();
                }
            }, 1000);
        },

        generateQrUrl() {
            const rollingHash = this.sessionSecret + '_' + this.tokenTimestamp;
            const checkinEndpoint = '{{ url('/attendance/verify') }}?session_id=' + this.sessionId + '&token=' + encodeURIComponent(rollingHash);
            this.qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=' + encodeURIComponent(checkinEndpoint);
        },

        trackGeolocation() {
            if (navigator.geolocation && this.latBoundary !== 0) {
                navigator.geolocation.getCurrentPosition((pos) => {
                    this.userLat = pos.coords.latitude;
                    this.userLng = pos.coords.longitude;
                    this.distanceMeters = this.calculateDistance(this.userLat, this.userLng, this.latBoundary, this.lngBoundary);
                    this.withinGeofence = this.distanceMeters <= this.maxRadiusMeters;
                }, (err) => {
                    console.warn('Geolocation not allowed or failed:', err);
                });
            }
        },

        calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // metres
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;
            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                      Math.cos(φ1) * Math.cos(φ2) *
                      Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return Math.round(R * c);
        },

        manualCheckin() {
            fetch('{{ route('attendance.checkin') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    session_id: this.sessionId,
                    token: this.sessionSecret + '_' + this.tokenTimestamp,
                    lat: this.userLat,
                    lng: this.userLng
                })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    this.checkinStatus = 'success';
                } else {
                    alert(data.message || 'Verification failed');
                }
            });
        }
     }">

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 text-center">
            <div class="lms-card p-5 shadow-lg position-relative overflow-hidden">
                <div class="mb-3">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill font-mono">
                        {{ $site_settings['label_rolling_qr'] ?? 'Rolling QR Session Protocol' }}
                    </span>
                </div>

                <h3 class="fw-bold mb-1">{{ $session->title }}</h3>
                <div class="text-secondary small mb-4 font-mono">
                    Course: {{ $session->course->title }} &bull; Date: {{ $session->session_date->format('M d, Y') }}
                </div>

                <!-- Geofence Badge Notice -->
                @if($session->lat_boundary)
                    <div class="mb-4 p-3 rounded small" :class="withinGeofence ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'">
                        <i class="bi" :class="withinGeofence ? 'bi-geo-alt-fill' : 'bi-geo-alt'"></i>
                        <span x-text="withinGeofence ? 'Geofence Verified: Within radius (' + (distanceMeters !== null ? distanceMeters + 'm' : '<=' + maxRadiusMeters + 'm') + ')' : 'Outside Geofence Radius (' + distanceMeters + 'm > ' + maxRadiusMeters + 'm max)'"></span>
                    </div>
                @endif

                <!-- Rolling QR Display with Countdown -->
                <div class="position-relative d-inline-block p-3 border rounded-3 bg-white mb-3">
                    <img :src="qrUrl" alt="Rolling QR" class="img-fluid" style="width: 260px; height: 260px;">
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2 badge bg-dark font-mono px-3 py-1 shadow">
                        Refreshes in: <span class="text-warning" x-text="countdown"></span>s
                    </div>
                </div>

                <p class="text-secondary small max-w-400 mx-auto mb-4">
                    {{ $site_settings['msg_qr_instructions'] ?? 'Scan this QR code using your mobile device or corporate check-in app. Tokens automatically rotate every 15 seconds to prevent sharing or proxy attendance.' }}
                </p>

                <!-- Checkin Action -->
                @auth
                    <template x-if="checkinStatus === 'success'">
                        <div class="alert alert-success d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <span class="fw-bold">Attendance successfully recorded as PRESENT!</span>
                        </div>
                    </template>
                    <template x-if="checkinStatus !== 'success'">
                        <button class="btn btn-accent btn-lg px-5 shadow" 
                                :disabled="!withinGeofence"
                                @click="manualCheckin()">
                            <i class="bi bi-check2-circle me-2"></i> {{ $site_settings['label_instant_checkin'] ?? 'Verify & Check In Now' }}
                        </button>
                    </template>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        Sign in to record attendance
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
