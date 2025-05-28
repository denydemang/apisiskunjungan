<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveKunjunganRequest;
use App\Models\SisKunjungan;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\New_;
use Illuminate\Support\Str;


class KunjunganController extends Controller
{
    public function saveKunjungan(SaveKunjunganRequest $request) : JsonResponse  {
        try {

             $dataValidated = $request->validated();

            // Proses upload gambar
            if ($request->hasFile('foto_knj')) {
                $file = $request->file('foto_knj');

                // Buat nama file unik (opsional)
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

                // Simpan ke storage/app/public/foto_kunjungan
                $path = $file->storeAs('foto_kunjungan', $filename, 'public');

                // Simpan path ke dalam data yang akan disimpan di DB
                $dataValidated['foto_knj'] = $path;
            }

            // Simpan ke DB
            $SisKunjungan = new SisKunjungan($dataValidated);
            $SisKunjungan->save();

        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        return response()->json([
            'data' => $dataValidated,
            'success' => 'Succesfully Created Kunjungan'
        ] , 200);

    }
}
