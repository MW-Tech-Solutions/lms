@extends('layouts.admin')

@section('title', 'SCORM 1.2 / 2004 Package Registry')

@section('content')
<div class="container-fluid" x-data="{ showUploadModal: false }">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $site_settings['title_scorm_registry'] ?? 'SCORM 1.2 & 2004 Runtime Registry' }}</h2>
            <div class="text-secondary small">{{ $site_settings['subtitle_scorm_registry'] ?? 'Upload interactive e-learning packages, verify imsmanifest metadata, and monitor telemetry tracks.' }}</div>
        </div>
        <button class="tj-btn-primary py-2 px-3 small shadow-sm" @click="showUploadModal = true">
            <i class="bi bi-cloud-arrow-up me-1"></i> Ingest New SCORM Package
        </button>
    </div>

    <!-- SCORM Packages List -->
    <div class="lms-card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-box-seam text-warning me-2"></i>Deployed SCORM Packages</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Package ID</th>
                        <th>Title</th>
                        <th>Standard</th>
                        <th>Storage Path</th>
                        <th>Telemetry Tracks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $pkg)
                        <tr>
                            <td class="font-mono small">#{{ $pkg->id }}</td>
                            <td class="fw-semibold">{{ $pkg->title }}</td>
                            <td>
                                <span class="badge {{ $pkg->version === '2004' ? 'bg-primary' : 'bg-warning text-dark' }} font-mono">
                                    SCORM {{ $pkg->version }}
                                </span>
                            </td>
                            <td class="font-mono text-muted small text-truncate" style="max-width: 250px;">
                                {{ $pkg->storage_path }}
                            </td>
                            <td class="font-mono">{{ $pkg->tracks()->count() }} CMI Events</td>
                            <td>
                                <a href="{{ route('scorm.launch', ['package_id' => $pkg->id]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-play-circle me-1"></i> Launch Sandbox
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No SCORM packages uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Live SCORM Telemetry Tracks -->
    <div class="lms-card p-4 shadow-sm">
        <h5 class="fw-bold mb-3"><i class="bi bi-activity text-info me-2"></i>Recent CMI Runtime Activity (/api/scorm/commit)</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Learner</th>
                        <th>Package</th>
                        <th>CMI Key</th>
                        <th>CMI Value</th>
                        <th>Last Synced</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTracks as $track)
                        <tr>
                            <td class="small fw-semibold">{{ $track->user->name }}</td>
                            <td>{{ $track->scormPackage->title }}</td>
                            <td class="font-mono text-primary small">{{ $track->cmi_key }}</td>
                            <td class="font-mono small">{{ Str::limit($track->cmi_value, 60) }}</td>
                            <td class="font-mono text-muted small">{{ $track->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No CMI tracking telemetry recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Package Modal -->
    <div class="modal fade show" tabindex="-1" role="dialog" x-show="showUploadModal" x-cloak style="background: rgba(0,0,0,0.6);" @click.self="showUploadModal = false" @keydown.escape.window="showUploadModal = false">
        <div class="modal-dialog">
            <div class="modal-content lms-card p-2">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Upload SCORM Archive (.zip)</h5>
                    <button type="button" class="btn-close" @click="showUploadModal = false"></button>
                </div>
                <form action="{{ route('admin.scorm.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Package Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="Cybersecurity Awareness 2026">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">SCORM ZIP File</label>
                            <input type="file" name="scorm_file" class="form-control" accept=".zip" required>
                            <div class="form-text small">Archive must contain a valid <code>imsmanifest.xml</code> at the root.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" @click="showUploadModal = false">Cancel</button>
                        <button type="submit" class="btn btn-accent px-4">Upload &amp; Extract</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
