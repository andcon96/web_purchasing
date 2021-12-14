@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
    <li class="breadcrumb-item active"><a href="{{url('/')}}">Home</a></li>
</ol>
@endsection

@section('content')

    <style>
        #interactive.viewport {position: relative; width: 100%; height: auto; overflow: hidden; text-align: center;}
        #interactive.viewport > canvas, #interactive.viewport > video {max-width: 100%;width: 100%;}
        canvas.drawing, canvas.drawingBuffer {position: absolute; left: 0; top: 0;}
    </style>

    <!-- Flash Menu -->
    @if(session()->has('updated'))
          <div class="alert alert-success  alert-dismissible fade show"  role="alert">
              {{ session()->get('updated') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
    @endif

    @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
              {{ session()->get('error') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
    @endif

    <ul>    
    @if(count($errors) > 0)
         <div class = "alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
               @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
               @endforeach
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
            </ul>
         </div>
    @endif
    </ul>

    </select>
    <!--Box Nbr-->

    <!-- {{Session::get('menu_flag')}} -->
    @if(str_contains( Session::get('menu_flag'), 'HO01'))
    <div class="row">
        <div class="offset-xl-3 col-xl-3">
        <a href="/dash">
                <div class="card text-white bg-flat-color-1">
                    <div class="card-body pb-0">
                        <h4 class="mb-0">
                        </h4>
                        <p class="text-light" style="font-size: 14px;">Inventory Dashboard</p>

                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <canvas id="widgetChart1"></canvas>
                        </div>

                    </div>

                </div>
        </a>
        </div>
        <div class="col-xl-3">
        <a href="/dash2">
            <div class="card text-white bg-flat-color-2">
                <div class="card-body pb-0">
                    <h4 class="mb-0">
                    </h4>
                    <p class="text-light" style="font-size: 14px;">Purchasing Dashboard</p>

                    <div class="chart-wrapper px-0" style="height:70px;" height="70">
                        <canvas id="widgetChart2"></canvas>
                    </div>

                </div>
            </div>
        </a>
        </div>        
    </div>
    @endif

    <!-- Barcode Scanner -->

    <!-- <div class="row">
        <div class="col-lg-6">
            <div class="input-group">
                <input id="scanner_input" class="form-control" placeholder="Click the button to scan an EAN..." type="text" /> 
                <span class="input-group-btn"> 
                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#livestream_scanner">
                        <i class="fa fa-barcode"></i>
                    </button> 
                </span>
            </div>
        </div>
    </div>
    
    <div class="modal" id="livestream_scanner">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Barcode Scanner</h4>
                </div>
                <div class="modal-body" style="position: static">
                    <div id="interactive" class="viewport">
                    <video autoplay="true" preload="auto"></video>
                    </div>
                    <div class="error"></div>
                </div>
                <div class="modal-footer">
                    <label class="btn btn-default pull-left">
                        <i class="fa fa-camera"></i> Use camera app
                        <input type="file" accept="image/*;capture=camera" capture="camera" class="hidden" />
                    </label>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Google Upload -->
    <form action="/uploadGoogle" method="post" class="mt-3" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group row">
            <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Upload') }}</label>
            <div class="col-md-7 col-lg-8 input-file-container">  
                <input type="hidden" id="filename" name="filename" value="">
                <input class="input-file" id="file" type="file" name="file">
                <label tabindex="0" for="file" class="btn btn-info input-file-trigger" style="font-size:16px;">Select a file</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right"></label>
            <div class="col-md-7 col-lg-8 input-file-container">  
                <p class="file-return"></p>
            </div>
        </div>
        <div class="form-group row">
            <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right"></label>
            <div class="col-md-7 col-lg-8 input-file-container">  
                <button type="submit" class="btn btn-success bt-action" id="btnconf">Save</button>
            </div>
        </div>

    </form>

    <!-- Whatsapp Send Message -->
    <!-- <form action="/sendwa" method="post" class="mt-3" >
        {{csrf_field()}}
        <div class="form-group row">
            <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right">No Telp</label>
            <div class="col-md-3 col-lg-3 input-file-container">  
                <input id="nowa" class="form-control" name = 'nowa' type="text" /> 
            </div>
        </div>
        <div class="form-group row">
            <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right">Text Message</label>
            <div class="col-md-7 col-lg-8 input-file-container">  
                <input id="isiwa" class="form-control" name = 'isiwa' type="text" /> 
            </div>
        </div>
        <div class="form-group row">
            <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right"></label>
            <div class="col-md-7 col-lg-8 input-file-container">  
                <button type="submit" class="btn btn-success bt-action" id="btnconf">Send Text</button>
            </div>
        </div>

    </form> -->

    <!-- Load PO -->
    <form action="/loadpo" method="post">
        {{csrf_field()}}
        <div class="form-group row">
            <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right"></label>
            <div class="col-md-7 col-lg-8 input-file-container">  
                <input type="submit" class="btn btn-success bt-action" value="Load PO">
            </div>
        </div>
    </form>

@endsection


@section('scripts')
    <script type="text/javascript">
    $(function() {
        // Create the QuaggaJS config object for the live stream
        var liveStreamConfig = {
                inputStream: {
                    type : "LiveStream",
                    constraints: {
                        width: {min: 640},
                        height: {min: 480},
                        aspectRatio: {min: 1, max: 100},
                        facingMode: "environment" // or "user" for the front camera
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: (navigator.hardwareConcurrency ? navigator.hardwareConcurrency : 4),
                decoder: {
                    "readers":[
                        {"format":"ean_reader","config":{}}
                    ]
                },
                locate: true
            };
        // The fallback to the file API requires a different inputStream option. 
        // The rest is the same 
        var fileConfig = $.extend(
                {}, 
                liveStreamConfig,
                {
                    inputStream: {
                        size: 800
                    }
                }
            );
        // Start the live stream scanner when the modal opens
        $('#livestream_scanner').on('shown.bs.modal', function (e) {
            Quagga.init(
                liveStreamConfig, 
                function(err) {
                    if (err) {
                        $('#livestream_scanner .modal-body .error').html('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle"></i> '+err.name+'</strong>: '+err.message+'</div>');
                        Quagga.stop();
                        return;
                    }
                    Quagga.start();
                }
            );
        });
        
        // Make sure, QuaggaJS draws frames an lines around possible 
        // barcodes on the live stream
        Quagga.onProcessed(function(result) {
            var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;
    
            if (result) {
                if (result.boxes) {
                    drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                    result.boxes.filter(function (box) {
                        return box !== result.box;
                    }).forEach(function (box) {
                        Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                    });
                }
    
                if (result.box) {
                    Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
                }
    
                if (result.codeResult && result.codeResult.code) {
                    Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
                }
            }
        });
        
        // Once a barcode had been read successfully, stop quagga and 
        // close the modal after a second to let the user notice where 
        // the barcode had actually been found.
        Quagga.onDetected(function(result) {    		
            if (result.codeResult.code){
                $('#scanner_input').val(result.codeResult.code);
                Quagga.stop();	
                setTimeout(function(){ $('#livestream_scanner').modal('hide'); }, 1000);			
            }
        });
        
        // Stop quagga in any case, when the modal is closed
        $('#livestream_scanner').on('hide.bs.modal', function(){
            if (Quagga){
                Quagga.stop();	
            }
        });
        
        // Call Quagga.decodeSingle() for every file selected in the 
        // file input
        $("#livestream_scanner input:file").on("change", function(e) {
            if (e.target.files && e.target.files.length) {
                Quagga.decodeSingle($.extend({}, fileConfig, {src: URL.createObjectURL(e.target.files[0])}), function(result) {alert(result.codeResult.code);});
            }
        });
    });

    document.querySelector("html").classList.add('js');

    var fileInput  = document.querySelector( ".input-file" ),  
        button     = document.querySelector( ".input-file-trigger" ),
        the_return = document.querySelector(".file-return");
          
    button.addEventListener( "keydown", function( event ) {  
        if ( event.keyCode == 13 || event.keyCode == 32 ) {  
            fileInput.focus();  
        }  
    });
    button.addEventListener( "click", function( event ) {
       fileInput.focus();
       return false;
    });  
    fileInput.addEventListener( "change", function( event ) {  
        the_return.innerHTML = this.value;  
    });  
    </script>
    <script src="vendors/chart.js/dist/Chart.bundle.min.js"></script>


    

    <!-- <script type="text/javascript">
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: '',
        showConfirmButton: false,
        timer: 1500,
        heightAuto: false,
        })
    </script> -->

    <!-- <script tpye="text/javascript">
    $('#t_test').select2({
        minimumInputLength: 3,
        ajax: {
            url: '/ajax_item',
            dataType: 'json',
        },
    });
    </script> -->
        

    <!-- <script src="assets/js/dashboard.js"></script> -->
    <script src="assets/js/widgets.js"></script>
@endsection