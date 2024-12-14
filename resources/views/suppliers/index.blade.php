@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
					<span>{{ __('Suppliers List') }}</span>
					<button type="button" class="btn btn-light waves-effect" onclick="addSupplier()">
						Add Supplier
					</button>
				</div>
				<div class="card-body">
					<table id="supplier_list" class="display" style="width:100%">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>Address</th>
								<th>Create Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<!-- Data will be dynamically populated here -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('js')
<script>
	var DataTable = $('#supplier_list').DataTable({
		processing:true,
		"language": {
			'loadingRecords': '&nbsp;',
			'processing': 'Loading...'
		},
		serverSide:true,
		bLengthChange: true,
		searching: true,
		bFilter: true,
		bInfo: true,
		iDisplayLength: 25,
		order: [[0, 'desc'] ],
		bAutoWidth: false,			 
		"ajax":{
			"url": "{{ url('get-supplier-list-ajax') }}",
			"dataType": "json",
			"type": "POST",
			"data": function (d) {
				d._token   = "{{csrf_token()}}";
				d.search   = $('input[type="search"]').val(); 
				d.filter_name   = $('#filter_name').val(); 
			}
		},
		"columns": [
		{ "data": "id" },
		{ "data": "name" },
		{ "data": "email" },
		{ "data": "address" },
		{ "data": "created_at" },
		{ "data": "action" }
		]
	});
	
	function addSupplier()
	{
		if (!modalOpen) 
		{
			modalOpen = true;
			closemodal();
			$.get("{{ url('supplier-add')}}", function(res) {
				$('body').find('#modal-view-render').html(res.view);
				$('#add_supplier_modal').modal('show');
			});
		}
	}
	
	
	function supplierEdit(obj,event)
	{  
		event.preventDefault(); 
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal();
			$.get(obj, function(res)
			{
				$('body').find('#modal-view-render').html(res.view); 
				$('#edit_supplier').modal('show');  
			}); 
		}
	}
	
	function supplierDelete(obj,event)
	{	 
		event.preventDefault();
		Swal.fire({
			title:"Are you sure?",
			text:"You won't be able to revert this!",
			type:"warning",
			showCancelButton:!0,
			confirmButtonColor:"#3085d6",
			cancelButtonColor:"#d33",
			confirmButtonText:"Yes, delete it!"
		}).then(function(t)
		{
			t.value&&
			
			$.post(obj,{_token:"{{csrf_token()}}"},function(res)
			{ 
				if(res.status == "error")
				{
					Swal.fire("Error!",res.msg,"error")
				}
				else
				{ 
					Swal.fire("Deleted!",res.msg,"success")
					DataTable.draw();
				}
			});
		}) 
	}
</script>
@endpush
