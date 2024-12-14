<?php
	namespace App\Exports;
	
	use App\Models\User;
	use App\Models\Role;
	use Maatwebsite\Excel\Concerns\FromCollection;
	use Maatwebsite\Excel\Concerns\WithHeadings;
	
	class UserRoleExport implements FromCollection, WithHeadings
	{
		public function collection()
		{
			$users = User::with('country:id,name','state:id,name','city:id,name','roles:id,name')->where('id','!=',1)->get(); // Get all users with roles
			
			$data = [];
			
			foreach ($users as $user) {
				$roleNames = $user->roles->pluck('name')->implode(', ');
				
				$data[] = [
				'User ID' => $user->id,
				'First Name' => $user->first_name,
				'Last Name' => $user->last_name,
				'Email' => $user->email,
				'Contact Number' => $user->contact_number,
				'Country' => $user->country->name ?? 'N/A',
				'State' => $user->state->name ?? 'N/A',
				'City' => $user->city->name ?? 'N/A',
				'Role' => $roleNames,
				];
			}
			
			return collect($data);
		}
		
		public function headings(): array
		{
			return [
            'User ID',
            'First Name',
            'Last Name',
            'Email',
            'Contact Number',
            'Country',
            'State',
            'City',
            'Role',
			];
		}
	}
	
