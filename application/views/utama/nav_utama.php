<nav style="background-color: #000033;">
    <ul class="nav justify-content-center">
        <li class="nav-item">
            <a class="nav-link " href="<?= base_url('/'); ?>">
                beranda</a>
        </li>
        <?php foreach ($surveys as $survey): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= site_url('welcome/form/' . $survey->slug); ?>"><?= $survey->menu; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>