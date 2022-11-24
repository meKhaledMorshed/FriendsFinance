<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CheckpostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SelectOptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* ================================== Start From Here =========================================== */
/* ================================== Start From Here =========================================== */


/* ================================== Register Entity Route Group ================================== */

Route::middleware('isAddEntity')->group(function () {
    Route::view('addentity', 'addentity.registration', ['pageTitle' => 'Register a Business'])->name('addentity');
    Route::post('create-entity', [RegistrationController::class, 'createentity'])->name('createentity');
    Route::get('default-admin', [RegistrationController::class, 'defaultadmin']);
    Route::post('create-admin', [RegistrationController::class, 'createadmin']);
});

/* ================================== Login Route Group ================================== */
Route::middleware('canLogin')->group(function () {

    Route::get('login', [CheckpostController::class, 'login'])->name('login');
    Route::post('login-credentials-check', [CheckpostController::class, 'check_login_credentials'])->name('login.check');

    Route::view('forgot-password', 'common.forgot-password')->name('password.forgot');
    Route::post('password-reseting-check', [CheckpostController::class, 'password_reseting_check'])->name('password.reseting.check');
});



/* ================================== Checkpost Route Group ================================== */
Route::middleware('isTwoFA')->group(function () {

    Route::view('login/checkpost', 'common.login-checkpost')->name('login.checkpost');
    Route::post('login/checkpost/2fa', [CheckpostController::class, 'check_login_2fa'])->name('login.2fa.check');
    Route::get('sendcodeagain', [CheckpostController::class, 'sendcodeagain']);

    Route::view('password-reset/checkpost', 'common.password-reset-checkpost')->name('password.checkpost');
    Route::post('password-reset', [CheckpostController::class, 'reset_password'])->name('password.reset');
});



