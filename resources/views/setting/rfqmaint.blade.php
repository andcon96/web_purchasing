@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">RFQ RFP Maintenance</li>
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
    <div class="table-responsive col-lg-12 col-md-12">
              <form action="{{route('rfqmaint.update', 'test')}}" method="post">
            {{ method_field('patch') }}
            {{ csrf_field() }}

            <div class="modal-header">
            </div>

            <div class="modal-body">

                <?php 
                  if($alert == null){
                      $datavalue = "No";
                      $datavaluepr = "No";
                      $datavaluerfqprefix = "";
                      $datavaluerfpprefix = ""; //Tommy 13/10/2020
                      $datavaluepoprefix = "";
                      $datavalueprprefix = "";
                      $datavaluerfqnbr = "";
                      $datavaluerfpnbr = ""; //Tommy 13/10/2020
                      $datavalueponbr = "";
                      $datavalueprnbr = "";
                  }else{
                      $datavalue = $alert->xrfq_po;
                      $datavaluepr = $alert->xrfq_pr;
                      $datavaluerfqprefix = $alert->xrfq_prefix;
                      $datavaluerfpprefix = $alert->xrfq_rfp_prefix; //Tommy 13/10/2020
                      $datavaluepoprefix = $alert->xrfq_po_prefix;
                      $datavalueprprefix = $alert->xrfq_pr_prefix;
                      $datavaluerfqnbr = $alert->xrfq_nbr;
                      $datavaluerfpnbr = $alert->xrfq_rfp_nbr; //Tommy 13/10/2020
                      $datavalueponbr = $alert->xrfq_po_nbr;
                      $datavalueprnbr = $alert->xrfq_pr_nbr;
                  }
                ?>

                <input type="hidden" name="cbpoallowed" id="cbpoallowed" value="<?php echo $datavalue; ?>">
                <input type="hidden" name="cbprallowed" id="cbprallowed" value="<?php echo $datavaluepr; ?>">

                <div class="form-group row">
                    <label for="prefix" class="col-md-3 col-form-label text-md-right">{{ __('Prefix RFQ') }}</label>
                    <div class="col-md-7">
                        <input id="prefix" type="text" class="form-control" name="prefix" maxlength="2" autocomplete="off"
                        value="<?php echo $datavaluerfqprefix;?>" autofocus> 
                    </div>
                </div>
                <div class="form-group row">
                    <label for="curnumber" class="col-md-3 col-form-label text-md-right">{{ __('RFQ No.') }}</label>
                    <div class="col-md-7">
                        <input id="curnumber" type="text" class="form-control" autocomplete="off" name="curnumber" value="<?php echo $datavaluerfqnbr; ?>"  maxlength="6">
                        <span id="errorcur" style="color:red"></span>
                    </div>
                </div>

                <!-- Input RFP prefix dan number -->
                <div class="form-group row">
                    <label for="prefix_rfp" class="col-md-3 col-form-label text-md-right">{{ __('Prefix RFP') }}</label>
                    <div class="col-md-7">
                        <input id="prefix_rfp" type="text" class="form-control" name="prefix_rfp" maxlength="2" autocomplete="off"
                        value="<?php echo $datavaluerfpprefix; ?>" autofocus> 
                    </div>
                </div>
                <div class="form-group row">
                    <label for="rfpnbr" class="col-md-3 col-form-label text-md-right">{{ __('RFP No.') }}</label>
                    <div class="col-md-7">
                        <input id="rfpnbr" type="text" class="form-control" autocomplete="off" name="rfpnbr" value="<?php echo $datavaluerfpnbr; ?>"  maxlength="6">
                        <span id="errorrfp" style="color:red"></span>
                    </div>
                </div>
                <!------------------------------------>


                <div class="form-group row">
                    <label for="poallowed" class="col-md-6 col-form-label text-md-right">{{ __('PO Allowed') }}</label>
                    <label class="switch" for="poallowed" style="margin-left:10px">
                          <input type="checkbox" id="poallowed" name="poallowed" value="Yes" />
                          <div class="slider round"></div>
                    </label>    
                </div>

                <div class="form-group row" id='rowprefix'>
                    <label for="prefix_po" class="col-md-3 col-form-label text-md-right">{{ __('Prefix PO') }}</label>
                    <div class="col-md-7">
                        <input id="prefix_po" type="text" class="form-control" name="prefix_po" maxlength="2" autocomplete="off"
                        value="<?php echo $datavaluepoprefix; ?>">
                    </div>
                </div>
                <div class="form-group row" id='rownbrpo'>
                    <label for="ponbr" class="col-md-3 col-form-label text-md-right">{{ __('PO No.') }}</label>
                    <div class="col-md-7">
                        <input id="ponbr" type="text" class="form-control" name="ponbr" value="<?php echo $datavalueponbr; ?>"  maxlength="6" autofocus autocomplete="off">
                        <span id="errorpo" style="color:red"></span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="prallowed" class="col-md-6 col-form-label text-md-right">{{ __('PR Allowed') }}</label>
                    <label class="switch" for="prallowed" style="margin-left:10px">
                          <input type="checkbox" id="prallowed" name="prallowed" value="Yes" />
                          <div class="slider round"></div>
                    </label>   
                </div>

                <div class="form-group row" id='rowprefixpr'>
                    <label for="prefix_pr" class="col-md-3 col-form-label text-md-right">{{ __('Prefix PR') }}</label>
                    <div class="col-md-7">
                        <input id="prefix_pr" type="text" class="form-control" name="prefix_pr" maxlength="2" autocomplete="off"
                        value="<?php echo $datavalueprprefix; ?>">
                    </div>
                </div>
                <div class="form-group row" id='rownbrpr'>
                    <label for="prnbr" class="col-md-3 col-form-label text-md-right">{{ __('PR No.') }}</label>
                    <div class="col-md-7">
                        <input id="prnbr" type="text" class="form-control" name="prnbr" value="<?php echo $datavalueprnbr; ?>"  maxlength="6" autofocus autocomplete="off">
                        <span id="errorpr" style="color:red"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success bt-action" id="btnconf">Save</button>
              <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                  <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

            </div>

