<?php

use App\Models\User;
use App\Models\Sample;
use Illuminate\Http\Request;
use App\Models\AppConfiguration;
use App\Services\SSO\SSOService;
use App\Models\TypeEtablissement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecteurController;
use App\Http\Controllers\Auth\SSOController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Test\TestController;
use App\Http\Controllers\FaqSectionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\PostCategoryController;
use App\Http\Controllers\AutoEvaluationController;
use App\Http\Controllers\Demande\DemandeController;
use App\Http\Controllers\AppConfigurationController;
use App\Http\Controllers\ArreteController;
use App\Http\Controllers\ManuelUtilisationController;
use App\Http\Controllers\TypeEtablissementController;
use App\Http\Controllers\TypeAutoEvaluationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\FormulaireEvaluationController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Statistiques\DemandeStatsController;
use App\Http\Controllers\Demande\Hebergement\ExploitationPremierClassementController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TypeDemandeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('login/otp', [AuthController::class, 'login_otp'])->name('otp.login');
Route::delete('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('email/verify/{id}', [EmailVerificationController::class, "verify"])->name('verification.verify');

Route::get('email/resend', [EmailVerificationController::class, "resend"])->name('verification.resend');

// App Configurations
Route::get('app-configurations', [AppConfigurationController::class, 'index'])->name('app-configurations.index');
Route::post('app-configurations/search', [AppConfigurationController::class, 'search'])->name('app-configurations.search');

// Posts
Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::post('posts/search', [PostController::class, 'search'])->name('posts.search');
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');

//Contact Mails
Route::post('mails', [MailController::class, 'store'])->name('mails.store');
Route::post('mails/types', [MailController::class, 'types'])->name('mails.types');

Route::get('public/test45', [TestController::class, 'test']);

Route::middleware('auth:sanctum')->get('/test45', function () {
    return response()->json(AppConfiguration::all());
});

Route::group(['middleware' => ['auth:sanctum']], function () {

    //Mail
    Route::post('mails/send', [MailController::class, 'send'])->name('mails.send');

    //Change Password
    Route::post('change-password', [AuthController::class, 'change_password']);

    //App Configuration
    Route::apiResource('app-configurations', AppConfigurationController::class)->except(['index']);

    // Roles
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');

    // User 
    Route::post('users/search', [UsersController::class, 'search'])->name('users.search');
    Route::apiResource('users', UsersController::class);

    //Permission
    Route::apiResource('permissions', PermissionController::class);
    Route::post('permissions/search', [PermissionController::class, 'search'])->name('permissions.search');

    //Permissions Manage
    Route::post('roles/permissions/manage', [PermissionController::class, 'role_manage']);

    //Post Category
    Route::apiResource('post-categories', PostCategoryController::class);
    Route::post('post-categories/search', [PostCategoryController::class, 'search'])->name('post-categories.search');

    //Post
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
    Route::post('posts/statuses', [PostController::class, 'statuses'])->name('posts.statuses');
    Route::post('posts/types', [PostController::class, 'types'])->name('posts.types');

    // Contact Mails
    Route::apiResource('mails', MailController::class)->except(['store']);
    Route::post('mails/search', [MailController::class, 'search'])->name('mails.search');

    // Projects
    Route::post('projects/search', [ProjectController::class, 'search'])->name('projects.search');
    Route::apiResource('projects', ProjectController::class);

    // Tasks
    Route::post('tasks/search', [TaskController::class, 'search'])->name('tasks.search');
    Route::apiResource('tasks', TaskController::class);
});
