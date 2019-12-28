<script type="text/javascript">
$(document).ready(function(){
	$("#name").blur(function(){
		var val = $(this).val();		
		val =  val.toLowerCase();
		val =  val.replace(/ /g,"-");
		val =  val.replace(/[^A-Za-z0-9^_\-]/g, "");
		
		$('#slug').val(val);
		
	});
	
	$('#price_sqft_area_div ins').on('click',function(){
		var value = $(this).siblings('input');
		if (value.is(':checked')) {
			$('.label_price span').removeClass('hide');
			$('.extra_price').removeClass('hide');
			$('.extra_price').slideDown();
		} else {
			$('.label_price span').addClass('hide');
			$('.extra_price').slideUp();
		}
	});

	$('.width_height_div ins').on('click',function(){
		var value = $(this).siblings('input');
		if (value.is(':checked')) {
			$('.min_width_height').removeClass('hide');
			$('.min_width_height').slideDown();
			$('.max_width_height').removeClass('hide');
			$('.max_width_height').slideDown();
		} else {
			$('.min_width_height').slideUp();
			$('.max_width_height').slideUp();
		}
	});
	
	$('.editors').each(function(){
		init_ckeditor($(this).attr('id'));
	});

	function init_ckeditor(element){
		CKEDITOR.replace( element, {
			filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
			height: '300',
		});
	}
	$(function() {
		// Multiple images preview in browser
		 var imagesPreview = function(input,main_div, remove_div, placeToInsertImagePreview,input_name) {

			if (input.files) {
				var filesAmount = input.files.length;
				for (i = 0; i < filesAmount; i++) {
					var reader = new FileReader();
					var tarr = input.files[i].name.split('/');
					var file = tarr[tarr.length-1];
					var data = file.split('.');
					var data = data[data.length-1];
					//alert(data);
					var noError= true;
					if ($.inArray(data, ['jpg', 'jpeg','png','gif']) == -1) {
						noError = false;
						$('#'+main_div).addClass('has-error');
						$('#'+main_div+' span').html('Please Select Only(gif, png, jpg, jpeg)');
						$("#"+input_name).val(null);
						$('div#'+remove_div).addClass('hide');
						return false;
					}
					
					if(noError){
						reader.onload = function(event) {
							$('#'+main_div+' span').html('');
							var clone = '<div class="col-sm-9 image_main_box"><label><img class="img-responsive" src="'+event.target.result+'" alt="Photo"></label></div>';
							//alert(clone);
							$(placeToInsertImagePreview).append(clone);
							$('div#'+remove_div).removeClass('hide');
						}
						reader.readAsDataURL(input.files[i]);
					}
				}
			}

		};
		
		$('#cat_image').on('change', function() {
			$('div#cat_images_div').html('');
			imagesPreview(this,'cat_page_image_div','cat_image_div', 'div#cat_images_div','cat_image');
		});
		
		$('#image').on('change', function() {
			$('div#product_images_div').html('');
			imagesPreview(this,'image_main_div','image_div', 'div#product_images_div','cat_image');
		});
		
		$('#product_image').on('change', function() {
			$('div#images').html('');
			imagesPreview(this,'product_image_div','images_div', 'div#images','product_image');
		});
		
	});

	$(document).on('click', ".remove-delete", function (event) {
		if(confirm('Are you sure to delete it.')){
			var ids = $(this).attr('data-id');
			var product_id = '{{$product->id}}';
			var _this = $(this);			
			  $.ajax({
			   url: "<?php echo url('admin/products/deleteData/'); ?>",
				type: "post",
				data: {id: ids,table:'product_options',product_id:product_id},
				dataType:'json',
				success: function (data) {
					//alert(data);
					if(data.flag == 1){
						_this.closest('tr').find('td').fadeOut(1000,function(){ 
							_this.parents('tr:first').remove();   
							$('select#custom_option_id').html(data.html);
							var count = $('.option_show table tbody tr').length;
							//alert(count);
							if(count == 0){
								$('.option_show').addClass('hide');
							}
						});
					}
				}
			});   
		}
	});

	$(document).on('click', ".add-option", function (event) {
		//alert($('#custom_option_id :selected').val());
		var on = true;
		var id = $('#custom_option_id :selected').val();
		var product_id = '{{$product->id}}';
		//alert(product_id);
		if(id == ''){
			alert("Please Select One option");
			on = false;
		}
		
		if(on){
			$.ajax({
			   url: "<?php echo url('admin/products/addOption/'); ?>",
				type: "post",
				data: {id: id,product_id:product_id},
				dataType:'json',
				success: function (data) {
					//alert(data.html);
					if(data.flag == 1){
						$('select#custom_option_id').html(data.html);
						$('div.option_show table tbody').append(data.tr_html);
					}
				}
			});
		}
	});

	$('#meta_tag').tokenfield({
	  autocomplete: {
		source: [],
		delay: 100
	  },
	  showAutocompleteOnFocus: true,
	  createTokensOnBlur: true,
	});
	
	var tab_count = Number('{{$k}}');
	
	$('.add_tab').click(function(){
		var str = '<li class="new_li_'+tab_count+'"><a class="pull-left" href="#new_tab'+tab_count+'" data-toggle="tab">New Tab '+tab_count+'</a><i class="fa fa-window-close delete_tab pull-left" aria-hidden="true" data="new" count="'+tab_count+'"></i></li>';
		
		var str1 = '<div class="tab-pane" id="new_tab'+tab_count+'">';
		str1 += '<label for="custom['+tab_count+'][title]">Title</label><input class="form-control" placeholder="Enter Title" name="custom['+tab_count+'][title]" type="text" value="" id="custom['+tab_count+'][title]"/>';
		str1 += '<label for="custom['+tab_count+'][body]">Body</label><textarea class="form-control editors" name="custom['+tab_count+'][body]" id="custom_tab_'+tab_count+'"></textarea>';
		str1 += '</div>';
		
		$(str).insertBefore($(this));
		$(str1).insertAfter('#design_template_div');
		
		CKEDITOR.replace( 'custom_tab_'+tab_count, {
			filebrowserBrowseUrl: '<?php echo config('constants.SITE_URL');?>public/js/admin/ckeditor/plugins/imageuploader/imgbrowser.php?type=Files',
			height: '300',
		});
		
		$('.nav-tabs a[href="#new_tab'+tab_count+'"]').tab('show');
		
		tab_count += 1;
	});
	
	$(document).on('click','.delete_tab',function(){
		if($(this).attr('data') == 'extra'){
			var id = $(this).attr('data-id');
			if(confirm('Are you sure for delete this tab.')){
				$.ajax({
					url:'{{url("admin/products/deleteTab")}}',
					type:'post',
					dataType:'json',
					data:{'id':id},
					success:function(data){
						if(data.status == 'success'){
							$('li.extra_li_'+id).remove();
							$('#extra_tab_'+id).remove();							
							$('.nav-tabs a[href="#product_detail_div"]').tab('show');
						}
					}
				});
			}
		}else{
			var count = $(this).attr('count');
			if(confirm('Are you sure for delete this tab.')){
				//alert(count);
				$('li.new_li_'+count).remove();
				$('#new_tab'+count).remove();							
				$('.nav-tabs a[href="#product_detail_div"]').tab('show');
			}
		}
	});
	
	
	var variant_count = Number('{{$count}}');
	
	$(document).on('ifChecked','[name="variant"]', function(event){
		$('div.variant_option_panel').html('');
		var str = '<div class="row varient_option">';
		
		str += '<div class="col-xs-4 col-sm-3 value"><div class="form-group"><label for="">Option Name</label><input placeholder="Option Name" name="options['+variant_count+'][name]" class="form-control" required/></div></div>';
		
		str += '<div class="col-xs-6 col-sm-5 value"><div class="form-group"><label for="">Option Values</label><input name="options['+variant_count+'][value]" class="form-control variant_option_values option_value_'+variant_count+'" placeholder="Separate options with a comma" required/></div></div>';
		
		str += '<div class="col-xs-2 col-sm-4 hide remove_div"><div class="form-group"> <label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-varient-option" type="button"><i class="fa fa-trash"></i></button></div></div><div class="clearfix"></div></div>';
		
		$('div.variant_option_panel').append(str);
		$('.variant_div').removeClass('hide');
		$('.variant_div').slideDown();
		$('.variant_option_values').tokenfield({
		  autocomplete: {
			source: [],
			delay: 100
		  },
		  showAutocompleteOnFocus: true,
		  createTokensOnBlur: true,
		});
	});
	
	$(document).on('ifUnchecked','[name="variant"]', function(event){
		$('div.variant_option_panel').html('');
		$('.variant_price_panel').html('');
		$('.variant_div').slideUp();
		$('.variant_price_div').slideUp();
		variant_count = 1;
	});
	
	$(document).on('click','.add-variant-option', function () {
		variant_count++;
		var str = '<div class="row varient_option">';
		str += '<div class="col-xs-4 col-sm-3 value"><div class="form-group"><label for="">Option Name</label><input placeholder="Option Name" name="options['+variant_count+'][name]" class="form-control" required/></div></div>';
		
		str += '<div class="col-xs-6 col-sm-5 value"><div class="form-group"><label for="">Option Values</label><input name="options['+variant_count+'][value]" class="form-control variant_option_values option_value_'+variant_count+'" placeholder="Separate options with a comma" required/></div></div>';
		
		str += '<div class="col-xs-2 col-sm-4 remove_div"><div class="form-group"> <label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-varient-option" type="button"><i class="fa fa-trash"></i></button></div></div><div class="clearfix"></div></div>';
			
		$('.variant_option_panel').append(str);
		$('.remove_div').removeClass('hide');
		
		$('.variant_option_values').tokenfield({
		  autocomplete: {
			source: [],
			delay: 100
		  },
		  showAutocompleteOnFocus: true,
		  createTokensOnBlur: true,
		});
		
		if(variant_count == 2){
			$('.add_more_varient').addClass('hide');
		}
	});
	
	
	$(document).on('click', '.remove-varient-option', function () {
		alert(variant_count);
		if(confirm('Are You sure to delete it ?')){
			$(this).closest('.varient_option').remove();
			variant_count--;
			$('.add_more_varient').removeClass('hide');
			if(variant_count < 1){
				$('[name="variant"]').iCheck('uncheck');
				variant_count = 1;
			}else{
				add_option_price();	
			}
		}
	});
	
	setTimeout(function() {
		$(document).on('tokenfield:createtoken','.variant_option_values', function(e) {
			//var value = e.attrs.value;
			setTimeout(function() {
				add_option_price();
			 }, 10);
		});
	 }, 15);
	$(document).on('tokenfield:removedtoken','.variant_option_values', function (event) {
		add_option_price();
	});
	
	
	
	function add_option_price(){
		
		$('.variant_price_panel').html('');
		var price = 0;
		if($('#price').val() != ''){
			price = $('#price').val();
		}
		var str = '<div class="col-xs-12"><div class="box box-primary"><div class="box-body table-responsive"><table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;"><thead><tr><th>Varient</th>';
		
		if($('#price_sqft_area').is(':checked')){
			str += '<th>Price (Area 0-300 sqft)</th><th>Price (Area 301-500 sqft)</th><th>Price (Area 501-1000 sqft)</th><th>Price (Area above 1001+ sqft)</th></tr></thead><tbody>';
		}else{
			str += '<th>Price</th></tr></thead><tbody>';
		}
		
		var existingTokens = $('input.option_value_1').tokenfield('getTokens');
		$.each(existingTokens, function(index, token) {
				
			if($('input[name="options[2][value]"]').length && $('input[name="options[2][value]"]').val().length){
				var existingTokens1 = $('input.option_value_2').tokenfield('getTokens');
				$.each(existingTokens1, function(index1, token1) {
					str += '<tr>';
					str += '<td><span class="text-success">'+token.value+'</span>.<span clas="text-denger">'+token1.value+'</span></td>';
					str += '<td><div class="form-group"><input type="text" name="variant_option_price['+token.value+']['+token1.value+'][price]" value="'+price+'" class="form-control" placeholder="Enter Price" required /></div></td>';
					if($('#price_sqft_area').is(':checked')){
						var price_300 = 0;
						if($('#price_300').val() != ''){
							price_300 = $('#price_300').val();
						}
						var price_500 = 0;
						if($('#price_500').val() != ''){
							price_500 = $('#price_500').val();
						}
						var price_1000 = 0;
						if($('#price_1000').val() != ''){
							price_1000 = $('#price_1000').val();
						}
						
						
						str += '<td><div class="form-group"><input type="text" name="variant_option_price['+token.value+']['+token1.value+'][price_300]" value="'+price_300+'" class="form-control" placeholder="Enter Price" required /></div></td>';
						str += '<td><div class="form-group"><input type="text" name="variant_option_price['+token.value+']['+token1.value+'][price_500]" value="'+price_500+'" class="form-control" placeholder="Enter Price" required /></div></td>';
						str += '<td><div class="form-group"><input type="text" name="variant_option_price['+token.value+']['+token1.value+'][price_1000]" value="'+price_1000+'" class="form-control" placeholder="Enter Price" required /></div></td>';
					}
					str += '</tr>'; 
				});
			}else{
			
				str += '<tr>';
				str += '<td><span class="text-success">'+token.value+'</span></td>';
				str += '<td><div class="form-group"><input type="text" name="variant_option_price['+token.value+'][price]" value="'+price+'" class="form-control" placeholder="Enter Price" required /></div></td>';
				if($('#price_sqft_area').is(':checked')){
					var price_300 = 0;
					if($('#price_300').val() != ''){
						price_300 = $('#price_300').val();
					}
					var price_500 = 0;
					if($('#price_500').val() != ''){
						price_500 = $('#price_500').val();
					}
					var price_1000 = 0;
					if($('#price_1000').val() != ''){
						price_1000 = $('#price_1000').val();
					}
					
					
					str += '<td><div class="form-group"><input type="text" name="variant_option_price['+token.value+'][price_300]" value="'+price_300+'" class="form-control" placeholder="Enter Price" required /></div></td>';
					str += '<td><div class="form-group"><input type="text" name="variant_option_price['+token.value+'][price_500]" value="'+price_500+'" class="form-control" placeholder="Enter Price" required /></div></td>';
					str += '<td><div class="form-group"><input type="text" name="variant_option_price['+token.value+'][price_1000]" value="'+price_1000+'" class="form-control" placeholder="Enter Price" required /></div></td>';
				}
				str += '</tr>'; 
			}
		});
		
		str += '</tbody></table></div></div></div>';
		$('.variant_price_panel').append(str);
		$('.variant_price_div').removeClass('hide');
		$('.variant_price_div').slideDown();
	}
	
	$('.variant_option_values').tokenfield({
	  autocomplete: {
		source: [],
		delay: 100
	  },
	  showAutocompleteOnFocus: true,
	  createTokensOnBlur: true,
	});
});

function delete_image(id,name){
	//alert(id+" and "+name);
	$.ajax({
		url:'<?php echo url('admin/products/delete_image/'); ?>',
		type:'post',
		data:'id='+id,
		success:function(data){
			if(data == 1){
				$('#image_div_'+id).remove();
			}
		}
	});
}
</script>