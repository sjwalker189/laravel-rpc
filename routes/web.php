<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => 'welcome');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
