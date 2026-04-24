@extends('layouts.app')

@section('title', 'Activity Logs')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Activity Logs</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Activity Logs</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">System Activity Logs</h2>
                <p class="section-lead">
                    Monitor and track all user activities in the system.
                </p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Filter & Search</h4>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('activity-logs.index') }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>User</label>
                                                <select name="user_id" class="form-control">
                                                    <option value="">All Users</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Event Type</label>
                                                <select name="event" class="form-control">
                                                    <option value="">All Events</option>
                                                    <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>Login</option>
                                                    <option value="logout" {{ request('event') == 'logout' ? 'selected' : '' }}>Logout</option>
                                                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                                                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                                                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                                    <option value="custom" {{ request('event') == 'custom' ? 'selected' : '' }}>Custom</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Search</label>
                                                <input type="text" name="search" class="form-control" placeholder="Search description or subject..." value="{{ request('search') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="submit" class="btn btn-primary btn-block">
                                                        <i class="fas fa-search"></i> Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>Activity Logs</h4>
                                <div class="card-header-action">
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                                        <i class="fas fa-trash"></i> Clear All Logs
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-md">
                                        <thead>
                                            <tr>
                                                <th>Timestamp</th>
                                                <th>User</th>
                                                <th>Event</th>
                                                <th>Description</th>
                                                <th>IP Address</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($logs as $log)
                                                <tr>
                                                    <td>
                                                        <div class="text-small">{{ $log->created_at->format('d M Y') }}</div>
                                                        <div class="text-muted text-small">{{ $log->created_at->format('H:i:s') }}</div>
                                                    </td>
                                                    <td>
                                                        @if($log->user)
                                                            <div>{{ $log->user->name }}</div>
                                                            <div class="text-muted text-small">{{ $log->user->email }}</div>
                                                        @else
                                                            <span class="text-muted">System</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $badgeColor = match($log->event) {
                                                                'login' => 'success',
                                                                'logout' => 'secondary',
                                                                'created' => 'primary',
                                                                'updated' => 'info',
                                                                'deleted' => 'danger',
                                                                default => 'warning'
                                                            };
                                                        @endphp
                                                        <span class="badge bg-{{ $badgeColor }}">{{ ucfirst($log->event) }}</span>
                                                    </td>
                                                    <td>
                                                        <div>{{ $log->description }}</div>
                                                        @if($log->subject)
                                                            <div class="text-muted text-small">{{ $log->subject }}</div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $log->ip_address }}</td>
                                                    <td>
                                                        <a href="{{ route('activity-logs.show', $log->id) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No activity logs found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                {{ $logs->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Clear Logs Modal -->
    <div class="modal fade" id="clearLogsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clear All Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('activity-logs.clear') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to clear all activity logs? This action cannot be undone.</p>
                        <div class="form-group">
                            <label for="retention_days">Retention (days, optional)</label>
                            <input id="retention_days" type="number" min="0" max="3650" class="form-control" name="retention_days" value="0">
                            <small class="text-muted">Set value greater than 0 to only clear older logs.</small>
                        </div>
                        <div class="form-group mt-3">
                            <label for="confirm">Type <strong>CLEAR</strong> to confirm</label>
                            <input id="confirm" type="text" name="confirm" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Clear All Logs</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endpush
