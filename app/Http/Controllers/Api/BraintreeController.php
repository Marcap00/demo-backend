<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Braintree\Gateway;

class BraintreeController extends Controller
{
    /* Dichiarazione la variabile d'istanza per la gestione del gateway di pagamento Braintree */
    protected $gateway;

    /**
     * Costruttore per inizializzare il gateway Braintree
     * Configurazione dell'ambiente (sandbox) e delle credenziali dell' API (merchantId, publicKey, privateKey)
     */
    public function __construct()
    {
        $this->gateway = new Gateway([
            'environment' => 'sandbox', // Ambiente di test
            'merchantId' => env('BRAINTREE_MERCHANT_ID'), // Merchant ID
            'publicKey' => env('BRAINTREE_PUBLIC_KEY'), // Public Key
            'privateKey' => env('BRAINTREE_PRIVATE_KEY') // Private Key
        ]);
    }

    /**
     * Generazione del token per il client:
     * Il token contiene tutte le informazioni d'autorizzazione e configurazione di cui il client ha bisogno
     * per inizializzare l'SDK
     * @return JsonResponse Risposta JSON con il token generato
     */
    public function token()
    {
        /* Generazione del token tramite il gateway */
        $clientToken = $this->gateway->clientToken()->generate();
        return response()->json([ // Restituisce un JSON con il token generato
            "success" => true,
            "clientToken" => $clientToken
        ]);
    }

    /**
     * Funzione del checkout del pagamento:
     * Elabora un pagamento ricevuto dal front-end
     * , valida i dati del pagamento
     * , crea la transazione
     * , restituisce il risultato della transazione come JSON
     * @param Request $request Richiesta HTTP con i dati del pagamento
     * @return JsonResponse Risposta JSON con il risultato della transazione
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
