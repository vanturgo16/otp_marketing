<?php

use App\Http\Controllers\Marketing\InputPOCustController;
use Illuminate\Support\Facades\Route;

Route::group(
  ['prefix' => 'marketing/inputPOCust'],
  function () {
    Route::controller(InputPOCustController::class)->middleware('permission:Marketing_inputPOCust')->group(function () {
      Route::get('/', 'index')->name('marketing.inputPOCust.index');
      Route::get('/create', 'create')->name('marketing.inputPOCust.create');
      Route::get('/get-data', 'getData')->name('marketing.inputPOCust.getData');
      Route::get('/get-customers', 'getCustomers')->name('marketing.inputPOCust.getCustomers');
      Route::get('/get-customer-detail', 'getCustomerDetail')->name('marketing.inputPOCust.getCustomerDetail');
      Route::get('/get-data-product', 'getDataProduct')->name('marketing.inputPOCust.getDataProduct');
      Route::get('/get-product-detail', 'getProductDetail')->name('marketing.inputPOCust.getProductDetail');
      Route::get('/get-all-unit', 'getAllUnit')->name('marketing.inputPOCust.getAllUnit');
      Route::post('/', 'store')->name('marketing.inputPOCust.store');
      Route::get('/edit/{encryptedPoNumber}', 'edit')->name('marketing.inputPOCust.edit');
      Route::get('/get-data-po-customer', 'getDataPOCustomer')->name('marketing.inputPOCust.getDataPOCustomer');
      Route::put('/', 'update')->name('marketing.inputPOCust.update');
      Route::get('/show/{encryptedPoNumber}', 'show')->name('marketing.inputPOCust.view');
      Route::post('/bulk-posted', 'bulkPosted')->name('marketing.inputPOCust.bulkPosted');
      Route::post('/bulk-unposted', 'bulkUnPosted')->name('marketing.inputPOCust.bulkUnPosted');
      Route::post('/bulk-deleted', 'bulkDeleted')->name('marketing.inputPOCust.bulkDeleted');
      Route::get('/preview/{encryptedPoNumber}', 'preview')->name('marketing.inputPOCust.preview');
      Route::get('/print/{encryptedPoNumber}', 'print')->name('marketing.inputPOCust.print');
      Route::get('/get-status', 'getStatus')->name('marketing.inputPOCust.getStatus');
      Route::get('/export-data', 'exportData')->name('marketing.inputPOCust.exportData');
    });
  }
);
