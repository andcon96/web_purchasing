@extends('layout.layout')

@section('menu_name','User Profile')

@section('content')
	
	<!-- Page Heading -->
  	<div class="d-sm-flex align-items-center justify-content-between mb-4">
    	<h1 class="h3 mb-0 text-gray-800">User Profile </h1>
  	</div>
    <br>
    @if(session()->has('updated'))
        <div class="alert alert-success">
            {{ session()->get('updated') }}
        </div>
    @endif

<div class="panel-body">
    <form class="form-horizontal" method="POST" action="/userprof/update">
        {{ csrf_field() }}

        <div class="col-md-6">
            <input id="id" type="hidden" class="form-control" name="id" value='{{ $users->id }}' required>
        </div>

        <div class="form-group{{ $errors->has('uname') ? ' has-error' : '' }}">
            <label for="uname" class="col-md-4 control-label">Username</label>
            <div class="col-md-6">
                <input id="uname" type="text" class="form-control" name="uname" value='{{ $users->username }}' required disabled>
            </div>
        </div>

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-4 control-label">Name</label>
            <div class="col-md-6">
                <input id="name" type="text" class="form-control" name="name" value="{{ $users->name }}" required autofocus>

                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        
         <div class="form-group{{ $errors->has('domain') ? ' has-error' : '' }}">
            <label for="domain" class="col-md-4 control-label">Domain</label>

            <div class="col-md-6">
                <input id="domain" type="text" class="form-control" name="domain" value="{{ $users->domain }}" required autofocus>

                @if ($errors->has('domain'))
                    <span class="help-block">
                        <strong>{{ $errors->first('domain') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" value="{{ $users->email }}" required>

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <br>
        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit" class="btn btn-primary">
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
	
   
@endsection