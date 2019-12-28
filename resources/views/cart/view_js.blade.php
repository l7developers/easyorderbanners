<script type="text/javascript">
//alert('${{\session()->get("carttotal.gross")}}')
$(document).ready(function() {
	$(document).on('click','.project_comment_btn',function(){
		var key = $(this).attr('data-id');
		var type = $(this).attr('data-type');
		var data = $(this).attr('data');
		var str = '<form name="project_comment_form" id="project_comment_form">';
		
		str += '<input type="hidden" name="key" value="'+key+'"/>';
		str += '<input type="hidden" name="field" value="'+type+'"/>';
		
		if(type == 'project_name'){
			$('#project_comment .modal-header h4').html('Product Project Name');
			str += '<input type="hidden" name="key_id" value="project_'+key+'"/>';
			str += '<div class="col-xs-6"><div class="form-group"><label>Enter Project Name</label><input type="text" class="form-control" name="field_value" id="field_value" value="'+data+'" placeholder="Enter Project Name"/></div></div>';
		}else{
			var label = 'Enter Comment';
			$('#project_comment .modal-header h4').html('Product Comment');
			str += '<input type="hidden" name="key_id" value="comment_'+key+'"/>';
			str += '<div class="col-xs-12"><div class="form-group"><label>Enter Comment</label><textarea class="form-control" name="field_value" id="field_value" placeholder="Enter Comment">'+data+'</textarea></div></div>';
		}
		
		str += '<div class="col-xs-12"><div class="form-group">{{ Form::button("Set",["class"=>"btn btn-success project_comment_submit"])}}';
		
		str += '<img id="project_comment_loader" class="loader_img" src="'+"{{url('public/img/loader/Spinner.gif')}}"+'" style="width:50px;display:none;" /></div></div></form>';
		
		$('#project_comment .modal-body .row').html(str);
		$('#project_comment').modal('show');
	});
	
	$(document).on('click','.project_comment_submit',function(){
		if($('#field_value').val() == ''){
			$('#field_value').closest('div').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter($('#field_value'));
		}else{
			$('#field_value').closest('div').removeClass('has-error');
			$('#field_value').nextAll().remove();
			
			$.ajax({
				url:'{{url("savecomment")}}',
				type:'post',
				dataType:'json',
				data:$('form#project_comment_form').serialize(),
				beforeSend: function() {
					$('#project_comment_loader').fadeIn();
					$('.project_comment_submit').prop('disabled',true);
				},
				success:function(data){
					$('#project_comment_loader').fadeOut();
					$('.project_comment_submit').prop('disabled',false);
					if(data.status == 'success'){
						$('#'+data.key_id).attr('data',data.res);
						$('#project_comment').modal('hide');
					}
				}
			});
		}
	});
	
	$(document).on('submit',"#project_comment_form" ,function( event ) {
	  event.preventDefault();
	});
	
	$(document).on('click','.updateCart',function(){
        updateCart();
	});

	function updateCart() {
        var form = $('#cart_form');
        $.ajax({
            url:'{{url("cart/update")}}',
            type:'post',
            dataType:'json',
            data:form.serialize(),
            beforeSend: function() {
                $('#fade').fadeIn();
                $('.update_spin.fa-spinner').removeClass('hide');
            },
            success:function(data){
                $('#fade').fadeOut();
                $('.update_spin.fa-spinner').addClass('hide');
                if(data.status == 'success'){
                    $.each( data.res.qty,function (index, val) {
                        $('.total_amount_'+index).html('$'+formatMoney(val.total));
                        $('.cart_product_'+index).html('Qty : '+val.qty);
                        $('.cart_product_amount_'+index).html('$'+formatMoney(val.total));
                    });
                    $('.cart_total').html('$'+formatMoney(data.res.cart.sub_total));
                    $('.total').html('$'+formatMoney(data.res.cart.total));
                }
            }
        });
    }

	$(document).on('click','.delete_btns',function(){
		if(confirm("Are you sure to delete this product from cart.")){
			var _this = $(this);
			var key = _this.attr('data');
			
			$.ajax({
				url:'{{url("cart/delete")}}',
				type:'post',
				data:{'key':key},
				dataType:'json',
				beforeSend: function() {
					$('#fade').fadeIn();  
					$('.delete_spin.spinner_'+key).removeClass('hide');
				},
				success:function(data){
					$('#fade').fadeOut();  
					$('.delete_spin.spinner_'+key).addClass('hide');
					if(data.status == 'success'){
						if(data.session_total <= 0){
							window.location.reload();
						}else{
							$('.cart_menu').html(data.res);
							$(_this).closest('div.yourcartlist').remove();
							$('.cart_total').html('$'+data.session_total);
							$('.total').html('$'+data.session_total);
							if(data.session_count > 0){
								$('#cart_count').html(data.session_count).removeClass('hide');
							}else{
								$('#cart_count').html('').removeClass('hide');
							}
							
							$("#coupon_code").val('');
							$('.coupon_code_btn').nextAll().remove();
						}
					}
				}
			});
		}
	});
	
	$(document).on('click','.cloneProduct',function(){
		var key = $(this).attr("data-key");
		$.ajax({
			url:'{{url("/cart/product-clone")}}',
			type:'post',
			dataType:'json',
			data:{'_token':"{{ csrf_token() }}",'key':key},
			beforeSend: function() {
				$('#fade').fadeIn();
			},
			success:function(data){
				$('#fade').fadeOut();
				$("div.yourcart").html(data.html);
			}
		});
	});
	
	$(document).on('click','.proceed',function(){
		var on = 1 ;
		/*$('.comment .fields').each(function(){
			$(this).closest('div').removeClass('has-error');
			$(this).nextAll().remove();
			if($(this).val() == ''){
				on = 0;
				$(this).closest('div').addClass('has-error');
				$("<span class='help-block'>This field is required</span>").insertAfter($(this));
			}
		});*/
		
		/* if(on){
			$.ajax({
				url:'{{url("savecomment")}}',
				type:'post',
				dataType:'json',
				data:{'project_name':$('#project_name').val(),'comments':$('#comments').val()},
				beforeSend: function() {
					$('.fa-spinner').removeClass('hide');
				},
				success:function(data){
					$('.fa-spinner').addClass('hide');
					if(data.status == 'success'){
						$(location).attr('href', '{{url("cart/checkout")}}')
					}
				}
			});
		} */
	});
	
	
	$(document).on('blur','.restict_zero',function(){
		if($(this).val() < 1)
			$(this).val('1');

        updateCart();
	});
	
	$(document).on('click','.coupon_code_btn',function(){
		var code = $('#coupon_code').val();
		if(code != ''){
			$('#coupon_code').closest('.form-group').removeClass('has-error');
			$('.coupon_code_btn').nextAll().remove();
			$.ajax({
					url:'{{url("applycoupon")}}',
					type:'post',
					dataType:'json',
					data:{'code':code},
					beforeSend: function() {
						$('#fade').fadeIn();
					},
					success:function(data){
						$('#fade').fadeOut();
						if(data.status == 'success'){
							if(data.code_apply == 1){
								$("<span class='text-success'>"+data.msg+"</span>").insertAfter($('.coupon_code_btn'));
								
								$('.cart_total').html('$'+formatMoney(data.gross));
								
								if(data.code_type != 'free_shipping'){
									$('.discount_tr td span').html('$'+formatMoney(data.discount_amount));
									$('.discount_tr').removeClass('hide');
								}						
								
								$('.total').html('$'+formatMoney(data.total));
							}else{
								$('#coupon_code').closest('.form-group').addClass('has-error');
								$("<span class='help-block'>"+data.msg+"</span>").insertAfter($('.coupon_code_btn'));
							}
						}
					}
				});
		}else{
			$('#coupon_code').closest('.form-group').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter($('.coupon_code_btn'));
		}
	});
	
});
</script>