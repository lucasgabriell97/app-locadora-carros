<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocacaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'cliente_id' => 'exists:clientes,id', 
            'carro_id' => 'exists:carros,id',
            'data_inicio_periodo' => 'required|date',
            'data_final_previsto_periodo' => 'required|date',
            'data_final_realizado_periodo' => 'required|date',
            'valor_diaria' => 'required|integer',
            'km_inicial' => 'required|integer',
            'km_final' => 'required|integer'
        ];

        if($this->method() === 'PATCH') {
            $regrasDinamicas = array();

            foreach($rules as $input => $regra) {
                if(array_key_exists($input, $this->request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            return $regrasDinamicas;
        }

        return $rules;
    }
}
