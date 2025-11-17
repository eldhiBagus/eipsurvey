$(document).ready(function(){
    let table;
    table = $('#tblSurveys').DataTable({
            ajax: {
                url: base + 'surveys/list',
                dataSrc: ''
            },
            columns: [
                {
                    data: 'posisi'
                },
                {
                    data: 'menu'
                },
                {
                    data: 'title'
                },
                {
                    data: 'slug'
                },
                {
                    data: null,
                    render: row => `
                    <button class="btn btn-sm btn-warning btnEditSurvey" data-id="${row.id}">Edit</button>
                        <a href="${base}questions?param=${row.id}" class="btn btn-sm btn-info">Pertanyaan</a>
                        <button class="btn btn-sm btn-danger btnDelSurvey" data-id="${row.id}">Hapus</button>
                        <button class="btn btn-sm btn-success" id="btnExport" data-id="${row.id}">Excel</button>`
                }
            ]
        });

        $('#btnAddSurvey').click(function() {
            $('#formSurvey')[0].reset();
            $('#id').val('');
            $('#modalSurvey').modal('show');
        });

        $(document).on('click', '.btnEditSurvey', function(){
    const id = $(this).data('id');

    // Ambil data survei lewat AJAX
    $('#formSurvey')[0].reset();
    $('overlay').show();
    $.getJSON(base + 'surveys/get/' + id, function(data){
        $('#overlay').hide();
        if(data){
            $('#id').val(data.id);
            $('#title').val(data.title);
            $('#menu').val(data.menu);
            $('#posisi').val(data.posisi || 0);
            $('#modalSurvey').modal('show');
        } else {
            Swal.fire('Error', 'Data survei tidak ditemukan', 'error');
        }
    });
});

        $('#btnSaveSurvey').click(function() {
            $('#overlay').show();
            $.post(base + 'surveys/save', $('#formSurvey').serialize(), function() {
                $('#overlay').hide();
                $('#modalSurvey').modal('hide');
                Swal.fire('Berhasil', 'Survei berhasil disimpan', 'success');
                table.ajax.reload();
            }, 'json');
        });

        $(document).on('click', '.btnDelSurvey', function() {
            Swal.fire({
                title: "apakah anda yakin?",
                text: "data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!"
                }).then((result) => {
                if (result.isConfirmed) {
                    $('#overlay').show();
                    $.getJSON(base + 'surveys/delete/' + $(this).data('id'), function() {
                    $('#overlay').hide();
                    Swal.fire('Terhapus!', 'Survei berhasil dihapus.', 'success');
                        table.ajax.reload()
                    });
                }
                });
        });

        $(document).on('click', '#btnExport', function() {
            const surveyId = $(this).data('id');
            window.location.href = base + 'surveys/export/' + surveyId;
        });
});