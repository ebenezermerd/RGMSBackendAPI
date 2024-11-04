<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityHistoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoeClassController;
use App\Http\Controllers\CoeDashboardController;
use App\Http\Controllers\CoeProposalController;
use App\Http\Controllers\FundRequestController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\StatusAssignmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CallController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/coe-classes', [CoeClassController::class, 'getAllCoeClasses']);
Route::get('/calls/latest', [CallController::class, 'index'])->name('calls.latest');
// COE Class Management
Route::prefix('coe-classes')->group(function () {
    Route::get('/', [CoeClassController::class, 'index']);            // List all COE classes
    Route::post('/', [CoeClassController::class, 'store']);           // Create a new COE class
    Route::get('/{id}', [CoeClassController::class, 'show']);         // Show details of a specific COE class
    Route::put('/{id}', [CoeClassController::class, 'update']);       // Update a specific COE class
    Route::delete('/{id}', [CoeClassController::class, 'destroy']);   // Delete a specific COE class
});
// CSRF Protection Route
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User-specific resource routes
    Route::apiResource('/user', UserController::class);
    Route::post('/user/edit-profile/{userId}', [UserController::class, 'update']);
    Route::group(['prefix' => 'users/{user}'], function () {
        Route::get('/messages', [MessageController::class, 'index']); // Get all messages
        Route::post('/message', [MessageController::class, 'store']); // Create a new message
        Route::get('/messages/{id}', [MessageController::class, 'show']); // Show a specific message
        Route::delete('/messages/{id}', [MessageController::class, 'destroy']); // Delete a message
        Route::patch('/messages/{id}/mark-as-read', [MessageController::class, 'markAsRead']); // Update a message
        Route::apiResource('proposals', ProposalController::class);
        Route::apiResource('transactions', TransactionController::class);
        Route::get('/fund-requests', [FundRequestController::class, 'userFundRequests']);
        Route::post('/fund-requests', [FundRequestController::class, 'store']);
    });

    // Proposal Phases and Activities
    Route::group(['prefix' => 'users/{user_id}/proposals/{proposal_id}'], function () {
        Route::get('/', [ProposalController::class, 'show']);
        Route::apiResource('phases', PhaseController::class);
        Route::group(['prefix' => 'phases/{phase}'], function () {
            Route::apiResource('activities', ActivityController::class);
        });

        Route::get('/status', [StatusAssignmentController::class, 'getProposalStatus']);
        Route::post('/status', [StatusAssignmentController::class, 'assignStatus']);
        Route::post('/update-status', [ProposalController::class, 'updateStatus']);
    
        Route::group(['prefix' => 'phases/{phase_id}'], function () {
            Route::get('/status', [StatusAssignmentController::class, 'getPhaseStatus']);
            Route::post('/status', [StatusAssignmentController::class, 'assignStatus']);
            Route::post('/status/{status_id}', [ProposalController::class, 'updateStatus']);
    
            Route::group(['prefix' => 'activities/{activity_id}'], function () {
                Route::get('/status', [StatusAssignmentController::class, 'getActivityStatus']);
                Route::post('/status', [StatusAssignmentController::class, 'assignStatus']);
                Route::post('/status/{status_id}', [ProposalController::class, 'updateStatus']);
            });
        });
    });

    // Proposal Status Assignments


    // Admin Routes
    
    Route::group(['prefix' => 'directorate/'], function () {
        Route::get('/proposals', [AdminController::class, 'getAllProposals']);
        Route::post('/proposals/{proposalId}/update-status', [AdminController::class, 'updateStatus']);
    });

    // Proposal Management under COE
    Route::group(['prefix' => 'coe/'], function () {
        Route::get('/fund-requests', [FundRequestController::class, 'index']);
        Route::prefix('/{coeClassId}')->group(function () {
            Route::get('/dashboard', [CoeDashboardController::class, 'index']);
            Route::get('/stats', [CoeDashboardController::class, 'stats']);
        });
        Route::prefix('/{coeClassId}/proposals')->group(function () {
            Route::get('/', [CoeProposalController::class, 'index']);
            Route::get('/reviewed-proposals', [CoeProposalController::class, 'getReviewedProposals']);
            Route::get('/{proposalId}', [CoeProposalController::class, 'show']);
    
            Route::post('/{proposalId}/assign-reviewer', [CoeProposalController::class, 'assignReviewer']);
            Route::post('/{proposalId}/remove-reviewer/{reviewerId}', [CoeProposalController::class, 'removeReviewer']); // Detach or remove reviewer
            Route::post('/{proposalId}/download', [CoeProposalController::class, 'downloadProposal']);
            Route::get('/{proposalId}/reviewers', [CoeProposalController::class, 'getAssignedReviewers']);
            Route::get('/{proposalId}/status', [StatusAssignmentController::class, 'getProposalStatus']);
            Route::post('/{proposalId}/update-status', [ProposalController::class, 'updateStatus']);
            // Proposal Reviews
            Route::get('/{proposalId}/reviews', [CoeProposalController::class, 'getReviews']);
        });
        Route::prefix('/reviewers')->group(function () {
            Route::get('/', [ReviewerController::class, 'index']);
            Route::post('/', [ReviewerController::class, 'store']);
            Route::delete('/{reviewerId}', [ReviewerController::class, 'destroy']);
        });
    });

    Route::group(['prefix' => 'reviewer/'],function () {
        Route::get('/assigned-proposals', [ReviewerController::class, 'getAssignedProposals']);
        Route::post('/{coeClassId}/proposals/{proposalId}/submit-review', [ReviewerController::class, 'submitReview']);
    });

    // Logout route
});


Route::middleware(['role:admin|directorate', 'auth:sanctum'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/fund-requests', [FundRequestController::class, 'index']);
        Route::get('/activities', [ActivityHistoryController::class, 'index']);
        Route::post('/activities', [ActivityHistoryController::class, 'store']);
        Route::post('/{adminId}/calls', [CallController::class, 'store'])->name('admin.calls.store');
        Route::get('/', [AdminController::class, 'index']);
        Route::get('/{user}', [AdminController::class, 'show']);
        Route::put('/{user}', [AdminController::class, 'update']);
        Route::delete('/{user}', [AdminController::class, 'destroy']);
        Route::patch('/change-role', [AdminController::class, 'changeUserRole']); // Change user role to COE or admin
        Route::post('/assign', [AdminController::class, 'assignUserToCoe']); // Assign user to COE class
        Route::get('/{coeClassId}/assignments', [AdminController::class, 'showAssignments']); // Show assignments for a COE class
    });

});


// Route for unauthenticated users to see a specific page
Route::get('/unauthenticated', function () {
    return response()->json(['message' => 'This is an unauthenticated page']);
});

// Route for unauthorized users to see an unauthorized page
Route::get('/unauthorized', function () {
    return response()->json(['message' => 'You are not authorized to access this page'], 403);
});

// Middleware to redirect non-admin users to unauthorized page
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin/*', function () {
        return redirect('/unauthorized');
    });
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

