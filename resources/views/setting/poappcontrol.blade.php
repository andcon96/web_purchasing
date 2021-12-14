@extends('layout.layout')

@section('menu_name','PO Approval Control')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">PO Approval Control</li>
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

  @include('setting.tablepo')

  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
     <div class="modal-dialog modal-xl " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-center" id="exampleModalLabel">Create Approval</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form class="form-horizontal" method="POST" id='update' action="/poappcontrol/edit" onkeydown="return event.key != 'Enter';"
        id="edit">
              {{ csrf_field() }}
              <div class="modal-body">
                  <input type="hidden" name="edit_id" id="edit_id">
                  <div class="form-group row">
                      <label for="app_name" class="col-md-4 col-form-label text-md-right">{{ __('Supplier') }}</label>
                      <div class="col-md-6">
                          <input id="app_name" type="text" class="form-control" name="app_name" value="" readonly>
                      </div>
                  </div>
                  <div class="form-group row">
                      <label for="reapprove" class="col-md-4 col-form-label text-md-right">{{ __('Need Reapprove') }}</label>
                      <div class="col-md-2">
                          <select class="form-control" name='reapprove' id='reapprove'>
                            <option value='Yes'>Yes</option>
                            <option valie='No'>No</option>>
                          </select>
                      </div>
                      <label for="int_rem" class="col-md-2 col-form-label text-md-right">{{ __('Interval Reminder') }}</label>
                      <div class="col-md-2">
                          <input id="int_rem" type="text" class="form-control" name="int_rem" placeholder="Days" value="" autocomplete="off">
                      </div>
                  </div>

                  <div class="form-group row mr-5 ml-5" >
                    <table id='suppTable' class='table order-list'>
                        <thead>
                            <tr>
                                <th style="width:30%">Approver</th>
                                <th style="width:15%">Min Amt</th>
                                <th style="width:15%">Max Amt</th>
                                <th style="width:30%">Alt Approver</th>
                                <th style="width:10%">Delete</th>
                            </tr>
                        </thead>
                        <tbody id='oldsupplier'>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <input type="button" class="btn btn-lg btn-block" 
                                    id="addrow" value="Add Row" style="background-color:#1234A5; color:white; font-size:16px" />
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Save</button>
                <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
                  <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                </button>
              </div>
        </form>

      </div>
    </div>
  </div>

@endsection


@section('scripts')


<script type="text/javascript">
    $(document).on('click','.editUser',function(){ // Click to only happen on announce links
     
     //alert('tst');
     var idsupp = $(this).data('idsupp');
     var suppcode = $(this).data('suppcode');
     var suppname = $(this).data('suppname');
     var dataint = $(this).data('int');
     var datareq = $(this).data('req');
     
     

     document.getElementById("edit_id").value = idsupp;
     document.getElementById("app_name").value = suppcode.concat(' - ',suppname);
     document.getElementById("int_rem").value = dataint;
     
     if(datareq == "Yes"){
        document.getElementById("reapprove").selectedIndex = "0";
     }else{
        document.getElementById("reapprove").selectedIndex = "1";
     }

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("searchpoapp") }}",
          data:{
            search : suppcode,
          },
          success:function(data){
            //alert(data);
            $('#oldsupplier').html(data);
          }
      });

     });


    $(document).ready(function () {
        var counter = 0;

        $("#addrow").on("click", function () {

            var newRow = $("<tr>");
            var cols = "";


            cols += '<td>';
            cols += '<select id="suppname[]" class="form-control suppname" name="suppname[]" required autofocus>';
            @foreach($names as $names)
            cols += '<option value="{{$names->id}}"> {{$names->name." - ".$names->role_type}} </option>';
            @endforeach
            cols += '</select>';
            cols += '</td>';

            cols += '<td data-title="min_amt[]"><input type="number" class="form-control form-control-sm minnbr" autocomplete="off" name="min_amt[]" style="height:37px" required/></td>';
            cols += '<td data-title="max_amt[]"><input type="number" class="form-control form-control-sm maxnbr" autocomplete="off" name="max_amt[]" style="height:37px" required/></td>';

            cols += '<td>';
            cols += '<select id="altname[]" class="form-control altname" name="altname[]" required autofocus>';
            @foreach($names1 as $names1)
            cols += '<option value="{{$names1->id}}"> {{$names1->name." - ".$names1->role_type}} </option>';
            @endforeach
            cols += '</select>';
            cols += '</td>';
            
            cols += '<td data-title="Action"><input type="button" class="ibtnDel btn btn-danger"  value="delete"></td>';
            cols += '</tr>'
            newRow.append(cols);
            $("table.order-list").append(newRow);
            counter++;
        });



        $("table.order-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            counter -= 1
        });

         $("#new").submit(function(e){
              if(counter == 0){
                Swal.fire({
                    icon: 'error',
                    text: 'Please Create A New Row Before Submiting',
                })
                e.preventDefault();
              }else{
                  document.getElementById('btnclose').style.display = 'none';
                  document.getElementById('btnconf').style.display = 'none';
                  document.getElementById('btnloading').style.display = '';
              }
          });

         $("#edit").submit(function(){
                document.getElementById('e_btnclose').style.display = 'none';
                document.getElementById('e_btnconf').style.display = 'none';
                document.getElementById('e_btnloading').style.display = '';
         });

         $("#delete").submit(function(){
                document.getElementById('d_btnclose').style.display = 'none';
                document.getElementById('d_btnconf').style.display = 'none';
                document.getElementById('d_btnloading').style.display = '';
         });

    });

    function formatNumber(num) {
      return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }
      
    $('tbody').delegate('.minnbr,.maxnbr','change',function(){
        var tr=$(this).parent().parent();
        var min=tr.find('.minnbr').val();
        var max=tr.find('.maxnbr').val();

        //var prevtr = tr.prev('tr');
        //var prevmax = prevtr.find('.maxnbr').val();
        
        // Test Digit
        var isnum = /^(\s*|\d+\.\d*|\d+)$/.test(min);
        var minlen = min.length;
        var isnummax = /^(\s*|\d+\.\d*|\d+)$/.test(max);
        var maxlen = max.length;
        // Parse Int untuk compare
        var intmin = parseInt(min);
        var intmax = parseInt(max);
        //var intprevmax = parseInt(prevmax);

        if(!isnum && minlen != 0){
            Swal.fire({
                icon: 'error',
                text: 'Min Must be Numeric',
            })
            tr.find('.minnbr').focus();
            //tr.find('.minnbr').css("border-color","red");
            return false;
        }else if(!isnummax && maxlen != 0){
            Swal.fire({
                icon: 'error',
                text: 'Max Must be Numeric',
            })
            tr.find('.maxnbr').focus();
            return false;
        }else if(intmin >= intmax && maxlen != 0 && minlen != 0){
            Swal.fire({
                icon: 'error',
                text: 'Max Value must be more than Min Value',
            })
            return false;
        }else{
            //tr.find('.minnbr').css("border-color","white");
            //tr.find('.maxnbr').css("border-color","white");
        }
    });

    $(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];

      //console.log(page);
      getData(page);

    });

    function getData(page){
      $.ajax({
          url: '/po/fetch_data_control?page='+ page,
          type: "get",
          datatype: "html" 
      }).done(function(data){
            console.log('Page = '+ page);

            $(".tag-container").empty().html(data);

      }).fail(function(jqXHR, ajaxOptions, thrownError){
                Swal.fire({
                    icon: 'error',
                    text: 'No response from server',
                })
      });
    }

</script>
@endsection