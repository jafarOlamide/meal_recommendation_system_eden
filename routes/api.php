<?php

use App\Http\Controllers\AllergyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/user/choose_allergy', [AllergyController::class, 'createUserAllergy']);
    Route::get('/user/allergies', [AllergyController::class, 'getAllergies']);

    Route::get('user/allergies/{id}', [AllergyController::class, 'getUserAllergies']);

    Route::get('/user/meal_recommendation/{id}', [MealController::class, 'userRecommendations']);
    Route::get('/user/meal_recommendations', [MealController::class, 'usersRecommendation']);


    //ADMIN ROUTES
    Route::post('/admin/add_meal_allergy', [AllergyController::class, 'createMealAllergy']);
    Route::post('/admin/add_sides_allergy', [AllergyController::class, 'createSideItemAllergy']);

    Route::post('/admin/add_meal', [MealController::class, 'store']);
});





Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
