<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Purchasing Module IMI</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{url('assets/lte/fontawesome-free/css/all.min.css')}}">
  <!-- IonIcons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{url('assets/lte/adminlte.min.css')}}">

  <!--Old CSS-->
  
  <link rel="stylesheet" href="{{url('vendors/bootstrap/dist/css/bootstrap.min.css')}}">

  <link rel="stylesheet" href="{{url('assets/css/bootstrap-select.min.css')}}">
  <link rel="stylesheet" href="{{url('assets/css/select2.min.css')}}">
    <!-- <link rel="stylesheet" href="{{url('assets/css/style.css')}}"> -->
    <link rel="stylesheet" href="{{url('assets/css/tablestyle.css')}}" >
    <link rel="stylesheet" href="{{url('assets/css/checkbox.css')}}" >
    <link rel="stylesheet" href="{{url('assets/css/jquery-ui.css')}}" >

</head>
<!--
`body` tag options:

  Apply one or more of the following classes to to the body tag
  to get the desired effect

  * sidebar-collapse
  * sidebar-mini
-->
<body class="hold-transition sidebar-mini sidebar-collapse">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      
      <!-- Notifications Dropdown Menu -->
      <a class="nav-link mr-2" data-toggle="dropdown" id="alertsDropdown" href="javascript:void(0)" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <span class="badge badge-primary">{{ auth()->user()->unreadNotifications->count() }}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="alertsDropdown" style="overflow-y:scroll; max-height: 250px; max-width:500px !important;">
        <a class="dropdown-item text-center small font-weight-bold mark-as-read-all" href="javascript:void(0)" data-id="{{ Session::get('userid') }}">Mark All as Read</a>
          <div class="dropdown-divider"></div>
        @forelse(Auth::User()->unreadNotifications as $notif)
          <a id="bell"  href="{{$notif->data['url']}}" data-id="{{$notif->id}}" data-link="{{$notif->data['url']}}" class="dropdown-item mark-as-read" style="text-wrap:break-word">
            <div class="media">
              <i class="fas fa-envelope mr-2"></i>
              <div class="media-body">
                <h3 class="dropdown-item-title">{{$notif->data['data']}}</h3>
                <p class="font-weight-bold">{{$notif->data['nbr']}}</p>
                <p class="">{{$notif->data['note']}}</p>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
        @empty
          <a class="dropdown-item d-flex align-items-center"  href="javascript:void(0)" >
            <div class="mr-3">
              <i class="fas fa-times"></i>
            </div>
            <div class="">
              <span class="font-weight-bold">No New Notification</span>
            </div>
          </a>
        @endforelse
      </div>

      <li>
          <a class="nav-link" role="button" data-toggle="dropdown">
            <i class="fas fa-user mr-2"></i>    
            Hello, {{Session::get('name')}} 
          </a>
          <div class="dropdown-menu dropdown=menu-lg dropdown-menu-right">
            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                <i class="fas fa-power-off mr-2"></i> Logout
            </a>
          </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/')}}" class="brand-link">
      <img src="{{url('images/imi.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">IMI Modules</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{url('/home')}}" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>
                Home
              </p>
            </a>
          </li>
          <li class="nav-header">TRANSAKSI</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-shopping-basket"></i>
              <p>
                Purchase Order
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('pobrowse')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PO List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('poappbrowse')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PO Approval</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('poreceipt')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PO Receipt Confrim</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('resetapprove')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PO Approval Utility</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('poaudit')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PO Audit Trail</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('poappaudit')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PO Approval Audit Trail</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-store"></i>
              <p>
                RFQ
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('inputrfq')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFQ Data Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfqapprove')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFQ Approval</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfqhist')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFQ History</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('top10menu')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Top 10 PO & RFQ</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfqaudit')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFQ Audit Trail</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-store-alt"></i>
              <p>
                RFP
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('inputrfp')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
                    RFP Data Maintenance
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfpapproval')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
                    RFP Approval
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfphist')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFP Audit Trail</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfpaudit')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFP Audit App</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfputil')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFP Approval Utility</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Supplier
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('poconf')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PO Confirmation</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('sjmt')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Shipment Regis</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('sjmtbrw')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Shipment Browse</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('inputrfqsupp')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFQ Feedback</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-inbox"></i>
              <p>
                Inventory
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('bstock')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Safety Stock Data</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('expitem')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Expired Inv</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('noinv')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Slow Moving Inv</p>
                </a>
              </li>
            </ul>
          </li> 
          
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-lightbulb "></i>
              <p>
                Purchase Plan
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('purplanbrowse')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PP List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('purplanview')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PP Create</p>
                </a>
              </li>
            </ul>
          </li> 


          <li class="nav-header">MASTER</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-wrench"></i>
              <p>
                Setting
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('usermaint')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>User Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('deptmaint')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Departemen Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('alertcreate')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Alert Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rolecreate')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Role Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('role')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Role Menu Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('site')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Site Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('itmsetup')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Item Inventory Control</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('itmmstr')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Item Inventory Master</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('itemrfqset')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Item RFQ Control</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('itmrfqmstr')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Item RFQ Master</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('itemconvmenu')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Item Conversion Maint.</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('ummastermenu')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>UM Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('suppinv')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Supplier Inventory Maint.</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('supprel')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Supplier Item Maint.</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfqmaint')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFQ RFP Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('rfpapprove')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>RFP Approval Maint.</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('poappcontrol')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>PO Approval Maintenance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('budgetingapprove')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Budget Approval Maint.</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('thistinput')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Transaction Sync</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('wsasmenu')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>WSA Qxtend Maintenance</p>
                </a>
              </li>

            </ul>
          </li>


        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          </div>
          <div class="col-sm-6">
            @yield('breadcrumbs')
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
           @yield('content') 
           
          <div id="loader" class="lds-dual-ring hidden overlay"></div>
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

