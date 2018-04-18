
	@extends('layout.app')
		@section('content')
		<!-- begin #content -->
		<div id="content" class="content">
			
			<div class="row">
			    <div class="col-md-12">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">
                            <div class="panel-heading-btn">
                            	<a class="btn btn-info btn-xs" href="{{URL::to('/services')}}">All Services</a>
                                
                            </div>
                            <h4 class="panel-title">Add new Service</h4>
                        </div>
                        <div class="panel-body">
                            {!! Form::open(array('route' => 'services.store','class'=>'form-horizontal author_form','method'=>'POST','files'=>'true', 'id'=>'commentForm','role'=>'form','data-parsley-validate novalidate')) !!}
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4">Product Status *:</label>
                                    <div class="col-md-1 col-sm-1">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" value="1" id="radio-required" data-parsley-required="true" checked /> Active
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="status" id="radio-required2" value="0" /> Inactive
                                            </label>
                                        </div>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="product_name">Service Name * :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control" type="text" id="product_name" name="product_name" placeholder="Service Name" data-parsley-required="true" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="product_id">Service Code * :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control" type="text" id="product_id" name="product_id" placeholder="Service Code" data-parsley-required="true" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="sales_price">Service Price * :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control" type="text" id="sales_price" name="sales_price" placeholder="Service Price" data-parsley-required="true" />
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="specification">Service Specification :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control" type="text" id="specification" name="specification" placeholder="Service Specification" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="specification">Service Category *:</label>
                                    <div class="col-md-6 col-sm-6">
                                        <select class="form-control select" id="select-required" name="fk_category_id" data-parsley-required="true">
                                            <option value="">Please choose</option>
                                            @foreach($getCategories as $category)
                                            <option value="{{$category->id}}">{{$category->category_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-4" for="summery">Service Summery  :</label>
                                    <div class="col-md-6 col-sm-6">
                                        <textarea class="form-control" id="summery" name="summery" rows="4"  placeholder="Summery"></textarea>
                                    </div>
                                </div>
                                
								<div class="form-group">
									<label class="control-label col-md-4 col-sm-4"></label>
									<div class="col-md-6 col-sm-6">
										<button type="submit" class="btn btn-primary">Confirm</button>
									</div>
								</div>
                            {!! Form::close(); !!}
                        </div>
                    </div>
			    </div>
			</div>
		</div>
		<!-- end #content -->
		
    @endsection
