<?php

namespace App\Livewire;

use App\Models\pegawai as ModelsPegawai;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithPagination;

class Pegawai extends Component
{
    //pengaturan pagination
    protected $paginationTheme = "bootstrap";

    use WithPagination;
    public $nama;
    public $email;
    public $alamat;
    public $katakunci;
    public $pegawai_selected_id = [];
    public $sortColumn = 'nama';
    public $sortDirection = 'asc';

    // variabel tombol update
    public $dataUpdate = false;

    //variabel menangkap id 
    public $pegawai_id;

    public function clear()
    {
        $this->nama = '';
        $this->email = '';
        $this->alamat = '';
        $this->dataUpdate = '';
        $this->pegawai_id = '';
        $this->pegawai_selected_id = [];
    }

    public function simpan()
    {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required',
        ];
        $pesan = [
            'nama.required' => 'Nama wajib di isi',
            'email.required' => 'Email wajib di isi',
            'email.email' => 'Format email tidak sesuai',
            'alamat.required' => 'Alamat wajib di isi',
        ];
        $validated = $this->validate($rules, $pesan);

        ModelsPegawai::create($validated);
        session()->flash('message', 'Data berhasil di simpan');

        $this->clear();
    }

    public function edit($id)
    {
        $data = ModelsPegawai::find($id);

        $this->nama = $data->nama;
        $this->email = $data->email;
        $this->alamat = $data->alamat;

        $this->dataUpdate = true;
        $this->pegawai_id = $id;
    }

    public function update()
    {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required',
        ];
        $pesan = [
            'nama.required' => 'Nama wajib di isi',
            'email.required' => 'Email wajib di isi',
            'email.email' => 'Format email tidak sesuai',
            'alamat.required' => 'Alamat wajib di isi',
        ];
        $validated = $this->validate($rules, $pesan);
        $data = ModelsPegawai::find($this->pegawai_id);
        $data->update($validated);
        session()->flash('message', 'Data berhasil di Update');

        $this->clear();
    }

    public function delete()
    {
        if ($this->pegawai_id != '') {

            $id = $this->pegawai_id;
            ModelsPegawai::find($id)->delete();
        }
        if (count($this->pegawai_selected_id)) {
            for ($x = 0; $x < count($this->pegawai_selected_id); $x++) {
                ModelsPegawai::find($this->pegawai_selected_id[$x])->delete();
            }
        }
        session()->flash('message', 'Data berhasil dihapus');

        $this->clear();
    }

    public function delete_confirmation($id)
    {
        if ($id != '') {

            $this->pegawai_id = $id;
        }
    }

    public function sort($columnName)
    {
        $this->sortColumn = $columnName;
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
    }

    public function render()
    {
        if ($this->katakunci != null) {
            $data = ModelsPegawai::where('nama', 'like', '%' . $this->katakunci . '%')
                ->orWhere('email', 'like', '%' . $this->katakunci . '%')
                ->orWhere('alamat', 'like', '%' . $this->katakunci . '%')
                ->orderBy($this->sortColumn, $this->sortDirection)->paginate(10);
        } else {
            $data = ModelsPegawai::orderBy($this->sortColumn, $this->sortDirection)->paginate(10);
        }


        return view('livewire.pegawai', ['datapegawai' => $data]);
    }
}
