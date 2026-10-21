<?php
/**
 * Om Yaman Spices - Admin Dashboard Panel
 * Secure Management Panel
 * Credentials: om7758217 / 7758217om
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ADMIN_USER', 'om7758217');
define('ADMIN_PASS', '7758217om');

$loginError = '';

// Handle Logout
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Handle Form Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['om_yaman_admin'] = true;
        $_SESSION['admin_user'] = ADMIN_USER;
        $_SESSION['login_time'] = time();
        header("Location: admin.php");
        exit();
    } else {
        $loginError = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}

$isLoggedIn = isset($_SESSION['om_yaman_admin']) && $_SESSION['om_yaman_admin'] === true;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $isLoggedIn ? 'لوحة تحكم توابل أم يمان' : 'تسجيل دخول الإدارة | توابل أم يمان'; ?></title>
  
  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Admin Stylesheet -->
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<?php if (!$isLoggedIn): ?>
  <!-- ==========================================
       LOGIN VIEW
       ========================================== -->
  <div class="login-wrapper">
    <div class="login-card">
      <div class="login-logo">
        <i class="fa-solid fa-mortar-pestle"></i>
      </div>
      <h2>لوحة إدارة متجر أم يمان</h2>
      <p>أدخل بيانات الحساب لتعديل المنتجات والأسعار والمخزون</p>

      <?php if (!empty($loginError)): ?>
        <div style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 0.75rem; border-radius: 8px; margin-bottom: 1.25rem; font-weight: 700; font-size: 0.88rem;">
          <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($loginError); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="admin.php" class="login-form">
        <input type="hidden" name="action" value="login">

        <div class="form-group">
          <label for="adminUsername">اسم المستخدم</label>
          <input 
            type="text" 
            id="adminUsername" 
            name="username" 
            class="form-control" 
            placeholder="om7758217" 
            required 
            autofocus 
          />
        </div>

        <div class="form-group">
          <label for="adminPassword">كلمة المرور</label>
          <input 
            type="password" 
            id="adminPassword" 
            name="password" 
            class="form-control" 
            placeholder="••••••••" 
            required 
          />
        </div>

        <button type="submit" class="btn-admin-primary" style="margin-top: 0.5rem;">
          <i class="fa-solid fa-right-to-bracket"></i> دخول إلى لوحة التحكم
        </button>

        <a href="index.php" style="text-align: center; font-size: 0.85rem; color: var(--admin-muted); margin-top: 0.5rem; text-decoration: none;">
          <i class="fa-solid fa-arrow-right"></i> العودة للمتجر
        </a>
      </form>
    </div>
  </div>

<?php else: ?>
  <!-- ==========================================
       AUTHENTICATED DASHBOARD VIEW
       ========================================== -->
  <header class="admin-navbar">
    <div class="admin-nav-inner">
      <div class="admin-brand">
        <div class="admin-brand-logo">
          <i class="fa-solid fa-mortar-pestle"></i>
        </div>
        <div class="admin-brand-info">
          <h1>لوحة تحكم توابل أم يمان</h1>
          <span>مرحباً بك، أم يمان (المدير العام)</span>
        </div>
      </div>

      <div class="admin-user-nav">
        <a href="index.php" target="_blank" class="btn-admin-outline">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> مشاهدة المتجر
        </a>
        <a href="admin.php?logout=1" class="btn-admin-danger">
          <i class="fa-solid fa-power-off"></i> خروج
        </a>
      </div>
    </div>
  </header>

  <main class="admin-container">

    <!-- Metrics Cards -->
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-icon">
          <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <div class="metric-info">
          <h3 id="metricTotal">...</h3>
          <p>إجمالي الأصناف بالمتجر</p>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon green">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="metric-info">
          <h3 id="metricInStock">...</h3>
          <p>أصناف متوفرة للبيع</p>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon red">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="metric-info">
          <h3 id="metricOutStock">...</h3>
          <p>أصناف نافدة أو قليلة</p>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon blue">
          <i class="fa-solid fa-tags"></i>
        </div>
        <div class="metric-info">
          <h3 id="metricCategories">...</h3>
          <p>أقسام وتصنيفات</p>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-icon">
          <i class="fa-solid fa-shekel-sign"></i>
        </div>
        <div class="metric-info">
          <h3 id="metricAvgPrice">...</h3>
          <p>متوسط سعر الصنف</p>
        </div>
      </div>
    </div>

    <!-- Action & Filter Bar -->
    <div class="action-bar">
      <div class="action-bar-search">
        <input 
          type="text" 
          id="adminSearchInput" 
          class="form-control" 
          placeholder="بحث سريع بالاسم أو الكود..." 
        />
        <select id="adminCategoryFilter" class="form-control" style="max-width: 200px;">
          <option value="all">جميع الأقسام</option>
        </select>
      </div>

      <div class="action-bar-buttons">
        <button class="btn-admin-outline" id="btnExportJson">
          <i class="fa-solid fa-download"></i> تصدير البيانات (JSON)
        </button>
        <button class="btn-admin-primary" id="btnOpenAddProduct">
          <i class="fa-solid fa-plus"></i> إضافة منتج جديد
        </button>
      </div>
    </div>

    <!-- Products Table -->
    <div class="table-card">
      <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--admin-wood);">
          قائمة المنتجات والتوابل <span id="adminFilteredCount" style="font-size: 0.88rem; color: var(--admin-muted); font-weight: 600;"></span>
        </h3>
      </div>

      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th style="width: 70px;">الصورة</th>
              <th>اسم الصنف والكود</th>
              <th>القسم</th>
              <th>السعر (₪)</th>
              <th>الوزن / العبوة</th>
              <th>حالة التوفر</th>
              <th style="width: 100px;">إجراءات</th>
            </tr>
          </thead>
          <tbody id="adminProductsTableBody">
            <tr>
              <td colspan="8" style="text-align: center; padding: 2rem;">
                <i class="fa-solid fa-spinner fa-spin"></i> جاري تحميل المنتجات...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <!-- Add / Edit Product Modal -->
  <div class="modal-overlay" id="productModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="modalTitle">إضافة صنف جديد</h3>
        <button class="btn-close-modal"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <form id="productForm">
        <div class="modal-body">
          <input type="hidden" id="productIdInput" />

          <div class="form-group" style="margin-bottom: 1rem;">
            <label for="productNameInput">اسم الصنف بالكامل *</label>
            <input type="text" id="productNameInput" class="form-control" required placeholder="مثال: بهار منسف أردني فاخر" />
          </div>

          <div class="form-grid-2" style="margin-bottom: 1rem;">
            <div class="form-group">
              <label for="productCategoryInput">القسم / التصنيف *</label>
              <select id="productCategoryInput" class="form-control" required>
                <!-- Populated by JS -->
              </select>
            </div>

            <div class="form-group">
              <label for="productPriceInput">السعر بالشيكل (₪) *</label>
              <input type="number" id="productPriceInput" class="form-control" step="0.5" min="1" required placeholder="25" />
            </div>
          </div>

          <div class="form-grid-2" style="margin-bottom: 1rem;">
            <div class="form-group">
              <label for="productWeightInput">الوزن / حجم العبوة</label>
              <input type="text" id="productWeightInput" class="form-control" placeholder="مثال: 250 جم أو 1 كغم" />
            </div>

            <div class="form-group">
              <label for="productBadgeInput">شارة ترويجية (Badge)</label>
              <input type="text" id="productBadgeInput" class="form-control" placeholder="الأكثر طلباً، بلدي، فاخر..." />
            </div>
          </div>

          <div class="form-group" style="margin-bottom: 1rem;">
            <label for="productImageInput">رابط صورة المنتج (URL)</label>
            <input type="url" id="productImageInput" class="form-control" placeholder="https://images.unsplash.com/..." />
          </div>

          <div class="form-group" style="margin-bottom: 1rem;">
            <label for="productDescInput">وصف الصنف ومكوناته واستخداماته</label>
            <textarea id="productDescInput" class="form-control" rows="3" placeholder="اكتب تفاصيل التوليفة والفوائد..."></textarea>
          </div>

          <div style="display: flex; gap: 1.5rem; margin-top: 1rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 700;">
              <input type="checkbox" id="productInStockInput" checked />
              <span>متوفر في المخزن (جاهز للطلب)</span>
            </label>

            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 700;">
              <input type="checkbox" id="productFeaturedInput" />
              <span>تمييز في الصفحة الرئيسية</span>
            </label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn-admin-outline btn-close-modal">إلغاء</button>
          <button type="submit" class="btn-admin-primary">
            <i class="fa-solid fa-check"></i> حفظ التغييرات
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal-overlay" id="deleteModal">
    <div class="modal-box" style="max-width: 420px;">
      <div class="modal-header">
        <h3 style="color: var(--admin-danger);"><i class="fa-solid fa-triangle-exclamation"></i> تأكيد الحذف</h3>
        <button class="btn-close-modal"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <div class="modal-body">
        <p style="font-size: 1rem; color: var(--admin-wood);">
          هل أنت متأكد من رغبتك في حذف الصنف: <strong id="deleteProductName" style="color: var(--admin-primary);"></strong>؟
        </p>
        <p style="font-size: 0.85rem; color: var(--admin-muted); margin-top: 0.5rem;">
          لا يمكن التراجع عن هذا الإجراء وسيتم إزالة الصنف من المتجر فوراً.
        </p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-admin-outline btn-close-modal">تراجع</button>
        <button type="button" class="btn-admin-danger" id="btnConfirmDelete">
          <i class="fa-solid fa-trash-can"></i> نعم، حذف الصنف
        </button>
      </div>
    </div>
  </div>

  <!-- Admin JS -->
  <script src="assets/js/admin.js"></script>

<?php endif; ?>

</body>
</html>