/* ================================== Admin Route Group ================================== */
Route::middleware('isAdmin')->prefix('admin')->group(function () {

    // admin dashboard 
    Route::get('/', [DashboardController::class, 'dashboard'])->name('admin');
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

    //user page
    Route::view('user', 'backend.user.user')->name('admin.user');
    Route::get('pull-user/{filter?}', [UserController::class, 'pullUser'])->name('admin.pullUser'); // get users by api request  

    //view user
    Route::get('user/view/{id?}', [UserController::class, 'userView'])->name('admin.userView');

    // create user 
    Route::get('user/create', [UserController::class, 'userForm'])->name('admin.user.userForm');
    Route::post('user/create/{scope?}', [UserController::class, 'create'])->name('admin.user.create');

    // Transaction 
    Route::view('transaction/view', 'backend.transaction.index')->name('transaction.view');
    Route::view('transaction/create', 'backend.transaction.create')->name('transaction.create');
    Route::post('transaction/create', [TransactionController::class, 'create'])->name('transaction.create.post');

    Route::get('transaction/pull/global/{filter?}', [TransactionController::class, 'pullTransactions'])->name('transaction.pull.transactions');
    Route::get('transaction/pull/inserted-by-current-admin/{filter?}', [TransactionController::class, 'pullTxnByAdmin'])->name('transaction.pull.txnByAdmin');

    // need middleware for only master & super admin and accountent & teller
    Route::view('transaction/view/authorization', 'backend.transaction.authorization')->name('transaction.authorization');
    Route::get('transection/authorization/{request?}/{id?}', [TransactionController::class, 'txnAuthorization'])->name('transaction.authorization.request');


    Route::middleware('isEditor')->group(function () {

        // update user 
        Route::get('user/update/{id?}', [UserController::class, 'updateUser'])->name('admin.updateUser');
        Route::post('user/update/{scope?}', [UserController::class, 'update'])->name('admin.postUpdateUser');

        // 
    });

    Route::middleware('isAuthorizer')->group(function () {

        Route::view('user-authorization', 'backend.authorizations.approve_user')->name('admin.approve-user');
        Route::get('pull-user-for-authorization/{filter?}', [AuthorizationController::class, 'pullUserForAuth'])->name('admin.pullUserForAuth');
        Route::get('change-user-status/{change?}/{id?}', [AuthorizationController::class, 'changeUserStatus'])->name('admin.changeUserStatus');

        Route::view('verify-user-address', 'backend.authorizations.verify_user_address')->name('admin.verifyUserAddress');
        Route::get('pull-user-address/{filter?}', [AuthorizationController::class, 'pullUserAddress'])->name('admin.pullUserAddress');
        Route::get('change-user-address-status/{change?}/{id?}', [AuthorizationController::class, 'changeUserAddressStatus'])->name('admin.changeUserAddressStatus');

        Route::view('verify-user-documents', 'backend.authorizations.verify_user_document')->name('admin.verifyUserDocuments');
        Route::get('pull-user-documents/{filter?}', [AuthorizationController::class, 'pullUserDocuments'])->name('admin.pullUserDocuments');
        Route::get('change-user-documents-status/{change?}/{id?}', [AuthorizationController::class, 'changeUserDocumentStatus'])->name('admin.changeUserDocumentStatus');


        // Route::get('nominee-authorization', [AuthorizationController::class, 'nominee_authorization'])->name('admin.approve-nominee');
        // Route::get('approve-account', [AuthorizationController::class, 'approve_account'])->name('admin.approve-account');
    });

    // need middleware for only master & super admin
    Route::middleware('isMasterAdmin')->group(function () {


        // Designations Api for Master & super Admin
        Route::get('admin-panel/designations/{filter?}', [AdminController::class, 'handleGetDesignations'])->name('getDesignations');
        Route::post('admin-panel/designations', [AdminController::class, 'handlePostDesignations'])->name('postDesignations');

        //admin panel
        Route::get('admin-panel', [AdminController::class, 'viewAdminPanel'])->name('adminPanel');
        Route::post('admin-panel/{form?}', [AdminController::class, 'adminPanelForm'])->name('adminPanelForm');


        //branch panel
        Route::view('branch', 'backend.branch.index')->name('viewBranch');
        Route::get('pull-branch', [BranchController::class, 'pullBranch'])->name('pullBranch');
        Route::get('pull-branch-name/{id?}', [BranchController::class, 'pullBranchName'])->name('pullBranchName');
        Route::post('branch', [BranchController::class, 'addOrUpdateBranch'])->name('addOrUpdateBranch');

        //Account section
        Route::get('account', [AccountController::class, 'index'])->name('account');
        Route::post('account', [AccountController::class, 'postAccountForm'])->name('account.postForm');
        Route::get('account/pull/{filter?}', [AccountController::class, 'pullAccounts'])->name('account.pullAccounts');

        //account category 
        Route::get('account/category/pull/{filter?}', [AccountController::class, 'pullCategories'])->name('account.pullCategories');
        Route::get('account/category/name/pull/{id?}', [AccountController::class, 'pullCategoryName'])->name('account.pullCategoryName');

        Route::get('account/category', [AccountController::class, 'accountCategoryForm'])->name('account.category');
        Route::post('account/category', [AccountController::class, 'postAccountCategory'])->name('account.category.postForm');

        //account nominee
        Route::view('account/nominee', 'backend.account.nominee')->name('account.nominee');
        Route::post('account/nominee', [AccountController::class, 'postNomineeForm'])->name('account.nominee.postForm');
        Route::get('account/nominee/pull/{filter?}', [AccountController::class, 'pullNominees'])->name('account.pullNominees');


        Route::get('approve-admin', [AuthorizationController::class, 'approve_admin'])->name('admin.approve-admin');
        Route::get('approve-reset-password-for-user', [AuthorizationController::class, 'reset_pass4u'])->name('admin.reset_pass4u');

        //route for select options
        Route::view('select-option', 'backend.miscellaneous.select_option')->name('admin.select-option');
        Route::get('pull-select-options/{filter?}', [SelectOptionController::class, 'selectOption'])->name('admin.pull-select-option');
        Route::get('pull-single-select-option/{id}', [SelectOptionController::class, 'singleSelectOption'])->name('admin.pull-single-select-option');
        Route::get('pull-select-option-for-parent/{parent?}/{group?}', [SelectOptionController::class, 'selectOptionForParent'])->name('admin.selectOptionForParent');
        Route::get('pull-options-parent-value/{id}', [SelectOptionController::class, 'optionsParentValue'])->name('admin.pull-options-parent-value');
        Route::get('pull-options-groups', [SelectOptionController::class, 'optionsGroups'])->name('admin.pull-options-groups');
        Route::post('add-select-option', [SelectOptionController::class, 'addSelectOption'])->name('admin.add-select-option');
        Route::get('request-on-select_ptions-table/{request?}', [SelectOptionController::class, 'requestOnOSTable'])->name('admin.requestOnOSTable');
        Route::get('change-status-select-option/{request?}/{id?}', [SelectOptionController::class, 'changeStatusSO'])->name('admin.changeStatusSO');
    });



    //end here//
});


/* ================================== Admin Api Route Group ================================== */
Route::middleware('isAdmin')->prefix('api/admin')->group(function () {

    // api's for user name 
    Route::get('username/{id?}', [UserController::class, 'getUserName'])->name('admin.getUserName');
    // api's for Account name 
    Route::get('account-name/{number?}', [AccountController::class, 'getAccountName'])->name('admin.getAccountName');


    // End Admin API Route Group 
});



/* ================================== User Route Group ================================== */
Route::middleware('isUser')->group(function () {

    Route::get('/', function () {
        $logout = '<a   href="http://127.0.0.1:8000/logout" >Logout</a>';
        return 'From User Home page . ' . $logout;
    })->name('user.home');
});




/* ================================== LogOut ================================== */

Route::get('logout', [CheckpostController::class, 'logout']);

/* ================================== /LogOut ================================== */