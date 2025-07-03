<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AlternatifRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = $this->id ?? null;
        $rules = [
            'nama' => 'required|string|max:255',
        ];

        // Validasi untuk setiap kriteria
        $kriteria = \App\Models\Kriteria::all();
        foreach ($kriteria as $k) {
            $rules["kriteria.{$k->id}"] = 'required|numeric|min:0';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [
            'nama.required' => 'Nama alternatif harus diisi',
        ];

        // Custom messages untuk setiap kriteria
        $kriteria = \App\Models\Kriteria::all();
        foreach ($kriteria as $k) {
            $messages["kriteria.{$k->id}.required"] = "Nilai untuk {$k->kode} harus diisi";
            $messages["kriteria.{$k->id}.numeric"] = "Nilai untuk {$k->kode} harus berupa angka";
            $messages["kriteria.{$k->id}.min"] = "Nilai untuk {$k->kode} minimal 0";
        }

        return $messages;
    }
}