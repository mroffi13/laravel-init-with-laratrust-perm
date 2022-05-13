<div style="width: 98%" class="text-left">
    @foreach ($errors as $name => $error)
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="">{{$name}}</label>
                <p>{{$error[0]}}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>