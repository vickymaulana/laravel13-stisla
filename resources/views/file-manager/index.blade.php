@extends('layouts.app')

@section('title', 'File Manager')

@push('style')
    <!-- CSS Libraries -->
    <style>
        .file-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .file-icon {
            font-size: 3rem;
        }
        .file-preview {
            height: 150px;
            object-fit: cover;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>File Manager</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">File Manager</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Your Files</h2>
                <p class="section-lead">
                    Upload, organize, and manage your files.
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
                                <h4>
                                    <i class="fas fa-folder"></i> {{ $folder }}
                                </h4>
                                <div class="card-header-action">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                        <i class="fas fa-upload"></i> Upload Files
                                    </button>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                                        <i class="fas fa-folder-plus"></i> New Folder
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Filter and Search -->
                                <form method="GET" action="{{ route('file-manager.index') }}" class="mb-4">
                                    <input type="hidden" name="folder" value="{{ $folder }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control" placeholder="Search files..." value="{{ request('search') }}">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="type" class="form-control" onchange="this.form.submit()">
                                                <option value="">All Types</option>
                                                <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Images</option>
                                                <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="folder" class="form-control" onchange="this.form.submit()">
                                                <option value="/">Root Folder</option>
                                                @foreach($folders as $folderOption)
                                                    @if($folderOption !== '/')
                                                        <option value="{{ $folderOption }}" {{ $folder == $folderOption ? 'selected' : '' }}>
                                                            {{ $folderOption }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>

                                <!-- Files Grid -->
                                @if($files->count() > 0)
                                    <div class="row">
                                        @foreach($files as $file)
                                            <div class="col-6 col-md-4 col-lg-3 mb-4">
                                                <div class="card file-card" onclick="showFileDetails({{ $file->id }})">
                                                    <div class="card-body text-center p-3">
                                                        @if($file->isImage())
                                                            <img src="{{ $file->url }}" alt="{{ $file->original_name }}" class="img-fluid file-preview rounded">
                                                        @else
                                                            <i class="fas {{ $file->icon }} file-icon"></i>
                                                        @endif
                                                        <h6 class="mt-2 mb-1 text-truncate">{{ $file->original_name }}</h6>
                                                        <small class="text-muted">{{ $file->formatted_size }}</small>
                                                        <div class="mt-2">
                                                            <a href="{{ route('file-manager.download', $file->id) }}" class="btn btn-sm btn-primary" onclick="event.stopPropagation()">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-info" onclick="event.stopPropagation(); editFile({{ $file->id }})" data-bs-toggle="modal" data-bs-target="#editModal{{ $file->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="{{ route('file-manager.destroy', $file->id) }}" method="POST" class="d-inline" onclick="event.stopPropagation()">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this file?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Edit Modal for each file -->
                                                <div class="modal fade" id="editModal{{ $file->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit File</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('file-manager.update', $file->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>File Name</label>
                                                                        <input type="text" name="original_name" class="form-control" value="{{ $file->original_name }}" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Description</label>
                                                                        <textarea name="description" class="form-control" rows="3">{{ $file->description }}</textarea>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Folder</label>
                                                                        <input type="text" name="folder" class="form-control" value="{{ $file->folder }}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="form-check">
                                                                            <input type="checkbox" class="form-check-input" id="public{{ $file->id }}" name="is_public" value="1" {{ $file->is_public ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="public{{ $file->id }}">Public File</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No files found</h5>
                                        <p class="text-muted">Upload your first file to get started</p>
                                    </div>
                                @endif
                            </div>
                            @if($files->hasPages())
                                <div class="card-footer">
                                    {{ $files->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('file-manager.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="folder" value="{{ $folder }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Files (Max 10MB each)</label>
                            <input type="file" name="files[]" class="form-control" multiple required>
                            <small class="form-text text-muted">You can select multiple files</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('file-manager.create-folder') }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_folder" value="{{ $folder }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Folder Name</label>
                            <input type="text" name="folder_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function showFileDetails(fileId) {
    // You can implement AJAX call to show file details
    console.log('File ID:', fileId);
}

function editFile(fileId) {
    // The modal is already set up for each file
    console.log('Editing file:', fileId);
}
</script>
@endpush
