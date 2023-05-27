<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Role\RoleController;
use App\Http\Controllers\Admin\CRM\ServiceController;
use App\Http\Controllers\Admin\Account\BankController;
use App\Http\Controllers\Admin\HRM\HRMNoticeController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Expense\ExpenseController;
use App\Http\Controllers\Admin\Project\ProjectController;
use App\Http\Controllers\Admin\Revenue\RevenueController;
use App\Http\Controllers\Admin\Role\PermissionController;
use App\Models\Inventory\Products\RawMeterial\Rawmaterial;
use App\Http\Controllers\Admin\Account\Loan\LoanController;
use App\Http\Controllers\Admin\CRM\Client\ClientController;

use App\Http\Controllers\Admin\Employee\EmployeeController;

use App\Http\Controllers\Admin\Settings\PriorityController;
use App\Http\Controllers\Admin\Settings\ReferenceController;
use App\Http\Controllers\Admin\Account\BankAccountController;
use App\Http\Controllers\Admin\Employee\DepartmentController;
use App\Http\Controllers\Admin\Employee\Leeds\LeedController;
use App\Http\Controllers\Admin\Project\ProjectLinkController;
use App\Http\Controllers\Admin\Project\ProjectTypeController;
use App\Http\Controllers\Admin\Account\FundTransferController;
use App\Http\Controllers\Admin\Employee\DesignationController;
use App\Http\Controllers\Admin\HRM\SalaryManagementController;
use App\Http\Controllers\Admin\HRM\Settings\HolidayController;
use App\Http\Controllers\Admin\HRM\Settings\WeekendController;
use App\Http\Controllers\Admin\Account\CashIn\CashInController;
use App\Http\Controllers\Admin\CRM\Client\ClientTypeController;
use App\Http\Controllers\Admin\Project\ProjectModuleController;
use App\Http\Controllers\Admin\Settings\Address\CityController;
use App\Http\Controllers\admin\Employee\Salary\SalaryController;
use App\Http\Controllers\Admin\HRM\Settings\AllowanceController;
use App\Http\Controllers\Admin\Settings\Address\StateController;
use App\Http\Controllers\Admin\Account\Deposit\DepositController;
use App\Http\Controllers\Admin\Account\Loan\LoanReturnController;
use App\Http\Controllers\Admin\CRM\Client\InterestedOnController;
use App\Http\Controllers\Admin\Project\ProjectAssignToController;
use App\Http\Controllers\Admin\Project\ProjectDocumentController;
use App\Http\Controllers\Admin\Project\ProjectDurationController;
use App\Http\Controllers\Admin\Project\ProjectResourceController;

use App\Http\Controllers\Admin\Settings\Address\CountryController;
use App\Http\Controllers\Admin\Account\Withdraw\WithdrawController;
use App\Http\Controllers\Admin\CRM\Client\ClientDocumentController;
use App\Http\Controllers\Admin\CRM\Client\ClientIdentityController;
use App\Http\Controllers\Admin\CRM\Client\ContactThroughController;
use App\Http\Controllers\Admin\Employee\EmployeeDocumentController;
use App\Http\Controllers\Admin\Employee\EmployeeIdentityController;
use App\Http\Controllers\Admin\Expense\ExpenseCategoriesController;
use App\Http\Controllers\Admin\Project\ProjectCategoriesController;
use App\Http\Controllers\Admin\Revenue\RevenueCategoriesController;
use App\Http\Controllers\Admin\Account\Loan\LoanAuthorityController;
use App\Http\Controllers\Admin\CRM\Client\ClientReferenceController;
use App\Http\Controllers\Admin\Employee\EmployeeReferenceController;
use App\Http\Controllers\Admin\Settings\DashboardSettingsController;
use App\Http\Controllers\Admin\Settings\Identity\IdentityController;
use App\Http\Controllers\Admin\Account\Investment\InvestorController;
use App\Http\Controllers\Admin\CRM\Client\ClientBankAccontController;
// Inventory Start
use App\Http\Controllers\Admin\Employee\EmployeeBankAccontController;
use App\Http\Controllers\Admin\CRM\Client\Reminder\ReminderController;
use App\Http\Controllers\Admin\Employee\EmployeeCertificateController;
use App\Http\Controllers\Admin\Account\Investment\InvestmentController;
use App\Http\Controllers\Admin\Employee\EmployeeQualificationController;
use App\Http\Controllers\Admin\Account\Transaction\TransactionController;
use App\Http\Controllers\Admin\Employee\EmployeeWorkExperienceController;
// Inventory End

