<?php

namespace App\Http\Controllers;

use App\Models\AllergyType;
use App\Models\MealAllergy;
use App\Models\SideItemAllergy;
use App\Models\UserAllergy;
use App\Http\Traits\UserRole;
use Illuminate\Http\Request;

class AllergyController extends Controller
{
    use UserRole;
    
    //User defined allergy
    public function createUserAllergy(Request $request)
    {
        if (!$this->isUser($request->user())) {
            return ['res'=> false, 'message'=> 'Unauthorised access'];
        }

        
        $fields = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'allergies' => ['required', 'array', 'exists:allergy_types,id']
        ]);

        //save all allergies for user to db
        foreach ($fields['allergies'] as $allergy) {
            UserAllergy::create([
                'user_id'=>$fields['user_id'],
                'allergy_type_id'=> $allergy
            ]);
        }

        return response(['res'=>true, 'message'=> 'Allergy has been noted, your meal recommendations would avoid this allergy'], 200);
    }

    //Available allergies types on the system
    public function getAllergies(){
        return AllergyType::select('id', 'allergy_name')->get();
    }

    public function getUserAllergies($id){
        
        $user_allergies = UserAllergy::
        join('allergy_types', 'allergy_types.id', '=', 'user_allergies.allergy_type_id')
        ->select('allergy_types.allergy_name as allergy')
        ->where('user_allergies.user_id', $id)  
        ->get();

        return ["user_allergies" => $user_allergies];
    }



    public function createMealAllergy(Request $request){
        
        //Authorise user to access resource
        if (!$this->isAdmin($request->user())) {
            return ['res'=> false, 'message'=> 'Unauthorised access'];
        }

        //validate requests
        $fields = $request->validate([
            'meal_id' => ['required', 'exists:meals,id'],
            'allergies' => ['required', 'array', 'exists:allergy_types,id']
        ]);

        //save each allergy to db for meal 
        foreach ($fields['allergies'] as $allergy) {
            MealAllergy::create([
                'meal_id'=>$fields['user_id'],
                'allergy_type_id'=> $allergy
            ]);
        }

        return response(['res'=>true, 'message'=> 'Allergy has been noted'], 200);
    }

    public function createSideItemAllergy(Request $request){
        
        //Authorise user to access resource
        if (!$this->isAdmin($request->user())) {
            return ['res'=> false, 'message'=> 'Unauthorised access'];
        }
        
        $fields = $request->validate([
            'side_item_id' => ['required', 'exists:side_items,id'],
            'allergies' => ['required', 'array', 'exists:allergy_types,id']
        ]);

        //Save all allergies for side items
        foreach ($fields['allergies'] as $allergy) {
            SideItemAllergy::create([
                'side_item_id'=>$fields['user_id'],
                'allergy_type_id'=> $allergy
            ]);
        }

        return response(['res'=>true, 'message'=> 'Allergy has been noted'], 200);
    }


}
