<?php
	
	namespace App\Http\Controllers;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Validation\Rule;
	use App\Exports\UserRoleExport;
	use Maatwebsite\Excel\Facades\Excel;
	//use Barryvdh\DomPDF\Facade as PDF;
	use App\Models\User;
	use App\Models\Role;
	use DB, Validator, PDF;
	
	class UserController extends Controller
	{
		public function __construct()
		{
			$this->middleware('auth');
		}
		
		public function userList()
		{
			$query = User::with('country:id,name','state:id,name','city:id,name','roles:id,name')->first();
			
			return view('user.index');
		}
		
		public function userListAjax(Request $request)
		{
			$draw = $request->get('draw');
			$start = $request->get("start");
			$limit = $request->get("length"); // Rows display per page
			
			$columnIndex_arr = $request->get('order');
			$columnName_arr = $request->get('columns');
			$order_arr = $request->get('order');
			$search_arr = $request->get('search');
			
			$columnIndex = $columnIndex_arr[0]['column']; // Column index
			$order = $columnName_arr[$columnIndex]['data']; // Column name
			$dir = $order_arr[0]['dir']; // asc or desc
			if($order == "action")
			{
				$order = 'id';
			}
			
			$id = Auth::user()->id;
			
			$query = User::with('country:id,name','state:id,name','city:id,name','roles:id,name')->where('id','!=',$id)->where('id','!=',1);
			
			if ($search = $request->input('search')) 
			{
				$query->where(function ($q) use ($search) 
				{
					$q->where('name', 'LIKE', "%{$search}%")->orWhere('created_at', 'LIKE', "%{$search}%");
				});
			}
			
			$totalData = $query->count();
			
			$values = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
			
			$totalFiltered = $totalData;
			
			$data = array();
			if(!empty($values))
			{
				$i = $start+1;
				$j = 1;
				foreach ($values as $value)
				{    
					$roleNames = $value->roles->pluck('name')->implode(', ');
					
					$mainData['id'] = $i;  
					$mainData['name'] = $value->full_name;
					$mainData['email'] = $value->email;
					$mainData['contact_number'] = $value->contact_number;
					$mainData['country_id'] = $value->country->name ?? 'N/A';
					$mainData['state_id'] = $value->state->name ?? 'N/A';
					$mainData['city_id'] = $value->city->name ?? 'N/A';
					$mainData['roles'] = $roleNames;
					
					$mainData['action'] = '<a href="'.url('user-edit',$value->id).'" onclick="userEdit(this,event)"><button type="button" class="btn btn-light waves-effect">Edit</button></a> | <a href="'.url('user-delete',$value->id).'" onclick="userDelete(this,event)"><button type="button" class="btn btn-light waves-effect">Delete</button></a>';  
					$data[] = $mainData;
					($j == 3)?$j = 1:$j++;
					$i++;
				}
			}
			
			$response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalData,
            "iTotalDisplayRecords" => $totalFiltered,
            "aaData" => $data
			); 
			
			echo json_encode($response);
			exit;
		}
		
		public function userEdit($id)
		{
			$get_user = User::with('roles:id,name')->whereId($id)->first();  
			$get_roles = Role::whereStatus(1)->where('name','!=','Admin')->get();  
			$view = view('user.edit',compact('get_user','get_roles'))->render();
			
			return response()->json(['status'=>'success','view'=>$view]);
		}
		
		public function userUpdate(Request $request, $id)
		{
			$validation = Validator::make($request->all(), [
			'first_name' => 'required|alpha_num',
			'roles' => 'required|array',
			'roles.*' => 'numeric|exists:roles,id'
			]);
			
			if ($validation->fails()) {
				return response()->json(['status' => 'validation', 'errors' => $validation->errors()]);
			}
			
			try {
				DB::beginTransaction();
				
				$user = User::find($id);
				
				if ($user) {
					$user->first_name = $request->input('first_name');
					$user->last_name = $request->input('last_name');
					$user->updated_at = now();
					$user->save();
					
					$user->roles()->sync($request->input('roles')); // Sync the roles with the user
					
					DB::commit();
					return response()->json(['status' => 'success', 'msg' => 'The User has been successfully updated.']);
					} else {
					return response()->json(['status' => 'error', 'msg' => 'User Data not found.']);
				}
				} catch (\Throwable $e) {
				DB::rollBack();
				$message = $e->getMessage();
				return response()->json(['status' => 'error', 'msg' => $message]);
			}
		}
		
		
		public function userDelete(Request $request, $id)
		{
			try {
				DB::beginTransaction(); 
				
				$user = User::find($id);
				
				if($user) 
				{
					$user->roles()->detach(); 
					
					$user->delete();
					
					DB::commit();
					return response()->json(['status' => 'success', 'msg' => 'The User has been successfully deleted.']);
				}
				else 
				{
					return response()->json(['status' => 'error', 'msg' => 'User not found.']);
				}
			} 
			catch (\Throwable $e)
			{
				DB::rollBack();
				$message = $e->getMessage();
				return response()->json(['status' => 'error', 'msg' => $message]);
			}
		}
		
		public function exportUsers($format)
		{
			if ($format === 'csv') 
			{
				return Excel::download(new UserRoleExport, 'users.csv');
			} 
			elseif ($format === 'excel') 
			{
				return Excel::download(new UserRoleExport, 'users.xlsx');
			} 
			elseif ($format === 'pdf')
			{
				$users = User::with('country:id,name', 'state:id,name', 'city:id,name', 'roles:id,name')->where('id', '!=', 1)->get();
				
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
				
				$pdf = PDF::loadView('pdf.user', compact('data'));
				return $pdf->download('users.pdf');
			}
			
			return response()->json(['status' => 'error', 'msg' => 'Invalid format.']);
		}
	}
