@extends('layouts/contentNavbarLayout')

@section('title', 'Policy Management Dashboard')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
@endsection

@section('content')
<div class="row">
  <div class="col">
    <!-- Welcome Card -->
    <div class="card">
      <div class="card-content dashboard-bg-con">
        <div class="dashboard-bg">
          <img src="{{asset('assets/img/backgrounds/slsu_bg_2.jpeg') }}"  class="img-fluid rounded-top user-timeline-image" alt="user timeline image">
        </div>
        <div class="user-info-dashboard d-flex gap-3 p-3">
          <div class="user-profile">
              <img src="{{ auth()->user()->image }}" class="user-profile-image rounded" alt="user profile image" >
          </div>
          <b class="user-profile-text ml-1 text-dark">
            <div>
              <h6 class="">{{ auth()->user()->name }}</h6>
              <span>{{ config('usersetting.role.'.auth()->user()->role) }}</span>
            </div>
            
            <h5>DASHBOARD</h5>
          </b>
        </div>
        <div class="p-1">

        </div>
      </div>
    </div>
    <hr>
    <p class="font-medium-3 text-bold-500 d-flex align-items-center gap-3"><i class="bx bxs-megaphone text-danger"></i> ANNOUNCEMENTS <i class="text-danger bx bxs-megaphone"></i></p>
    <div class="card mt-3 mb-3">
      <div class="card-body">
        <p>No announcements have been made yet.</p>
      </div>
      </div>


        <div class="row g-6">
        <div class="col">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="card-title mb-0">
            <h5 class="m-0 me-2">Reasons for delivery exceptions</h5>
          </div>
          <div class="dropdown">
            <button class="btn p-0" type="button" id="deliveryExceptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base bx bx-dots-vertical-rounded icon-lg text-body-secondary"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="deliveryExceptions">
              <a class="dropdown-item" href="javascript:void(0);">Select All</a>
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Share</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div id="deliveryExceptionsChart" style="min-height: 400px;" class=""><div id="apexcharts1dglig09" class="apexcharts-canvas apexcharts1dglig09 apexcharts-theme-" style="width: 499px; height: 400px;"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" width="499" height="400"><foreignObject x="0" y="0" width="499" height="400"><div class="apexcharts-legend apexcharts-align-center apx-legend-position-bottom" xmlns="http://www.w3.org/1999/xhtml" style="right: 0px; position: absolute; left: 0px; top: 351px; max-height: 198px;"><div class="apexcharts-legend-series" rel="1" seriesname="Incorrectxaddress" data:collapsed="false" style="margin: 5px 15px;"><span class="apexcharts-legend-marker" rel="1" data:collapsed="false" style="height: 12px; width: 12px; left: 0px; top: 0px;"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%"><path d="M 0, 0 
           m -5, 0 
           a 5,5 0 1,0 10,0 
           a 5,5 0 1,0 -10,0" fill="var(--bs-success)" fill-opacity="1" stroke="#ffffff" stroke-opacity="0.9" stroke-linecap="butt" stroke-width="1" stroke-dasharray="0" cx="0" cy="0" shape="circle" class="apexcharts-legend-marker apexcharts-marker apexcharts-marker-circle" style="transform: translate(50%, 50%);"></path></svg></span><span class="apexcharts-legend-text" rel="1" i="0" data:default-text="Incorrect%20address" data:collapsed="false" style="color: var(--bs-heading-color); font-size: 13px; font-weight: 400; font-family: var(--bs-font-family-base);">Incorrect address</span></div><div class="apexcharts-legend-series" rel="2" seriesname="Weatherxconditions" data:collapsed="false" style="margin: 5px 15px;"><span class="apexcharts-legend-marker" rel="2" data:collapsed="false" style="height: 12px; width: 12px; left: 0px; top: 0px;"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%"><path d="M 0, 0 
           m -5, 0 
           a 5,5 0 1,0 10,0 
           a 5,5 0 1,0 -10,0" fill="color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg))" fill-opacity="1" stroke="#ffffff" stroke-opacity="0.9" stroke-linecap="butt" stroke-width="1" stroke-dasharray="0" cx="0" cy="0" shape="circle" class="apexcharts-legend-marker apexcharts-marker apexcharts-marker-circle" style="transform: translate(50%, 50%);"></path></svg></span><span class="apexcharts-legend-text" rel="2" i="1" data:default-text="Weather%20conditions" data:collapsed="false" style="color: var(--bs-heading-color); font-size: 13px; font-weight: 400; font-family: var(--bs-font-family-base);">Weather conditions</span></div><div class="apexcharts-legend-series" rel="3" seriesname="FederalxHolidays" data:collapsed="false" style="margin: 5px 15px;"><span class="apexcharts-legend-marker" rel="3" data:collapsed="false" style="height: 12px; width: 12px; left: 0px; top: 0px;"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%"><path d="M 0, 0 
           m -5, 0 
           a 5,5 0 1,0 10,0 
           a 5,5 0 1,0 -10,0" fill="color-mix(in sRGB, var(--bs-success) 60%, var(--bs-paper-bg))" fill-opacity="1" stroke="#ffffff" stroke-opacity="0.9" stroke-linecap="butt" stroke-width="1" stroke-dasharray="0" cx="0" cy="0" shape="circle" class="apexcharts-legend-marker apexcharts-marker apexcharts-marker-circle" style="transform: translate(50%, 50%);"></path></svg></span><span class="apexcharts-legend-text" rel="3" i="2" data:default-text="Federal%20Holidays" data:collapsed="false" style="color: var(--bs-heading-color); font-size: 13px; font-weight: 400; font-family: var(--bs-font-family-base);">Federal Holidays</span></div><div class="apexcharts-legend-series" rel="4" seriesname="Damagexduringxtransit" data:collapsed="false" style="margin: 5px 15px;"><span class="apexcharts-legend-marker" rel="4" data:collapsed="false" style="height: 12px; width: 12px; left: 0px; top: 0px;"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%"><path d="M 0, 0 
           m -5, 0 
           a 5,5 0 1,0 10,0 
           a 5,5 0 1,0 -10,0" fill="color-mix(in sRGB, var(--bs-success) 40%, var(--bs-paper-bg))" fill-opacity="1" stroke="#ffffff" stroke-opacity="0.9" stroke-linecap="butt" stroke-width="1" stroke-dasharray="0" cx="0" cy="0" shape="circle" class="apexcharts-legend-marker apexcharts-marker apexcharts-marker-circle" style="transform: translate(50%, 50%);"></path></svg></span><span class="apexcharts-legend-text" rel="4" i="3" data:default-text="Damage%20during%20transit" data:collapsed="false" style="color: var(--bs-heading-color); font-size: 13px; font-weight: 400; font-family: var(--bs-font-family-base);">Damage during transit</span></div></div>
           <style type="text/css">
      .apexcharts-flip-y {
        transform: scaleY(-1) translateY(-100%);
        transform-origin: top;
        transform-box: fill-box;
      }
      .apexcharts-flip-x {
        transform: scaleX(-1);
        transform-origin: center;
        transform-box: fill-box;
      }
      .apexcharts-legend {
        display: flex;
        overflow: auto;
        padding: 0 10px;
      }
      .apexcharts-legend.apexcharts-legend-group-horizontal {
        flex-direction: column;
      }
      .apexcharts-legend-group {
        display: flex;
      }
      .apexcharts-legend-group-vertical {
        flex-direction: column-reverse;
      }
      .apexcharts-legend.apx-legend-position-bottom, .apexcharts-legend.apx-legend-position-top {
        flex-wrap: wrap
      }
      .apexcharts-legend.apx-legend-position-right, .apexcharts-legend.apx-legend-position-left {
        flex-direction: column;
        bottom: 0;
      }
      .apexcharts-legend.apx-legend-position-bottom.apexcharts-align-left, .apexcharts-legend.apx-legend-position-top.apexcharts-align-left, .apexcharts-legend.apx-legend-position-right, .apexcharts-legend.apx-legend-position-left {
        justify-content: flex-start;
        align-items: flex-start;
      }
      .apexcharts-legend.apx-legend-position-bottom.apexcharts-align-center, .apexcharts-legend.apx-legend-position-top.apexcharts-align-center {
        justify-content: center;
        align-items: center;
      }
      .apexcharts-legend.apx-legend-position-bottom.apexcharts-align-right, .apexcharts-legend.apx-legend-position-top.apexcharts-align-right {
        justify-content: flex-end;
        align-items: flex-end;
      }
      .apexcharts-legend-series {
        cursor: pointer;
        line-height: normal;
        display: flex;
        align-items: center;
      }
      .apexcharts-legend-text {
        position: relative;
        font-size: 14px;
      }
      .apexcharts-legend-text *, .apexcharts-legend-marker * {
        pointer-events: none;
      }
      .apexcharts-legend-marker {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin-right: 1px;
      }

      .apexcharts-legend-series.apexcharts-no-click {
        cursor: auto;
      }
      .apexcharts-legend .apexcharts-hidden-zero-series, .apexcharts-legend .apexcharts-hidden-null-series {
        display: none !important;
      }
      .apexcharts-inactive-legend {
        opacity: 0.45;
      }

    </style></foreignObject><g class="apexcharts-inner apexcharts-graphical" transform="translate(0, 15)"><defs><clipPath id="gridRectMask1dglig09"><rect width="499" height="321" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="gridRectBarMask1dglig09"><rect width="503" height="325" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="gridRectMarkerMask1dglig09"><rect width="499" height="321" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMask1dglig09"></clipPath><clipPath id="nonForecastMask1dglig09"></clipPath><filter id="SvgjsFilter1047" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1046" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1049" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1048" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1051" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1050" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1053" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1052" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1055" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1054" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1057" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1056" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1059" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1058" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1061" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1060" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1063" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1062" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1065" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1064" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1067" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1066" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1069" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1068" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1071" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1070" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1073" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1072" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1075" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1074" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1077" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1076" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1079" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1078" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1081" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1080" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1083" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1082" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1085" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1084" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1087" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1086" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1089" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1088" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1091" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1090" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1093" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1092" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1095" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1094" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1097" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1096" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1099" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1098" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1101" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1100" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1103" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1102" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1105" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1104" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1107" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1106" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1109" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1108" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter><filter id="SvgjsFilter1111" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feColorMatrix id="SvgjsFeColorMatrix1110" result="brightness" in="SourceGraphic" type="matrix" values="
          2 0 0 0 0
          0 2 0 0 0
          0 0 2 0 0
          0 0 0 1 0
        "></feColorMatrix></filter></defs><g class="apexcharts-pie"><g transform="translate(0, 0) scale(1)"><circle r="117.49073170731708" cx="249.5" cy="160.5" fill="transparent"></circle><g class="apexcharts-slices"><g class="apexcharts-series apexcharts-pie-series" seriesName="Incorrectxaddress" rel="1" data:realIndex="0"><path d="M 249.5 7.914634146341456 A 152.58536585365854 152.58536585365854 0 0 1 360.7299447109354 56.0481293978079 L 335.14705742742024 80.07205963631208 A 117.49073170731708 117.49073170731708 0 0 0 249.5 43.00926829268292 L 249.5 7.914634146341456 z " fill="var(--bs-success)" fill-opacity="1" stroke="#ffffff" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-0" index="0" j="0" data:angle="46.8" data:startAngle="0" data:strokeWidth="0" data:value="13" data:pathOrig="M 249.5 7.914634146341456 A 152.58536585365854 152.58536585365854 0 0 1 360.7299447109354 56.0481293978079 L 335.14705742742024 80.07205963631208 A 117.49073170731708 117.49073170731708 0 0 0 249.5 43.00926829268292 L 249.5 7.914634146341456 z "></path></g><g class="apexcharts-series apexcharts-pie-series" seriesName="Weatherxconditions" rel="2" data:realIndex="1"><path d="M 360.7299447109354 56.0481293978079 A 152.58536585365854 152.58536585365854 0 0 1 353.9518706021921 271.7299447109354 L 329.9279403636879 246.14705742742024 A 117.49073170731708 117.49073170731708 0 0 0 335.14705742742024 80.07205963631208 L 360.7299447109354 56.0481293978079 z " fill="color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg))" fill-opacity="1" stroke="#ffffff" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-1" index="0" j="1" data:angle="90.00000000000001" data:startAngle="46.8" data:strokeWidth="0" data:value="25" data:pathOrig="M 360.7299447109354 56.0481293978079 A 152.58536585365854 152.58536585365854 0 0 1 353.9518706021921 271.7299447109354 L 329.9279403636879 246.14705742742024 A 117.49073170731708 117.49073170731708 0 0 0 335.14705742742024 80.07205963631208 L 360.7299447109354 56.0481293978079 z "></path></g><g class="apexcharts-series apexcharts-pie-series" seriesName="FederalxHolidays" rel="3" data:realIndex="2"><path d="M 353.9518706021921 271.7299447109354 A 152.58536585365854 152.58536585365854 0 0 1 159.812572235568 283.9441540685286 L 180.44068062138737 255.551998632767 A 117.49073170731708 117.49073170731708 0 0 0 329.9279403636879 246.14705742742024 L 353.9518706021921 271.7299447109354 z " fill="color-mix(in sRGB, var(--bs-success) 60%, var(--bs-paper-bg))" fill-opacity="1" stroke="#ffffff" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-2" index="0" j="2" data:angle="79.19999999999999" data:startAngle="136.8" data:strokeWidth="0" data:value="22" data:pathOrig="M 353.9518706021921 271.7299447109354 A 152.58536585365854 152.58536585365854 0 0 1 159.812572235568 283.9441540685286 L 180.44068062138737 255.551998632767 A 117.49073170731708 117.49073170731708 0 0 0 329.9279403636879 246.14705742742024 L 353.9518706021921 271.7299447109354 z "></path></g><g class="apexcharts-series apexcharts-pie-series" seriesName="Damagexduringxtransit" rel="4" data:realIndex="3"><path d="M 159.812572235568 283.9441540685286 A 152.58536585365854 152.58536585365854 0 0 1 249.47336882989012 7.91463647034945 L 249.47949399901538 43.00927008216908 A 117.49073170731708 117.49073170731708 0 0 0 180.44068062138737 255.551998632767 L 159.812572235568 283.9441540685286 z " fill="color-mix(in sRGB, var(--bs-success) 40%, var(--bs-paper-bg))" fill-opacity="1" stroke="#ffffff" stroke-opacity="1" stroke-linecap="butt" stroke-width="0" stroke-dasharray="0" class="apexcharts-pie-area apexcharts-donut-slice-3" index="0" j="3" data:angle="144" data:startAngle="216" data:strokeWidth="0" data:value="40" data:pathOrig="M 159.812572235568 283.9441540685286 A 152.58536585365854 152.58536585365854 0 0 1 249.47336882989012 7.91463647034945 L 249.47949399901538 43.00927008216908 A 117.49073170731708 117.49073170731708 0 0 0 180.44068062138737 255.551998632767 L 159.812572235568 283.9441540685286 z "></path></g></g></g><g class="apexcharts-datalabels-group" transform="translate(0, 0) scale(1)" style="opacity: 1;"><text x="249.5" y="190.5" text-anchor="middle" dominant-baseline="auto" font-size="15px" font-family="var(--bs-font-family-base)" font-weight="400" fill="var(--bs-body-color)" class="apexcharts-text apexcharts-datalabel-label" style="font-family: var(--bs-font-family-base);">AVG. Exceptions</text><text x="249.5" y="156.5" text-anchor="middle" dominant-baseline="auto" font-size="24px" font-family="var(--bs-font-family-base)" font-weight="500" fill="var(--bs-heading-color)" class="apexcharts-text apexcharts-datalabel-value" style="font-family: var(--bs-font-family-base);">30%</text></g></g><line x1="0" y1="0" x2="499" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line x1="0" y1="0" x2="499" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line></g><g class="apexcharts-datalabels-group" transform="translate(0, 0) scale(1)"></g></svg><div class="apexcharts-tooltip apexcharts-theme-false" style="left: 289.266px; top: 187px;"><div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-0" style="order: 1; display: none; background-color: color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg));"><span class="apexcharts-tooltip-marker" style="background-color: color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg)); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Weather conditions: </span><span class="apexcharts-tooltip-text-y-value">25</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-1 apexcharts-active" style="order: 2; display: flex; background-color: color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg));"><span class="apexcharts-tooltip-marker" style="background-color: color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg)); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Weather conditions: </span><span class="apexcharts-tooltip-text-y-value">25</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-2" style="order: 3; display: none; background-color: color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg));"><span class="apexcharts-tooltip-marker" style="background-color: color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg)); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Weather conditions: </span><span class="apexcharts-tooltip-text-y-value">25</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-3" style="order: 4; display: none; background-color: color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg));"><span class="apexcharts-tooltip-marker" style="background-color: color-mix(in sRGB, var(--bs-success) 80%, var(--bs-paper-bg)); display: none;"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">Weather conditions: </span><span class="apexcharts-tooltip-text-y-value">25</span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div></div></div>
        </div>
      </div>
    </div>
    <div class="col">
    </div>
    <div class="col">
      
    </div>
    </div>
    </div>
    <!-- <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h3 class="card-title text-primary">Welcome Back, {{ auth()->user()->name }}! 🎉</h3>
            <p class="mb-4">
              Stay informed and manage policies efficiently. Use this dashboard to track updates, review compliance, and access key insights.
            </p>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" 
              alt="Policy Management Overview"
              data-app-dark-img="illustrations/man-with-laptop-dark.png" 
              data-app-light-img="illustrations/man-with-laptop-light.png">
          </div>
        </div>
      </div>
    </div> -->

    <!-- @php
      $userRole = auth()->user()->role;
    @endphp -->

    <!-- Admin & Authorized Roles -->
    <!-- @if (in_array($userRole, [0, 1, 2, 6]))
      <div class="card mt-3">
        <div class="card-body">
          <div class="alert alert-success">
            <p>The Meetings are Available</p>
          </div>
        </div>
      </div>
    @else
      <div class="card mt-3">
        <div class="card-body">
          <div class="alert alert-warning">
            <p>The Meetings are not Available</p>
          </div>
        </div>
      </div> -->
      
      <!-- Meeting Notification -->
      <!-- @if(isset($meetingCreatedBySecretary) && $meetingCreatedBySecretary)
        <div class="card mt-3">
          <div class="card-body">
            <div class="alert alert-warning">
              <p><strong>Notification:</strong> A new meeting has been scheduled by the local secretary.</p>
            </div>
          </div>
        </div>
      @else -->
        <!-- No Meeting Notification -->
        <!-- <div class="card mt-3">
          <div class="card-body">
            <div class="alert alert-warning">
              <p><strong>Notification:</strong> No meeting set by Local Secretary</p>
            </div>
          </div>
        </div>
      @endif
    @endif -->

    <!-- Display Meetings as Announcements -->
    <!-- @if(isset($meetings) && $meetings->count() > 0)
      @foreach($meetings as $meeting)
        <div class="card mt-3">
          <div class="card-body">
            <div class="alert alert-info">
              <strong>Meeting Announcement</strong>
              <ul class="list-unstyled">
                <li><strong>Quarter:</strong> {{ $meeting->quarter }}</li>
                <li><strong>Year:</strong> {{ $meeting->year }}</li>
                <li><strong>Submission Start:</strong> {{ $meeting->submission_start }}</li>
                <li><strong>Submission End:</strong> {{ $meeting->submission_end }}</li>
                <li><strong>Meeting Date & Time:</strong> {{ $meeting->meeting_date_time }}</li>
                <li><strong>Meeting Type:</strong> {{ $meeting->meeting_type }}</li>
                <li><strong>Status:</strong> 
                  @if($meeting->status == 'scheduled')
                    <span class="badge bg-success">Scheduled</span>
                  @else
                    <span class="badge bg-danger">Not Scheduled</span>
                  @endif
                </li>
              </ul>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <div class="card mt-3">
        <div class="card-body">
          <div class="alert alert-info">
            <p>No meetings available at the moment.</p>
          </div>
        </div>
      </div>
    @endif -->
  </div>
</div>
@endsection
