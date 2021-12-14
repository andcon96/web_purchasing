<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        IMI
    </title>
    <link rel="icon" type="image/gif/jpg" href="images/imi.png">
    <meta name="description">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('metahead')

    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">

    <link rel="stylesheet" href="{{url('vendors/bootstrap/dist/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('vendors/font-awesome/css/font-awesome.min.css')}}">
    <link href="{{ asset('vendors/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{url('vendors/themify-icons/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{url('vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{url('vendors/selectFX/css/cs-skin-elastic.css')}}">
    <link rel="stylesheet" href="{{url('vendors/jqvmap/dist/jqvmap.min.css')}}">
    <link rel="stylesheet" href="{{url('assets/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{url('assets/css/fontawal.css')}}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> <!--Icon ilang pas pke local-->
    <!--
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
    -->

    <link rel="stylesheet" href="{{url('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{url('assets/css/tablestyle.css')}}" >
    <link rel="stylesheet" href="{{url('assets/css/checkbox.css')}}" >


</head>

<body class="open">
    <!-- Side Navbar -->

    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="{{url('/')}}">Home</a>
                <a class="navbar-brand hidden" href="{{url('/')}}">H</a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <h3 class="menu-title">PO</h3>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-list-alt"></i>Purchase Order</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('pobrowse')}}">PO List</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('poappbrowse')}}">PO Approval</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('poreceipt')}}">Receipt Confirm</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('resetapprove')}}">PO Approval Utility</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('top10menu')}}">Last 10 RFQ and PO</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('poaudit')}}">PO Audit Trail</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('poappaudit')}}">PO App Audit Trail</a></li>
                        </ul>
                    </li>

                    <h3 class="menu-title">RFQ & RFP</h3>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-folder"></i>RFQ</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('inputrfq')}}">RFQ Data MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('rfqapprove')}}">RFQ Approval</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('rfqhist')}}">RFQ History</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('top10menu')}}">Top 10 PO & RFQ</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('rfqaudit')}}">RFQ Audit Trail</a></li>
                        </ul>
                    </li>

                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-folder"></i>RFP</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('inputrfp')}}">RFP Data MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('rfpapproval')}}">RFP Approval</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('rfphist')}}">RFP Audit Trail</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('rfpaudit')}}">RFP Audit App</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('rfputil')}}">RFP App Utility</a></li>
                        </ul>
                    </li>

                    <h3 class="menu-title">Supplier</h3>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Supplier</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('poconf')}}">PO Confirmation</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('sjmt')}}">Shipment Regis</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('sjmtbrw')}}">Shipment Browse</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('inputrfqsupp')}}">RFQ Feedback</a></li>
                        </ul>
                    </li>

                    <h3 class="menu-title">Inventory</h3>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-inbox"></i>Inventory</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('bstock')}}">Safety Stock</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('expitem')}}">Expired Inv</a></li>
                            <li><i class="menu-icon fa fa-paper-plane"></i><a href="{{url('noinv')}}">Slow Moving Inv</a></li>
                        </ul>
                    </li>
                    
                    <h3 class="menu-title">PP</h3>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>PP</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('purplanbrowse')}}">PO List</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('purplanview')}}">PO Create</a></li>
                        </ul>
                    </li>

                    <h3 class="menu-title">Setting</h3>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-gears"></i>Setting</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('usermaint')}}">User MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('deptmaint')}}">Departemen MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('alertcreate')}}">Alert MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('rolecreate')}}">Role MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('role')}}">Role Menu MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('site')}}">Site MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('itmsetup')}}">Item Inv Cont</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('itmmstr')}}">Item Inv Master</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('itemrfqset')}}">Item RFQ Cont</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('itmrfqmstr')}}">Item RFQ Master</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('itemconvmenu')}}">Item Conv MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('ummastermenu')}}">UM MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('suppinv')}}">Supp Inv MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('supprel')}}">Supp Item MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('rfqmaint')}}">RFQ RFP MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('rfpapprove')}}">RFP App MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('poappcontrol')}}">PO App MT</a></li>
                            <li><i class="menu-icon fa fa-book"></i><a href="{{url('thistinput')}}">Transaction Sync</a></li>
                        </ul>
                    </li>

                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside>

    <!-- Side Navbar -->

    <!-- Header & Menu -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                    <div class="header-left">
                        @yield('head-content')
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="{{asset('images/icon.jpg')}}" alt="User Avatar">
                        </a>
                        <div class="user-menu dropdown-menu">
                            <!--
                            <a class="nav-link bot" href="#"><i class="fa fa-user"></i> My Profile</a>

                            <a class="nav-link bot" href="#"><i class="fa fa-cog"></i> Settings</a>
                            -->
                            <a class="nav-link" data-toggle="modal" data-target="#logoutModal" style="cursor: pointer; padding:10px; border-top: 1px solid black;border-bottom: 1px solid black">
                              <i class="fa fa-power-off"></i>
                              Logout
                            </a>
                        </div>
                    </div>
                    <div class="vertical float-right"></div>
                    <div class="float-right" style="margin: 7px 15px 0 0;">Hello, {{Session::get('name')}} </div>
                </div>

            </div>

        </header><!-- /header -->
        
        <div class="content mt-3">

            @yield('content')
        
        </div> <!-- .content -->
    </div>


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

    <!-- Scripts -->
    <script src="{{url('assets/css/jquery-3.2.1.min.js')}}"></script>
    <script src="{{url('assets/css/jquery-ui.js')}}"></script> <!--Date Picker-->
    <script src="{{url('assets/css/select2.min.js')}}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!--Sweet Alert-->
    @include('sweetalert::alert')
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
    </script>
    
    <script src="{{url('vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{url('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{url('assets/js/main.js')}}"></script>   <!--buat side navbar-->
    

</body>

</html>
