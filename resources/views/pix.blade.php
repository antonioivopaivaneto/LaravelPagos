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
                    <h1>Pagar com pix</h1>
                    <input type="hidden" name="type" value="pix">
                    <input type="hidden" name="payment_platform" value="3">

                    <div class="text-center mt-3">
                        <button type="submit"
                        id="payButton" class="btn btn-primary">Gerar qrcode</button>
                    </div>


                   </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
