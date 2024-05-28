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
      Route::get('/get-customer-detail', 'getCustomerDetail')->name('marketing.salesOrder.getCustomerDetail');
      Route::get('/get-order-detail', 'getOrderDetail')->name('marketing.salesOrder.getOrderDetail');
      Route::get('/generate-so-number', 'generateSONumber')->name('marketing.salesOrder.generateSONumber');
      Route::get('/get-data-product', 'getDataProduct')->name('marketing.salesOrder.getDataProduct');
      Route::get('/get-product-detail', 'getProductDetail')->name('marketing.salesOrder.getProductDetail');
      Route::get('/get-all-unit', 'getAllUnit')->name('marketing.salesOrder.getAllUnit');
      Route::post('/', 'store')->name('marketing.salesOrder.store');
      Route::get('/edit/{encryptedSONumber}', 'edit')->name('marketing.salesOrder.edit');
      Route::get('/get-data-sales-order', 'getDataSalesOrder')->name('marketing.salesOrder.getDataSalesOrder');
      Route::put('/', 'update')->name('marketing.salesOrder.update');
      Route::get('/show/{encryptedSONumber}', 'show')->name('marketing.salesOrder.view');
      Route::post('/bulk-posted', 'bulkPosted')->name('marketing.salesOrder.bulkPosted');
      Route::post('/bulk-unposted', 'bulkUnPosted')->name('marketing.salesOrder.bulkUnPosted');
      Route::post('/bulk-deleted', 'bulkDeleted')->name('marketing.salesOrder.bulkDeleted');
      // Route::get('/preview/{encryptedSONumber}', 'preview')->name('marketing.salesOrder.preview');
      Route::get('/print/{encryptedSONumber}', 'print')->name('marketing.salesOrder.print');
      Route::get('/generateWO/{encryptedSONumber}', 'generateWO')->name('marketing.salesOrder.generateWO');
      Route::post('/cancel-qty', 'cancelQty')->name('marketing.salesOrder.cancelQty');
    });
  }
);

Route::group(
  ['prefix' => 'ppic/workOrder'],
  function () {
    Route::controller(salesOrderController::class)->group(function () {
      Route::get('/show/{encryptedSONumber}', 'showWO')->name('ppic.workOrder.viewWO');
    });
  }
);
