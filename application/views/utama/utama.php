<div id="about" data-aos="zoom-out" data-aos-delay="600">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 mt-3">

                <!-- Judul & Progress -->
                <h4 class="text-center"><?php echo $title; ?></h4>
                <p class="mb-1">
                    Progress: <strong><?= $current ?>/<?= $total ?></strong>
                </p>

                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: <?= ($current / $total) * 100 ?>%;">
                    </div>
                </div>

                <hr>

                <!-- FORM STEP -->
                <form method="post" action="<?php echo site_url('welcome/save_step'); ?>">

                    <!-- Wajib: info survei -->
                    <input type="hidden" name="survey_id" value="<?= $survey->id ?>">
                    <input type="hidden" name="response_id" value="<?php echo $response_id; ?>">

                    <?php foreach ($questions as $i => $q): ?>
                    <?php
                        $meta = json_decode($q->meta, true);
                        $req = isset($meta['required']) && $meta['required'] ? 'required' : '';
                        $value = isset($progress[$q->id]) ? $progress[$q->id] : '';
                        ?>

                    <div class="form-group">
                        <label>
                            <b>
                                <?= ($i + 1) . ". " . $q->label ?>
                                <?php if (!empty($meta['required'])): ?>
                                <span style="color:red">*</span>
                                <?php endif; ?>
                            </b>
                        </label>

                        <!-- TEXT -->
                        <?php if ($q->type == 'text'): ?>
                        <input type="text" class="form-control" name="q_<?= $q->id ?>" value="<?= $value ?>"
                            <?= $req ?>>

                        <!-- TEXTAREA -->
                        <?php elseif ($q->type == 'textarea'): ?>
                        <textarea class="form-control" name="q_<?= $q->id ?>" rows="3"
                            <?= $req ?>><?= $value ?></textarea>

                        <!-- RADIO / CHECKBOX / SELECT -->
                        <?php elseif (in_array($q->type, ['radio', 'checkbox', 'select'])): ?>
                        <?php $options = $meta['options'] ?? []; ?>

                        <?php if ($q->type == 'radio'): ?>
                        <?php foreach ($options as $opt): ?>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="q_<?= $q->id ?>" value="<?= $opt ?>"
                                <?= ($value == $opt) ? 'checked' : '' ?>>
                            <label class="form-check-label"><?= $opt ?></label>
                        </div>
                        <?php endforeach; ?>

                        <?php elseif ($q->type == 'checkbox'): ?>
                        <?php
                                    $checkedValues = is_array($value) ? $value : explode(',', $value);
                                    ?>
                        <?php foreach ($options as $opt): ?>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="q_<?= $q->id ?>[]" value="<?= $opt ?>"
                                <?= in_array($opt, $checkedValues) ? 'checked' : '' ?>>
                            <label class="form-check-label"><?= $opt ?></label>
                        </div>
                        <?php endforeach; ?>

                        <?php elseif ($q->type == 'select'): ?>
                        <select class="form-control" name="q_<?= $q->id ?>">
                            <?php foreach ($options as $opt): ?>
                            <option value="<?= $opt ?>" <?= ($opt == $value) ? 'selected' : '' ?>>
                                <?= $opt ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>

                        <?php endif; ?>
                    </div>

                    <?php endforeach; ?>

                    <!-- Tombol SIMPAN & LANJUT -->
                    <div class="row">
                        <div class="col-6">
                            <?php if ($current > 1): ?>
                            <a href="<?= site_url('welcome/form/' . $prev_slug) ?>" class="btn btn-secondary">←
                                Sebelumnya</a>
                            <?php endif; ?>
                        </div>
                        <div class="col-6 text-end">
                            <button type="submit" class="btn btn-primary">
                                Simpan Jawaban & Lanjut
                            </button>
                        </div>
                    </div>
                </form>

                <!-- TOMBOL SELESAI hanya muncul di survei terakhir -->
                <?php if ($current == $total): ?>

                <div class="text-center mt-4">

                    <p class="text-muted mb-2">
                        <i>Pastikan Anda telah memeriksa semua jawaban sebelum menyelesaikan survei.</i><br>
                        <strong>Tekan tombol "Selesai" untuk mengirim jawaban Anda.</strong>
                    </p>

                    <form method="post" action="<?= site_url('welcome/finish') ?>"
                        onsubmit="return confirm('Yakin menyelesaikan survei ini? Anda tidak dapat mengubah jawaban lagi.');">

                        <button class="btn btn-success btn-lg px-4 my-3">
                            ✔ Selesai
                        </button>
                    </form>

                </div>

                <?php endif; ?>


            </div>
        </div>
    </div>
</div>