</div>
<!-- ./wrapper -->


<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                          document.getElementById('logout-form').submit();">
                            {{ __('Logout') }} </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
        </div>
      </div>
    </div>
</div>

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{url('assets/css/jquery-3.2.1.min.js')}}"></script>
<script src="{{url('assets/css/jquery-ui.js')}}"></script> <!--Date Picker-->
<script src="{{url('vendors/popper.js/dist/umd/popper.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{url('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>

<script src="{{url('assets/js/bootstrap-select.min.js')}}"></script>
<!-- AdminLTE -->
<script src="{{url('assets/lte/adminlte.js')}}"></script>

<script src="{{url('assets/css/select2.min.js')}}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!--Sweet Alert-->
@include('sweetalert::alert')
<!-- Chart JS -->
<script src="{{ url('vendors/chart.js/dist/Chart.min.js') }}"></script>
<!-- Barcode Scanner -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>


@yield('scripts')

<script type="text/javascript">
    $(document).ready(function() {
        if(window.innerWidth <= 576){
            document.querySelector('body').classList.remove('open');
            }else{
            document.querySelector('body').classList.add('open');   
            }

        
        window.addEventListener("resize", myFunction);

        function myFunction() {
            if(window.innerWidth <= 576){
            document.querySelector('body').classList.remove('open');
            }else{
            document.querySelector('body').classList.add('open');   
            }
        }
    });

    /** add active class and stay opened when selected */
    var url = window.location;

    // for sidebar menu entirely but not cover treeview
    $('ul.nav-sidebar a').filter(function() {
        return this.href == url;
    }).addClass('active');

    // for treeview
    $('ul.nav-treeview a').filter(function() {
        return this.href == url;
    }).parentsUntil(".nav-sidebar > .nav-treeview").prev('a').addClass('active');


    // Notification
    function sendMarkRequest(id = null) {
          return $.ajax("{{ route('notifread') }}", {
              method: 'POST',
              data: {
                  "_token" : "{{csrf_token()}}" ,
                  "id" : id
              }
          });
    }
    
    function sendMarkAllRequest(id = null) {
          return $.ajax("{{ route('notifreadall') }}", {
                method: 'POST',
                data: {
                          "_token" : "{{csrf_token()}}",
                          "id" : id
                }
          });
    }

    $(function() {
          $('.mark-as-read').click(function() {
              let request = sendMarkRequest($(this).data('id'));
              request.done(() => {
                  $(this).parents('div.alert').remove();
              });
          });
          
          $('.mark-as-read-all').click(function(){
              let request = sendMarkAllRequest($(this).data('id'));
              request.done(() => {
                  $(this).parents('div.alert').remove();
                  window.location.reload();
              });
          });
    });


</script>

</body>
</html>
