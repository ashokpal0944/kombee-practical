<?php
	
	namespace Database\Seeders;
	
	use Illuminate\Database\Seeder;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Hash;
	use App\Models\User;
	
	class UserSeeder extends Seeder
	{
		/**
			* Run the database seeds.
			*
			* @return void
		*/
		public function run()
		{
			$adminEmail = 'admin@gmail.com';
			if (User::where('email', $adminEmail)->doesntExist()) 
			{
				$admin = User::factory()->create([
                'first_name' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make('12345678'),
                'country_id' => 101,
                'state_id' => 12,
                'city_id' => 1041,
				]);
				
				$admin->roles()->attach(1); 
			}
			
			User::factory()->count(10)->create()->each(function ($user) {
				$roleId = rand(2, 5);
				$user->roles()->attach($roleId);
			});
		}
	}
