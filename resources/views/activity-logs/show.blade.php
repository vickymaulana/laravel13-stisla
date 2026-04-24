@extends('layouts.app')

@section('title', 'Activity Log Detail')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Activity Log Detail</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">Activity Logs</a></div>
                    <div class="breadcrumb-item">Detail</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Log Information</h4>
                                <div class="card-header-action">
                                    <a href="{{ route('activity-logs.index') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="200">Timestamp:</th>
                                                <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <th>User:</th>
                                                <td>
                                                    @if($log->user)
                                                        {{ $log->user->name }} ({{ $log->user->email }})
                                                    @else
                                                        <span class="text-muted">System</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Event Type:</th>
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
                                            </tr>
                                            <tr>
                                                <th>Description:</th>
                                                <td>{{ $log->description }}</td>
                                            </tr>
                                            @if($log->subject)
                                            <tr>
                                                <th>Subject:</th>
                                                <td>{{ $log->subject }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            @if($log->model_type)
                                            <tr>
                                                <th width="200">Model Type:</th>
                                                <td>{{ class_basename($log->model_type) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Model ID:</th>
                                                <td>{{ $log->model_id }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>IP Address:</th>
                                                <td>{{ $log->ip_address }}</td>
                                            </tr>
                                            <tr>
                                                <th>User Agent:</th>
                                                <td>{{ $log->user_agent }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($log->properties)
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h6>Additional Properties:</h6>
                                        <pre class="bg-light p-3 rounded">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="card-footer text-right">
                                <form method="POST" action="{{ route('activity-logs.destroy', $log->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this log?')">
                                        <i class="fas fa-trash"></i> Delete Log
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
