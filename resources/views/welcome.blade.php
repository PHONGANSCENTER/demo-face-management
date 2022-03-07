@extends('layouts.index')
@section('content')

@if($product)
<div class="card">
    <img src="data:image/png;base64,{{$product->photo}}" class="card-img-top border border-success " alt="...">
    <div class="card-body">
        <h4 class="card-title text-center text-success">Successfuly verified</h4>
        <p class="card-text text-center">{{$product->created_at}}</p>
        <div class="row g-3">
            <div class="col d-flex col-md">
                @foreach($users as $user)
                @if($user->name == $product->name)
                    <img src="data:image/png;base64,{{$user->picture}}" class="card-img-top img-circle border"  width="50%"  alt="Image Register">
                @endif
                @endforeach
            </div>
            <div class="col col-md">
                <p class="card-text">FaceID: {{$product->name}}</p>
                <p class="card-text">Accuracy: {{$product->price * 100}} %</p>
                <p class="card-text">Verify by Face </p>
            </div>
        </div>
    </div>
    <div class="text-center mb-3" hidden>
        <form action="/" method="get" style="display: inline-block;" enctype="multipart/form-data">
            <input type="number" value="{{$product->id}}" name="id" hidden>
            <input type="submit" class="btn btn-xs btn-danger" value="report">
        </form>
    </div>
</div>
<hr>
<section>
    @foreach($products->reverse() as $key => $st)
    @if($st->reported == TRUE)
    <div class="fluid-container p-2">
        <div class="row no-gutters">
            <div class="col-md col">
                <div class="menu-image h-100 d-flex align-items-start">
                    <img src="data:image/png;base64,{{$st->photo}}" width="90%" class="img-fluid border border-info" alt="menu image">
                </div>
            </div>
            <div class="col-md col">
                <p>{{$st->name}} ({{$st->price * 100}}%)</p>
                <p>{{$st->created_at}}</p>
                <form action="/" method="get" style="display: inline-block;" enctype="multipart/form-data">
                    <input type="number" value="{{$st->id}}" name="id" hidden>
                    <input type="submit" class="btn btn-sm btn-xs btn-danger" value="report">
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</section>
@endif

@endsection
@section('scripts')
@parent
<script>
    // Auto reload face after 5 seconds
    setTimeout(function() {
        window.location = window.location;
    }, 30000);
</script>
@endsection