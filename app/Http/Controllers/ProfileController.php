<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function getDataProfile()
    {
        /** 
         * method ini digunakan untuk menampilkan seluruh isi data dari table profile yang dibuat
         * ke dalam bentuk json dari package datatable yang digunakan
         * 
         */
        $model = Profile::latest()->get();
        return datatables()->of($model)
            /** Menambahkan tombol action berupa edit dan delete yang ada pada file action.blade.php */
            ->addColumn('action', 'profile.action')
            ->addIndexColumn()

            /** Menambahkan tag html img dan memanggil accessor getImage() yang sudah di definisikan di model Profile */
            ->editColumn('image', function (Profile $model) {
                return '<img src="' . $model->getImage() . '" height="80px">';
            })
            ->rawColumns(['action', 'image'])
            ->toJson();
    }

    public function index()
    {
        return view('profile.index');
    }

    public function store(Request $request)
    {
        /** 
         * Menggunakan facades validator untuk validasi data yang dikirim dari form input yang 
         * ada pada modal
        */
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'image|mimes:png,jpg,jpeg'
        ]);

        /** 
         * Jika $validator /validasi gagal maka akan me-return response json dengan status
         * HTTP_BAD_REQUEST atau 400 dan juga pesan erorrnya 
        */
        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $validator->errors(),
            ]);
        } else {
            /**
             * Jika lolos validasi maka lakukan proses simpan data dan jika terdapat
             * inputan berupa image simpan juga gambar tersebut dan jika tidak ada biarkan
             * kosong. Kemudain akan mengembalikan response HTTP_OK
             */
            $profile = new Profile;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $uploadFile  = time() . '_' . $file->getClientOriginalName();
                $file->move('uploads/imgProfile/', $uploadFile);
                $profile->image = $uploadFile;
            }

            $profile->name = $request->input('name');
            $profile->email = $request->input('email');

            $profile->save();

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => 'Data has been stored',
            ]);
        }
    }

    public function edit($id)
    {
        $profile = Profile::findOrFail($id);

        /**
         * Method edit akan melakukan pengecekan data berdasarkan parameter id yang dikirim
         * Jika data id ditemukan tampilkan data tersebut, jika tidak tampilkan pesan gagal / error
         */
        if ($profile) {
            return response()->json([
                'status' => Response::HTTP_OK,
                'profile' => $profile
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data Not Found'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'image|mimes:png,jpg,jpeg'
        ]);

        /**
         * Sama seperti method store pada method update juga melakukan hal yang sama
         * Yaitu validasi pada data yang akan di update
         */
        if ($validator->fails()) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $validator->messages(),
            ]);
        } else {
            $profile = Profile::findOr($id);

            /**
             * Jika data gambar juga ajab di ubah maka hapus data gambar yang sudah disimpan
             * sebelumnya lalu lakukan update data
             */
            if ($profile) {
                $profile->name = $request->input('name');
                $profile->email = $request->input('email');

                if ($request->hasFile('image')) {
                    if (File::exists("uploads/imgProfile/" . $profile->image)) {
                        File::delete("uploads/imgProfile/" . $profile->image);
                    }
                    $file = $request->file("image");
                    $uploadFile = time() . '_' . $file->getClientOriginalName();
                    $file->move('uploads/imgProfile/', $uploadFile);
                    $profile->image = $uploadFile;
                }

                $profile->update();

                return response()->json([
                    'status' => Response::HTTP_OK,
                    'message' => 'Data has been updated'
                ]);
            } else {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'Data Not Found'
                ]);
            }
        }
    }

    public function destroy($id)
    {
        /**
         * Cek data di tabel profiles jika data ada maka hapus data sesuai dengan paramater id  
         * yang di dapat begitu juga dengan file gambar. Kemudian kirim response HTTP_OK jika berhasil
         * Dan jika gagal kririm response HTTP_NO_FOUND.
         */

        $profile = Profile::findOr($id);
        if (File::exists("uploads/imgProfile/" . $profile->image)) {
            File::delete("uploads/imgProfile/" . $profile->image);
        }

        if ($profile) {
            $profile->delete();
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Data Has Been Deleted'
            ]);
        } else {
            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data Not Found'
            ]);
        }
    }
}
