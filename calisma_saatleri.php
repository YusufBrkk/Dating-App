<?php
include_once './header.php';
include_once './includes/dbh-inc.php';

// Çalışma saatleri kaydet/güncelle
if (isset($_POST['save_hours'])) {
    $baslangic = $_POST['baslangic'];
    $bitis = $_POST['bitis'];
    $haftalik_tatil = isset($_POST['haftalik_tatil']) ? implode(',', $_POST['haftalik_tatil']) : '';
    $exists = mysqli_fetch_assoc(mysqli_query($connection, "SELECT id FROM calisma_saatleri LIMIT 1"));
    if ($exists) {
        $stmt = mysqli_prepare($connection, "UPDATE calisma_saatleri SET baslangic=?, bitis=?, haftalik_tatil=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $baslangic, $bitis, $haftalik_tatil, $exists['id']);
    } else {
        $stmt = mysqli_prepare($connection, "INSERT INTO calisma_saatleri (baslangic, bitis, haftalik_tatil) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $baslangic, $bitis, $haftalik_tatil);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $success = "Çalışma saatleri güncellendi.";
}

// Tatil günü ekle
if (isset($_POST['add_holiday'])) {
    $tarih = $_POST['tatil_tarih'];
    $aciklama = trim($_POST['tatil_aciklama']);
    $stmt = mysqli_prepare($connection, "INSERT INTO tatil_gunleri (tarih, aciklama) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $tarih, $aciklama);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $success = "Tatil günü eklendi.";
}

// Tatil günü sil
if (isset($_GET['delete_holiday'])) {
    $id = intval($_GET['delete_holiday']);
    mysqli_query($connection, "DELETE FROM tatil_gunleri WHERE id=$id");
    $success = "Tatil günü silindi.";
}

// Mevcut verileri çek
$calisma = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM calisma_saatleri LIMIT 1"));
$tatil_gunleri = [];
$res = mysqli_query($connection, "SELECT * FROM tatil_gunleri ORDER BY tarih ASC");
while ($row = mysqli_fetch_assoc($res)) $tatil_gunleri[] = $row;

$hafta = ['Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'];
$tatil_list = isset($calisma['haftalik_tatil']) ? explode(',', $calisma['haftalik_tatil']) : [];
?>

<style>
    body {
        background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
        min-height: 100vh;
    }
    .settings-card {
        max-width: 520px;
        width: 100%;
        border: none;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        background: linear-gradient(135deg, #f1f5f9 0%, #fff 100%);
        padding: 0;
        margin-top: 2.5rem;
        border-radius: 1.3rem;
        overflow: hidden;
        position: relative;
    }
    .settings-banner {
        background: linear-gradient(90deg, #2563eb 0%, #38bdf8 100%);
        color: #fff;
        padding: 2.2rem 1.5rem 1.2rem 1.5rem;
        text-align: center;
        border-top-left-radius: 1.3rem;
        border-top-right-radius: 1.3rem;
        position: relative;
    }
    .settings-banner i {
        font-size: 2.7rem;
        margin-bottom: 0.5rem;
        color: #fff;
        filter: drop-shadow(0 2px 8px #2563eb55);
    }
    .settings-banner h2 {
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 0.2rem;
        letter-spacing: 1px;
    }
    .settings-banner .desc {
        font-size: 1.08rem;
        color: #e0e7ef;
        margin-bottom: 0;
    }
    .settings-list {
        background: transparent;
        padding: 2rem 1.5rem 1.2rem 1.5rem;
    }
    .settings-list .form-label { font-weight: 500; color: #2563eb; }
    .settings-list .btn-primary { border-radius: 0.5rem; font-weight: 500; }
    .settings-list .btn-outline-danger { border-radius: 0.5rem; }
    .settings-list .form-check-input:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    .settings-list .form-check-label {
        font-weight: 500;
        color: #334155;
    }
    .settings-list .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #f1f5f9;
    }
    .settings-list .table-striped > tbody > tr:nth-of-type(even) {
        background-color: #e0e7ef;
    }
    .settings-list .table tbody tr:hover {
        background: #dbeafe !important;
        transition: background 0.2s;
    }
    .settings-list hr {
        margin: 2rem 0 1.5rem 0;
        border-top: 2px solid #e0e7ef;
    }
    .settings-footer {
        padding: 0 1.5rem 1.5rem 1.5rem;
    }
    @media (max-width: 600px) {
        .settings-card {
            margin-top: 1.2rem;
        }
        .settings-banner,
        .settings-list,
        .settings-footer {
            padding-left: 0.7rem;
            padding-right: 0.7rem;
        }
    }
</style>

<!-- Sidebar Aç/Kapa Butonu (Mobil) -->
<button class="sidebar-mobile-toggle" id="sidebarMobileToggle" title="Menüyü Aç/Kapat">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-title">Admin Paneli</span>
        <button class="sidebar-toggle" id="sidebarToggle" title="Menüyü Daralt/Aç">
            <i class="bi bi-chevron-double-left"></i>
        </button>
    </div>
    <div class="user-section">
        <i class="bi bi-person-circle"></i>
        <span class="user-label">Yönetici</span>
    </div>
    <nav class="nav flex-column mt-2">
        <a href="index.php" class="nav-link">
            <i class="bi bi-house"></i>
            <span class="sidebar-link-text">Ana Sayfa</span>
        </a>
        <a href="appts.php" class="nav-link">
            <i class="bi bi-calendar2-week"></i>
            <span class="sidebar-link-text">Randevuları Yönet</span>
        </a>
        <a href="users.php" class="nav-link">
            <i class="bi bi-people"></i>
            <span class="sidebar-link-text">Müşterileri Görüntüle</span>
        </a>
        <a href="calisma_saatleri.php" class="nav-link active">
            <i class="bi bi-clock-history"></i>
            <span class="sidebar-link-text">Çalışma Saatleri & Tatil</span>
        </a>
    </nav>
    <div class="sidebar-footer mt-auto">
        <a href="./includes/signout-inc.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right"></i>
            <span class="sidebar-link-text">Çıkış Yap</span>
        </a>
        <small>Yönetici olarak giriş yaptınız.</small>
    </div>
</div>

<script>
    // Sidebar aç/kapa
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarMobileToggle = document.getElementById('sidebarMobileToggle');
    let isCollapsed = false;

    sidebarToggle.addEventListener('click', function () {
        isCollapsed = !isCollapsed;
        sidebar.classList.toggle('collapsed', isCollapsed);
        sidebarToggle.innerHTML = isCollapsed
            ? '<i class="bi bi-chevron-double-right"></i>'
            : '<i class="bi bi-chevron-double-left"></i>';
    });

    sidebarMobileToggle.addEventListener('click', function () {
        sidebar.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 900 && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !sidebarMobileToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });

    document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
        if (window.location.pathname.endsWith(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
</script>

<div class="main-content">
    <div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 90vh;">
        <div class="settings-card rounded-4">
            <div class="settings-banner">
                <i class="bi bi-clock-history"></i>
                <h2>Çalışma Saatleri & Tatil Günleri</h2>
                <div class="desc">Sistem genelinde geçerli olacak şekilde ayarlayın.</div>
            </div>
            <div class="settings-list">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success fw-bold text-center py-2"><?= $success ?></div>
                <?php endif; ?>
                <form method="post" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Başlangıç Saati</label>
                            <input type="time" class="form-control" name="baslangic" value="<?= htmlspecialchars($calisma['baslangic'] ?? '09:00') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bitiş Saati</label>
                            <input type="time" class="form-control" name="bitis" value="<?= htmlspecialchars($calisma['bitis'] ?? '18:00') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Haftalık Tatil Günleri</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($hafta as $gun): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="haftalik_tatil[]" value="<?= $gun ?>" id="tatil_<?= $gun ?>" <?= in_array($gun, $tatil_list) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tatil_<?= $gun ?>"><?= $gun ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" name="save_hours" class="btn btn-primary w-100"><i class="bi bi-save"></i> Kaydet</button>
                        </div>
                    </div>
                </form>
                <hr>
                <div class="mb-3">
                    <h5 class="fw-bold mb-2"><i class="bi bi-calendar-x text-danger"></i> Özel Tatil Günleri</h5>
                    <form method="post" class="row g-2 align-items-end mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Tatil Tarihi</label>
                            <input type="date" class="form-control" name="tatil_tarih" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Açıklama</label>
                            <input type="text" class="form-control" name="tatil_aciklama" placeholder="(Opsiyonel)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="add_holiday" class="btn btn-outline-danger w-100"><i class="bi bi-plus"></i> Ekle</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Açıklama</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tatil_gunleri)): ?>
                                    <tr><td colspan="3" class="text-center text-muted">Kayıtlı özel tatil günü yok.</td></tr>
                                <?php else: foreach ($tatil_gunleri as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['tarih']) ?></td>
                                        <td><?= htmlspecialchars($row['aciklama']) ?></td>
                                        <td>
                                            <a href="?delete_holiday=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tatil günü silinsin mi?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="settings-footer"></div>
        </div>
    </div>
</div>

<?php
include_once './footer.php';
mysqli_close($connection);
?>