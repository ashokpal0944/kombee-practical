<?php
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Facades\Password;
	use Illuminate\Auth\Events\Registered;
	use App\Models\User;
	use App\Models\Role;
	use Auth,DB;
	
	class RoleController extends Controller
	{
		public function list()
		{
			$get_role = Role::where('id','!=',1)->where('status',1)->get();
			
			return response()->json(['status' => true,'data' => $get_role]);
		}
		
		public function insert(Request $request)
		{
			$rules = [
            'name' => 'required|string|max:255|unique:roles,name',
			];
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
				]);
			}
			
			
			try {
				
				DB::beginTransaction();
				
				$currentTime = now();
				
				Role::create([
					'name' => $request->input('name'),
					'created_at' => $currentTime,
					'updated_at' => $currentTime,
				]);
				
				DB::commit();
				return response()->json(['status' => true,'message' => 'The role has been created successfully.']);
				
			} catch (\Throwable $e) {
				
				DB::rollBack();
				
				return response()->json([
				'status' => false,
				'message' => 'An error occurred: ' . $e->getMessage(),
				]);
			}
		}
	}
