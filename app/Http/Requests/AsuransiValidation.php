<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsuransiValidation extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
			/* 'alamat'           => 'required' */
			/* 'tipe_asuransi'    => 'required', */
			/* 'nama'             => 'required|unique:asuransis,nama,' . $this->id, */
			/* 'kali_obat'        => 'required|numeric', */
			/* 'no_telp'          => 'required', */
			/* 'tanggal_berakhir' => 'required|date_format:d-m-Y', */
        ];
    }
}
