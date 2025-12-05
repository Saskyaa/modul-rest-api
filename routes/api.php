<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;

// Route dengan grouping untuk endpoint tambahan tugas akhir
Route::controller(BookController::class)->group(function () {
    // endpoint utama
    Route::get('/books', 'index');

    // endpoint search (punya dari code lama)
    Route::get('/books/search', 'search');

    // filter by year (punya lama)
    Route::get('/books/filter/year', 'filterByYear');

    // filter by publisher & author (punya lama)
    Route::get('/books/filter', 'filterByPublisherAndAuthor');

    // tugas akhir tambahan:
    Route::get('/books/range', 'filterByYearRange'); // filter rentang tahun
    Route::get('/books/sort', 'sortByYear');         // sorting ascending/descending
});