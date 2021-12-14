@extends('layout.layout')

@section('menu_name','Change Password')

@section('content')
    
    <!-- Page Heading -->
    <br>
    @if(session()->has('updated'))
        <div class="alert alert-success">
            {{ session()->get('updated') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif


<div class="panel-body">
    <form class="form-horizontal" method="POST" action="/userchange/changepass">
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

        <div class="form-group{{ $errors->has('oldpass') ? ' has-error' : '' }}">
            <label for="oldpass" class="col-md-4 control-label">Old Password</label>

            <div class="col-md-6">
                <input id="oldpass" type="password" class="form-control" name="oldpass" required>

                @if ($errors->has('oldpass'))
                    <span class="help-block">
                        <strong>{{ $errors->first('oldpass') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label for="password" class="col-md-4 control-label">Password</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control" name="password" required>

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('confpass') ? ' has-error' : '' }}">
            <label for="confpass" class="col-md-4 control-label">Confirm Password</label>

            <div class="col-md-6">
                <input id="confpass" type="password" class="form-control" name="confpass" required>

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
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