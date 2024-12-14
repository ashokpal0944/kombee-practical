<?php
	
	namespace App\Http\Controllers\Auth;
	
	use App\Http\Controllers\Controller;
	use App\Providers\RouteServiceProvider;
	use Illuminate\Foundation\Auth\RegistersUsers;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Validator;
	use Illuminate\Validation\Rule;
	use Illuminate\Http\Request;
	use Illuminate\Auth\Events\Registered;
	use App\Events\UserRegistered;
	use App\Models\User;
	use App\Models\RoleUser;
	use App\Models\UserFile;
	use DB;
	
	class RegisterController extends Controller
	{
		/*
			|--------------------------------------------------------------------------
			| Register Controller
			|--------------------------------------------------------------------------
			|
			| This controller handles the registration of new users as well as their
			| validation and creation. By default this controller uses a trait to
			| provide this functionality without requiring any additional code.
			|
		*/
		
		use RegistersUsers;
		
		/**
			* Where to redirect users after registration.
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
			$this->middleware('guest');
		}
		
		public function userRegister(Request $request)
		{
			DB::beginTransaction();
			
			try {
				$currentTime = now();
				
				$data = $request->except('_token', 'password', 'hobbies', 'role_data');
				
				if (!empty($request->hobbies)) {
					$data['hobbies'] = implode(',', $request->hobbies);
					} else {
					$data['hobbies'] = '';
				}
				
				$data['created_at'] = $currentTime;
				$data['updated_at'] = $currentTime;
				$data['password'] = Hash::make($request->input('password'));
				
				$user = User::create($data);
				
				$roles = $request->role_data;
				if (count($roles) > 0) {
					foreach ($roles as $role) {
						RoleUser::create([
						'user_id' => $user->id,
						'role_id' => $role,
						'created_at' => $currentTime,
						'updated_at' => $currentTime,
						]);
					}
				}
				
				if ($files = $request->file('files')) {
					foreach ($files as $file) {
						$filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
						$filePath = $file->storeAs('uploads/file', $filename, 'public');
						
						UserFile::create([
						'user_id' => $user->id,
						'files' => $filePath,
						'created_at' => $currentTime,
						'updated_at' => $currentTime,
						]);
					}
				}
				
				event(new UserRegistered($user));
				
				DB::commit();
				
				return response()->json(['status' => 'success', 'msg' => 'User Register Successfully.']);
			} 
			catch (\Throwable $e) {
				// Rollback the transaction in case of an error
				DB::rollBack();
				
				return response()->json(['status' => 'error', 'msg' => 'An error occurred: ' . $e->getMessage()]);
			}
		}
		
		/**
			* Get a validator for an incoming registration request.
			*
			* @param  array  $data
			* @return \Illuminate\Contracts\Validation\Validator
		*/
		protected function validator(array $data)
		{
			return Validator::make($data, [
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
			]);
		}
		
		/**
			* Create a new user instance after a valid registration.
			*
			* @param  array  $data
			* @return \App\Models\User
		*/
		protected function create(array $data)
		{
			return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => Hash::make($data['password']),
			]);
		}
	}
