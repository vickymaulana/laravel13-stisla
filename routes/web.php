<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\HakaksesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
 * Scope the {file} route parameter to the authenticated user's own files.
 * A file that exists but belongs to another user resolves to a 404, so file
 * existence is never leaked across accounts.
 */
Route::bind('file', function (string $value): File {
    return File::where('user_id', Auth::id())->findOrFail($value);
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/blank-page', [HomeController::class, 'blank'])->name('blank');
    Route::view('/quick-tour', 'layouts.quick-tour')->name('quick-tour');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Role access management (superadmin or manage-users permission)
    Route::middleware('role_or_permission:superadmin|manage-users')->group(function () {
        Route::get('/hakakses', [HakaksesController::class, 'index'])->name('hakakses.index');
        Route::get('/hakakses/edit/{user}', [HakaksesController::class, 'edit'])->name('hakakses.edit');
        Route::put('/hakakses/update/{user}', [HakaksesController::class, 'update'])->name('hakakses.update');
        Route::delete('/hakakses/delete/{user}', [HakaksesController::class, 'destroy'])->name('hakakses.delete');
    });

    // Activity logs (superadmin or manage-activity-logs permission)
    Route::middleware('role_or_permission:superadmin|manage-activity-logs')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::delete('/activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
        Route::delete('/activity-logs', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
    });

    // Settings (superadmin or manage-settings permission)
    Route::middleware('role_or_permission:superadmin|manage-settings')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
        Route::post('/settings/reset', [SettingController::class, 'reset'])->name('settings.reset');
    });

    // Notification admin actions (superadmin or send-notifications permission)
    Route::middleware('role_or_permission:superadmin|send-notifications')->group(function () {
        Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');
    });

    // Template examples
    Route::get('/table-example', [ExampleController::class, 'table'])->name('table.example');
    Route::get('/clock-example', [ExampleController::class, 'clock'])->name('clock.example');
    Route::get('/chart-example', [ExampleController::class, 'chart'])->name('chart.example');
    Route::get('/form-example', [ExampleController::class, 'form'])->name('form.example');
    Route::get('/map-example', [ExampleController::class, 'map'])->name('map.example');
    Route::get('/calendar-example', [ExampleController::class, 'calendar'])->name('calendar.example');
    Route::get('/gallery-example', [ExampleController::class, 'gallery'])->name('gallery.example');
    Route::get('/todo-example', [ExampleController::class, 'todo'])->name('todo.example');
    Route::get('/contact-example', [ExampleController::class, 'contact'])->name('contact.example');
    Route::get('/faq-example', [ExampleController::class, 'faq'])->name('faq.example');
    Route::get('/news-example', [ExampleController::class, 'news'])->name('news.example');
    Route::get('/about-example', [ExampleController::class, 'about'])->name('about.example');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
    Route::get('/notifications/send-test', [NotificationController::class, 'sendTest'])->name('notifications.send-test');

    // File manager ({file} is bound to the authenticated user's own files only)
    Route::get('/file-manager', [FileManagerController::class, 'index'])->name('file-manager.index');
    Route::post('/file-manager/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');
    Route::get('/file-manager/{file}/download', [FileManagerController::class, 'download'])->name('file-manager.download');
    Route::put('/file-manager/{file}', [FileManagerController::class, 'update'])->name('file-manager.update');
    Route::delete('/file-manager/{file}', [FileManagerController::class, 'destroy'])->name('file-manager.destroy');
    Route::get('/file-manager/{file}/show', [FileManagerController::class, 'show'])->name('file-manager.show');
    Route::post('/file-manager/create-folder', [FileManagerController::class, 'createFolder'])->name('file-manager.create-folder');
});
