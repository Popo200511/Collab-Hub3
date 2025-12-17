<?php
use App\Http\Controllers\AddJobcontroller;
use App\Http\Controllers\Admincontroller;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Billingcontroller;
use App\Http\Controllers\Implementcontroller;
use App\Http\Controllers\ImportItemController;
use App\Http\Controllers\Prcontroller;
use App\Http\Controllers\Refcodecontroller;
use App\Http\Controllers\SubcInvoicecontroller;
use App\Http\Controllers\TowerDismantleController;
use App\Http\Controllers\Truecontroller;
use App\Http\Controllers\UserAddJobcontroller;
use App\Http\Controllers\UserProjectDatabasescontroller;
use App\Http\Controllers\Wocontroller;
use App\Http\Middleware\CheckInventory;
use App\Http\Middleware\CheckStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

//PO
use App\Http\Controllers\PurchaseOrderController;


Route::get('/', function () {
    return redirect()->route('login');
});


//Collab HUB

// Home
Route::get('user/home', [UserAddJobcontroller::class, 'home'])->name('user.home');

// Add job User
Route::get('user/addjob/home', [UserAddJobcontroller::class, 'index'])->name('addjob.user');
// Add job SDA
Route::get('user/sda/home', [UserAddJobcontroller::class, 'sda'])->name('user.sda.home');
Route::get('/notification/read/{id}', [UserAddJobController::class, 'markAsRead'])->name('notification.read');

// Admin register
Route::get('user/sda/register', [RegisterController::class, 'showRegistrationForm'])->name('sda.register');
Route::post('user/sda/register', [RegisterController::class, 'register'])->name('sda.register');

// Project Databases
Route::get('user/project/projectview', [UserProjectDatabasescontroller::class, 'project16'])->name('project.projectview');

Route::post('user/permissions/save/{project_code}', [UserProjectDatabasescontroller::class, 'save'])->name('permissions.save');

// Inline update for collab_newjob
Route::post('user/newjob/inline-update', [UserProjectDatabasescontroller::class, 'inlineUpdate'])->name('newjob.inlineUpdate');








// ProjectDatabases
// 98_TRUE
Route::get('projectdatabases/98true/home', [Truecontroller::class, 'index'])->name('98true.home');

// New Job Assignment
Route::get('newjobassignment/addjob', [AddJobcontroller::class, 'index'])->name('addjob.index');
Route::post('newjobassignment/savenewjob', [AddJobcontroller::class, 'savenewjob'])->name('addjob.savenewjob');
Route::post('newjobassignment/addjob', [AddJobcontroller::class, 'importnewjob'])->name('addjob.importnewjob');
Route::post('newjobassignment/saveaddjob', [AddJobcontroller::class, 'saveimportnewjob'])->name('addjob.saveimportnewjob');
Route::put('/job/status/{id}', [AddJobcontroller::class, 'updateStatus'])->name('update.job.status');

// SDA
Route::get('newjobassignment/sda/home', [AddJobcontroller::class, 'sda'])->name('sda.home');

// Implement
Route::get('/implement/home', [Implementcontroller::class, 'index'])->name('implement.home');
Route::post('/implement/save', [Implementcontroller::class, 'addrefcode'])->name('implement.save');
Route::get('/implement/edit/{id}', [ImplementController::class, 'edit'])->name('implement.edit');

// Search Sitecode
Route::get('/search-sitecode', [Implementcontroller::class, 'searchSitecode']);
Route::get('/search-refcodeimplement', [Implementcontroller::class, 'searchRefcode']);

// TowerDismantle
Route::get('/towerDismantle/home', [TowerDismantleController::class, 'index']);
Route::get('/towerDismantle/update/{id}', [TowerDismantleController::class, 'edit'])->name('towerDismantle.update');
Route::post('/towerDismantle/save', [TowerDismantleController::class, 'addrefcode'])->name('towerDismantle.save');

Route::post('/towerDismantle/update/{id}', [TowerDismantleController::class, 'update'])->name('towerDismantle.updateId');

// Taking
Route::get('dashboard', [Admincontroller::class, 'dashboard']);

// Import
Route::post('/import', [Admincontroller::class, 'importrefcode']); //import sitecode
Route::get('/import', [Admincontroller::class, 'importrefcode']);  //import sitecode

