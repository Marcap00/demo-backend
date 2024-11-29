<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Braintree\Gateway;

class BraintreeController extends Controller
{
    /* Dichiaro la variabile d'istanza per il gateway */
    protected $gateway;

    /**
     * Configurato l'ambiente e le credenziali dell' API
     */
    public function __construct()
    {
        $this->gateway = new Gateway([
            'environment' => 'sandbox',
            'merchantId' => env('BRAINTREE_MERCHANT_ID'),
            'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
            'privateKey' => env('BRAINTREE_PRIVATE_KEY')
        ]);
    }

    /**
     * Generato un token per il client
     * Il token contiene tutte le informazione d'autorizzazione e configurazione che il client ha bisogno
     * per inizializzare l'SDK
     */
    public function token()
    {
        $clientToken = $this->gateway->clientToken()->generate();
        return response()->json([
            "success" => true,
            "clientToken" => $clientToken
        ]);
    }

    /**
     * Processo del checkout
     */
    public function checkout(Request $request)
    {
        /* Validazione dei dati proveniente dal front */
        $request->validate([
            "payment_method_nonce" => "required",
            "amount" => "required|numeric"
        ]);

        /* Assegnati i risultati della richiesta a delle variabili */
        $payment_nonce = $request->input("payment_method_nonce");
        $amount = $request->input("amount");

        /* Creata una transazione */
        $result = $this->gateway->transaction()->sale([
            'amount' => $amount, /* Totale importo della transazione */
            'paymentMethodNonce' => $payment_nonce, /* Token del metodo di pagamento */
            'options' => [
                'submitForSettlement' => True /* Il pagamento viene effettuato immediatamente */
            ]
        ]);

        if ($result->success) { /* Se il risultato è positivo restitutiamo true con la transazione */
            return response()->json([
                "success" => true,
                "transaction" => $result->transaction
            ]);
        } else { /* Altrimenti restitutiamo un messaggio d'errore */
            return response()->json([
                "success" => false,
                "transaction" => $result->message
            ]);
        }
    }
}
