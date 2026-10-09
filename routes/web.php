<?php

declare(strict_types=1);

Route::livewire('/', 'pages::landing-page')->name('landing-page');
Route::livewire('/page/{staticPage}', 'pages::page-view')->name('page.view');
Route::livewire('/faq', 'pages::faq-view')->name('faq.view');
Route::livewire('/contact', 'pages::contact-view')->name('contact.view');

Route::get('/quote/{reference}', App\Http\Controllers\QuotePageController::class)
    ->where('reference', '[A-Za-z0-9\-]{8,64}')
    ->middleware('throttle:60,1')
    ->name('quote.show');
