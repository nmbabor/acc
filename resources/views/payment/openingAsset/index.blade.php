	@extends('layout.app')
		@section('content')
        <style type="text/css">
            form{display: inline;}
            .form-group{width: 100%;height: auto; overflow: hidden; display: block !important; margin: 5px;}
            .form-control{width: 100% !important;}
        </style>
		<!-- begin #content -->
		<div id="content" class="content">
			
			<div class="row">
			    <div class="col-md-12">
                    <div class="panel panel-inverse">
                        <div class="panel-heading">
                            <div class="panel-heading-btn">
                                <a class="btn btn-info btn-xs" href="{{URL::to('/opening-asset/create')}}">Add New</a>
                            </div>
                            <h4 class="panel-title">Opening Asset</h4>
                        </div>
                        <div class="panel-body">
                            <table id="all_data" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Sector</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Purchase Amount</th>
                                        <th>Current Value</th>
                                        <th>Asset Age</th>
                                        <th>Branch</th>
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
@endsection

@section('script')

<script type="text/javascript">
    $(function() {
        $('#all_data').DataTable( {
            processing: true,
            serverSide: true,
            ajax: '{!! URL::to("opening-asset/show/edit") !!}',
            columns: [
                { data: 'sl'},
                { data: 'sub_category_name',name:'sub_category.sub_category_name'},
                { data: 'date'},
                { data: 'description'},
                { data: 'total_amount'},
                { data: 'current_amount'},
                { data: 'asset_age'},
                { data: 'branch_name',name:'inventory_branch.branch_name'},
                { data: 'action'}
            ]
        });
    });
</script>
@endsection
