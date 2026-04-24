@extends('layouts.app')

@section('title', 'Notifications')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Notifications</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Notifications</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Your Notifications</h2>
                <p class="section-lead">
                    View and manage all your notifications.
                </p>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Notifications</h4>
                                <div class="card-header-action">
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-check-double"></i> Mark All as Read
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('notifications.destroy-all') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete all notifications?')">
                                            <i class="fas fa-trash"></i> Delete All
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                @if($notifications->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($notifications as $notification)
                                            @php
                                                $data = $notification->data;
                                                $bgClass = $notification->read_at ? '' : 'bg-light';
                                                $iconColor = match($data['type'] ?? 'info') {
                                                    'success' => 'success',
                                                    'warning' => 'warning',
                                                    'danger' => 'danger',
                                                    default => 'info'
                                                };
                                                $icon = match($data['type'] ?? 'info') {
                                                    'success' => 'fa-check-circle',
                                                    'warning' => 'fa-exclamation-triangle',
                                                    'danger' => 'fa-times-circle',
                                                    default => 'fa-info-circle'
                                                };
                                            @endphp
                                            <div class="list-group-item {{ $bgClass }}">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <i class="fas {{ $icon }} fa-2x text-{{ $iconColor }}"></i>
                                                    </div>
                                                    <div class="col">
                                                        <h6 class="mb-1">
                                                            {{ $data['title'] ?? 'Notification' }}
                                                            @if(!$notification->read_at)
                                                                <span class="badge bg-primary">New</span>
                                                            @endif
                                                        </h6>
                                                        <p class="mb-1">{{ $data['message'] ?? '' }}</p>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    <div class="col-auto">
                                                        @if(isset($data['action_url']) && $data['action_url'])
                                                            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-primary">
                                                                    {{ $data['action_text'] ?? 'View' }}
                                                                </button>
                                                            </form>
                                                        @elseif(!$notification->read_at)
                                                            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success">
                                                                    <i class="fas fa-check"></i> Mark as Read
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this notification?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-5 text-center">
                                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No notifications</h5>
                                        <p class="text-muted">You don't have any notifications yet.</p>
                                    </div>
                                @endif
                            </div>
                            @if($notifications->hasPages())
                                <div class="card-footer">
                                    {{ $notifications->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
