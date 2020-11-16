<?php

namespace App\Http\Controllers;

use App\Helpers\PagarMe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssinaturaController extends Controller
{
    public function insert(Request $request, $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'payment_method' => 'required',
                'card_number' => 'required',
                'card_holder_name' => 'required',
                'card_expiration_date' => 'required',
                'card_cvv' => 'required',
                'document_number' => 'required',
                'street' => 'required',
                'street_number' => 'required',
                'complementary' => 'required',
                'neighborhood' => 'required',
                'zipcode' => 'required',
                'ddd' => 'required',
                'number' => 'required'
            ]
        );

        if ($validate->fails()) {
            return response()->json(['message' => 'Dados informados com erro!'], 400);
        }

        $payment_method = $request->input('payment_method');
        $card_number = $request->input('card_number');
        $card_holder_name = $request->input('card_holder_name');
        $card_expiration_date = $request->input('card_expiration_date');
        $card_cvv = $request->input('card_cvv');
        $document_number = $request->input('document_number');
        $street = $request->input('street');
        $street_number = $request->input('street_number');
        $complementary = $request->input('complementary');
        $neighborhood = $request->input('neighborhood');
        $zipcode = $request->input('zipcode');
        $ddd = $request->input('ddd');
        $number = $request->input('number');

        $userId = $request->session()->get('user_id');
        $sql = "SELECT
                    nome,
                    email 
                FROM
                    usuarios 
                WHERE
                    id = $userId";
        $usuario = DB::selectOne($sql);

        $data = [
            'plan_id' => $id,
            'payment_method' => $payment_method,
            'card_number' => $card_number,
            'card_holder_name' => $card_holder_name,
            'card_expiration_date' => $card_expiration_date,
            'card_cvv' => $card_cvv,
            'customer' => [
                'email' => $usuario->email,
                'name' => $usuario->nome,
                'document_number' => $document_number,
                'address' => [
                    'street' => $street,
                    'street_number' => $street_number,
                    'complementary' => $complementary,
                    'neighborhood' => $neighborhood,
                    'zipcode' => $zipcode
                ],
                'phone' => [
                    'ddd' => $ddd,
                    'number' => $number
                ],
            ]
        ];

        $pagarme = new PagarMe();
        $assinatura = $pagarme->post('subscriptions', $data);

        return response()->json($assinatura);
    }

    public function select($id)
    {
        $pagarme = new PagarMe();
        $assinatura = $pagarme->get("subscriptions/$id");

        return response()->json($assinatura);
    }

    public function cancel($id)
    {
        $pagarme = new PagarMe();
        $assinatura = $pagarme->cancel("subscriptions/$id/cancel");

        return response()->json(['message' => 'Assinatura foi cancelada com sucesso!']);
    }
}
