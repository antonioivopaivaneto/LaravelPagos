<label class="mt-3">Card details:</label>

<div class="row mb-3">
    <div class="col-md-5">
        <div id="cardNumber"></div>
    </div>
    <div class="col-md-2">
        <div id="securityCode"></div>
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-2">
        <div id="expirationDate"></div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-5">
        <input class="form-control" name="first_name" type="text" id="cardholderName" placeholder="Your Name">
    </div>
    <div class="col-md-5">
        <input class="form-control" type="email" id="cardholderEmail" placeholder="email@example.com" name="email">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-2">
        <select class="form-select" id="docType"></select>
    </div>
    <div class="col-md-3">
        <input class="form-control" name="document_number" type="text" id="docNumber" placeholder="Document">
    </div>
</div>

<div class="row mb-3">
    <div class="col">
        <small class="form-text text-muted" role="alert">Your payment will be converted to {{ strtoupper(config('services.mercadopago.base_currency')) }}</small>
    </div>
</div>

<div class="row mb-3">
    <div class="col">
        <small class="form-text text-danger" id="paymentErrors" role="alert"></small>
    </div>
</div>

<input type="hidden" id="cardNetwork" name="card_network">
<input type="hidden" id="cardToken" name="card_token">

@push('scripts')
<script src="https://sdk.mercadopago.com/js/v2"></script>

<script>
    const mp = new MercadoPago("{{ config('services.mercadopago.key') }}");

    const cardNumberElement = mp.fields.create('cardNumber', { placeholder: "Número do cartão" }).mount('cardNumber');
    const expirationDateElement = mp.fields.create('expirationDate', { placeholder: "MM/YY" }).mount('expirationDate');
    const securityCodeElement = mp.fields.create('securityCode', { placeholder: "Código de segurança" }).mount('securityCode');

    (async function getIdentificationTypes() {
        try {
            const identificationTypes = await mp.getIdentificationTypes();
            const identificationTypeElement = document.getElementById('docType');
            createSelectOptions(identificationTypeElement, identificationTypes);
        } catch (e) {
            console.error('Error getting identificationTypes: ', e);
        }
    })();

    function createSelectOptions(elem, options, labelsAndKeys = { label: "name", value: "id" }) {
        const { label, value } = labelsAndKeys;
        elem.options.length = 0;
        const tempOptions = document.createDocumentFragment();
        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option[value];
            opt.textContent = option[label];
            tempOptions.appendChild(opt);
        });
        elem.appendChild(tempOptions);
    }

    cardNumberElement.on('binChange', async function(data) {
        try {
            const { bin } = data;
            const { results } = await mp.getPaymentMethods({ bin });
            document.getElementById("cardNetwork").value = results[0].id;
        } catch (e) {
            console.error("Error getting payment methods: ", e);
        }
    });

    document.getElementById("paymentForm").addEventListener('submit', async function(e) {
        e.preventDefault();
    try {
        const token = await mp.fields.createCardToken({
            cardholderName: document.getElementById("cardholderName").value,
            identificationType: document.getElementById("docType").value,
            identificationNumber: document.getElementById("docNumber").value
        });

        document.getElementById("cardToken").value = token.id;
        this.submit();
    } catch (e) {
        document.getElementById("paymentErrors").textContent = e.message;
    }
    });
</script>
@endpush
