<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Braintree\Gateway;

class BraintreeController extends Controller
{
    /* Dichiarazione la variabile d'istanza per il gateway */
    protected $gateway;

    /**
     * Configurazione dell'ambiente e delle credenziali dell' API
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
     * Generazione del token per il client
     * Il token contiene tutte le informazioni d'autorizzazione e configurazione di cui il client ha bisogno
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
     * Funzione del checkout del pagamento
     */
    public function checkout(Request $request)
    {
        /* Validazione dei dati provenienti dal front-end */
        $request->validate([
            "payment_method_nonce" => "required",
            "amount" => "required|numeric"
        ]);

        /* Assegnazione dei dati della richiesta a delle variabili */
        $payment_nonce = $request->input("payment_method_nonce");
        $amount = $request->input("amount");

        /* Creazione della transazione */
        $result = $this->gateway->transaction()->sale([
            'amount' => $amount, /* Totale importo della transazione */
            'paymentMethodNonce' => $payment_nonce, /* Token del metodo di pagamento */
            'options' => [
                'submitForSettlement' => True /* Il pagamento viene effettuato immediatamente */
            ]
        ]);

        if ($result->success) { /* Se il risultato è positivo restituisce true con il risultato transazione */
            return response()->json([
                "success" => true,
                "transaction" => $result->transaction
            ]);
        } else { /* Altrimenti restituisce false con il messaggio d'errore del risultato della transazione */
            return response()->json([
                "success" => false,
                "transaction" => $result->message
            ]);
        }
    }
}