// Project Start
use App\Http\Controllers\Admin\CRM\Client\Comment\ClientCommentController;
use App\Http\Controllers\Admin\Inventory\Settings\InventoryUnitController;
// Project End

// HRM Start
use App\Http\Controllers\Admin\Account\BalanceSheet\BalanceSheetController;
use App\Http\Controllers\Admin\Inventory\Settings\InventoryBrandController;
use App\Http\Controllers\Admin\Account\Investment\InvestmentReturnController;
use App\Http\Controllers\Admin\Inventory\Services\InventoryServiceController;
use App\Http\Controllers\Admin\CRM\Client\ClientImport\ClientImportController;
use App\Http\Controllers\Admin\Inventory\Customers\InventoryCustomerController;
use App\Http\Controllers\Admin\Inventory\Settings\InventoryWarehouseController;
use App\Http\Controllers\Admin\Account\Loan\Authority\AuthorityDocumentController;
// HRM End

// Inventory Start
use App\Http\Controllers\Admin\Account\Loan\Authority\AuthorityIdentityController;
use App\Http\Controllers\Admin\CRM\Client\ClientAssignto\ClientAssignToController;
use App\Http\Controllers\Admin\Inventory\Products\RawMaterial\ProductionController;
use App\Http\Controllers\Admin\Inventory\Products\RawMaterial\RawMaterialController;
use App\Http\Controllers\Admin\Account\Investment\Investor\InvestorDocumentController;

use App\Http\Controllers\Admin\Account\Investment\Investor\InvestorIdentityController;
use App\Http\Controllers\Admin\CRM\Client\ContactPerson\ContactPersonController;
use App\Http\Controllers\Admin\Inventory\Products\InventoryProductCategoriesController;
use App\Http\Controllers\Admin\Inventory\Services\InventoryServiceCategoriesController;
use App\Http\Controllers\Admin\HRM\Settings\AllowanceController as SettingsAllowanceController;
use App\Http\Controllers\Admin\HRM\Settings\PaidLeaveController;
use App\Http\Controllers\Admin\Project\ProjectAccountController;
use App\Http\Controllers\Admin\Project\ProjectReceiptController;
use App\Http\Controllers\Admin\Project\Task\TaskController;

// use App\Models\Inventory\Products\RawMeterial\Rawmaterial;

// Inventory End


Route::get('/',DashboardController::class)->name('dashboard');

Route::group(['prefix' => 'expense', 'as' => 'expense.'], function () {
    Route::resource('category', ExpenseCategoriesController::class);
    Route::get('category/status/{id}', [ExpenseCategoriesController::class, 'statusUpdate'])->name('category.status');
    Route::resource('expense', ExpenseController::class);
    Route::post('employee/search', [ExpenseController::class, 'employeeSearch'])->name('employee.search');

});

//revenue
Route::resource('revenue', RevenueController::class);
Route::group(['prefix' => 'revenue', 'as' => 'revenue.'], function () {
    Route::get('status/{id}', [RevenueController::class, 'statusUpdate'])->name('update.status');

});
//revenue category
Route::resource('revenue-category', RevenueCategoriesController::class);
Route::group(['prefix' => 'revenue-category', 'as' => 'revenue-category.'], function () {
    Route::get('status/{id}', [RevenueCategoriesController::class, 'statusUpdate'])->name('update.status');
});

