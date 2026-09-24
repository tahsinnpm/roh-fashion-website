@foreach ($attributes as $attribute)
<div class="attribute-choice-item mb-3 p-3 border rounded bg-white shadow-sm" id="attribute_choice_{{$attribute->id}}">
	<div class="d-flex justify-content-between align-items-center mb-2">
		<label class="col-form-label mb-0" style="font-weight: bold; font-size: 15px;">
			<i class="fa fa-sliders me-1 text-primary"></i> {{$attribute->name}} :
		</label>
		<button type="button" class="btn btn-sm btn-outline-danger remove-attribute-btn" data-id="{{$attribute->id}}" title="Remove {{$attribute->name}}">
			<i class="fa fa-times me-1"></i> Remove
		</button>
	</div>
	<div class="custom_select cit-multi-select">
		<select class="form-control form-select js-example-basic-multiple" name="choice_options{{$attribute->id}}[]" id="choice_options{{$attribute->id}}" onchange="makeCombinationTable(this)" multiple data-placeholder="Select {{$attribute->name}} values">
			@foreach($attribute->attribute_values as $value)
				<option value="{{ $value->value }}">{{ $value->value }}</option>
			@endforeach
		</select>
	</div>
</div>
@endforeach

<script>
	$(document).ready(function() {
    	$('.js-example-basic-multiple').select2();
	});
</script>