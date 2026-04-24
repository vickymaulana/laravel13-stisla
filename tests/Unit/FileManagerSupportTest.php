<?php

namespace Tests\Unit;

use App\Support\FileManager\FileTypeFilter;
use App\Support\FileManager\FolderPath;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FileManagerSupportTest extends TestCase
{
    public function test_folder_paths_are_normalized(): void
    {
        $this->assertSame('/', FolderPath::normalize(null));
        $this->assertSame('/', FolderPath::normalize(''));
        $this->assertSame('/clients/reports', FolderPath::normalize('clients//./reports/'));
        $this->assertSame('/clients/reports', FolderPath::normalize('\\clients\\reports'));
    }

    public function test_folder_paths_reject_traversal_segments(): void
    {
        $this->expectException(ValidationException::class);

        FolderPath::normalize('/clients/../secrets');
    }

    public function test_folder_paths_join_parent_and_child(): void
    {
        $this->assertSame('/clients/reports', FolderPath::join('/clients', 'reports'));
    }

    public function test_document_mime_types_are_explicit(): void
    {
        $this->assertContains('application/pdf', FileTypeFilter::documentMimeTypes());
        $this->assertContains('application/vnd.openxmlformats-officedocument.wordprocessingml.document', FileTypeFilter::documentMimeTypes());
        $this->assertContains('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', FileTypeFilter::documentMimeTypes());
    }
}
