<?php
//google routes
Route::get('login/google', 'Auth\LoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');

Auth::routes();
//home route
Route::get('/home', 'FilesController@showFiles')->middleware('auth');
