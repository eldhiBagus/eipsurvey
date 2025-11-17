<div class="container mt-5 text-center">
    <h2 class="text-success">🎉 Terima kasih telah mengisi survei!</h2>
    <?php if (isset($next) && $next): ?>
        <p class="mt-3">Lanjutkan ke survei berikutnya:</p>
        <a href="<?php echo site_url('welcome/form/' . $next->slug); ?>" class="btn btn-primary btn-lg">
            Lanjut ke <?php echo $next->title; ?> →
        </a>
    <?php else: ?>
        <p class="mt-3">Anda telah menyelesaikan pengisian survei.</p>
        <a href="<?php echo site_url('/'); ?>" class="btn btn-secondary btn-lg">Kembali ke Halaman Awal</a>
    <?php endif; ?>
</div>