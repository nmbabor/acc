@extends('layout.app')
    @section('content')
      <!-- Area Chart Example-->
      <div class="card mb-3">
        <div class="card-header">
          <span class="col-md-7"><i class="fa fa-area-chart"></i> Total Sales in Last 15 days</span>
          {!! Form::open(['url'=>'dashboard','method'=>'get']) !!}
          <div class="full-right col-md-5">
            <div class="col-md-3 no-padding">
              {{Form::text('start',date('d-m-Y', strtotime('today - 14 days')),['class'=>'form-control datepicker text-center','placeholder'=>'start'])}}
            </div>
            <div class="col-md-3">
              <span class="text-center form-control">TO</span>
            </div>
            <div class="col-md-3 no-padding">
              {{Form::text('end',date('d-m-Y'),['class'=>'form-control datepicker text-center','placeholder'=>'start'])}}
            </div>
            <div class="col-md-2">
              <button class="btn btn-info">Find</button>
            </div>
          </div>
          {!! Form::close() !!}
        </div>
        <div class="card-body">
          <canvas id="myAreaChart" width="100%" height="30"></canvas>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-7">
          <!-- Example Bar Chart Card-->
          <div class="card mb-12">
            <div class="card-header">
              <i class="fa fa-bar-chart"></i> Branch Wise sales  in {{date('Y')}}</div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-12 my-auto">
                  <canvas id="myBarChart" width="100" height="50"></canvas>
                </div>

              </div>
            </div>

          </div>
        </div>
        <div class="col-lg-5">
          <!-- Example Pie Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-pie-chart"></i> Branch wise sales in {{date('F, Y')}}</div>
            <div class="card-body">
              <canvas id="myPieChart" width="100%" height="69"></canvas>
            </div>
          </div>
        </div>
      </div>

    <!-- /.container-fluid-->
    <!-- /.content-wrapper-->
      <script src="{{asset('public/plugins/jquery/jquery-1.9.1.min.js')}}"></script>
      <script src="{{asset('public/dashboard/vendor/chart.js/Chart.min.js')}}"></script>
      @include('_partials.cartScript');
    @stop

