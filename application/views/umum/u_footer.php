<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-center small">
            <div class="text-muted">Copyright &copy; Eco-Industrial Park 2025</div>
        </div>
    </div>
</footer>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>
<script src="<?= base_url(); ?>assets/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="<?= base_url(); ?>assets/assets/demo/chart-area-demo.js"></script>
<script src="<?= base_url(); ?>assets/assets/demo/chart-bar-demo.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="<?= base_url(); ?>assets/js/datatables-simple-demo.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url(); ?>assets/js/notif.js"></script>
<script src="<?= base_url(); ?>assets/js/survey.js"></script>
<script src="<?= base_url(); ?>assets/js/question.js"></script>
<script src="<?= base_url(); ?>myjs.js"></script>
<script src="<?= base_url(); ?>assets/js/bootstrap-datepicker.min.js"></script>

<script>
$(function() {
    $('#loading1').hide();
    $('#loketuser').DataTable();
})
var base = '<?php echo site_url(); ?>';
var booking_table;
var layanan_table;
var urlz;




$(".datepicker").datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    todayHighlight: true,
});


function notifikasi(status, msg) {
    if (status == false) {
        let title = 'Opppss...!';
        let sts = 'warning';
        Swal.fire(
            title,
            msg,
            sts
        );
    } else {
        let title = 'Selamat';
        let sts = 'success';
        Swal.fire(
            title,
            msg,
            sts
        );
    }
}
</script>
</body>

</html>