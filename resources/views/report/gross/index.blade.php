@extends('layout.app')
    @section('content')
    <style type="text/css">
        .filter_info{display: none;}
    </style>
    
    <!-- begin #content -->
    <div id="content" class="content">
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" onclick="printPage('print_body')" class="printbtn btn btn-xs btn-success pull-right"><i class="fa fa-print m-r-5"></i> Print</a>
                        </div>
                        <h4 class="panel-title">Gross Profit</h4>
                    </div>
                    <div class="panel-body print_body">
                        <div class="report_filler">
                            <div class="row">
                                <div class="form-group col-md-3">
                                     <label class="control-label col-md-12" for="Date">Year :</label>
                                    <div class="col-md-12">
                                        <input class="form-control" type="number" min="1970"  name="year" value="<?php echo date('Y'); ?>" data-parsley-required="true" id="year" />
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                 <label class="control-label col-md-12" for="Date">Month :</label>
                                    <div class="col-md-12">
                                        <? for ($m=1; $m<=12; $m++) {
                                            $months[$m] = date('F', mktime(0,0,0,$m, 1, date('Y')));
                                        } ?>
                                        {{Form::select('month',$months,date('m'),['class'=>'form-control','id'=>'month'])}}
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                 <label class="col-md-12" for="Date">&nbsp;</label>
                                       
                                    <div class="col-sm-12 ">

                                        <button type="submit" id="submit" class="btn btn-success btn-bordred waves-effect w-md waves-light m-b-5">
                                            Confirm
                                        </button>
                                    </div>
                                </div>
                                

                            
                            </div> 
                        </div>
                        <br>
                        <div id="print_body">
                            @include('pad.header')
                            <style type="text/css">
                                @page {
                                  margin-bottom: 100px;
                                }
                            </style>
                            <div class="report_result" id="load_result">
                            <div class="filter_info">
                            <h3 align="center">Profit and Loss for {{$month}}</h3>
                            </div>
                        <div class="col-md-6 no-padding-left">
                            <table class="table table-bordered table-striped">
                                <theader>
                                    <tr class="success">
                                        <th colspan="3" class="text-center">Revenues</th>
                                    </tr>
                                    <tr class="active">
                                        <th width="5%">SL</th>
                                        <th>Title</th>
                                        <th>Amount</th>
                                    </tr>
                                </theader>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Product Sales</td>
                                        <td>{{round($sales->total_amount,3)}}</td>
                                    </tr>
                                    
                                    <? 
                                    $i=1;
                                    $total_deposit=0;
                                     ?>
                                @foreach($deposit as $dep)
                                <? 
                                $i++;
                                $total_deposit+=$dep->total_amount;
                                ?>
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$dep->sub_category_name}}</td>
                                        <td>{{round($dep->total_amount,3)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfooter>
                                    <tr class="warning">
                                        <th colspan="2" style="text-align:right">Total Revenues : </th>
                                        <th>{{round($total_deposit+$sales->total_amount,3)}}</th>
                                    </tr>
                                </tfooter>
                            </table>
                        </div>
                        <div class="col-md-6 no_padding">
                           <table class="table table-bordered table-striped">
                                <theader>
                                    <tr class="success">
                                        <th colspan="3" class="text-center">Costs and expenses</th>
                                    </tr>
                                    <tr class="active">
                                        <th width="5%">SL</th>
                                        <th>Title</th>
                                        <th>Amount</th>
                                    </tr>
                                </theader>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Product Purchase</td>
                                        <td>{{round($sales->total_cost,3)}}</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Total Salary</td>
                                        <td>{{round($salary->total_amount,3)}}</td>
                                    </tr>
                                    <? 
                                    $j=2;
                                    $total_payment=0;
                                    ?>
                                    @foreach($payment as $pay)
                                    <? 
                                    $j++;
                                    $total_payment+=$pay->total_amount;
                                     ?>
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{$pay->sub_category_name}}</td>
                                            <td>{{round($pay->total_amount,3)}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            <tfooter>
                                    <tr class="warning">
                                        <th colspan="2" style="text-align:right">Total Costs and expenses : </th>
                                        <th>{{round($total_payment+$sales->total_cost+$salary->total_amount,3)}}</th>
                                    </tr>
                                </tfooter>
                            </table>
                           
                        </div>
                        <table class="table table-bordered">
                            <tr class="danger">
                                <th>Net Profit or Loss </th>
                                <th>{{round((($total_deposit+$sales->total_amount)-($total_payment+$sales->total_cost+$salary->total_amount)),3)}}</th>
                            </tr>
                        </table>

                            </div>
                      
                    </div>
                
                    </div>
                </div>
            </div>
        </div>
    <!-- end #content -->
@endsection
@section('script')
<script src="{{asset('public/custom_js/printThis.js')}}"></script>
<script type="text/javascript">
    function printPage(id){
        $('#'+id).printThis();
    }
    $("#submit").click(function(){
        month = $("#month").val();
        year = $("#year").val();
        $('#load_result').load("{{URL::to('inventory-gross-profit-result')}}?month="+month+'&year='+year);
    });

</script>
@endsection