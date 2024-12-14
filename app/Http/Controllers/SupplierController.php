<?php
	
	namespace App\Http\Controllers;
	
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Validation\Rule;
	use App\Models\Supplier;
	use DB, Validator;
	
	class SupplierController extends Controller
	{
		public function __construct()
		{
			/* $this->middleware('permission:create supplier', ['only' => ['create', 'store']]);
			$this->middleware('permission:view supplier', ['only' => ['index', 'show']]);
			$this->middleware('permission:update supplier', ['only' => ['edit', 'update']]);
			$this->middleware('permission:delete supplier', ['only' => ['destroy']]); */
		}
		
		public function supplierList()
		{
			  $this->authorize('view_supplier');
			return view('suppliers.index');
		}
		
		public function supplierListAjax(Request $request)
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
			
			$query = Supplier::where('id','!=','');
			
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
					
					$mainData['action'] = '<a href="'.url('supplier-edit',$value->id).'" onclick="supplierEdit(this,event)"><button type="button" class="btn btn-light waves-effect">Edit</button></a> | <a href="'.url('supplier-delete',$value->id).'" onclick="supplierDelete(this,event)"><button type="button" class="btn btn-light waves-effect">Delete</button></a>';  
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
		
		public function supplierAdd()
		{
			$this->authorize('create_supplier');
			$view = view('suppliers.add')->render();
			
			return response()->json(['status'=>'success','view'=>$view]);
		}
		
		public function supplierInsert(Request $request)
		{
			$validation = Validator::make($request->all(), [
			'name' => "required|string",
			'email' => 'required|string|email|unique:suppliers,email',
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
				
				Supplier::create($data);
				
				DB::commit();
				return response()->json(['status' => 'success', 'msg' => 'The Supplier has been create successfully.']);
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return response()->json(['status' => 'error', 'msg' => 'An error occurred: ' . $e->getMessage()]);
			}
			
		}
		
		public function supplierEdit($id)
		{
			 $this->authorize('edit_supplier');
			$get_supplier = Supplier::whereId($id)->first();  
			$view = view('suppliers.edit',compact('get_supplier'))->render();
			
			return response()->json(['status'=>'success','view'=>$view]);
		}
		
		public function supplierUpdate(Request $request, $id)
		{
			$validation = Validator::make($request->all(), [
			'name' => "required|string",
			'email' => "required|string|email|unique:suppliers,email,$id,id",
			'address' => 'required',
			]);
			
			if ($validation->fails()) {
				return response()->json(['status' => 'validation', 'errors' => $validation->errors()]);
			}
			
			try {
				DB::beginTransaction();
				
				$object = Supplier::find($id);
				
				if ($object) 
				{
					$object->name = $request->input('name'); 
					$object->email = $request->input('email');
					$object->address = $request->input('address');
					$object->updated_at = now();
					$object->save(); 
					
					DB::commit();
					return response()->json(['status' => 'success', 'msg' => 'The suppliers has been successfully updated.']);
				} 
				else 
				{
					return response()->json(['status' => 'error', 'msg' => 'suppliers Data not found.']);
				}
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				$message = $e->getMessage();
				return response()->json(['status' => 'error', 'msg' => $message]);
			}
		}
		
		public function supplierDelete(Request $request, $id)
		{
			 $this->authorize('delete_supplier');
			 
			try {
				DB::beginTransaction();
				
				$supplier = Supplier::find($id);
				
				if ($supplier) {
					
					$supplier->delete();
					
					DB::commit();
					return response()->json(['status' => 'success', 'msg' => 'The Supplier has been successfully deleted.']);
					} else {
					return response()->json(['status' => 'error', 'msg' => 'Supplier not found.']);
				}
				} catch (\Throwable $e) {
				DB::rollBack();
				$message = $e->getMessage();
				return response()->json(['status' => 'error', 'msg' => $message]);
			}
		}
		
	}
