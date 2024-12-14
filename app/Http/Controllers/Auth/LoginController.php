<?php
	
	namespace App\Http\Controllers\Auth;
	
	use App\Http\Controllers\Controller;
	use App\Providers\RouteServiceProvider;
	use Illuminate\Foundation\Auth\AuthenticatesUsers;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Http\Request;
	use Illuminate\Validation\Rule;
	use App\Models\User;
	use App\Models\Role;
	use DB, Auth;
	
	class LoginController extends Controller
	{
		/*
			|--------------------------------------------------------------------------
			| Login Controller
			|--------------------------------------------------------------------------
			|
			| This controller handles authenticating users for the application and
			| redirecting them to your home screen. The controller uses a trait
			| to conveniently provide its functionality to your applications.
			|
		*/
		
		use AuthenticatesUsers;
		
		public function userLogin(Request $request)
		{
			$validation = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required|min:6',
			]);
			
			if ($validation->fails()) 
			{
				return response()->json(['status' => 'validation', 'errors' => $validation->errors()]);
			}  
	
			if (Auth::attempt(['email' => $request->email, 'password' => $request->password]))
			{
				$user = Auth::user();
				//$token = $user->createToken('UserToken')->accessToken;
				
				return response()->json([
				'status' => 'success',
				'msg' => 'Login successful.',
				'data' => [
                'user' => $user,
               // 'token' => $token,
				]
				]);
			}
			
			// Return error response for failed authentication
			return response()->json([
			'status' => 'error',
			'msg' => 'Invalid email or password.',
			]);
		}
		
		/**
			* Where to redirect users after login.
			*
			* @var string
		*/
		protected $redirectTo = RouteServiceProvider::HOME;
		
		/**
			* Create a new controller instance.
			*
			* @return void
		*/
		public function __construct()
		{
			$this->middleware('guest')->except('logout');
		}
	}
