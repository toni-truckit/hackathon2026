<?php

declare(strict_types=1);

Route::livewire('/', 'pages::landing-page')->name('landing-page');
Route::livewire('/page/{staticPage}', 'pages::page-view')->name('page.view');
Route::livewire('/faq', 'pages::faq-view')->name('faq.view');
Route::livewire('/contact', 'pages::contact-view')->name('contact.view');
