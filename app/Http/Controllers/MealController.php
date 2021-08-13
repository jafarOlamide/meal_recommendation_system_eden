<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\UserAllergy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class MealController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'main_item' => ['required', 'string'],
            'time_type' => ['required', 'string']
        ]);

       Meal::create($request->all());
       $this->updateRedisCache('meals');

       return ['res'=> 'success', 'message'=> 'Meal created'];
    }

    public function userRecommendations($id)
    {
        $cached_data = Redis::get('meal_recommendations_' . $id);

        if ($cached_data) {
            $recommendations = json_decode($cached_data, false);

            return ['res'=> 'success',  'message'=> 'redis', 'meal_recommendations'=>$recommendations];
        }

        $user_allergies = UserAllergy::
        select('allergy_type_id as allergy')
        ->where('user_allergies.user_id', $id)  
        ->get();

        $new_user_allergies = [];

        foreach ($user_allergies as $user_allergy) {
            array_push($new_user_allergies, $user_allergy['allergy']);
        }

        // $allergies_str = implode(",", $new_user_allergies);

        $meal_recommendations = Meal::
        select('meals.id as meal_id', 'meals.main_item', 'meals.time_type AS meal_time')
        ->selectRaw('GROUP_CONCAT(DISTINCT side_items.item_name) AS meal_side_items')
        ->leftJoin('side_items', 'meals.id', '=', 'side_items.meal_id')
        ->leftJoin('meal_allergies', 'meals.id', '=', 'meal_allergies.meal_id')
        ->whereNotIn('meal_allergies.allergy_type_id', $new_user_allergies)
        ->groupBy('meals.main_item')
        ->orderBy('meals.id', 'ASC')
        ->get();

        Redis::set('meal_recommendations_' . $id, $meal_recommendations);

        return ['res'=> 'success', 'meal_recommendations'=>$meal_recommendations];
    }

    public function usersRecommendation(Request $request)
    {
        $cached_data = Redis::get('meal_recommendations');

        if ($cached_data) {
            $recommendations = json_decode($cached_data, false);

            return ['res'=> 'success',  'message'=> 'redis', 'meal_recommendations'=>$recommendations];
        }

        $meal_recommendations = Meal::
        select('meals.id as meal_id', 'meals.main_item', 'meals.time_type AS meal_time')
        ->selectRaw('GROUP_CONCAT(DISTINCT side_items.item_name) AS meal_side_items')
        ->leftJoin('side_items', 'meals.id', '=', 'side_items.meal_id')
        ->leftJoin('meal_allergies', 'meals.id', '=', 'meal_allergies.meal_id')
        ->whereNotIn('meal_allergies.allergy_type_id', [1,2,3])
        ->groupBy('meals.main_item')
        ->orderBy('meals.id', 'ASC')
        ->get();

        Redis::set('meal_recommendations', $meal_recommendations);

        return ['res'=> 'success', 'meal_recommendations'=>$meal_recommendations];

    }
}