Route::group(['prefix' => 'crm', 'as' => 'crm.'], function () {
    //contact through
    Route::resource('contact-through', ContactThroughController::class);
    Route::group(['prefix' => 'contact-through', 'as' => 'contact-through.'], function () {
        Route::get('status/{id}', [ContactThroughController::class, 'statusUpdate'])->name('update.status');
    });

    //interested on
    Route::resource('interested-on', InterestedOnController::class);
    Route::group(['prefix' => 'interested-on', 'as' => 'interested-on.'], function () {
        Route::get('status/{id}', [InterestedOnController::class, 'statusUpdate'])->name('update.status');
    });

    //client type
    Route::resource('client-type', ClientTypeController::class);
    Route::group(['prefix' => 'client-type', 'as' => 'client-type.'], function () {
        Route::get('status/{id}', [ClientTypeController::class, 'statusUpdate'])->name('update.status');
    });

    //client
    Route::resource('client', ClientController::class);
    Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
        Route::post('client', [ClientController::class, 'import'])->name('client');
        Route::get('export', [ClientController::class, 'export'])->name('export');
    });

    Route::get('client-type-priority/{id}', [ClientController::class, 'getClientTypePriority'])->name('client.type.priority');
    Route::post('client-address-update/{id}', [ClientController::class, 'ClientAddressUpdate'])->name('address.update');
    Route::group(['prefix' => 'client', 'as' => 'client.'], function () {
        Route::get('status/{id}', [ClientController::class, 'statusUpdate'])->name('update.status');
        Route::get('profile/{id}', [ClientController::class, 'clientProfile'])->name('profile');
    });

    //client all details route
    Route::resource('client-document', ClientDocumentController::class);
    Route::resource('client-identity', ClientIdentityController::class);
    Route::resource('client-reference', ClientReferenceController::class);

    //client Comment
    Route::group(['prefix' => 'comment', 'as' => 'comment.'], function () {
        Route::get('edit/{id}/{parameter}', [ClientCommentController::class, 'commentEdit'])->name('edit');
        Route::put('update/{id}/{parameter}', [ClientCommentController::class, 'commentUpdate'])->name('update');
    });
    Route::get('comment/{id}', [ClientController::class, 'clientComment'])->name('client.comment');
    Route::resource('client-comment', ClientCommentController::class);
    Route::get('comment/status/{id}', [ClientCommentController::class, 'statusUpdate'])->name('comment.update.status');
    Route::post('client-search', [ClientCommentController::class, 'ClientSearch'])->name('client.search');

    //Reminder Route
    Route::group(['prefix' => 'reminder', 'as' => 'reminder.'], function () {
        Route::get('edit/{id}/{parameter}', [ReminderController::class, 'reminderEdit'])->name('edit');
        Route::put('update/{id}/{parameter}', [ReminderController::class, 'reminderUpdate'])->name('update');
        Route::post('employee/search', [ReminderController::class, 'employeeSearch'])->name('employee-search');
    });

    Route::get('reminder/{id}', [ClientController::class, 'clientReminder'])->name('client.reminder');
    Route::resource('client-reminder', ReminderController::class);
    Route::post('client-reminder-today', [ReminderController::class, 'todayReminder'])->name('client-reminder-today');

    Route::get('reminder/status/{id}', [ReminderController::class, 'statusUpdate'])->name('reminder.update.status');
    //client bank account
    Route::resource('client-bank-account', ClientBankAccontController::class);
    Route::get('client/bank/status/{id}', [ClientBankAccontController::class, 'statusUpdate'])->name('client.bank.update.status');

    //AssignTo
    Route::get('client-assign/{id}', [ClientAssignToController::class, 'clientAssignTo'])->name('client.client.assignto');
    Route::resource('client-assignto', ClientAssignToController::class);
    Route::group(['prefix' => 'client', 'as' => 'client.'], function () {
        Route::post('assign-to/{id}', [ClientAssignToController::class, 'AssignToDelete'])->name('assignto.delete');
        Route::get('edit/{id}/{parameter}', [ClientBankAccontController::class, 'bankAccountEdit'])->name('bank.account.edit');
        Route::put('update/{id}/{parameter}', [BankAccountController::class, 'bankAccountUpdate'])->name('bank.account.update');
        // contact person
        Route::resource('contact-person', ContactPersonController::class);
    });
    Route::post('all-employee-search', [ClientAssignToController::class, 'AllEmployeeSearch'])->name('allemplyee.search');
});


//Employee
Route::resource('employee', EmployeeController::class);
Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
        Route::post('employee', [EmployeeController::class, 'import'])->name('employee');
        Route::get('export', [EmployeeController::class, 'export'])->name('export');
    });
Route::group(['prefix' => 'employee-profile', 'as' => 'employee-profile.'], function () {
        Route::get('pdf', [EmployeeController::class, 'employeePdf'])->name('pdf');
    });
Route::resource('employees-identity', EmployeeIdentityController::class);
Route::resource('employees-document', EmployeeDocumentController::class);
Route::resource('employee-qualification', EmployeeQualificationController::class);
Route::resource('employee-work-experience', EmployeeWorkExperienceController::class);
Route::resource('employee-certificate', EmployeeCertificateController::class);
Route::resource('employee-reference', EmployeeReferenceController::class);
Route::resource('employee-bank-account', EmployeeBankAccontController::class);
Route::resource('employee-leeds', LeedController::class);