// Save import
Route::get('/saveImport', [Admincontroller::class, 'saveAdd']);  //import Save
Route::post('/saveImport', [Admincontroller::class, 'saveAdd']); //import Save

Route::get('/blog', [Admincontroller::class, 'index'])->name('blog')->middleware(CheckStatus::class);
Route::get('edit/{id}', [Admincontroller::class, 'edit'])->name('edit');
Route::put('update/{id}', [Admincontroller::class, 'update'])->name('update');

Route::get('add', [Admincontroller::class, 'add'])->name('add');
Route::post('insert', [Admincontroller::class, 'insert'])->name('insert');

Auth::routes();

// Home
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::delete('/user/{id}', [App\Http\Controllers\HomeController::class, 'destroy'])->name('user.delete');
Route::put('/update-status/{user}', [App\Http\Controllers\HomeController::class, 'updateStatus'])->name('user.updateStatus');

Route::post('/register', [RegisterController::class, 'register'])->name('register');            // status = 4
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register'); // status = 4


//test
/*
Route::get('/test/are', [Dropdowncontroller::class, 'total'])->name('are');
Route::get('/test/user', [Dropdowncontroller::class, 'user'])->name('user');
*/












// Module ERP

// Search Refcode
Route::get('refcode/home', [Refcodecontroller::class, 'index']);
Route::get('/search-refcode', [RefcodeController::class, 'searchRefcode'])->name('searchRefcode');

//import refcode
//Route::get('refcode/home', [Refcodecontroller::class, 'importrefcode']);
Route::post('refcode/home', [RefcodeController::class, 'importrefcode'])->name('refcode.import');

//save refcode
Route::get('refcode/saverefcode', [Refcodecontroller::class, 'saveAdd']);
Route::post('refcode/saverefcode', [Refcodecontroller::class, 'saveAdd']);
// export excel
Route::get('/export-refcode', [RefcodeController::class, 'exportRefcode'])->name('exportRefcode');

// import billing
// Billing
Route::get('billing/home', [Billingcontroller::class, 'index'])->name('billing.home');
Route::post('billing/import', [Billingcontroller::class, 'importbilling'])->name('billing.import');
Route::post('/savebilling', [Billingcontroller::class, 'savebilling'])->name('billing.savebilling');
Route::get('billing/search', [Billingcontroller::class, 'search'])->name('billing.search');

// import PR
Route::get('pr/home', [Prcontroller::class, 'index'])->name('pr.home');
Route::post('pr/home', [Prcontroller::class, 'importpr'])->name('pr.home');
Route::post('pr/savepr', [Prcontroller::class, 'savepr'])->name('pr.savepr');

// purchase
Route::get('pr/purchase', [Prcontroller::class, 'purchase'])->name('pr.purchase');
Route::post('pr/purchase', [Prcontroller::class, 'importpurchase'])->name('pr.purchase');
Route::post('pr/savepurchase', [Prcontroller::class, 'savepurchase'])->name('pr.savepurchase');
// Import WO
Route::get('wo/home', [Wocontroller::class, 'index'])->name('wo.home');

// Import SubcInvoice
Route::get('subcinvoice/home', [SubcInvoicecontroller::class, 'index'])->name('subcinvoice.home');

// Inventory

// หน้า import add
Route::get('/import', [ImportItemController::class, 'index'])->middleware(CheckInventory::class)->name('inventory');
Route::get('/check-refcode', [ImportItemController::class, 'checkRefcode'])->name('check.refcode')->middleware(CheckInventory::class);
Route::get('/check-import', [ImportItemController::class, 'checkImport_add'])->name('check.import')->middleware(CheckInventory::class);

Route::get('/import', [ImportItemController::class, 'material'])->name('import_get')->middleware(CheckInventory::class);

// กด import add
Route::post('/importadd', [ImportItemController::class, 'additem'])->name('importadd')->middleware(CheckInventory::class);
Route::get('/importadd', [ImportItemController::class, 'additem'])->name('importadd')->middleware(CheckInventory::class);

//หน้า Refcode
Route::post('/addrefcode', [ImportItemController::class, 'import_refcode'])->name('addrefcode')->middleware(CheckInventory::class);
Route::get('/addrefcode', [ImportItemController::class, 'import_refcode'])->name('addrefcode')->middleware(CheckInventory::class);

