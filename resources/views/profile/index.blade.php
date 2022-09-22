@extends('layouts.main')

@section('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.12.0/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
    <!-- Add/Create Modal -->
    <div class="modal fade" id="createModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Create New Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="form-create" class="form-create" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group mb-3 mt-3">
                            <label for="">Name</label>
                            <input type="text" class="form-control create_name" id="create_name" name="name">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="">Email</label>
                            <input type="email" class="form-control create_email" id="create_email" name="email">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="">Upload Image</label>
                            <input type="file" class="form-control create_image" id="create_image" name="image">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="create-btn" class="btn btn-primary create-btn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add/Create Modal -->

    <!-- Edit/Update Modal -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-editData" enctype="multipart/form-data">

                    <div class="modal-body form-edit">
                        <input type="hidden" name="id" class="edit_id" id="edit_id">
                        <div class="form-group mb-3 mt-3">
                            <label for="">Name</label>
                            <input type="text" class="form-control edit_name" id="edit_name" name="name">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <label for="">Email</label>
                            <input type="email" class="form-control edit_email" id="edit_email" name="email">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3 mt-3">
                            <label for="">Upload Image</label>
                            <input type="file" class="form-control create_image" id="create_image" name="image">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update-btn" class="btn btn-primary update-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit/Update Modal -->

    <!-- Content/ List of Table -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <h1 class="text-center">Ajax Crud laravel 9</h1>
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        {{ __('List Profile') }}
                        <a href="#" class="btn btn-info btn-sm float-end" data-bs-toggle="modal"
                            data-bs-target="#createModal">Create</a>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="dataProfile">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Option</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content/ List of Table -->
@endsection

@push('javascript')
    <script src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.0/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            /** 
             * Function datatable untuk melakukan render secara server side
             */
            $('#dataProfile').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                lengthChange: true,
                autoWidth: true,
                ajax: '{{ route('table-profile') }}',
                /**Route tabel-profile untuk mendapatkan response data dalam bentuk json */
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name' /** data name dan email di dapat dari field name tabel profiles*/
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'image' /** data image di dapat dari function editColumn pada method getDataProfile di ProfileController */
                    },
                    {
                        data: 'action' /**  Sedangkan action di dapat dari function addColumn yang akan memuat tombol dari file action.blade.php*/
                    }
                ]
            })
        })
    </script>

    <script>
        $(document).ready(function() {

            /** 
             * Setup CSRF_TOKEN dari laravel
             */
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            /** 
             * ketika melakukan proses submit pada form dengan id form-create 
             * jalankan fungsi di bawah
             */
            $(document).on('submit', '#form-create', function(e) {
                e.preventDefault()

                /** 
                 * Mendefinisikan variable formData untuk mendapatkan semua value yang 
                 * di inputkan pada form-create 
                 */
                let formData = new FormData($('#form-create')[0]);

                /** 
                 * Selanjutnya jalankan proses ajax di bawah
                 */
                $.ajax({
                    type: "POST",
                    /** kirim method dengan type POST*/
                    url: "profile",
                    /**  Akses route profile dengan method post pada file web.php*/
                    data: formData,
                    /**  Kirim semua value dari variabel formData*/
                    contentType: false,
                    processData: false,
                    success: function(response) {

                        /** 
                         *  Jika response status yang di terima 400 atau HTTP_BAD_REQUEST
                         *  tambahkan class is-invalid pada tag input yang terdapat error
                         * berdasarkan properti name dan tampilkan pesan errornya dengan class invalid-feedback
                         */
                        if (response.status == 400) {
                            $.each(response.errors, function(key, err_values) {
                                $('.form-create').find('[name="' + key + '"]')
                                    .addClass(
                                        'is-invalid').siblings('.invalid-feedback')
                                    .text(err_values)
                            });
                        } else {
                            /** 
                             * Jika response yang diterima HTTP_OK atau 200
                             * Tampilkan notifikasi dari swetlaert2
                             * Sembunyikan modal Create reset form
                             * Dan refresh / reload datatable
                            */
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $('#createModal').modal('hide')
                            $('#createModal').find('input').val("")
                            $('.form-create').find('input').val("")
                            $('#dataProfile').DataTable().ajax.reload();
                           
                            console.log(response.status);
                        }
                    }
                });
            });

            /** 
             * Menjalankan event onClick saat tombol dengan id edit-btn di klik
             * Mengambil nilai dari tombol tersebut, menampilkan modal dengan id editModal 
             * untuk melakukan edit Data. Jalankan ajax function 
             * 
            */
            $(document).on('click', '#edit-btn', function(e) {
                e.preventDefault()

                let profile_id = $(this).val(); /** Variabel untuk mendapatkan id dari data yang dipilih*/
                $('#editModal').modal('show');
                $.ajax({
                    type: "GET", /** Kirim method dengan Type GET */
                    url: "profile-edit/" + profile_id + "/edit", /** Akses url/route dan kirim parameter id */
                    success: function(response) {
                        
                        /** Jika response yang di terima gagal atau id tidak di temukan 
                         *  Tampilkan pesan error dan tambahkan class alert-danger
                         *  Pada tag div yang memiliki id info_message
                        */
                        if (response.status == 404) {
                            $('#info_message').html("")
                            $('#info_message').addClass('alert alert-danger')
                            $('#info_message').text(response.message)
                        } else {

                            /** 
                             * Jika response berhasil akses semua data yang diperlukan
                             * dari response json yang di dapat lalu masukkan nilainya 
                             * ke dalam tag input seperti di bawah ini
                            */
                            $('#edit_id').val(response.profile.id)
                            $('#edit_name').val(response.profile.name)
                            $('#edit_email').val(response.profile.email)
                            $('#edit_phone').val(response.profile.phone)
                        }
                    }
                });
            });

            /** 
             * Jalankan proses onSubmit pada form dengan id form-editData
             * ketika tombol update di klik
            */
            $(document).on('submit', '#form-editData', function(e) {
                e.preventDefault()

                /** 
                 * untuk proses update data sama dengan proses store data seperti di atas
                 * Yang membedakan hanya url dimana url itu juga mengirimkan id yang datanya akan di update
                */
                let profile_id = $('#edit_id').val(); /** Mendapatkan id dari tag input */
                let formData = new FormData(document.getElementById("form-editData"));
                console.log("Form data edit", formData);
                $.ajax({
                    type: "POST",
                    url: "profile-update/" + profile_id,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status == 400) {
                            $.each(response.errors, function(key, err_values) {
                                $('#form-editData').find('[name="' + key + '"]')
                                    .addClass(
                                        'is-invalid').siblings('.invalid-feedback')
                                    .text(err_values)
                            });
                        } else if (response.status == 404) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            })
                        } else {
                            console.log(response.status);
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $('#editModal').modal('hide')
                            $('#editModal').find('input').val("")
                            $('.form-create').find('input').val("")
                            $('#dataProfile').DataTable().ajax.reload();
                           
                        }
                    }
                });
            });

            /** 
             * Menjalankan proses hapus data ketika tombol dengan id delete-btn di klik
            */
            $(document).on('click', '#delete-btn', function(e) {
                e.preventDefault()

                let profile_id = $(this).val(); /** Mendapatkan nilai id dari data yang dipilih*/

                /** 
                 * menampilkan konfirmasi hapus data dengan sweet alert 2
                */
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        /** 
                         * Ketika tombol yes yang di tekan jalankan fungsi ajax di bawah
                        */
                        $.ajax({
                            type: "DELETE",
                            url: "delete-profile/" + profile_id,
                            success: function(response) {
                                $('#dataProfile').DataTable().ajax.reload(); 
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                )
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
