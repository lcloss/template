<?php

use Illuminate\Support\Facades\Route;
use Lcloss\Template\Http\Controllers\TemplateController;

Route::get('template', [TemplateController::class, 'index'])->name('template');
Route::get('template/{page}', [TemplateController::class, 'page'])->name('template.page');
