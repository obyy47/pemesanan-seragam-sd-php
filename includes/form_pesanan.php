<?php

/**
 * @var array  $anak_list
 * @var array  $v
 * @var string $tombol
 */
?>
<?php if (!$anak_list): ?>
  <div class="empty-state">
    <p>Kamu belum punya data anak.</p>
    <a class="btn" href="<?= BASE ?>/user/anak.php">+ Tambah Data Anak Dulu</a>
  </div>
<?php else: ?>
  <form method="post">
    <?= csrf_field() ?>
    <label>Anak
      <select name="anak_id" required>
        <option value="">-- pilih anak --</option>
        <?php foreach ($anak_list as $a): ?>
          <option value="<?= $a['id'] ?>" <?= (int)$v['anak_id'] === (int)$a['id'] ? 'selected' : '' ?>>
            <?= e($a['nama_anak']) ?> (Kelas <?= e($a['kelas']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Ukuran seragam
      <select name="ukuran" id="ukuran" required>
        <?php foreach (HARGA as $uk => $h): ?>
          <option value="<?= $uk ?>" <?= $v['ukuran'] === $uk ? 'selected' : '' ?>><?= $uk ?> - <?= rupiah($h) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Jumlah
      <input type="number" name="jumlah" id="jumlah" min="1" max="100" value="<?= e($v['jumlah']) ?>" required>
    </label>

    <div class="total-box">
      <span>Estimasi Total Harga</span>
      <strong id="estimasi-total">Rp 0</strong>
    </div>

    <button><?= e($tombol) ?></button>
  </form>

  <script>
    (function() {
      const hargaMap = <?= json_encode(HARGA) ?>;
      const ukuranEl = document.getElementById('ukuran');
      const jumlahEl = document.getElementById('jumlah');
      const totalEl = document.getElementById('estimasi-total');

      function updateTotal() {
        const harga = hargaMap[ukuranEl.value] || 0;
        const jumlah = Math.max(0, parseInt(jumlahEl.value, 10) || 0);
        totalEl.textContent = 'Rp ' + (harga * jumlah).toLocaleString('id-ID');
      }

      ukuranEl.addEventListener('change', updateTotal);
      jumlahEl.addEventListener('input', updateTotal);
      updateTotal();
    })();
  </script>
<?php endif; ?>