Route::post('/saveadd', [ImportItemController::class, 'saveAdd'])->name('saveadd')->middleware(CheckInventory::class);
Route::get('/saveadd', [ImportItemController::class, 'saveAdd'])->name('saveadd')->middleware(CheckInventory::class);
// add refcodemanual
Route::post('/addrefcodemanual', [ImportItemController::class, 'addrefcodemanual'])->middleware(CheckInventory::class);

//หน้า Material
Route::get('/material', [ImportItemController::class, 'import_material'])->name('material')->middleware(CheckInventory::class);
Route::post('/material', [ImportItemController::class, 'import_material'])->name('material')->middleware(CheckInventory::class);

Route::get('/savematerial', [ImportItemController::class, 'savematerial'])->name('savematerial')->middleware(CheckInventory::class);
Route::post('/savematerial', [ImportItemController::class, 'savematerial'])->name('savematerial')->middleware(CheckInventory::class);
//Add Material
Route::post('/addmaterialmanual', [ImportItemController::class, 'addmaterialmanual'])->name('addmaterialmanual')->middleware(CheckInventory::class);

//Droppoint

Route::get('/droppoint', [ImportItemController::class, 'droppoint'])->name('droppoint')->middleware(CheckInventory::class);
//add
Route::get('/Adddroppoint', [ImportItemController::class, 'addDroppoint'])->name('Adddroppoint')->middleware(CheckInventory::class);
Route::post('/Adddroppoint', [ImportItemController::class, 'addDroppoint'])->name('Adddroppoint')->middleware(CheckInventory::class);
//import
Route::post('/droppoint', [ImportItemController::class, 'import_droppoint'])->name('droppoint')->middleware(CheckInventory::class);
Route::get('/droppoint', [ImportItemController::class, 'import_droppoint'])->name('droppoint')->middleware(CheckInventory::class);
//save
Route::get('/savedroppoint', [ImportItemController::class, 'savedroppoint'])->name('savematerial')->middleware(CheckInventory::class);
Route::post('/savedroppoint', [ImportItemController::class, 'savedroppoint'])->name('savematerial')->middleware(CheckInventory::class);

//withdraw
Route::get('/withdraw', [ImportItemController::class, 'withdraw'])->name('withdraw')->middleware(CheckInventory::class);

Route::post('/withdrawAdd', [ImportItemController::class, 'addWithdraw'])->name('withdrawAdd')->middleware(CheckInventory::class);

//SUM
Route::get('/sum', [ImportItemController::class, 'summary'])->name('sum')->middleware(CheckInventory::class);

//Region
Route::get('/region', [ImportItemController::class, 'region'])->middleware(CheckInventory::class);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


////////////////////////////////////////////////////////แผนก PO \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

// หน้าแสดงรายการ PO
Route::get('/PO/purchase', [PurchaseOrderController::class, 'index']) ->name('purchase-orders.index');


// บันทึก PO ใหม่
Route::post('/purchase-orders/store', [PurchaseOrderController::class, 'store']) ->name('purchase-orders.store');



// แสดงรายละเอียด PO
Route::get('/purchase-order/show/{id}', [PurchaseOrderController::class, 'show'])->name('purchase-order.show');

Route::post('/purchase-order/save', [PurchaseOrderController::class, 'save'])->name('purchase-order.save');

Route::get('/po-items/{id}', [PurchaseOrderController::class, 'fetchPoItems']);


// importItems
Route::post('/import-items', [PurchaseOrderController::class, 'importItems']);

Route::post('/check-items-duplicates', [PurchaseOrderController::class, 'checkItemsDuplicates']);












// (หน้า Form สำหรับสร้าง PO)
Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create']) ->name('purchase-orders.create');



        



        // ดึงข้อมูลจาก Filter (PO No / Date / Customer)
Route::get('/purchase-order/get-data', [PurchaseOrderController::class, 'getPoData'])
        ->name('purchase-order.getData');

// Import Items (Excel → Database)
Route::post('/purchase-order/import-items', [PurchaseOrderController::class, 'importItems'])
        ->name('purchase-order.import-items');

// Import PO Items (แบบ AJAX)
Route::post('/purchase-order/import', [PurchaseOrderController::class, 'import'])
        ->name('purchase-order.import');

// Export Excel
Route::get('/items/export', [PurchaseOrderController::class, 'export'])
        ->name('items.export');

// Check Import ก่อนบันทึกจริง
Route::post('/items/check-import', [PurchaseOrderController::class, 'checkImport'])
        ->name('items.check-import');






