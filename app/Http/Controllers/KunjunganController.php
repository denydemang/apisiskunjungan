<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveKunjunganRequest;
use App\Http\Resources\KunjunganResourceCollection;
use App\Http\Resources\ProjectResourceCollection;
use App\Models\Projects;
use App\Models\SisKunjungan;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\New_;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class KunjunganController extends Controller
{
    public function saveKunjungan(SaveKunjunganRequest $request) : JsonResponse  {
        try {

             $dataValidated = $request->validated();

            if ($request->hasFile('foto_knj')) {
                $file = $request->file('foto_knj');

                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        
                $path = $file->storeAs('foto_kunjungan', $filename, 'public');


                $dataValidated['foto_knj'] = $path;
            }

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

    public function getKunjungan(Request $request) : KunjunganResourceCollection {

        // $startdDate = $request->get('start_date' , null);
        // $endDate = $request->get('end_date', null);
        $page = $request->get('page' ,1);
        $perpage = $request->get('perpage' ,25);
        $iduser = $request->get('iduser' ,null);
        $keyword =  $request->get('keyword', null);
        $user_pmr =  $request->get('user_pmr', null);
        $user_mgm =  $request->get('user_mgm', null);
        $data = SisKunjungan::join('projects', 'sis_kunjungans.project_id', '=', 'projects.id')->join('users', 'sis_kunjungans.user_id' , '=', 'users.id')
            ->when($iduser !== null, function($query) use($iduser){
                    $query->where('user_id', $iduser);
                })

            ->when($user_pmr !== null, function($query) use($user_pmr){
                    $query->where('user_pmr', $user_pmr);
                })
            
            ->when($user_mgm !== null, function($query) use($user_mgm){
                    $query->where('user_mgm', $user_mgm);
                })

            ->when($keyword !== null , function($query) use($keyword){
                    $query->where('nama_pro', 'like', '%' + $keyword + '%' );
                    $query->orWhere('name' , 'like', '%' + $keyword + '%');
                    $query->orWhere('lokasi_knj' , 'like', '%' + $keyword + '%');
                    $query->orWhere('nama_knj' , 'like', '%' + $keyword + '%');
                    $query->orWhere('hasil_knj' , 'like', '%' + $keyword + '%');
                    $query->orWhere('sumber_knj' , 'like', '%' + $keyword + '%');
                    $query->orWhere('user_knj' , 'like', '%' + $keyword + '%');
                })
            ->select('sis_kunjungans.*', 'projects.nama_pro' , 'users.name as user_knj' ,'users.divisi as user_divisi', 'users.jabatan as user_jabatan')
            ->orderBy('tgl_knj', 'DESC')->orderBy('created_at', 'DESC')
            ->paginate(perPage:$perpage, page:$page);
        $dataModified= [];
        foreach ($data as $item) {
           
            $item['foto_knj'] = url('/public') .'/'. $item['foto_knj'];
            $item['jam'] = Carbon::parse(  $item['created_at'])->toTimeString();
            $dataModified[] =$item;
        }

        return new KunjunganResourceCollection($dataModified , 'Successfully Get Data Kunjungan');

    }

    public function groupKunjungan(String $userId) : JsonResponse {
      $data = DB::table('sis_kunjungans as a')
        ->join('users as b', 'a.user_id', '=', 'b.id')
        ->where('user_id',$userId )
        ->select(
            'a.user_id',
            'b.name',
            'b.divisi',
            DB::raw("COUNT(CASE WHEN DATE(a.tgl_knj) = CURDATE() THEN 1 END) as daily_visits"),
            DB::raw("COUNT(CASE WHEN DATE(a.tgl_knj) BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE() THEN 1 END) as weekly_visits"),
            DB::raw("COUNT(CASE WHEN MONTH(a.tgl_knj) = MONTH(CURDATE()) AND YEAR(a.tgl_knj) = YEAR(CURDATE()) THEN 1 END) as monthly_visits")
        )
        ->groupBy('a.user_id', 'b.name', 'b.divisi')
        ->orderByDesc('monthly_visits')
        ->get()->toArray();
        // dd($data[0]->daily_visits);

        return response()->json($data);



       }
    public function topKunjungan(Request $request) : KunjunganResourceCollection {
        $topUsers = SisKunjungan::select(
        'sis_kunjungans.user_id',
        'users.name',
        'users.divisi',
        DB::raw('COUNT(sis_kunjungans.user_id) as jmlh')
            )
            ->join('users', 'sis_kunjungans.user_id', '=', 'users.id')
            ->whereMonth('tgl_knj', now()->month)
            ->whereYear('tgl_knj', now()->year)
            ->groupBy('sis_kunjungans.user_id', 'users.name', 'users.divisi')
            ->orderByDesc('jmlh')
            ->limit(3)
            ->get();


        return new KunjunganResourceCollection( $topUsers , 'Successfully Get Data Top Kunjungan');

       }

    public function getProject() : ProjectResourceCollection {

        
        $data = Projects::select('id', 'nama_pro')->get();


        return new ProjectResourceCollection($data , 'Successfully Get Data Project');

    }


}
