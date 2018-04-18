	@extends('layout.app')
		@section('content')
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
        <style type="text/css">
            form{display: inline;}
            .form-group{width: 100%;height: auto; overflow: hidden; display: block !important; margin: 5px;}
            .form-control{width: 100% !important;}
            table.dataTable tbody th, table.dataTable tbody td{padding: 5px;}
        </style>
		<!-- begin #content -->
		<div id="content" class="content">
			
			<div class="row">
			    <div class="col-md-12">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">
                            <div class="panel-heading-btn">
                                <a class="btn btn-info btn-xs" href="{{URL::to('/inventory-return/create')}}">New Return</a>
                                
                            </div>
                            <h4 class="panel-title">View All Return </h4>
                        </div>
                        <div class="panel-body">
                            <table id="all_data" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Invoice Id</th>
                                        <th>Organization Name</th>
                                        <th>Return Date</th>
                                        <th>Total Amount</th>
                                        <th>Total Return</th>
                                        <th>Back Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
			    </div>
			</div>
		</div>
		<!-- end #content -->
		
        
    <script src="{{asset('public/plugins/jquery/jquery-1.9.1.min.js')}}"></script>

    <script type="text/javascript">
        $(function() {
            $('#all_data').DataTable( {
                processing: true,
                serverSide: true,
                ajax: '{!! URL::to("inventory-purchase-return-all") !!}',
                columns: [
                    { data: 'sl',searchable:false},
                    { data: 'inventory_order_id',name:'inventory_product_add.inventory_order_id'},
                    { data: 'company_name',name:'inventory_supplier.company_name'},
                    { data: 'date',name:'inventory_purchase_return.date'},
                    { data: 'total_amount',name:'inventory_purchase_return.total_amount'},
                    { data: 'total_return',name:'inventory_purchase_return.total_return'},
                    { data: 'back_amount',name:'inventory_purchase_return.back_amount'},
                    { data: 'action'}
                ]
            });
        });
    </script>
    @endsection
