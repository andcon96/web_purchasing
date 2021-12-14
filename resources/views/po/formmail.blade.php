@extends('layout.layout')
@section('content')
    <!-- Main Section -->
    <section class="main-section">
        <!-- Add Your Content Inside -->
        <div class="content">
            <!-- Remove This Before You Start -->
            <h1>Create Email</h1>
            @if(\Session::has('alert-failed'))
                <div class="alert alert-failed">
                    <div>{{Session::get('alert-failed')}}</div>
                </div>
            @endif
            @if(\Session::has('alert-success'))
                <div class="alert alert-success">
                    <div>{{Session::get('alert-success')}}</div>
                </div>
            @endif
            <form action="{{ url('/sendemailx') }}" method="post">
                {{ csrf_field() }}                
                <div class="form-group col-lg-6 col-xl-6">
                    <label for="judul">Subject:</label>
                    <input type="text" class="form-control" id="judul" name="judul" value=" Order {{ $nbr }}"/>
                </div>
                <div class="form-group col-lg-6 col-xl-6">
                    <label for="pesan">Pesan:</label>
                    <textarea class="form-control" rows="8" id="pesan" name="pesan"></textarea>
                </div>
                <div class="form-group col-lg-6 col-xl-6">
				    <input type="text" class="form-control" id="nbr" name="nbr" value={{ $nbr }} />
                    <button type="submit" class="btn btn-md btn-primary">Send Email</button>
                </div>
            </form>
        </div>
        <!-- /.content -->
    </section>
    <!-- /.main-section -->
@endsection