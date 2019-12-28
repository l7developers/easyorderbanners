@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Create Estimate</h1>
</section>
<section class="content">
	<div class="row">
		<ul class="errorMessages hide"></ul>
		<div class="col-xs-12">
			{{ Form::model('order', ['url' => ['admin/order/add'],'id'=>'order_form']) }}
				{{Form::hidden('cart_amount','',['id'=>'cart_amount'])}}
				{{Form::hidden('shipping_amount','',['id'=>'shipping_amount'])}}
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active customer_tab"><a href="#customer_div" data-toggle="tab">Customer</a></li>
					<li class="product_tab"><a href="javascript:void(0)">Products</a></li>
					<li class="address_tab"><a href="javascript:void(0)">Address</a></li>
					<li class="payment_tab"><a href="javascript:void(0)">Payment</a></li>
					
					<li class="pull-right">
						<div class="btn btn-warning pull-right cart">
							<i class="fa fa-shopping-cart" aria-hidden="true"></i> <span id="amount"> $0</span>
						</div>
					</li>
					<li class="pull-right hide">
						<div class="btn btn-primary pull-right">
							Shipping : <span id="shipping"> $0</span>
						</div>
					</li>
				</ul>
				<div class="tab-content">
					<div class="active tab-pane" id="customer_div">
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group row{{ $errors->has('customer') ? ' has-error' : '' }}">
									{{ Form::label('customer', 'Select Customer',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-6">
										{{Form::select('customer', [''=>'Select Customer','0'=>'Create New Customer'] + $users, old('customer'),array('class'=>'form-control','id'=>'customer','required'))}}
										@if ($errors->has('customer'))
											<span class="help-block">{{ $errors->first('customer') }}</span>
										@endif
									</div>
								</div>
								<div class="form-group row hide" id="new_customer_div">
									{{ Form::label('', '',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-6">
										<div class="row">
											<div class="form-group col-sm-6">
												{{ Form::label('company_name','Company Name',array('class'=>'form-control-label'))}}
												{{ Form::text('company_name',old('company_name'),['class'=>'form-control new_customer_input','placeholder'=>'Company Name'])}}
											</div>
											<div class="clearfix"></div>
											<div class="form-group col-sm-6">
												{{ Form::label('fname','First Name',array('class'=>'form-control-label'))}}
												{{ Form::text('fname',old('fname'),['class'=>'form-control new_customer_input','placeholder'=>'First Name'])}}
											</div>
											<div class="form-group col-sm-6">
												{{ Form::label('lname','Last Name',array('class'=>'form-control-label'))}}
												{{ Form::text('lname',old('lname'),['class'=>'form-control new_customer_input','placeholder'=>'Last Name'])}}
											</div>
											<div class="form-group col-sm-6">
												{{ Form::label('email','Email',array('class'=>'form-control-label'))}}
												{{ Form::text('email',old('email'),['class'=>'form-control new_customer_input','placeholder'=>'Email'])}}
											</div>
											<div class="form-group col-sm-6">
												{{ Form::label('phone_number','Phone Number',array('class'=>'form-control-label'))}}
												{{ Form::text('phone_number',old('phone_number'),['class'=>'form-control new_customer_input','placeholder'=>'Phone Number'])}}
											</div>
										</div>
									</div>
								</div>
								<div class="form-group row next_div hide">
									<label class="col-sm-3 form-control-label">&nbsp;</label>
									<div class="col-sm-6 offset-sm-2">
										<input type="button" class="btn btn-primary next" data="customer" value="Next"/>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="products">
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group row{{ $errors->has('category') ? ' has-error' : '' }}">
									{{ Form::label('category', 'Select category',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-6">
										{{Form::select('category', [''=>'Select Category'] + $categories+['custom'=>'Miscellaneous'], old('category'),array('class'=>'form-control','id'=>'category','required'))}}
										@if ($errors->has('category'))
											<span class="help-block">{{ $errors->first('category') }}</span>
										@endif
									</div>
								</div>
								<div class="form-group row{{ $errors->has('product') ? ' has-error' : '' }}">
									{{ Form::label('product', 'Select product',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-6">
										{{Form::select('product', [''=>'Select caregory first'], old('product'),array('class'=>'form-control','id'=>'product'))}}
										@if ($errors->has('product'))
											<span class="help-block">{{ $errors->first('product') }}</span>
										@endif
									</div>
								</div>
								<div class="col-xs-12 cart_products hide">
									<div class="box box-primary">
										<div class="box-body table-responsive">
											<table class="table table-bordered table-hover table-striped addedfeature">
												<thead>
													<tr>
														<th>S.No.</th>
														<th>Product</th>
														<th>Options</th>
														<th>Gross Price</th>
														<th>Quantity</th>
														<th>Gross Total</th>
														<th>Qty Discount</th>
														<th>Total</th>
														<th>Remove</th>
													</tr>
												</thead>
												<tbody></tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="form-group row hide" id="product_option_div">
									{{ Form::label('', '',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-9">   
									</div>
								</div>
								<div class="form-group row" id="custom_product_div" style="display:none">								
									{{ Form::label('', '',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-9"> 
										<div class="form-group col-xs-12">
											<h3 class="page-header">Product Info</h3>
											<div class="col-xs-12 col-sm-12 form-group">
												<label for="" class="form-control-label">Product Name:</label>
												<input class="form-control option_fields" id="product_name" name="product_name" placeholder="Enter Product Name"> 
											</div>
											<div class="col-xs-12 col-sm-12 form-group">
												<label for="" class="form-control-label">Product Option : (one per line)</label>
												<textarea class="form-control" name="description" id="description" value="" placeholder="Enter Product Option" rows="5"></textarea> 
											</div>
										</div>
										<div class="form-group col-xs-12">
											<h3 class="page-header">Price Info</h3>
											<div class="col-xs-3 col-sm-3 form-group">
												<label for="" class="form-control-label">Qty:</label>
												<input class="form-control option_fields" id="quantity" min="1" name="quantity" type="number" value="1"> 
											</div>
											<div class="col-xs-3 col-sm-3 form-group">
												<label for="" class="form-control-label">Price:</label>
												<input class="form-control option_fields" id="price" min="1" name="price" type="number" value="" placeholder="Price Per unit">
											</div>
											<div class="col-xs-3 col-sm-3 form-group">
												<label for="" class="form-control-label">Gross Price:</label>
												<input class="form-control option_fields" id="gross_price" name="gross_price" type="number" value="" placeholder="Gross Price">
											</div>
											<div class="col-xs-3 col-sm-3 form-group">
												<label for="" class="form-control-label">Shipping Price:</label>
												<input class="form-control option_fields" id="shipping_price" name="shipping_price" type="number" value="" placeholder="Shipping Price">
											</div>											
										</div>
										<div class="col-xs-12 text-right">											
											<button type="button" class="btn btn-info " onclick="add_to_cart()"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Add to Cart</button></div>
									</div>									
								</div>
								<div class="form-group row next_div hide">
									<label class="col-sm-3 form-control-label">&nbsp;</label>
									<div class="col-sm-6 offset-sm-2">
										<input type="button" class="btn btn-info add_more_item" data="products" value="Add More Item"/>
										<input type="button" class="btn btn-primary next" data="products" value="Next"/>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="addresses">
						<div class="row">
							<div class="col-xs-12">
								<div class="row form-group">
									{{ Form::label('shipping_option', 'Shipping Option',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-6">
										{{Form::select('shipping_option',[''=>'Select Shipping Option']+config('constants.Shipping_option'),'',['class'=>'form-control'])}}
									</div>
								</div>
								<div class="form-group row billing_main_div">
									{{ Form::label('billing_add', 'Billing Address',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="row col-sm-9">
										<div  class="billing_add_div" id="billing_add_div">
											<div class="col-xs-12">Empty</div>
										</div>
										<div class="clearfix"></div>
										<div class="new_billing_add order_option_box hide">
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('billing_company_name','Company Name',array('class'=>'form-control-label'))}} {{Form::text('billing_company_name','',['class'=>'form-control  opt','placeholder'=>'Company Name'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('billing_phone_number','Phone Number',array('class'=>'form-control-label'))}} {{Form::text('billing_phone_number','',['class'=>'form-control  opt','placeholder'=>'Phone Number'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('billing_fname','First Name',array('class'=>'form-control-label'))}} {{Form::text('billing_fname','',['class'=>'form-control','placeholder'=>'First Name'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('billing_lname','Last Name',array('class'=>'form-control-label'))}} {{Form::text('billing_lname','',['class'=>'form-control','placeholder'=>'Last Name'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('billing_address1','Address Line 1',array('class'=>'form-control-label'))}} {{Form::text('billing_address1','',['class'=>'form-control','placeholder'=>'Address Line 1'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('billing_address2','Address Line 2',array('class'=>'form-control-label'))}} {{Form::text('billing_address2','',['class'=>'form-control add2 opt','placeholder'=>'Address Line 2'])}}
											</div>
											<div class="clearfix"></div>
											<div class="form-group col-sm-6 col-xs-12">{{Form::label('billing_zipcode','Zipcode',array('class'=>'form-control-label'))}}{{Form::number('billing_zipcode','',['class'=>'form-control','placeholder'=>'Zipcode'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">								{{Form::label('billing_city','City',array('class'=>'form-control-label'))}}{{Form::text('billing_city','',['class'=>'form-control','placeholder'=>'City'])}}
											</div>
											<div class="clearfix"></div>
											<div class="form-group col-sm-6 col-xs-12">{{Form::label('billing_state','State',array('class'=>'form-control-label'))}}{{Form::select('billing_state',$states,'',['class'=>'form-control','placeholder'=>'Select State'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">{{Form::label('billing_country','Country',array('class'=>'form-control-label'))}} {{Form::text('billing_country','US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
											</div>
										</div>
									</div>
								</div>
								<div class="form-group row multiple_shipping_div">
									{{ Form::label('', 'Multiple Shipping',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-6 multiple_shipping">{{Form::checkbox('multiple_shipping',1,false,['class'=>'flat-red'])}}
									</div>
								</div>
								<div class="row form-group multiple_shipping_add_div hide"></div>
								<div class="form-group row shipping_main_div">
									{{ Form::label('shipping_add', 'Shipping Address',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="row col-sm-9">
										<div  class="shipping_add_div">Empty</div>
										<div class="clearfix"></div>
										<div class="shipping_add_box order_option_box hide">
											<div class="form-group col-sm-12 col-xs-12">
												{{Form::label('shipping_address_name','Address Name',array('class'=>'form-control-label'))}} {{Form::text('shipping_address_name','',['class'=>'form-control  opt','placeholder'=>'Address Name'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('shipping_company_name','Company Name',array('class'=>'form-control-label'))}} {{Form::text('shipping_company_name','',['class'=>'form-control  opt','placeholder'=>'Company Name'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('shipping_phone_number','Phone Number',array('class'=>'form-control-label'))}} {{Form::text('shipping_phone_number','',['class'=>'form-control  opt','placeholder'=>'Phone Number'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('shipping_fname','First Name',array('class'=>'form-control-label'))}} {{Form::text('shipping_fname','',['class'=>'form-control','placeholder'=>'First Name'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
												{{Form::label('shipping_lname','Last Name',array('class'=>'form-control-label'))}} {{Form::text('shipping_lname','',['class'=>'form-control','placeholder'=>'Last Name'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
											{{Form::label('shipping_address1','Address Line 1',array('class'=>'form-control-label'))}} {{Form::text('shipping_address1','',['class'=>'form-control shipping_input','placeholder'=>'Address Line 1'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">
											{{Form::label('shipping_address2','Address Line 2',array('class'=>'form-control-label'))}} {{Form::text('shipping_address2','',['class'=>'form-control shipping_input add2  opt','placeholder'=>'Address Line 2'])}}
											</div>
											<div class="form-group col-sm-12 col-xs-12">
											{{Form::label('shipping_ship_in_care','Ship in care of',array('class'=>'form-control-label'))}} {{Form::text('shipping_ship_in_care','',['class'=>'form-control shipping_input  opt','placeholder'=>'Ship in care of'])}}
											</div>
											<div class="clearfix"></div>
											<div class="form-group col-sm-6 col-xs-12">{{Form::label('shipping_zipcode','Zipcode',array('class'=>'form-control-label'))}} {{Form::number('shipping_zipcode','',['class'=>'form-control shipping_input','placeholder'=>'Zipcode'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">	{{Form::label('shipping_city','City',array('class'=>'form-control-label'))}} {{Form::text('shipping_city','',['class'=>'form-control shipping_input','placeholder'=>'City'])}}
											</div>
											<div class="clearfix"></div>
											<div class="form-group col-sm-6 col-xs-12">{{Form::label('shipping_state','State',array('class'=>'form-control-label'))}}{{Form::select('shipping_state',$states,'',['class'=>'form-control shipping_input','placeholder'=>'Select State'])}}
											</div>
											<div class="form-group col-sm-6 col-xs-12">{{Form::label('shipping_country','Country',array('class'=>'form-control-label'))}} {{Form::text('shipping_country','US',['class'=>'form-control shipping_input','placeholder'=>'Country','readonly'])}}
											</div>
										</div>
									</div>
								</div>
								<div class="form-group row next_div hide">
									<label class="col-sm-3 form-control-label">&nbsp;</label>
									<div class="col-sm-6 offset-sm-2">
										<input type="hidden" value="0" name="skip_address" id="skip_address"/>
										<input type="button" class="btn btn-primary next" data="addresses" value="Next"/>
										<input type="button" class="btn btn-primary next" data="skip_address" value="Continue Without Address" style="margin-left:25px;"/>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="payment">
						<div class="row">
							<div class="col-sm-12">
								<?php /*<div class="row form-group">
									{{ Form::label('rush_fee', 'Rush Fee',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-6">
										<div class="input-group">
											<span class="input-group-addon">$</span>
											{{Form::text('rush_fee','',['class'=>'form-control','placeholder'=>'Rush Fee'])}}
										</div>
									</div>
								</div> */?>
								<div class="form-group row payment_tab hide">
									{{ Form::label('', '',array('class'=>'col-sm-3 form-control-label'))}}
									<div class="col-sm-6 offset-sm-2">
										<br/>
										<input type="submit" class="btn btn-success" name="create_estimate" data="payment" value="Create Estimate"/>
										<input type="submit" class="btn btn-primary" name="send_estimate" data="payment" value="Send Estimate to Customer" style="margin-left: 25px" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</section>  	

@include('partials.order.order_add_js')
@include('partials.order.order_cart')
	  
@endsection		  