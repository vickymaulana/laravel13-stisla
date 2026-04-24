<?php

namespace Tests\Unit;

use App\Http\Requests\FileManager\CreateFolderRequest;
use App\Http\Requests\FileManager\UpdateFileRequest;
use App\Http\Requests\FileManager\UploadFilesRequest;
use App\Http\Requests\Notifications\SendNotificationRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Roles\UpdateUserRoleRequest;
use App\Http\Requests\Settings\StoreSettingRequest;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class FormRequestRulesTest extends TestCase
{
    public function test_file_manager_request_rules_match_current_contract(): void
    {
        $this->assertArrayHasKey('files.*', (new UploadFilesRequest)->rules());
        $this->assertSame('required|string|max:100|regex:/^[a-zA-Z0-9 _.-]+$/', (new CreateFolderRequest)->rules()['folder_name']);
        $this->assertSame('boolean', (new UpdateFileRequest)->rules()['is_public']);
    }

    public function test_profile_request_rules_match_current_contract(): void
    {
        $user = new User(['name' => 'Vicky', 'email' => 'vicky@example.com']);
        $user->id = 10;

        $request = new UpdateProfileRequest;
        $request->setUserResolver(fn () => $user);

        $this->assertArrayHasKey('email', $request->rules());
        $this->assertSame(['required', 'string'], (new ChangePasswordRequest)->rules()['current_password']);
    }

    public function test_admin_request_rules_match_current_contract(): void
    {
        $this->assertSame(['required', 'string', 'in:user,superadmin'], (new UpdateUserRoleRequest)->rules()['role']);
        $this->assertSame('required|array', (new UpdateSettingsRequest)->rules()['settings']);
        $this->assertSame('required|in:info,success,warning,danger', (new SendNotificationRequest)->rules()['type']);
        $this->assertSame('required|in:text,number,boolean,json,email,url,textarea', (new StoreSettingRequest)->rules()['type']);
    }
}
