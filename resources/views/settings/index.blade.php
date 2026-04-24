@extends('layouts.app')

@section('title', 'Settings')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>System Settings</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Settings</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Application Configuration</h2>
                <p class="section-lead">
                    Manage your application settings and preferences.
                </p>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')

                    @foreach($settings as $group => $groupSettings)
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ ucfirst($group) }} Settings</h4>
                                <div class="card-header-action">
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="if(confirm('Reset {{ $group }} settings?')) { document.getElementById('reset-form-{{ $group }}').submit(); }">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($groupSettings as $setting)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="{{ $setting->key }}">
                                                    {{ $setting->label }}
                                                    @if($setting->description)
                                                        <small class="text-muted d-block">{{ $setting->description }}</small>
                                                    @endif
                                                </label>

                                                @if($setting->type === 'textarea')
                                                    <textarea name="settings[{{ $setting->key }}]" 
                                                              id="{{ $setting->key }}" 
                                                              class="form-control" 
                                                              rows="3">{{ old("settings.{$setting->key}", $setting->value) }}</textarea>
                                                @elseif($setting->type === 'boolean')
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" 
                                                               class="form-check-input"
                                                               id="{{ $setting->key }}" 
                                                               name="settings[{{ $setting->key }}]"
                                                               value="1"
                                                               {{ old("settings.{$setting->key}", $setting->value) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $setting->key }}">
                                                            {{ $setting->value ? 'Enabled' : 'Disabled' }}
                                                        </label>
                                                    </div>
                                                @else
                                                    <input type="{{ $setting->type }}" 
                                                           name="settings[{{ $setting->key }}]" 
                                                           id="{{ $setting->key }}" 
                                                           class="form-control @error("settings.{$setting->key}") is-invalid @enderror"
                                                           value="{{ old("settings.{$setting->key}", $setting->value) }}">
                                                    @error("settings.{$setting->key}")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Hidden reset form for each group -->
                        <form id="reset-form-{{ $group }}" method="POST" action="{{ route('settings.reset') }}" style="display: none;">
                            @csrf
                            <input type="hidden" name="group" value="{{ $group }}">
                        </form>
                    @endforeach

                    <div class="card">
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Add New Setting Section -->
                <div class="card">
                    <div class="card-header">
                        <h4>Add New Setting</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Key <span class="text-danger">*</span></label>
                                        <input type="text" name="key" class="form-control @error('key') is-invalid @enderror" required>
                                        @error('key')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Label <span class="text-danger">*</span></label>
                                        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror" required>
                                        @error('label')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Type <span class="text-danger">*</span></label>
                                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                            <option value="text">Text</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="number">Number</option>
                                            <option value="email">Email</option>
                                            <option value="url">URL</option>
                                            <option value="boolean">Boolean</option>
                                            <option value="json">JSON</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Group <span class="text-danger">*</span></label>
                                        <input type="text" name="group" class="form-control @error('group') is-invalid @enderror" required>
                                        @error('group')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Order</label>
                                        <input type="number" name="order" class="form-control" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Value</label>
                                        <input type="text" name="value" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <input type="text" name="description" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Add Setting
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
@endpush
