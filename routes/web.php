<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTutorController;
use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Tutor\TutorDashboardController;
use App\Http\Controllers\Tutor\TutorSessionController;
use App\Http\Controllers\Tutor\TutorAnnouncementController;
use App\Http\Controllers\Tutor\TutorProfileController;
use App\Http\Controllers\Tutor\TutorNotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Authentication)
Route::middleware('guest:student,tutor,admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Student Routes
Route::middleware(['auth:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('dashboard');
});

// Tutor Routes
Route::middleware(['auth:tutor'])->prefix('tutor')->name('tutor.')->group(function () {
    // Pending page (accessible before approval)
    Route::get('/pending', function () {
        return view('tutor.pending');
    })->name('pending');
    
    // Protected routes (require approval)
    Route::middleware(['App\Http\Middleware\ApprovedTutor'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [TutorDashboardController::class, 'index'])->name('dashboard');
        
        // Sessions Management
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/', [TutorSessionController::class, 'index'])->name('index');
            Route::get('/{id}', [TutorSessionController::class, 'show'])->name('show');
            Route::put('/{sessionId}/attendance/{studentId}', [TutorSessionController::class, 'updateAttendance'])->name('attendance');
            
            // Student enrollment management
            Route::post('/{sessionId}/add-student', [TutorSessionController::class, 'addStudent'])->name('addStudent');
            Route::post('/{sessionId}/approve/{studentId}', [TutorSessionController::class, 'approveStudent'])->name('approve');
            Route::delete('/{sessionId}/reject/{studentId}', [TutorSessionController::class, 'rejectStudent'])->name('reject');
        });
        
        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [TutorNotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [TutorNotificationController::class, 'markAsRead'])->name('markRead');
            Route::post('/read-all', [TutorNotificationController::class, 'markAllAsRead'])->name('markAllRead');
            Route::delete('/{id}', [TutorNotificationController::class, 'destroy'])->name('delete');
        });
        
        // Announcements (FIXED - removed duplicate prefix/name/middleware)
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [TutorAnnouncementController::class, 'index'])->name('index');
            Route::get('/create', [TutorAnnouncementController::class, 'create'])->name('create');
            Route::post('/', [TutorAnnouncementController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TutorAnnouncementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TutorAnnouncementController::class, 'update'])->name('update');
        });
        
        // Status update route (inside tutor group, correct name)
        Route::patch('sessions/{session}/status', [TutorSessionController::class, 'updateStatus'])
            ->name('sessions.updateStatus');


        // Profile
        Route::get('/profile', [TutorProfileController::class, 'index'])->name('profile');
        Route::put('/profile/update', [TutorProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [TutorProfileController::class, 'updatePassword'])->name('profile.password');
        Route::put('/profile/resume', [TutorProfileController::class, 'updateResume'])->name('profile.resume');
    });
});

// Admin Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [AdminDashboardController::class, 'export'])->name('dashboard.export');
    
    // Students
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [AdminStudentController::class, 'index'])->name('index');
        Route::get('/create', [AdminStudentController::class, 'create'])->name('create');
        Route::post('/', [AdminStudentController::class, 'store'])->name('store');
        Route::get('/{student}', [AdminStudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [AdminStudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [AdminStudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [AdminStudentController::class, 'destroy'])->name('destroy');
        Route::post('/{student}/toggle-status', [AdminStudentController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Tutors
    Route::prefix('tutors')->name('tutors.')->group(function () {
        Route::get('/', [AdminTutorController::class, 'index'])->name('index');
        Route::get('/create', [AdminTutorController::class, 'create'])->name('create');
        Route::post('/', [AdminTutorController::class, 'store'])->name('store');
        Route::get('/{tutor}', [AdminTutorController::class, 'show'])->name('show');
        Route::get('/{tutor}/edit', [AdminTutorController::class, 'edit'])->name('edit');
        Route::put('/{tutor}', [AdminTutorController::class, 'update'])->name('update');
        Route::delete('/{tutor}', [AdminTutorController::class, 'destroy'])->name('destroy');
        Route::post('/{tutor}/approve', [AdminTutorController::class, 'approve'])->name('approve');
        Route::post('/{tutor}/reject', [AdminTutorController::class, 'reject'])->name('reject');
        Route::post('/{tutor}/toggle-status', [AdminTutorController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{tutor}/resume', [AdminTutorController::class, 'downloadResume'])->name('downloadResume');
    });

    Route::get('/users', [AdminStudentController::class, 'usersIndex'])->name('users.index');
    
    // Sessions
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [AdminSessionController::class, 'index'])->name('index');
        Route::get('/create', [AdminSessionController::class, 'create'])->name('create');
        Route::post('/', [AdminSessionController::class, 'store'])->name('store');
        Route::get('/{session}', [AdminSessionController::class, 'show'])->name('show');
        Route::get('/{session}/edit', [AdminSessionController::class, 'edit'])->name('edit');
        Route::put('/{session}', [AdminSessionController::class, 'update'])->name('update');
        Route::delete('/{session}', [AdminSessionController::class, 'destroy'])->name('destroy');
        Route::post('/{session}/cancel', [AdminSessionController::class, 'cancel'])->name('cancel');
    });
    
    // Announcements
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AdminAnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [AdminAnnouncementController::class, 'update'])->name('update');
        Route::delete('/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('destroy');
        Route::post('/{announcement}/archive', [AdminAnnouncementController::class, 'archive'])->name('archive');
    });
});

// Fallback Route
Route::fallback(function () {
    return view('errors.404');
});