<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$roles = ["HR", "Staff", "GM"];

		foreach ($roles as $value) {
			if (!DB::table('roles')->where('name', $value)->exists()) {
				DB::table('roles')->insert([
					'name' => $value,
					'guard_name' => "web",
					'created_at' => now(),
					'updated_at' => now(),
				]);
			}
		}
    }
}