Route::get('employee/bank/status/{id}', [EmployeeBankAccontController::class, 'statusUpdate'])->name('employee.bank.update.status');
Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
    Route::get('status/{id}', [EmployeeController::class, 'statusUpdate'])->name('update.status');
    Route::get('profile/{id}', [EmployeeController::class, 'employeeProfile'])->name('profile');
    Route::post('details-address-country-search', [EmployeeController::class, 'countrySearch'])->name('details.address.country.search');
    Route::post('details-address-state-search', [EmployeeController::class, 'stateSearch'])->name('details.address.state.search');
    Route::post('details-address-city-search', [EmployeeController::class, 'citySearch'])->name('details.address.city.search');
    Route::post('details-department-search', [EmployeeController::class, 'departmentSearch'])->name('details.department.search');
    Route::post('details-designation-search', [EmployeeController::class, 'designationSearch'])->name('details.designation.search');
    Route::post('details-reference-search', [EmployeeController::class, 'referenceSearch'])->name('details.reference.search');
    Route::post('employee-address-update/{id}', [EmployeeController::class, 'EmployeeAddressUpdate'])->name('address.update');
//leeds
    Route::get('employee-own-leeds/{id}', [LeedController::class, 'ownLeeds'])->name('employee-own-leeds');
    Route::get('employee-assign-leeds/{id}', [LeedController::class, 'assignLeeds'])->name('employee-assign-leeds');


});

//Department
Route::resource('department', DepartmentController::class);
Route::group(['prefix' => 'department', 'as' => 'department.'], function () {
    Route::get('status/{id}', [DepartmentController::class, 'statusUpdate'])->name('update.status');
});

//Designation
Route::resource('designation', DesignationController::class);
Route::group(['prefix' => 'designation', 'as' => 'designation.'], function () {
    Route::get('status/{id}', [DesignationController::class, 'statusUpdate'])->name('update.status');
});


Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    //role
    Route::resource('role', RoleController::class);
    Route::post('all-role', [RoleController::class, 'role'])->name('all-role');

    //permission
    Route::resource('permission', PermissionController::class);

    //Reference
    Route::resource('reference', ReferenceController::class);
    Route::group(['prefix' => 'reference', 'as' => 'reference.'], function () {
        Route::get('status/{id}', [ReferenceController::class, 'statusUpdate'])->name('update.status');
    });

    //Priority
    Route::resource('priority', PriorityController::class);
    Route::group(['prefix' => 'priority', 'as' => 'priority.'], function () {
        Route::get('status/{id}', [PriorityController::class, 'statusUpdate'])->name('update.status');
    });

    //Dashboard
    Route::get('dashboard', [DashboardSettingsController::class, 'create'])->name('dashboard.create');
    Route::post('dashboard/store', [DashboardSettingsController::class, 'store'])->name('dashboard.store');

    //identity
    Route::resource('identity', IdentityController::class);
    Route::group(['prefix' => 'identity', 'as' => 'identity.'], function () {
        Route::get('status/{id}', [IdentityController::class, 'statusUpdate'])->name('update.status');
    });
    Route::post('identity-search', [EmployeeController::class, 'identitySearch'])->name('identity.search');
});

// Address Route
Route::group(['prefix' => 'address', 'as' => 'address.'], function () {
    Route::resource('country', CountryController::class);
    Route::resource('state', StateController::class);
    Route::resource('city', CityController::class);
});
Route::resource('raw-material', RawMaterialController::class);
Route::group(['prefix' => 'raw-material', 'as' => 'raw-material.'], function () {
    Route::get('status/{id}', [RawMaterialController::class, 'statusUpdate'])->name('update.status');
});
Route::group(['prefix' => 'production', 'as' => 'production.'], function () {
    Route::resource('/', ProductionController::class);
    Route::post('rawmaterial', [ProductionController::class, 'rawmaterial'])->name('rawmaterial');
});

//investor route
Route::resource('investor', InvestorController::class);
Route::group(['prefix' => 'investor', 'as' => 'investor.'], function () {
    Route::get('status/{id}', [InvestorController::class, 'statusUpdate'])->name('update.status');
    Route::post('address-update/{id}', [InvestorController::class, 'InvestorAddressUpdate'])->name('address.update');
    Route::resource('document', InvestorDocumentController::class);
    Route::resource('identity', InvestorIdentityController::class);

});

