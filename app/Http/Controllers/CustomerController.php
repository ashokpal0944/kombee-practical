<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Validation\Rule;
	use App\Models\Customer;
	use DB, Validator;
	
	class CustomerController extends Controller
	{
		public function __construct()
		{
			/* $this->middleware('permission:create customer', ['only' => ['create', 'store']]);
			$this->middleware('permission:view customer', ['only' => ['index', 'show']]);
			$this->middleware('permission:update customer', ['only' => ['edit', 'update']]);
			$this->middleware('permission:delete customer', ['only' => ['destroy']]); */
		}
		
		public function customersList()
		{
			$this->authorize('view_customer');
			
			return view('customer.index');
		}
		
		public function customersListAjax(Request $request)
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
			
			$query = Customer::where('id','!=','');
			
			if ($search = $request->input('search')) 
			{
				$query->where(function ($q) use ($search) 
				{
					$q->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%")->orWhere('address', 'LIKE', "%{$search}%")->orWhere('created_at', 'LIKE', "%{$search}%");
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
					$mainData['email'] = $value->email;
					$mainData['address'] = $value->address;
					$mainData['created_at'] = date('Y-m-d h:i A',strtotime($value->created_at)); 
					
					$mainData['action'] = '<a href="'.url('customers-edit',$value->id).'" onclick="customerEdit(this,event)"><button type="button" class="btn btn-light waves-effect">Edit</button></a> | <a href="'.url('customers-delete',$value->id).'" onclick="customerDelete(this,event)"><button type="button" class="btn btn-light waves-effect">Delete</button></a>';  
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
		
		public function customersAdd()
		{
			$this->authorize('create_customer');
			
			$view = view('customer.add')->render();
			
			return response()->json(['status'=>'success','view'=>$view]);
		}
		
		public function customersInsert(Request $request)
		{
			$validation = Validator::make($request->all(), [
			'name' => "required|string",
			'email' => 'required|string|email|unique:customers,email',
			'address' => 'required',
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
				
				Customer::create($data);
				
				DB::commit();
				return response()->json(['status' => 'success', 'msg' => 'The Customer has been create successfully.']);
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return response()->json(['status' => 'error', 'msg' => 'An error occurred: ' . $e->getMessage()]);
			}
			
		}
		
		public function customersEdit($id)
		{
			 $this->authorize('edit_customer');
			 
			$get_customer = Customer::whereId($id)->first();  
			$view = view('customer.edit',compact('get_customer'))->render();
			
			return response()->json(['status'=>'success','view'=>$view]);
		}
		
		public function customersUpdate(Request $request, $id)
		{
			$validation = Validator::make($request->all(), [
			'name' => "required|string",
			'email' => "required|string|email|unique:customers,email,$id,id",
			'address' => 'required',
			]);
			
			if ($validation->fails()) {
				return response()->json(['status' => 'validation', 'errors' => $validation->errors()]);
			}
			
			try {
				DB::beginTransaction();
				
				$object = Customer::find($id);
				
				if ($object) 
				{
					$object->name = $request->input('name'); 
					$object->email = $request->input('email');
					$object->address = $request->input('address');
					$object->updated_at = now();
					$object->save(); 
					
					DB::commit();
					return response()->json(['status' => 'success', 'msg' => 'The Customer has been successfully updated.']);
				} 
				else 
				{
					return response()->json(['status' => 'error', 'msg' => 'Customer Data not found.']);
				}
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				$message = $e->getMessage();
				return response()->json(['status' => 'error', 'msg' => $message]);
			}
		}
		
		public function customersDelete(Request $request, $id)
		{
			$this->authorize('edit_customer');
			
			try {
				DB::beginTransaction();
				
				$customer = Customer::find($id);
				
				if ($customer) {
					
					$customer->delete();
					
					DB::commit();
					return response()->json(['status' => 'success', 'msg' => 'The Customer has been successfully deleted.']);
					} else {
					return response()->json(['status' => 'error', 'msg' => 'Customer not found.']);
				}
				} catch (\Throwable $e) {
				DB::rollBack();
				$message = $e->getMessage();
				return response()->json(['status' => 'error', 'msg' => $message]);
			}
		}
		
	}
