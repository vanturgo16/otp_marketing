<?php

use App\Http\Controllers\Marketing\salesOrderController;


Route::group(
  ['prefix' => 'marketing/salesOrder'],
  function () {
    Route::controller(salesOrderController::class)->group(function () {
      Route::get('/', 'index')->name('marketing.salesOrder.index');
      Route::get('/create', 'create')->name('marketing.salesOrder.create');
      // Route::get('/get-data', 'getData')->name('marketing.salesOrder.getData');
      // Route::get('/get-customers', 'getCustomers')->name('marketing.salesOrder.getCustomers');
      Route::get('/get-order-detail', 'getOrderDetail')->name('marketing.salesOrder.getOrderDetail');
      Route::get('/generate-so-number', 'generateSONumber')->name('marketing.salesOrder.generateSONumber');
      // Route::get('/get-data-product', 'getDataProduct')->name('marketing.salesOrder.getDataProduct');
      // Route::get('/get-product-detail', 'getProductDetail')->name('marketing.salesOrder.getProductDetail');
      // Route::get('/get-all-unit', 'getAllUnit')->name('marketing.salesOrder.getAllUnit');
      Route::post('/', 'store')->name('marketing.salesOrder.store');
      // Route::get('/edit/{encryptedPoNumber}', 'edit')->name('marketing.salesOrder.edit');
      // Route::get('/get-data-po-customer', 'getDataPOCustomer')->name('marketing.salesOrder.getDataPOCustomer');
      // Route::put('/', 'update')->name('marketing.salesOrder.update');
      // Route::get('/show/{encryptedPoNumber}', 'show')->name('marketing.salesOrder.view');
      // Route::post('/bulk-posted', 'bulkPosted')->name('marketing.salesOrder.bulkPosted');
      // Route::post('/bulk-unposted', 'bulkUnPosted')->name('marketing.salesOrder.bulkUnPosted');
      // Route::post('/bulk-deleted', 'bulkDeleted')->name('marketing.salesOrder.bulkDeleted');
      // Route::get('/preview/{encryptedPoNumber}', 'preview')->name('marketing.salesOrder.preview');
      // Route::get('/print/{encryptedPoNumber}', 'print')->name('marketing.salesOrder.print');
    });
  }
);
