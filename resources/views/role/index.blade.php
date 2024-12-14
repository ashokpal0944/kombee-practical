@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
					<span>{{ __('Role List') }}</span>
					<button type="button" class="btn btn-light waves-effect" onclick="addRole()">
						Add Role
					</button>
				</div>
				<div class="card-body">
					<table id="role_list" class="display" style="width:100%">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Status</th>
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
var DataTable = $('#role_list').DataTable({
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
		"url": "{{ url('get-role-list-ajax') }}",
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
	{ "data": "status" },
	{ "data": "created_at" },
	{ "data": "action" }
	]
});

function addRole()
	{
		if (!modalOpen) 
		{
			modalOpen = true;
			closemodal();
			$.get("{{ url('role-add')}}", function(res) {
				$('body').find('#modal-view-render').html(res.view);
				$('#add_role_modal').modal('show');
			});
		}
	}
	
		
function roleEdit(obj,event)
{  
	event.preventDefault(); 
	if (!modalOpen)
	{
		modalOpen = true;
		closemodal();
		$.get(obj, function(res)
		{
			$('body').find('#modal-view-render').html(res.view); 
			$('#edit_role').modal('show');  
		}); 
	}
}

function roleDelete(obj,event)
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
		