@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <h2>Escaneie o QR Code para pagar</h2>
                    <img style="width: 300px" src="data:image/png;base64,{{ $qr_code_base64 }}" alt="QR Code para pagamento">

                    <p>Ou copie e cole este código no seu banco:</p>
                    <pre>{{ $qr_code }}</pre>

                    <p>Se preferir, você pode acessar o boleto <a href="{{ $ticket_url }}" target="_blank">clicando aqui</a>.</p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
