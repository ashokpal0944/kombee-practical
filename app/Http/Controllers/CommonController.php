<?php
	
	namespace App\Http\Controllers;
	use Illuminate\Http\Request;
	use App\Models\Country;
	use App\Models\State;
	use App\Models\City;
	use App\Models\User;
	use Helper, DB, Validator, Storage;
	
	class CommonController extends Controller
	{
		public function getStateData(Request $request)
		{
			$country_id = $request->input('country_id');
			$get_states = State::where("country_id",$country_id)->get();
			
			$html = '<option value="">-- Select State--</option>';
			foreach($get_states as $value)
			{
				$html .= '<option value="'.$value->id .'">'.$value->name .'</option>';
			}
			
			echo $html;
			die;
		}
		
		public function getCityData(Request $request)
		{
			$state_id = $request->input('state_id');
			$get_citys = City::where("state_id",$state_id)->get();
			
			$html = '<option value="">-- Select Coty--</option>';
			foreach($get_citys as $value)
			{
				$html .= '<option value="'.$value->id .'">'.$value->name .'</option>';
			}
			
			echo $html;
			die;
		}
		
		public function checkEmail(Request $request)
		{
			$exists = User::where('email', $request->email)->exists();
			return response()->json(!$exists); 
		}
		
		public function checkContact(Request $request)
		{
			$exists = User::where('contact_number', $request->contact_number)->exists();
			return response()->json(!$exists); // Return true if not exists, false if exists
		}
	}