//investment route
Route::resource('investment', InvestmentController::class);
Route::resource('investment-return', InvestmentReturnController::class);
Route::group(['prefix' => 'investment', 'as' => 'investment.'], function () {
    Route::get('list/{id}', [InvestmentController::class, 'InvestmentList'])->name('list');
});

//loan route
Route::resource('loan', LoanController::class);
Route::resource('loan-return', LoanReturnController::class);
Route::resource('loan-authority', LoanAuthorityController::class);
Route::group(['prefix' => 'loan', 'as' => 'loan.'], function () {
    Route::get('list/{id}', [LoanController::class, 'LoanList'])->name('list');
    Route::post('authority-address-update/{id}', [LoanAuthorityController::class, 'AuthorAddressUpdate'])->name('author.address.update');
    //authority route
    Route::group(['prefix' => 'authority', 'as' => 'authority.'], function () {
        Route::resource('document', AuthorityDocumentController::class);
        Route::resource('identity', AuthorityIdentityController::class);
    });
});

//Bank
Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
    Route::resource('bank', BankController::class);
    Route::group(['prefix' => 'bank', 'as' => 'bank.'], function () {
        Route::get('status/{id}', [BankController::class, 'statusUpdate'])->name('update.status');
    });
    Route::resource('bank-account', BankAccountController::class);
    Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
        Route::get('status/{id}', [BankAccountController::class, 'statusUpdate'])->name('update.status');
    });

    //transaction route
    Route::resource('transaction', TransactionController::class);
    Route::group(['prefix' => 'transaction', 'as' => 'transaction.'], function () {
        Route::get('status/{id}', [TransactionController::class, 'statusUpdate'])->name('update.status');
    });
    // deposit
    Route::resource('deposit', DepositController::class);
    Route::resource('withdraw', WithdrawController::class);
    Route::resource('cash-in', CashInController::class);

    //bank balance sheet
    Route::get('balance-sheet', [BalanceSheetController::class, 'index'])->name('balance.sheet.index');
    Route::get('balance-sheet-data', [BalanceSheetController::class, 'balanceSheetData'])->name('balanceSheetData');
    Route::post('account-statement/data', [BalanceSheetController::class, 'accountStatementData'])->name('statement.data');
    //get cash balance sheet
    Route::get('cash-balance-sheet', [BalanceSheetController::class, 'cashBalanceSheet'])->name('cash.balance.sheet.index');
    Route::group(['prefix' => 'bank.account', 'as' => 'bank.account.'], function () {
        Route::get('balance/{id}', [BalanceSheetController::class, 'bankAccountBalance'])->name('balance');
        Route::get('statement', [BalanceSheetController::class, 'bankAccountStatement'])->name('Statement');
    });

    //fund transafer
    Route::resource('fund-transfer', FundTransferController::class);
});

//Inventory
Route::group(['prefix' => 'inventory', 'as' => 'inventory.'], function () {
    // Settings
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        //Unit
        Route::resource('unit', InventoryUnitController::class);
        Route::group(['prefix' => 'unit', 'as' => 'unit.'], function () {
            Route::get('status/{id}', [InventoryUnitController::class, 'statusUpdate'])->name('update.status');
        });
        //Warehouse
        Route::resource('warehouse', InventoryWarehouseController::class);
        Route::group(['prefix' => 'warehouse', 'as' => 'warehouse.'], function () {
            Route::get('status/{id}', [InventoryWarehouseController::class, 'statusUpdate'])->name('update.status');
        });
        //Brand
        Route::resource('brand', InventoryBrandController::class);
        Route::group(['prefix' => 'brand', 'as' => 'brand.'], function () {
            Route::get('status/{id}', [InventoryBrandController::class, 'statusUpdate'])->name('update.status');
        });
        //ProductCategory
        Route::resource('productCategory', InventoryProductCategoriesController::class);
        Route::group(['prefix' => 'productCategory', 'as' => 'productCategory.'], function () {
            Route::get('status/{id}', [InventoryProductCategoriesController::class, 'statusUpdate'])->name('update.status');
        });
    });

    // Products
    Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
        //ProductCategory
        Route::resource('category', InventoryProductCategoriesController::class);
        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('status/{id}', [InventoryProductCategoriesController::class, 'statusUpdate'])->name('update.status');
        });
    });

    // Service
    Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
        // Category
        Route::resource('category', InventoryServiceCategoriesController::class);
        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('status/{id}', [InventoryServiceCategoriesController::class, 'statusUpdate'])->name('update.status');
        });

        //Service
        Route::resource('service', InventoryServiceController::class);
        Route::group(['prefix' => 'service', 'as' => 'service.'], function () {
            Route::get('status/{id}', [InventoryServiceController::class, 'statusUpdate'])->name('update.status');
        });
    });

    // Customers
    Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
        //customer
        Route::get('client-type-priority/{id}', [InventoryCustomerController::class, 'getCustomerTypePriority'])->name('customer.type.priority');
        Route::resource('customer', InventoryCustomerController::class);
        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
            Route::get('status/{id}', [InventoryCustomerController::class, 'statusUpdate'])->name('update.status');
        });
    });
});


