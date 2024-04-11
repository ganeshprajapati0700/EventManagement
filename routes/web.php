<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [EventController::class,'event'])->name('event');
Route::post('event', [EventController::class,'eventdatastore'])->name('event.store');
Route::post('save-ticket', [TicketController::class,'ticketstore'])->name('tickets.store');
Route::get('tickets', [TicketController::class,'tickets'])->name('tickets.index');
Route::get('edit/{id}', [TicketController::class,'editickets']);
Route::delete('delete/{id}', [TicketController::class, 'destroy']);
Route::post('/update/{ticketId}', [TicketController::class, 'update'])->name('tickets.update');
