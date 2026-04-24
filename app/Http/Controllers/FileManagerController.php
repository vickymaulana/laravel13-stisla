<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileManager\CreateFolderRequest;
use App\Http\Requests\FileManager\UpdateFileRequest;
use App\Http\Requests\FileManager\UploadFilesRequest;
use App\Models\ActivityLog;
use App\Models\File;
use App\Support\FileManager\FileTypeFilter;
use App\Support\FileManager\FolderPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * File manager controller.
 *
 * Provides upload, download, update, delete, and folder management
 * with ownership checks and path-traversal prevention.
 */
class FileManagerController extends Controller
{
    /**
     * Display file manager.
     */
    public function index(Request $request): View
    {
        $folder = FolderPath::normalize($request->get('folder', '/'));

        $query = File::where('user_id', auth()->id())
            ->where('folder', $folder)
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $query->where('original_name', 'like', '%'.$request->search.'%');
        }

        FileTypeFilter::apply($query, $request->input('type'));

        $files = $query->paginate(20);

        // Get folders
        $folders = File::where('user_id', auth()->id())
            ->select('folder')
            ->distinct()
            ->orderBy('folder')
            ->get()
            ->pluck('folder');

        return view('file-manager.index', compact('files', 'folder', 'folders'));
    }

    /**
     * Upload files.
     */
    public function upload(UploadFilesRequest $request): RedirectResponse
    {
        $folder = FolderPath::normalize($request->get('folder', '/'));
        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $name = Str::random(40).'.'.$extension;
            $path = $file->storeAs('uploads', $name, 'public');

            $uploadedFile = File::create([
                'user_id' => auth()->id(),
                'name' => $name,
                'original_name' => $originalName,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => $extension,
                'folder' => $folder,
            ]);

            $uploadedFiles[] = $uploadedFile;

            ActivityLog::log(
                'File uploaded: '.$originalName,
                'File Manager',
                'created',
                $uploadedFile
            );
        }

        return redirect()->route('file-manager.index', ['folder' => $folder])
            ->with('success', count($uploadedFiles).' file(s) uploaded successfully.');
    }

    /**
     * Download a file.
     */
    public function download(int $id): StreamedResponse
    {
        $file = File::where('user_id', auth()->id())->findOrFail($id);

        ActivityLog::log(
            'File downloaded: '.$file->original_name,
            'File Manager',
            'custom',
            $file
        );

        return Storage::disk('public')->download($file->path, $file->original_name);
    }

    /**
     * Update file details.
     */
    public function update(UpdateFileRequest $request, int $id): RedirectResponse
    {
        $file = File::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->only(['original_name', 'description', 'is_public']);
        $validated['folder'] = FolderPath::normalize((string) $request->input('folder', '/'));

        $file->update($validated);

        ActivityLog::log(
            'File updated: '.$file->original_name,
            'File Manager',
            'updated',
            $file
        );

        return redirect()->route('file-manager.index', ['folder' => $file->folder])
            ->with('success', 'File updated successfully.');
    }

    /**
     * Delete a file.
     */
    public function destroy(int $id): RedirectResponse
    {
        $file = File::where('user_id', auth()->id())->findOrFail($id);

        // Delete physical file
        if (Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        $fileName = $file->original_name;
        $folder = $file->folder;

        $file->delete();

        ActivityLog::log(
            'File deleted: '.$fileName,
            'File Manager',
            'deleted'
        );

        return redirect()->route('file-manager.index', ['folder' => $folder])
            ->with('success', 'File deleted successfully.');
    }

    /**
     * Get file details (AJAX).
     */
    public function show(int $id): JsonResponse
    {
        $file = File::where('user_id', auth()->id())->findOrFail($id);

        return response()->json($file);
    }

    /**
     * Create folder.
     */
    public function createFolder(CreateFolderRequest $request): RedirectResponse
    {
        $parentFolder = FolderPath::normalize($request->get('parent_folder', '/'));
        $folderPath = FolderPath::join($parentFolder, (string) $request->folder_name);

        // Create a placeholder file to represent the folder
        File::create([
            'user_id' => auth()->id(),
            'name' => '.folder',
            'original_name' => $request->folder_name,
            'path' => 'folders/'.Str::random(40).'.placeholder',
            'mime_type' => 'folder',
            'size' => 0,
            'folder' => $folderPath,
        ]);

        Storage::disk('public')->makeDirectory('uploads'.$folderPath);

        return redirect()->route('file-manager.index', ['folder' => $folderPath])
            ->with('success', 'Folder created successfully.');
    }
}