//Project

Route::resource('projects', ProjectController::class);
Route::group(['prefix' => 'projects', 'as' => 'projects.'], function () {
    Route::post('category/search', [ProjectController::class, 'projectCategorySearch'])->name('category.search');
    Route::post('employee-search', [ProjectController::class, 'employeeSearch'])->name('employee.search');
});

Route::group(['prefix' => 'project', 'as' => 'project.'], function () {
    Route::resource('document', ProjectDocumentController::class);
    Route::get('hold/{id}', [ProjectController::class, 'projectHold'])->name('hold');
    Route::get('un-hold/{id}', [ProjectController::class, 'projectUnHold'])->name('unhold');
    Route::get('hold-history/{id}', [ProjectController::class, 'holdList'])->name('hold.list');
    Route::resource('link', ProjectLinkController::class);
    Route::group(['prefix' => 'link', 'as' => 'link.'], function () {
        Route::get('details/{id}', [ProjectLinkController::class, 'linkDetails'])->name('details');
    });

    //Task
    Route::resource('task', TaskController::class);
    Route::group(['prefix' => 'task', 'as' => 'task.'], function () {
        Route::get('task/{id}', [TaskController::class, 'taskDetails'])->name('view');
    });

    Route::resource('duration', ProjectDurationController::class);
    Route::group(['prefix' => 'duration', 'as' => 'duration.'], function () {
        Route::get('details/{id}', [ProjectDurationController::class, 'durationDetails'])->name('details');
        Route::get('hold-list/{id}', [ProjectDurationController::class, 'durationHoldList'])->name('hold.list');
    });
    Route::get('project-duration/{id}', [ProjectDurationController::class, 'projectDuration'])->name('duration');

    //Resource
    Route::resource('resource', ProjectResourceController::class);
    Route::group(['prefix' => 'resource', 'as' => 'resource.'], function () {
        Route::get('details/{id}', [ProjectResourceController::class, 'resourceDetails'])->name('details');
    });

    Route::get('project-resource/{id}', [ProjectResourceController::class, 'projectResource'])->name('resource');
    // Resource End

    Route::resource('module', ProjectModuleController::class);
    Route::group(['prefix' => 'module', 'as' => 'module.'], function () {
        Route::get('show/{id}', [ProjectModuleController::class, 'projectModule'])->name('show-all');
        Route::post('edit', [ProjectModuleController::class, 'projectUpdate'])->name('project-update');
        Route::get('hold/{id}', [ProjectModuleController::class, 'moduleHold'])->name('hold');
        Route::get('un-hold/{id}', [ProjectModuleController::class, 'moduleUnHold'])->name('unhold');
        Route::get('module-add/{id}', [ProjectModuleController::class, 'create'])->name('add');
        Route::post('module-search', [ProjectModuleController::class, 'moduleSearch'])->name('search.module');
        Route::get('hold-history/{id}', [ProjectModuleController::class, 'holdList'])->name('hold.list');
    });

    Route::get('grid-view', [ProjectController::class, 'gridView'])->name('grid.view');
    Route::resource('assign-to', ProjectAssignToController::class);
    Route::post('employee-search', [ProjectAssignToController::class,'AllEmployeeSearch'])->name('employee.search');
    Route::get('employee-assign/{id}', [ProjectAssignToController::class, 'employeeAssignTo'])->name('employee.assign.to');

    Route::group(['prefix' => 'reporting', 'as' => 'reporting.'], function () {
        Route::post('add-reporting-person', [ProjectAssignToController::class, 'AddReportingEmployee'])->name('assign');
        Route::post('search-reporting-person', [ProjectAssignToController::class, 'ReportingEmployeeSearch'])->name('search');
        Route::get('reporting-person-show/{id}', [ProjectAssignToController::class, 'ReportingEmployeeShow'])->name('show');
        Route::post('reporting-person-delete/{id}', [ProjectAssignToController::class, 'destroyReporting'])->name('delete');
    });

    //Accounts
    Route::resource('account-budget', ProjectAccountController::class);
    Route::group(['prefix' => 'account-budget', 'as' => 'account-budget.'], function () {
        Route::get('show/{id}', [ProjectAccountController::class, 'projectAccounts'])->name('view');
    });

    Route::resource('budget-receipt', ProjectReceiptController::class);
    Route::group(['prefix' => 'budget-receipt', 'as' => 'budget-receipt.'], function () {
        Route::post('document', [ProjectReceiptController::class, 'receiveDocument'])->name('document');
    });

    // category
    Route::resource('category', ProjectCategoriesController::class);
    Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
        Route::get('status/{id}', [ProjectCategoriesController::class, 'statusUpdate'])->name('update.status');
    });

    // type
    Route::resource('type', ProjectTypeController::class);
    Route::group(['prefix' => 'type', 'as' => 'type.'], function () {
        Route::get('status/{id}', [ProjectTypeController::class, 'statusUpdate'])->name('update.status');
    });
});


