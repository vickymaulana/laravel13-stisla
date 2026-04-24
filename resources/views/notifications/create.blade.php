@extends('layouts.app')

@section('title', 'Send Notification')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Send Notification</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('notifications.index') }}">Notifications</a></div>
                    <div class="breadcrumb-item">Send</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Send Notification to Users</h2>
                <p class="section-lead">
                    Send custom notifications to selected users.
                </p>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12 col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Notification Details</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('notifications.send') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                               value="{{ old('title') }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Message <span class="text-danger">*</span></label>
                                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                                                  rows="4" required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Type <span class="text-danger">*</span></label>
                                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                            <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Info</option>
                                            <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>Success</option>
                                            <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                                            <option value="danger" {{ old('type') == 'danger' ? 'selected' : '' }}>Danger</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Action URL (Optional)</label>
                                        <input type="url" name="action_url" class="form-control @error('action_url') is-invalid @enderror" 
                                               value="{{ old('action_url') }}" placeholder="https://example.com">
                                        @error('action_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">URL to redirect when notification is clicked</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Action Button Text</label>
                                        <input type="text" name="action_text" class="form-control @error('action_text') is-invalid @enderror" 
                                               value="{{ old('action_text', 'View') }}">
                                        @error('action_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Recipients <span class="text-danger">*</span></label>
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="selectAllUsers()">Select All</button>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="deselectAllUsers()">Deselect All</button>
                                        </div>
                                        <div class="selectgroup selectgroup-pills" id="user-list">
                                            @foreach($users as $user)
                                                <label class="selectgroup-item">
                                                    <input type="checkbox" name="users[]" value="{{ $user->id }}" 
                                                           class="selectgroup-input user-checkbox"
                                                           {{ in_array($user->id, old('users', [])) ? 'checked' : '' }}>
                                                    <span class="selectgroup-button">{{ $user->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('users')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-paper-plane"></i> Send Notification
                                        </button>
                                        <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-lg">
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
function selectAllUsers() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllUsers() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endpush
