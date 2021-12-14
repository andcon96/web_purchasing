@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Budget Approval Maintenance</li>
</ol>
@endsection


@section('content')


  <!-- Page Heading -->
  @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
          {{ session()->get('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
  @endif

  @if(session()->has('updated'))
      <div class="alert alert-success  alert-dismissible fade show"  role="alert">
          {{ session()->get('updated') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
  @endif
    
    <form id="form_approverbudget" action="/inputbudgetappr" method="post" style="background-color:white; padding: 5%;">
        {{ csrf_field() }}

        <?php 
                    if($datas == null){
                        $dataappr = "";
                        $dataaltappr = "";

                    }else{
                        $dataappr = $datas->approver_budget;
                        $dataaltappr=  $datas->alt_approver_budget;
                    }
        ?>

        <div class="form-group row">
            <label for="appr_budget" class="col-md-3 col-form-label text-md-left">{{ __('Approver Budget') }}</label>
            <div class="col-md-4">
                <select id="appr_budget" class="form-control" name="appr_budget" autocomplete="off" value="" autofocus>
                    <option> -- Select Approver -- </option>
                    @foreach($users as $showappr)

                        @if($dataappr == $showappr->id)
                        <option value="{{$showappr->id}}" selected>{{$showappr->username}} -- {{$showappr->name}}</option>
                        @else
                        <option value="{{$showappr->id}}">{{$showappr->username}} -- {{$showappr->name}}</option>
                        @endif
                        
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="alt_appr_budget" class="col-md-3 col-form-label text-md-left">{{ __('Alt. Approver Budget') }}</label>
            <div class="col-md-4">
                <select id="alt_appr_budget" class="form-control" autocomplete="off" name="alt_appr_budget" value="" autofocus>
                    <option> -- Select Alt. Approver -- </option>
                    @foreach($users as $showaltappr)
                        @if($dataaltappr == $showaltappr->id)
                            <option value="{{$showaltappr->id}}" selected>{{$showaltappr->username}} -- {{$showaltappr->name}}</option>
                        @else
                            <option value="{{$showaltappr->id}}">{{$showaltappr->username}} -- {{$showaltappr->name}}</option>
                        @endif

                        
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-3"></div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success bt-action" id="btnconf">Save</button>
                <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                    <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                </button>
            </div>

        </div>
        
        

    </form>

@endsection

@section('scripts')

<script type="text/javascript">
$(document).ready(function(){
    // alert('masuk');
    var appr = "<?php echo $dataappr ?>";

    var altappr = "<?php echo $dataaltappr ?>";

    console.log(appr);
    console.log(altappr);

    $('#btnconf').on('click', function(event){
        // alert('masuk');
        
        var appr = document.getElementById('appr_budget').value;
        var altappr = document.getElementById('alt_appr_budget').value;

        console.log(appr);
        console.log(altappr);

        if(appr == altappr || altappr == appr){
            swal.fire({
                        // position: 'top-end',
                        icon: 'error',
                        title: 'Approver cannot be the same with alt. approver',
                        // toast: true,
                        showConfirmButton: true,
                        timer: 3000,
            })
            event.preventDefault();
        }else{
            document.getElementById("form_approverbudget").submit();
        }
    });




});

</script>
@endsection