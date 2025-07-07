<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermohonanTeraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uttp_id' => 'required|exists:uttps,id',
            'jenis_layanan' => 'required|in:Di Kantor,Luar Kantor,Sidang Tera',
            'tanggal_permohonan' => 'required|date',
            'tanggal_jadwal' => 'nullable|date|after:tanggal_permohonan',
            'catatan_pemohon' => 'nullable|string|max:1000',
            'dokumen_pendukung' => 'nullable|array',
            'nomor_permohonan' => 'nullable|unique:permohonan_teras,nomor_permohonan',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_permohonan.unique' => 'Nomor permohonan sudah ada. Sistem akan generate nomor baru otomatis.',
            'uttp_id.required' => 'UTTP harus dipilih.',
            'uttp_id.exists' => 'UTTP yang dipilih tidak valid.',
        ];
    }
}