@endsection

@section('scripts')

<script type="text/javascript">

    $( document ).ready(function() {
        var po = document.getElementById('cbpoallowed').value;
        var pr = document.getElementById('cbprallowed').value;

        if(po == "Yes"){
            document.getElementById("poallowed").checked = true;  
            document.getElementById("rowprefix").style.display = '';
            document.getElementById("rownbrpo").style.display = '';
         }else{
            document.getElementById("poallowed").checked = false;
            document.getElementById("rowprefix").style.display = 'none';
            document.getElementById("rownbrpo").style.display = 'none';
         }

         if(pr == "Yes"){
            document.getElementById("prallowed").checked = true;
            document.getElementById("rowprefixpr").style.display = '';
            document.getElementById("rownbrpr").style.display = '';  
         }else{
            document.getElementById("prallowed").checked = false;
            document.getElementById("rowprefixpr").style.display = 'none';
            document.getElementById("rownbrpr").style.display = 'none';
         }    

          $('form').on("submit",function(){
              document.getElementById('btnclose').style.display = 'none';
              document.getElementById('btnconf').style.display = 'none';
              document.getElementById('btnloading').style.display = '';
          });
    });
    
    $(document).on('blur','#curnumber',function(){ // Click to only happen on announce links
       
        //alert('tst');
        var isi = document.getElementById("curnumber").value;
        var nbr = isi.length;

        var isnum = /^\d+$/.test(isi);

        if(nbr > 6){
          document.getElementById("errorcur").innerHTML = 'Current Number Must Be 6 Digits';
          document.getElementById("curnumber").focus();
          return false;
        }else if(nbr < 6){
          document.getElementById("errorcur").innerHTML = 'Current Number Must Be 6 Digits';
          document.getElementById("curnumber").focus();

        }else if(!isnum){
          document.getElementById("errorcur").innerHTML = 'Current Number Must Be 6 Digits';
          document.getElementById("curnumber").focus();
          return false;
        }else{
          document.getElementById("errorcur").innerHTML = ''; 
        }
    });

    //Tommy 13/10/2020
    $(document).on('blur', '#rfpnbr', function(){

        var isi = document.getElementById("rfpnbr").value;
        var nbr = isi.length;

        var isnum = /^\d+$/.test(isi);

        if(nbr > 6){
          document.getElementById('errorrfp').innerHTML = 'Current Number Must Be 6 Digits';
          document.getElementById('rfpnbr').focus();
          return false;
        }else if(nbr < 6){
          document.getElementById('errorrfp').innerHTML = 'Current Number Must Be 6 Digits';
          document.getElementById('rfpnbr').focus();
          return false;
        }else if(!isnum){
          document.getElementById('errorrfp').innerHTML = 'Current Number Must Be 6 Digits';
          document.getElementById('rfpnbr').focus();
          return false;
        }else{
          document.getElementById('errorrfp').innerHTML = '';
        }
    });

    $(document).on('change','#poallowed',function(){

        if(document.getElementById("poallowed").checked)
        {
           document.getElementById("rowprefix").style.display = '';
           document.getElementById("rownbrpo").style.display = '';
           document.getElementById("cbpoallowed").value = 'Yes'
        }else{
           document.getElementById("rowprefix").style.display = 'none';
           document.getElementById("rownbrpo").style.display = 'none';
           document.getElementById("cbpoallowed").value = 'No'
        }
    });

    $(document).on('blur','#ponbr',function(){

        var isi = document.getElementById("ponbr").value;
        var nbr = isi.length;

        var isnum = /^\d+$/.test(isi);

        if(nbr > 6){
          document.getElementById("errorpo").innerHTML = 'PO Number Must Be 6 Digits';
          document.getElementById("ponbr").focus();
          return false;
        }else if(nbr < 6){
          document.getElementById("errorpo").innerHTML = 'PO Number Must Be 6 Digits';
          document.getElementById("ponbr").focus();
          return false;
        }else if(!isnum){
          document.getElementById("errorpo").innerHTML = 'PO Number Must Be 6 Digits';
          document.getElementById("ponbr").focus();
          return false;
        }else{
          document.getElementById("errorpo").innerHTML = ''; 
        }
    });

    $(document).on('change','#prallowed',function(){
        if(document.getElementById("prallowed").checked)
        {
           document.getElementById("rowprefixpr").style.display = '';
           document.getElementById("rownbrpr").style.display = '';
           document.getElementById("cbprallowed").value = 'Yes'
        }else{
           document.getElementById("rowprefixpr").style.display = 'none';
           document.getElementById("rownbrpr").style.display = 'none';
           document.getElementById("cbprallowed").value = 'No'
        }
    });

    $(document).on('blur','#prnbr',function(){

        var isi = document.getElementById("prnbr").value;
        var nbr = isi.length;

        var isnum = /^\d+$/.test(isi);

        if(nbr > 6){
          document.getElementById("errorpr").innerHTML = 'PR Number Must Be 6 Digits';
          document.getElementById("ponbr").focus();
          return false;
        }else if(nbr < 6){
          document.getElementById("errorpr").innerHTML = 'PR Number Must Be 6 Digits';
          document.getElementById("ponbr").focus();
          return false;
        }else if(!isnum){
          document.getElementById("errorpr").innerHTML = 'PR Number Must Be 6 Digits';
          document.getElementById("ponbr").focus();
          return false;
        }else{
          document.getElementById("errorpr").innerHTML = ''; 
        }
    });

</script>
@endsection