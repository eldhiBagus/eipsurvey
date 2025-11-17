<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="my-3"><?php echo $title; ?></h4>
            <div class="card my-3 w-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div>
                            Daftar Data Survey
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" id="btnAddSurvey">+ tambah</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table style="width: '100%';" id="tblSurveys" class="table table-bordered table-borderless">
                        <thead>
                            <tr class="text-center text-uppercase">
                                <th scope="col" style="width: 10px;">urutan</th>
                                <th scope="col" width="15%">Menu</th>
                                <th scope="col" width="35%">Judul</th>
                                <th scope="col" width="25%">Slug</th>
                                <th scope="col" width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
    </main>
    <!-- Modal -->
    <div class="modal fade" id="modalSurvey" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Form Survei</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSurvey">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label>Judul Survei</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Menu</label>
                            <input type="text" name="menu" id="menu" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Urutan ke -</label>
                            <input type="number" name="posisi" id="posisi" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" id="btnSaveSurvey">Simpan</button>
                </div>
            </div>
        </div>
    </div>