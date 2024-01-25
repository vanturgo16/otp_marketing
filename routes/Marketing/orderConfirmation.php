<?php

use App\Http\Controllers\Marketing\orderConfirmationController;

// Route::controller(orderConfirmationController::class)->group(function () {
//   Route::get('marketing/orderConfirmation', 'index')->name('marketing.orderConfirmation.index');
//   Route::post('marketing/orderConfirmation', 'store')->name('marketing.orderConfirmation.store');
// });

Route::group(
  ['prefix' => 'marketing/orderConfirmation'],
  function () { 
    Route::controller(orderConfirmationController::class)->group(function () {
      Route::get('/', 'index')->name('marketing.orderConfirmation.index');
      Route::get('/create', 'create')->name('marketing.orderConfirmation.create');
      Route::get('/get-customers', 'getCustomers')->name('marketing.orderConfirmation.getCustomers');
      Route::get('/get-customer-detail', 'getCustomerDetail')->name('marketing.orderConfirmation.getCustomerDetail');
      Route::get('/get-data-product', 'getDataProduct')->name('marketing.orderConfirmation.getDataProduct');
      Route::get('/get-product-detail', 'getProductDetail')->name('marketing.orderConfirmation.getProductDetail');
      Route::get('/get-all-unit', 'getAllUnit')->name('marketing.orderConfirmation.getAllUnit');
      Route::post('/', 'store')->name('marketing.orderConfirmation.store');
    });
  }
);
