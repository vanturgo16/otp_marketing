<?php

use App\Http\Controllers\Marketing\orderConfirmationController;
use Illuminate\Support\Facades\Route;
// Route::controller(orderConfirmationController::class)->group(function () {
//   Route::get('marketing/orderConfirmation', 'index')->name('marketing.orderConfirmation.index');
//   Route::post('marketing/orderConfirmation', 'store')->name('marketing.orderConfirmation.store');
// });

Route::group(
  ['prefix' => 'marketing/orderConfirmation'],
  function () { 
    Route::controller(orderConfirmationController::class)->middleware('permission:Marketing_orderConfirmation')->group(function () {
      Route::get('/', 'index')->name('marketing.orderConfirmation.index');
      Route::get('/create', 'create')->name('marketing.orderConfirmation.create');
      Route::get('/get-customers', 'getCustomers')->name('marketing.orderConfirmation.getCustomers');
      Route::get('/get-customer-detail', 'getCustomerDetail')->name('marketing.orderConfirmation.getCustomerDetail');
      Route::get('/get-data-product', 'getDataProduct')->name('marketing.orderConfirmation.getDataProduct');
      Route::get('/get-product-detail', 'getProductDetail')->name('marketing.orderConfirmation.getProductDetail');
      Route::get('/get-all-unit', 'getAllUnit')->name('marketing.orderConfirmation.getAllUnit');
      Route::post('/', 'store')->name('marketing.orderConfirmation.store');
      Route::get('/edit/{encryptedOCNumber}', 'edit')->name('marketing.orderConfirmation.edit');
      Route::get('/get-data-order-confirmation', 'getDataOrderConfirmation')->name('marketing.orderConfirmation.getDataOrderConfirmation');
      Route::put('/', 'update')->name('marketing.orderConfirmation.update');
      Route::get('/show/{encryptedOCNumber}', 'show')->name('marketing.orderConfirmation.view');
      Route::post('/bulk-posted', 'bulkPosted')->name('marketing.orderConfirmation.bulkPosted');
      Route::post('/bulk-unposted', 'bulkUnPosted')->name('marketing.orderConfirmation.bulkUnPosted');
      Route::post('/bulk-deleted', 'bulkDeleted')->name('marketing.orderConfirmation.bulkDeleted');
      Route::get('/preview/{encryptedOCNumber}', 'preview')->name('marketing.orderConfirmation.preview');
      Route::get('/print/{encryptedOCNumber}', 'print')->name('marketing.orderConfirmation.print');
    });
  }
);
