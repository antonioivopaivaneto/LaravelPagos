@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">

                   <form action="{{route('pay')}}" method="POST" id="paymentForm">
                    @csrf

                    <div class="row">
                        <div class="col-auto">
                            <label for="">How much you eant to pay?</label>
                            <input required type="number" name="value" min="5" step="0.01" class="form-control" value="{{mt_rand(500,10000) / 100}}">
                            <small class="form-text text-muted">use values with decimal point values</small>
                        </div>
                        <div class="col-auto">
                            <label for="">Currency</label>
                            <select required name="currency" id="" class="form-select">
                                @foreach ($currencies as $currency )
                                <option value="{{$currency->iso}}">{{$currency->iso}}</option>
                                @endforeach
                            </select>
                    </div>
                    </div>
                    <div class="row mt-3" >
                        <div class="col">
                        <label>select payment platform</label>
                        <div class="form-group" id="toggler">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                @foreach ($paymentPlatforms as $paymentPlatform )
                                <label data-bs-target="#{{$paymentPlatform->name}}Collapse" data-bs-toggle="collapse" class="btn btn-outline-secondary rounded m-2 p-1">
                                    <input type="radio" name="payment_platform" value="{{$paymentPlatform->id}}" required>
                                    <img class="img-thumbnail" src="{{asset($paymentPlatform->image)}}" alt="">
                                </label>
                                @endforeach
                            </div>
                            @foreach ($paymentPlatforms as $paymentPlatform )
                            <div class="collapse" data-bs-parent="#toggler" id="{{$paymentPlatform->name}}Collapse">
                                @includeIf('components.'. strtolower($paymentPlatform->name).'-collapse')
                            </div>

                            @endforeach
                        </div>
                    </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit"
                        id="payButton" class="btn btn-primary">Pay</button>
                    </div>


                   </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
