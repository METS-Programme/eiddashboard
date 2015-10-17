<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>@yield('meta-title', 'EID LIMS')</title>
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery.dataTables.css') }}" rel="stylesheet">    
    <link href="{{ asset('/css/jquery-ui.css')}}" rel="stylesheet" >
    <link href="{{ asset('/css/eid.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/tab.css') }}" rel="stylesheet">

    <script src="{{ asset('/js/general.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery-2.1.3.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery-ui.js')}}" type="text/javascript"></script>
`
    <script src="{{ asset('/js/tab.js')}}" type="text/javascript"></script>

    <script src="{{ asset('/js/Chart.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/js/angular.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/js/angular-route.js')}}" type="text/javascript"></script>

    
</head>

<body ng-app="dashboard" ng-controller="DashController" onload="init()">
<div class="navbar-custom navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/db"> <span class='glyphicon glyphicon-home'></span> EID LIMS</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                    <li id='l1' class='active'>{!! link_to("/","DASH BOARD",['class'=>'hdr']) !!}</li> 
                    <li id='l2'>{!! link_to("#","REPORTS",['class'=>'hdrs']) !!}</li>             
            </ul>
        </div>
    </div>
</div> 

<div class='container'>
    <br>
    <?php if(!isset($filter_val)) $filter_val="National Metrics, ".date('Y')." thus far" ?>
     <!-- <p><label class='hdr hdr-grey'> FILTERS:</label> <label class='hdr val-grey'>{!! $filter_val !!}</label> </p> -->
    
     <table border='1' cellpadding='0' cellspacing='0' class='filter-tb'>
        <tr>
            <td width='25%'>{!! Form::select('time',[''=>'YEAR']+MyHTML::years(2010),$time,["id"=>"time_fltr"]) !!}</td>
            <td width='25%'>{!! Form::select('region',$regions,"all",['ng-init'=>"region='all'","ng-model"=>"region",'ng-change'=>"regionFilter()"]) !!}</td>
            <td width='25%'>{!! Form::select('district',[''=>'DISTRICT']+$districts,"",["ng-model"=>"district",'ng-change'=>"districtFilter()"]) !!}</td>
            <td width='25%'>{!! Form::select('care_level',[''=>'CARE LEVEL']+$facility_levels) !!}</td>
        </tr>
     </table>
     <br>

     <div class="tabs">
        <ul id='tabs'>
            <li class="selected">
                <a href="#tab1" id='tb_hd1' ng-click="setCountPos()">
                <label class='tab-labels'>
                    <span ng-model="count_positives" ng-init="count_positives={!! $count_positives !!}">
                        <% count_positives %>
                    </span>
                                
                    <font class='tab-sm-ttl'>HIV POSITIVE INFANTS</font>
                </label>
                </a>
            </li>
            <li>
                <a href="#tab2" id='tb_hd2'>
                    <label class='tab-labels'>00.0%
                        <font class='tab-sm-ttl'>AVERAGE UPTAKE RATE</font>
                    </label>
                </a>
            </li>
            <li>
                <a href="#tab3" id='tb_hd3'>
                    <label class='tab-labels'>
                        29.1%
                        <font class='tab-sm-ttl'>AVERAGE ART INITIATION RATE</font>
                    </label>
                </a>
            </li>
            <li>
                <a href="#tab4" id='tb_hd4' ng-click="avPositivity()">
                    <label class='tab-labels'>
                        <span ng-model="av_positivity" ng-init="av_positivity={!! $av_positivity !!}">
                            <% av_positivity %>
                        </span>  
                        <font class='tab-sm-ttl'>AVERAGE POSITIVITY RATE</font>
                    </label>
                </a>
            </li>
        </ul>

        <?php 

        $key_nat="<label class='sm_box national'>&nbsp;</label>&nbsp;National";
        $key_slctn="<label class='sm_box selection'>&nbsp;</label>&nbsp;Selection";
        $key_lb="$key_nat &nbsp;&nbsp;&nbsp; $key_slctn <br>";

        ?>

        <div>
            <div id="tab1" class="tabContent">
                 
                {!!$key_lb !!}
                <canvas id="hiv_postive_infants" class='db-charts'></canvas>              
            </div>

            <div id="tab2" class="tabContent hide">
                00.0%
            </div>
 
            <div id="tab3" class="tabContent hide">
                <p>Tab #3 content goes here!</p>
                <p>Donec pulvinar neque sed semper lacinia. Curabitur lacinia ullamcorper nibh; quis imperdiet velit eleifend ac. Donec blandit mauris eget aliquet lacinia! Donec pulvinar massa interdum ri.</p>
            </div>
 
            <div id="tab4" class="tabContent hide">
                {!!$key_lb !!}
                <canvas id="av_positivity" class='db-charts'></canvas>               
            </div>
        </div>
    </div>
</div>
</body>
<?php
$chart_stuff=[
    "fillColor"=>"rgba(151,187,205,0.2)",
    "strokeColor"=>"rgba(151,187,205,1)",
    "pointColor"=>"rgba(151,187,205,1)",
    "pointStrokeColor"=>"#fff",
    "pointHighlightFill"=>"#fff",
    "pointHighlightStroke"=> "rgba(151,187,205,1)"
    ];

$chart_stuff2=[
    "fillColor"=>"#FFFFCC",
    "strokeColor"=>"#FFCC99",
    "pointColor"=>"#FFCC99",
    "pointStrokeColor"=>"#fff",
    "pointHighlightFill"=>"#fff",
    "pointHighlightStroke"=> "#FFCC99"
    ];
$st2= ["Jan"=>2, "Feb"=>2, "Mar"=>3, "Apr"=>6, "May"=>3, "Jun"=>6, "Jul"=>6,"Aug"=>6,"Sept"=>6,"Oct"=>2,"Nov"=>6,"Dec"=>2];
?>


<script type="text/javascript">
var months=["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug","Sept","Oct","Nov","Dec"];
var count_positives_json=<?php echo json_encode($chart_stuff + ["data"=>$count_positives_arr]) ?>;
var count_positives_json2=<?php echo json_encode($chart_stuff2 + ["data"=>$st2]) ?>;
var av_positivity_json=<?php echo json_encode($chart_stuff + ["data"=>$av_positivity_arr]) ?>;

$(document).ready( function(){
    var ctx = $("#hiv_postive_infants").get(0).getContext("2d");
   // This will get the first returned node in the jQuery collection. 
   var data = {
        labels: months,
        datasets: [count_positives_json] 
    };
    var myLineChart = new Chart(ctx).Line(data);
});

/*
function av_positivity(){
    var ctx = $("#av_positivity").get(0).getContext("2d");
   // This will get the first returned node in the jQuery collection. 
   var data = {
        labels: months,
        datasets: [av_positivity_json] 
    };
    var myLineChart = new Chart(ctx).Line(data);
}    

$("#tb_hd4").on("click",setTimeout("av_positivity()",1200));
*/

$("#time_fltr").change(function(){
    return window.location.assign("/"+this.value);
});


//angular stuff
var app=angular.module('dashboard', [], function($interpolateProvider) {
        $interpolateProvider.startSymbol('<%');
        $interpolateProvider.endSymbol('%>');
    });
var ctrllers={};

ctrllers.DashController=function($scope,$timeout){
    //for filtering by region
    $scope.positives_by_region=<?php echo json_encode($positives_by_region) ?>;   
    $scope.pos_by_reg_sums=<?php echo json_encode($pos_by_reg_sums) ?>;

    $scope.av_by_region=<?php echo json_encode($av_by_region) ?>;
    $scope.av_by_reg_mth=<?php echo json_encode($av_by_reg_mth) ?>;

    //for filtering by district
    $scope.positives_by_dist=<?php echo json_encode($positives_by_dist) ?>;   
    $scope.pos_by_dist_sums=<?php echo json_encode($pos_by_dist_sums) ?>;

    $scope.av_by_dist=<?php echo json_encode($av_by_dist) ?>;
    $scope.av_by_dist_mth=<?php echo json_encode($av_by_dist_mth) ?>;



    $scope.regionFilter=function(){
        $scope.setCountPos();
        $scope.avPositivity();
    };

    $scope.districtFilter=function(){
        $scope.setCountPosDist();
        $scope.avPositivityDist();
    };

    $scope.setCountPos=function(){
        $scope.count_positives=$scope.pos_by_reg_sums[$scope.region];
        $timeout(function(){
            if($("#tb_hd1").hasClass('selected')){
                var ctx = $("#hiv_postive_infants").get(0).getContext("2d");
                var data = {
                    labels: months,datasets: [
                    count_positives_json,
                    {
                        "fillColor":"#FFFFCC",
                        "strokeColor":"#FFCC99",
                        "pointColor":"#FFCC99",
                        "pointStrokeColor":"#fff",
                        "pointHighlightFill":"#fff",
                        "pointHighlightStroke":"#FFCC99",
                        "data":$scope.positives_by_region[$scope.region]
                    }] 
                };
                var myLineChart = new Chart(ctx).Line(data);
            }

        },1);
    };


    $scope.avPositivity=function (){
        $scope.av_positivity=$scope.av_by_region[$scope.region];
        $timeout(function(){
            if($("#tb_hd4").hasClass('selected')){
                var ctx = $("#av_positivity").get(0).getContext("2d");
                var data = {
                    labels: months,datasets: [
                    av_positivity_json,{
                        "fillColor":"#FFFFCC",
                        "strokeColor":"#FFCC99",
                        "pointColor":"#FFCC99",
                        "pointStrokeColor":"#fff",
                        "pointHighlightFill":"#fff",
                        "pointHighlightStroke":"#FFCC99",
                        "data":$scope.av_by_reg_mth[$scope.region]
                    }] 
                };
                var myLineChart = new Chart(ctx).Line(data);
            };
        },1);
    }

     $scope.setCountPosDist=function(){
        $scope.count_positives=$scope.pos_by_dist_sums[$scope.district];
        $timeout(function(){
            if($("#tb_hd1").hasClass('selected')){
                var ctx = $("#hiv_postive_infants").get(0).getContext("2d");
                var data = {
                    labels: months,datasets: [
                    count_positives_json,
                    {
                        "fillColor":"#FFFFCC",
                        "strokeColor":"#FFCC99",
                        "pointColor":"#FFCC99",
                        "pointStrokeColor":"#fff",
                        "pointHighlightFill":"#fff",
                        "pointHighlightStroke":"#FFCC99",
                        "data":$scope.positives_by_dist[$scope.district]
                    }] 
                };
                var myLineChart = new Chart(ctx).Line(data);
            }

        },1);
    };


    $scope.avPositivity=function (){
        $scope.av_positivity=$scope.av_by_dist[$scope.district];
        $timeout(function(){
            if($("#tb_hd4").hasClass('selected')){
                var ctx = $("#av_positivity").get(0).getContext("2d");
                var data = {
                    labels: months,datasets: [
                    av_positivity_json,{
                        "fillColor":"#FFFFCC",
                        "strokeColor":"#FFCC99",
                        "pointColor":"#FFCC99",
                        "pointStrokeColor":"#fff",
                        "pointHighlightFill":"#fff",
                        "pointHighlightStroke":"#FFCC99",
                        "data":$scope.av_by_dist_mth[$scope.district]
                    }] 
                };
                var myLineChart = new Chart(ctx).Line(data);
            };
        },1);
    }
};

app.controller(ctrllers);
</script>
</html>
