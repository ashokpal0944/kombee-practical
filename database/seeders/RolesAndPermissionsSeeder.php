<?php
	
	namespace Database\Seeders;
	
	use Illuminate\Database\Seeder;
	use Spatie\Permission\Models\Permission;
	use Spatie\Permission\Models\Role;
	
	class RolesAndPermissionsSeeder extends Seeder
	{
		public function run()
		{
			// Define permissions for suppliers
			$supplierPermissions = [
            'view_supplier',
            'create_supplier',
            'edit_supplier',
            'delete_supplier',
			];
			
			// Define permissions for customers
			$customerPermissions = [
            'view_customer',
            'create_customer',
            'edit_customer',
            'delete_customer',
			];
			
			// Create permissions for suppliers
			foreach ($supplierPermissions as $permission) {
				Permission::firstOrCreate(['name' => $permission]);
			}
			
			// Create permissions for customers
			foreach ($customerPermissions as $permission) {
				Permission::firstOrCreate(['name' => $permission]);
			}
			
			// Create roles
			$adminRole = Role::firstOrCreate(['name' => 'Admin']);
			$managerRole = Role::firstOrCreate(['name' => 'Manager']);
			
			// Assign permissions to roles
			$adminRole->givePermissionTo(Permission::all()); // Give all permissions to Admin
			$managerRole->givePermissionTo(['view_supplier', 'view_customer']); // Manager only gets view permissions
		}
	}	