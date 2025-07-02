<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Cases Module Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the Cases module.
| These routes follow Perfex CRM's standard patterns for HMVC modules.
|
*/

// Perfex CRM standard client area routes: domain.com/module/controller/method
// Note: Routes should match actual controller file name Cases_client.php

// Alternative clean routes (module name same as controller) 
$route['cases_client'] = 'cases/Cases_client/index';
$route['cases_client/index'] = 'cases/Cases_client/index';
$route['cases_client/consultations'] = 'cases/Cases_client/consultations';
$route['cases_client/view/(:num)'] = 'cases/Cases_client/view/$1';
$route['cases_client/consultation/(:num)'] = 'cases/Cases_client/consultation/$1';
$route['cases_client/hearing/(:num)'] = 'cases/Cases_client/hearing/$1';
$route['cases_client/download_document/(:num)'] = 'cases/Cases_client/download_document/$1';
$route['cases_client/get_case_details/(:num)'] = 'cases/Cases_client/get_case_details/$1';
$route['cases_client/(:any)'] = 'cases/Cases_client/$1';
$route['cases_client/(:any)/(:num)'] = 'cases/Cases_client/$1/$2';