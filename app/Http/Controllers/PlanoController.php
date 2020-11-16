<?php

namespace App\Http\Controllers;

use App\Helpers\PagarMe;
use App\Http\Resources\Plano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanoController extends Controller
{
    public function insert(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'amount' => 'required',
                'days' => 'required',
                'name' => 'required',
                'payment_methods' => 'required',
                'trial_days' => 'required'
            ]
        );

        if ($validate->fails()) {
            return response()->json(['message' => 'Dados informados com erro!'], 400);
        }
        
        $amount = $request->input('amount');
        $days = $request->input('days');
        $name = $request->input('name');
        $payment_methods = $request->input('payment_methods');
        $trial_days = $request->input('trial_days');

        $data = [
            'amount' => $amount,
            'days' => $days,
            'name' => $name,
            'payment_methods' => $payment_methods,
            'trial_days' => $trial_days
        ];

        $pagarme = new PagarMe();
        $plano = $pagarme->post('plans', $data);

        return response()->json(['message' => 'Plano cadastrado com sucesso!', 'id' => $plano['id'], 'nome' => $plano['name']]);
    }

    public function selectAll()
    {
        $pagarme = new PagarMe();
        $planos = $pagarme->get('plans');

        return response()->json(Plano::collection($planos));
    }

    public function select($id)
    {
        $pagarme = new PagarMe();
        $planos = $pagarme->get("plans/$id");

        return response()->json(new Plano($planos));
    }

    public function update(Request $request, $id)
    {
        $name = $request->input('name');
        $trial_days = $request->input('trial_days');
        $invoice_reminder = $request->input('invoice_reminder');

        $pagarme = new PagarMe();
        $plano = $pagarme->get("plans/$id");

        $data = [
            'name' => $name ? $name : $plano['name'],
            'trial_days' => $trial_days ? $trial_days : $plano['trial_days'],
            'invoice_reminder' => $invoice_reminder ? $invoice_reminder : $plano['invoice_reminder']
        ];


        $planoUpdate = $pagarme->put("plans/$id", $data);
        return response()->json(['message' => 'Plano atualizado com sucesso!', 'id' => $planoUpdate['id'], 'nome' => $planoUpdate['name']]);
    }
}
