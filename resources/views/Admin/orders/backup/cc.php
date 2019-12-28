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