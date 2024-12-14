

<!-- Modal -->
<div class="modal fade" id="edit_user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserModal" action="{{ url('user-edit', $get_user->id) }}" method="post">
                @csrf
                <div class="modal-body">
                    <!-- First Name -->
                    <div class="mb-3">
                        <label for="first_name" class="col-form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $get_user->first_name }}">
                    </div>

                    <!-- Last Name -->
                    <div class="mb-3">
                        <label for="last_name" class="col-form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $get_user->last_name }}">
                    </div>

                    <!-- Role Dropdown -->
                    <div class="mb-3">
                        <label for="roles" class="col-form-label">Role</label>
                        <select class="form-select" id="roles" name="roles[]" multiple style="width: 100%;">
                            <option value="">Select Roles</option>
                            @foreach($get_roles as $get_role)
                                <option value="{{ $get_role->id }}" 
                                    @if(in_array($get_role->id, $get_user->roles->pluck('id')->toArray())) 
                                        selected 
                                    @endif
                                >{{ $get_role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
	$('#editUserModal').submit(function(event) {
		event.preventDefault();
		
		$(this).find('button').prop('disabled', true);
		$(this).find('button.spin-button').addClass('loading').html('<span class="spinner"></span>');
		
		var formData = new FormData(this);
		formData.append('_token', "{{csrf_token()}}");
		
		$.ajax({
			async: true,
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: formData,
		cache: false,
		processData: false,
		contentType: false,
		dataType: 'Json',
		success: function(res) {
			$('#editUserModal').find('button').prop('disabled', false);
			$('#editUserModal').find('button.spin-button').removeClass('loading').html('Save');
			if (res.status == "error") {
				toastrMsg(res.status, res.msg);
				} else if (res.status == "validation") {
				$('.error').remove();
				$.each(res.errors, function(key, value) {
					var inputField = $('#' + key);
					var errorSpan = $('<span>')
					.addClass('error text-danger')
					.attr('id', key + 'Error')
					.text(value[0]);
					inputField.parent().append(errorSpan);
				});
				} else {
				toastrMsg(res.status, res.msg);
				$('#edit_user').modal('hide');
				$('#edit_user').remove();
				$('.modal-backdrop').remove();
				$('body').css({
					'overflow': 'auto'
				});
				DataTable.draw();
			}
		}
		});
	});
</script>
