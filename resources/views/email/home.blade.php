@extends('layout.layout')


@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

  <style type="text/css">
    @media (min-width: 450px) {
      .chart-pie {
        height: 150px !important;
        padding-bottom: 0px !important;
        padding-top: 10px !important;
      }

      .chart{
        width: auto;
        height: 300px;
      }
	  
  	  .empat {
        font-size: 30px;   
        color:darkblue;
        margin-bottom: 20px;
      }

      .card-header{
        height: 10px !important;
      }

      .card-body{
        padding: 5px !important;
      }

      .header-text{
        font-size: 12px !important;
      }

      p{
        font-size: 14px !important;
        font-weight: bold;
        color: black;
      }
	  
	  bag2{
		  margin-top:150px;
	  }
    }
	
	@media (min-width: 1200px){
	  .menu{
		margin-top : 100px;
	  }
	}
	
	
    p{
      font-weight: bold;
      color: black;
    }
    .fa-refresh:hover{
      color: red !important;
      cursor: pointer;
    }
  </style>

  <!--Error Message-->
  <div class="row">
        @if(session('errors'))
          <div class="alert alert-danger">
            @foreach($errors as $error)
              <li>{{ $error }}</li>
            @endforeach
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger" id="getError">
            {{ session()->get('error') }}
          </div>
        @endif

        @if(session('updated'))
          <div class="alert alert-success" id="getUpdated">
            {{ session()->get('updated') }}
          </div>
        @endif
  </div>
  <!--
  <form method="post" action="/loadpo">
      @csrf
      <button type="submit">Load PO</button>
  </form>
  
  <form method="post" action="/loadinv">
      @csrf
      <button type="submit">Load Inventory</button>
  </form>
  <form method="post" action="/loaditm">
      @csrf
      <button type="submit">Load Item Master</button>
  </form>
	-->
   <div class="row text-center menu">
  		<div class="col-xl-6 col-md-12">       		
  			 <img  src="/img/bar.gif"/ class="chart">
         <h1><a href="{{url('/dash')}}" class="empat"><b>Inventory Dashboard</b></a></h1>
  		</div>
      <div class="col-xl-6 col-md-12 offset-0 bag2">       		
  			 <img  src="/img/piex.gif"/ class="chart">
         <h1><a href="{{url('/dash2')}}" class="empat"><b>Purchasing Dashboard</b></a></h1>
  		</div>      
	</div>

@endsection


@section('footer')
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; PT Intelegensia Mustaka Indonesia 2020</span>
          </div>
        </div>
      </footer>
@endsection

