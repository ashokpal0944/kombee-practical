<?php
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Support\Facades\Password;
	use Illuminate\Auth\Events\Registered;
	use App\Models\User; 
	use Auth,DB;
	
	class AuthenticationController extends Controller
	{
		public function loginPost(Request $request)
		{
			//return response()->json(['status' => false,'message' => 'Could not create token.']);
			
			$rules = [
            'email' => 'required|email',
            'password' => 'required|min:8',
			];
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
				]);
			}
			
			$credentials = $request->only('email', 'password');
			
			if (!Auth::attempt($credentials)) {
				return response()->json([
                'status' => false,
                'message' => 'Invalid email or password.',
				]);
			}
			
			$user = Auth::user();
			
			if (!$user) 
			{
				return response()->json([
                'status' => false,
                'message' => 'Authentication failed. User not found.',
				]);
			}
			
			$token = $user->createToken('AuthToken')->accessToken;
			
			return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
			]);
		}
	}