//HRM
Route::group(['prefix' => 'hrm', 'as' => 'hrm.'], function () {
    //salary
    Route::resource('salary', SalaryManagementController::class);
    // allowance
    Route::resource('allowance', AllowanceController::class);
    Route::post('allowance/status-update/{id}', [AllowanceController::class,'statusUpdate'])->name('allowance.status.update');
    Route::post('allowance-show', [AllowanceController::class,'allowanceShow'])->name('allowance-show');
    Route::post('allowance-update', [AllowanceController::class, 'allowanceUpdate'])->name('allowance-update');
   // paid-leave
   Route::resource('paid-leave', PaidLeaveController::class);
   Route::post('paid-leave-show', [PaidLeaveController::class,'leaveShow'])->name('paid-leave-show');
   Route::post('paid-leave/status-update/{id}', [PaidLeaveController::class,'statusUpdate'])->name('paid-leave.status');
   Route::post('paid-leave-update', [PaidLeaveController::class, 'paidLeaveUpdate'])->name('paid-leave-update');
    // notice
    Route::post('department/search', [HRMNoticeController::class, 'departmentSearch'])->name('department.search');
    Route::post('department/employee', [HRMNoticeController::class, 'getDepartmentWiseEmployee'])->name('getDepartmentWiseEmployee');
    Route::post('department/dept', [HRMNoticeController::class, 'getDepartment'])->name('getDepartment');
    Route::resource('notice', HRMNoticeController::class);
    Route::group(['prefix' => 'notice', 'as' => 'notice.'], function () {
        Route::get('status/{id}', [HRMNoticeController::class, 'statusUpdate'])->name('update.status');
    });

    // Settings
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        // Holiday
        Route::resource('holiday', HolidayController::class);
        Route::group(['prefix' => 'holiday', 'as' => 'holiday.'], function () {
            Route::get('status/{id}', [HolidayController::class, 'statusUpdate'])->name('update.status');
        });

        // Weekend
        Route::resource('weekend', WeekendController::class);
        Route::group(['prefix' => 'weekend', 'as' => 'weekend.'], function () {
            Route::get('status/{id}', [WeekendController::class, 'statusUpdate'])->name('update.status');
        });
    });

});

//ALLOWANCE




Route::resource('salary', SalaryController::class);
Route::post('salaryList', [SalaryController::class,'salaryList'])->name('salarylist');
Route::post('salary-show', [SalaryController::class,'salaryShow'])->name('salary-show');
Route::post('salary/statusupdate/{id}', [SalaryController::class,'statusUpdate'])->name('salary.status.update');
Route::post('salary-update', [SalaryController::class, 'salaryUpdate'])->name('salary-update');
Route::get('project/ongoing', function () {
    $project = \App\Models\Project\Projects::where('status',2)->latest()->paginate(5);
    return view('admin.project.project.partial.grid.ongoing')->render();
});




