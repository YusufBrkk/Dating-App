<?php
include_once './header.php';
include_once './includes/dbh-inc.php';

// Müşteri silme işlemi
if (isset($_GET['deleteID'])) {
    $deleteID = intval($_GET['deleteID']);
    $stmt = mysqli_prepare($connection, "DELETE FROM musteriler WHERE musteriID = ?");
    mysqli_stmt_bind_param($stmt, "i", $deleteID);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Müşteri başarıyla silindi.";
    } else {
        $error = "Silme işlemi sırasında hata oluştu!";
    }
    mysqli_stmt_close($stmt);
}

// Müşteri güncelleme işlemi
if (isset($_POST['editID'])) {
    $editID = intval($_POST['editID']);
    $ad_soyad = trim($_POST['ad_soyad']);
    $telefon = trim($_POST['telefon']);
    if ($ad_soyad !== "" && $telefon !== "") {
        $stmt = mysqli_prepare($connection, "UPDATE musteriler SET ad_soyad=?, telefon=? WHERE musteriID=?");
        mysqli_stmt_bind_param($stmt, "ssi", $ad_soyad, $telefon, $editID);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Müşteri bilgileri güncellendi.";
        } else {
            $error = "Güncelleme sırasında hata oluştu!";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Ad Soyad ve Telefon boş olamaz!";
    }
}

// Müşterileri çek
$result = mysqli_query($connection, "SELECT * FROM musteriler ORDER BY musteriID DESC");
?>

<style>
    body {
        background: linear-gradient(120deg, #f8fafc 0%, #e0e7ef 100%);
        min-height: 100vh;
    }
    .users-card {
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
    .users-banner {
        background: linear-gradient(90deg, #2563eb 0%, #38bdf8 100%);
        color: #fff;
        padding: 2.2rem 1.5rem 1.2rem 1.5rem;
        text-align: center;
        border-top-left-radius: 1.3rem;
        border-top-right-radius: 1.3rem;
        position: relative;
    }
    .users-banner i {
        font-size: 2.7rem;
        margin-bottom: 0.5rem;
        color: #fff;
        filter: drop-shadow(0 2px 8px #2563eb55);
    }
    .users-banner h2 {
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 0.2rem;
        letter-spacing: 1px;
    }
    .users-banner .desc {
        font-size: 1.08rem;
        color: #e0e7ef;
        margin-bottom: 0;
    }
    .users-list {
        background: transparent;
        padding: 2rem 1.5rem 1.2rem 1.5rem;
    }
    .users-list .table {
        background: #f8fafc;
        border-radius: 0.7rem;
        box-shadow: 0 2px 8px 0 rgba(31,38,135,0.07);
        overflow: hidden;
    }
    .users-list .table thead {
        background: linear-gradient(90deg, #2563eb 0%, #06b6d4 100%);
        color: #fff;
    }
    .users-list .btn-outline-danger {
        border: 1.5px solid #dc3545;
        color: #dc3545;
        background: #fff;
    }
    .users-list .btn-outline-danger:hover {
        background: #dc3545;
        color: #fff;
    }
    .users-list .btn-outline-primary {
        border: 1.5px solid #2563eb;
        color: #2563eb;
        background: #fff;
    }
    .users-list .btn-outline-primary:hover {
        background: #2563eb;
        color: #fff;
    }
    .users-footer {
        padding: 0 1.5rem 1.5rem 1.5rem;
    }
    .users-footer .btn {
        width: 100%;
        font-size: 1.1rem;
        border-radius: 0.7rem;
        font-weight: 600;
        padding: 0.8rem 0;
    }
    @media (max-width: 600px) {
        .users-card {
            margin-top: 1.2rem;
        }
        .users-banner,
        .users-list,
        .users-footer {
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
        <a href="users.php" class="nav-link active">
            <i class="bi bi-people"></i>
            <span class="sidebar-link-text">Müşterileri Görüntüle</span>
        </a>
        <a href="calisma_saatleri.php" class="nav-link">
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

<div class="main-content">
    <div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 90vh;">
        <div class="card users-card rounded-4">
            <div class="users-banner">
                <i class="bi bi-people"></i>
                <h2>Müşteriler</h2>
                <div class="desc">Kayıtlı tüm müşteriler burada listelenir.</div>
            </div>
            <div class="users-list">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success fw-bold text-center py-2"><?= $success ?></div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger fw-bold text-center py-2"><?= $error ?></div>
                <?php endif; ?>
                <div class="table-responsive rounded-3 mb-3">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                            <tr class="fs-5">
                                <th class="p-3" scope="col">ID</th>
                                <th class="p-3" scope="col">Ad Soyad</th>
                                <th class="p-3" scope="col">Telefon</th>
                                <th class="p-3 text-center" scope="col">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) == 0) {
                                echo '<tr><td class="p-3 text-dark fw-bold fs-5 text-center" colspan="4">Kayıtlı müşteri yok.</td></tr>';
                            } else {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $id = $row["musteriID"];
                                    $adsoyad = htmlspecialchars($row["ad_soyad"]);
                                    $telefon = htmlspecialchars($row["telefon"]);
                                    echo '<tr>
                                        <td class="p-3">' . $id . '</td>
                                        <td class="p-3">' . $adsoyad . '</td>
                                        <td class="p-3">' . $telefon . '</td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-primary btn-sm me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="' . $id . '"
                                                data-adsoyad="' . $adsoyad . '"
                                                data-telefon="' . $telefon . '">
                                                <i class="bi bi-pencil"></i> Düzenle
                                            </button>
                                            <a href="users.php?deleteID=' . $id . '" class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Müşteri silinsin mi?\')">
                                                <i class="bi bi-trash"></i> Sil
                                            </a>
                                        </td>
                                    </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="users-footer">
                <?php
                $backUrl = (isset($_SESSION["adminID"])) ? "./admin.php" : "./index.php";
                ?>
                <a class="btn btn-outline-dark btn-lg mt-3" href="<?= $backUrl ?>">
                    <i class="bi bi-arrow-left"></i> Geri
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Düzenle Modalı -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel"><i class="bi bi-pencil"></i> Müşteri Bilgilerini Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="editID" id="editID">
        <div class="mb-3">
            <label for="editAdSoyad" class="form-label">Ad Soyad</label>
            <input type="text" class="form-control" id="editAdSoyad" name="ad_soyad" required>
        </div>
        <div class="mb-3">
            <label for="editTelefon" class="form-label">Telefon</label>
            <input type="text" class="form-control" id="editTelefon" name="telefon" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary w-100">Kaydet</button>
      </div>
    </form>
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

    // Modal açılırken ilgili müşteri bilgilerini doldur
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var adsoyad = button.getAttribute('data-adsoyad');
        var telefon = button.getAttribute('data-telefon');
        document.getElementById('editID').value = id;
        document.getElementById('editAdSoyad').value = adsoyad;
        document.getElementById('editTelefon').value = telefon;
    });
</script>

<?php
include_once './footer.php';
mysqli_close($connection);
?>