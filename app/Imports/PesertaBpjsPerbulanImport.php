<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Pasien;
use DB;
use Carbon\Carbon;


class PesertaBpjsPerbulanImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $collection
    */
    public $data;
    public $tanggal_lahir_dms;
    public $tanggal_lahir_hts;
    public $pasiens_dms;
    public $pasiens_hts;
    public $ht_terkonfirmasi;
    public $dm_terkonfirmasi;
    public $riwayat_dm;
    public $riwayat_ht;

    /**
     * @param 
     */
    public function __construct()
    {
        $this->tanggal_lahir_dms = [];
        $this->tanggal_lahir_hts = [];
        $this->ht_terkonfirmasi  = [];
        $this->dm_terkonfirmasi  = [];
        $this->riwayat_dm        = 0;
        $this->riwayat_ht        = 0;
    }
    
    public function collection(Collection $collection)
    {
        DB::statement('Update pasiens set prolanis_dm = 0, prolanis_ht = 0;');
        $this->golongkanTanggalLahirMenurutDmHt($collection);

        $dm       = [];
        $ht       = [];

        foreach ($collection as $c) {
            $dm[] = $this->kumpulkanRppt($c, $this->pasiens_dms, 'riwayat_dm', 'prolanis_dm');
            $ht[] = $this->kumpulkanRppt($c, $this->pasiens_hts, 'riwayat_ht', 'prolanis_ht');
        }

        $dm       = array_filter($dm);
        $ht       = array_filter($ht);

        $this->data = compact('dm', 'ht');
    }
    /**
     * undocumented function
     *
     * @return void
     */
    private function kumpulkanRppt($c, $pasiens, $riwayat, $prolanis)
    {
        if ( !empty( $c[$riwayat]   ) ) {
            $this->$riwayat++;
            $pasien_filtered  = [];
            $harus_konfirmasi = true;
            foreach ($pasiens as $p) {
                if ( $p->tanggal_lahir->format('Y-m-d') ==  $this->excelToDate( $c['tanggal_lahir'] )  ) {
                    if (str_contains($this->normalisasiString($p->nama), $this->normalisasiString($c['nama']))) {
                        $p->$prolanis    = 1;
                        $p->nama         = $c['nama'];
                        $p->nama_peserta = $c['nama'];
                        $p->sex          = strtolower($c['jenis_kelamin']) == 'laki-laki'? 1 : 0;
                        $p->save();
                        $harus_konfirmasi = false;
                        if ( $prolanis == 'prolanis_dm' ) {
                            $this->dm_terkonfirmasi[] = $c;
                        }
                        if ( $prolanis == 'prolanis_ht' ) {
                            $this->ht_terkonfirmasi[] = $c;
                        }
                        break;
                    } else {
                        $pasien_filtered[] = $p;
                    }
                }
            }
            if ($harus_konfirmasi && count($pasien_filtered)) {
                $htdm = $riwayat == 'riwayat_dm' ? 'dm' : 'ht';
                $prolanis = [
                    'data_bpjs' => [
                        'nama'          => $c['nama'],
                        'alamat'        => $c['alamat'],
                        'jenis_kelamin' => $c['jenis_kelamin'],
                        'rppt'          => $htdm,
                        'tanggal_lahir' => $this->excelToDate( $c['tanggal_lahir'] )
                    ],
                    'pasiens' => $pasien_filtered
                ];
                return $prolanis;
            } else {
                return false;
            }
        }
    }
    /**
     * undocumented function
     *
     * @return void
     */
    /**
     * undocumented function
     *
     * @return void
     */
    private function excelToDate($date)
    {
       return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
    }
    /**
     * undocumented function
     *
     * @return void
     */
    private function golongkanTanggalLahirMenurutDmHt($collection)
    {
        foreach ($collection as $c) {
            if (
                !empty( $c['riwayat_dm'] && 
                !in_array($this->excelToDate($c['tanggal_lahir']), $this->tanggal_lahir_dms)
            ) ) {
                $this->tanggal_lahir_dms[] = $this->excelToDate($c['tanggal_lahir']);
            }
            if ( 
                !empty( $c['riwayat_ht'] &&
                !in_array($this->excelToDate($c['tanggal_lahir']), $this->tanggal_lahir_dms)
            ) ) {
                $this->tanggal_lahir_hts[] =  $this->excelToDate($c['tanggal_lahir']);
            }
        }
        $this->pasiens_dms = Pasien::whereIn('tanggal_lahir', $this->tanggal_lahir_dms)->get();
        $this->pasiens_hts = Pasien::whereIn('tanggal_lahir', $this->tanggal_lahir_hts)->get();
    }
    /**
     * undocumented function
     *
     * @return void
     */
    private function normalisasiString($param)
    {
        return str_replace('.', '', str_replace(' ', '', strtolower($param)));
    }
    public function rules(): array
    {
        return [
            /* 'tanggal_lahir' => 'date_format:d-m-Y' */ 
        ];
    }
}
