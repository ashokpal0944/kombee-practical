<?php
	
	namespace App\Http\Controllers;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Validation\Rule;
	use App\Models\Role;
	use DB, Validator;
	
	class RoleController extends Controller
	{
		public function __construct()
		{
			$this->middleware('auth');
		}
		
		public function roleList()
		{
			return view('role.index');
		}
		
		public function roleListAjax(Request $request)
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
			
			$query = Role::where('id','!=','1');
			
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
					$mainData['id'] = $i;  
					$mainData['name'] = $value->name;
					$mainData['status'] = $value->status == 1 ? '<span style="color:green" class="badge badge-success badge-pill">Active</span>' : '<span style="color:red" class="badge badge-danger badge-pill">In-active</span>';
					$mainData['created_at'] = date('Y-m-d h:i A',strtotime($value->created_at)); 
					
					$mainData['action'] = '<a href="'.url('role-edit',$value->id).'" onclick="roleEdit(this,event)"><button type="button" class="btn btn-light waves-effect">Edit</button></a> | <a href="'.url('role-delete',$value->id).'" onclick="roleDelete(this,event)"><button type="button" class="btn btn-light waves-effect">Delete</button></a>';  
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
		
		public function roleAdd()
		{
			$view = view('role.add')->render();
			
			return response()->json(['status'=>'success','view'=>$view]);
		}
		
		public function roleInsert(Request $request)
		{
			$validation = Validator::make($request->all(), [
			'name' => "required|string|unique:roles,name",
			'status' => 'required|numeric',
			]);
			
			if ($validation->fails()) {
				return response()->json(['status' => 'validation', 'errors' => $validation->errors()]);
			}
			
			if ($validation->fails()) 
			{
				return response()->json(['status' => 'validation', 'errors' => $validation->errors()]);
			} 
			
			try {
				DB::beginTransaction();
				
				$currentTime = now();
				
				$data = $request->except('_token');
				$data['created_at'] = $currentTime;
				$data['updated_at'] = $currentTime;
				
				Role::create($data);
				
				DB::commit();
				return response()->json(['status' => 'success', 'msg' => 'The Role has been create successfully.']);
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return response()->json(['status' => 'error', 'msg' => 'An error occurred: ' . $e->getMessage()]);
			}
			
		}
		
		public function roleEdit($id)
		{
			$get_role = Role::whereId($id)->first();  
			$view = view('role.edit',compact('get_role'))->render();
			
			return response()->json(['status'=>'success','view'=>$view]);
		}
		
		public function roleUpdate(Request $request, $id)
		{
			$validation = Validator::make($request->all(), [
			'name' => "required|string|unique:roles,name,$id,id",
			'status' => 'required|numeric',
			]);
			
			if ($validation->fails()) {
				return response()->json(['status' => 'validation', 'errors' => $validation->errors()]);
			}
			
			try {
				DB::beginTransaction();
				
				$object = Role::find($id);
				
				if ($object) 
				{
					$object->name = $request->input('name'); 
					$object->status = $request->input('status');
					$object->updated_at = now();
					$object->save(); 
					
					DB::commit();
					return response()->json(['status' => 'success', 'msg' => 'The Role has been successfully updated.']);
				} 
				else 
				{
					return response()->json(['status' => 'error', 'msg' => 'Role Data not found.']);
				}
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				$message = $e->getMessage();
				return response()->json(['status' => 'error', 'msg' => $message]);
			}
		}
		
		public function roleDelete(Request $request, $id)
		{
			try {
				DB::beginTransaction();

				$role = Role::find($id);
				
				if ($role) {
					
					$role->users()->detach();
					$role->delete();
					
					DB::commit();
					return response()->json(['status' => 'success', 'msg' => 'The Role has been successfully deleted.']);
					} else {
					return response()->json(['status' => 'error', 'msg' => 'Role not found.']);
				}
				} catch (\Throwable $e) {
				DB::rollBack();
				$message = $e->getMessage();
				return response()->json(['status' => 'error', 'msg' => $message]);
			}
		}
		
		
	}
