

<!-- Modal -->
<div class="modal fade" id="edit_supplier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Edit Supplier</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="editsupplierModal"  action="{{ url('supplier-edit',$get_supplier->id) }}" method="post">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="name" class="col-form-label">Name</label>
						<input type="text" class="form-control" name="name" id="name" value="{{ $get_supplier->name }}">
					</div>
					<div class="mb-3">
						<label for="email" class="col-form-label">Email</label>
						<input type="email" class="form-control" name="email" id="email" value="{{ $get_supplier->email }}">
					</div>
					
					<div class="mb-3">
						<label for="message-text" class="col-form-label">Address</label>
						<input type="text" class="form-control" name="address" id="address" value="{{ $get_supplier->address }}">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$('#editsupplierModal').submit(function(event) {
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
			$('#editsupplierModal').find('button').prop('disabled', false);
			$('#editsupplierModal').find('button.spin-button').removeClass('loading').html('Save');
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
				$('#edit_supplier').modal('hide');
				$('#edit_supplier').remove();
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
