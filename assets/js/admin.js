/**
 * Om Yaman Spices - Admin Dashboard Logic
 * Credentials: om7758217 / 7758217om
 */

const ADMIN_STATE = {
  products: [],
  categories: [],
  filtered: [],
  currentEditId: null,
  activeCategory: 'all',
  searchQuery: ''
};

document.addEventListener('DOMContentLoaded', () => {
  initAdminDashboard();
});

async function initAdminDashboard() {
  bindAdminEvents();
  await loadAdminData();
}

/**
 * Load Products & Categories from API
 */
async function loadAdminData() {
  try {
    const [prodRes, catRes] = await Promise.all([
      fetch('api.php?action=get_products').then(r => r.json()),
      fetch('api.php?action=get_categories').then(r => r.json())
    ]);

    if (prodRes.success) {
      ADMIN_STATE.products = prodRes.products;
    } else {
      // Fallback
      const fb = await fetch('data/products.json').then(r => r.json());
      ADMIN_STATE.products = fb;
    }

    if (catRes.success) {
      ADMIN_STATE.categories = catRes.categories;
    } else {
      const fc = await fetch('data/categories.json').then(r => r.json());
      ADMIN_STATE.categories = fc;
    }

    populateCategoryDropdowns();
    updateMetrics();
    applyAdminFilter();

  } catch (err) {
    console.error('Failed to load admin data:', err);
    showAdminToast('حدث خطأ أثناء تحميل بيانات المنتجات', 'error');
  }
}

/**
 * Update Metric Cards
 */
function updateMetrics() {
  const totalCount = ADMIN_STATE.products.length;
  const inStockCount = ADMIN_STATE.products.filter(p => p.in_stock !== false).length;
  const outStockCount = totalCount - inStockCount;
  const totalCategories = ADMIN_STATE.categories.length;

  const sumPrices = ADMIN_STATE.products.reduce((acc, p) => acc + (parseFloat(p.price) || 0), 0);
  const avgPrice = totalCount > 0 ? (sumPrices / totalCount).toFixed(1) : 0;

  const elTotal = document.getElementById('metricTotal');
  const elInStock = document.getElementById('metricInStock');
  const elOutStock = document.getElementById('metricOutStock');
  const elCategories = document.getElementById('metricCategories');
  const elAvgPrice = document.getElementById('metricAvgPrice');

  if (elTotal) elTotal.textContent = totalCount;
  if (elInStock) elInStock.textContent = inStockCount;
  if (elOutStock) elOutStock.textContent = outStockCount;
  if (elCategories) elCategories.textContent = totalCategories;
  if (elAvgPrice) elAvgPrice.textContent = `${avgPrice} ₪`;
}

/**
 * Populate Category Dropdowns
 */
function populateCategoryDropdowns() {
  const filterSelect = document.getElementById('adminCategoryFilter');
  const modalSelect = document.getElementById('productCategoryInput');

  if (filterSelect) {
    let opts = '<option value="all">جميع التصنيفات</option>';
    ADMIN_STATE.categories.forEach(c => {
      opts += `<option value="${c.id}">${c.name}</option>`;
    });
    filterSelect.innerHTML = opts;
  }

  if (modalSelect) {
    let opts = '';
    ADMIN_STATE.categories.forEach(c => {
      opts += `<option value="${c.id}">${c.name}</option>`;
    });
    modalSelect.innerHTML = opts;
  }
}

/**
 * Filter & Render Admin Table
 */
function applyAdminFilter() {
  let list = [...ADMIN_STATE.products];

  if (ADMIN_STATE.activeCategory && ADMIN_STATE.activeCategory !== 'all') {
    list = list.filter(p => p.category === ADMIN_STATE.activeCategory);
  }

  if (ADMIN_STATE.searchQuery.trim() !== '') {
    const q = ADMIN_STATE.searchQuery.trim().toLowerCase();
    list = list.filter(p => {
      const name = (p.name || '').toLowerCase();
      const sku = (p.sku || '').toLowerCase();
      return name.includes(q) || sku.includes(q);
    });
  }

  ADMIN_STATE.filtered = list;
  renderAdminTable();
}

function renderAdminTable() {
  const tbody = document.getElementById('adminProductsTableBody');
  const countEl = document.getElementById('adminFilteredCount');
  if (!tbody) return;

  if (countEl) countEl.textContent = `(عرض ${ADMIN_STATE.filtered.length} صنف)`;

  if (ADMIN_STATE.filtered.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--admin-muted);">
          لا توجد منتجات مطابقة لخيارات البحث والتصفية
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = ADMIN_STATE.filtered.map((p, index) => `
    <tr>
      <td style="font-weight: 700; color: var(--admin-muted);">${index + 1}</td>
      <td>
        <img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" class="table-thumb" onerror="this.src='https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=800&q=80'" />
      </td>
      <td>
        <div style="font-weight: 800; color: var(--admin-wood);">${escapeHtml(p.name)}</div>
        <small style="color: var(--admin-muted); font-size: 0.78rem;">كود: ${escapeHtml(p.sku || 'OY')}</small>
      </td>
      <td>
        <span class="badge-tag">${getCatTitle(p.category)}</span>
      </td>
      <td style="font-weight: 800; color: var(--admin-primary); font-size: 1.05rem;">
        ${p.price} ₪
      </td>
      <td>${escapeHtml(p.weight || '-')}</td>
      <td>
        <span class="status-badge ${p.in_stock !== false ? 'in-stock' : 'out-stock'}">
          <i class="fa-solid ${p.in_stock !== false ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>
          ${p.in_stock !== false ? 'متوفر' : 'غير متوفر'}
        </span>
      </td>
      <td>
        <div style="display: flex; gap: 0.4rem;">
          <button class="btn-action edit" onclick="openEditProductModal(${p.id})" title="تعديل المنتج">
            <i class="fa-solid fa-pen-to-square"></i>
          </button>
          <button class="btn-action delete" onclick="confirmDeleteProduct(${p.id}, '${escapeHtml(p.name)}')" title="حذف المنتج">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </div>
      </td>
    </tr>
  `).join('');
}

function getCatTitle(id) {
  const c = ADMIN_STATE.categories.find(item => item.id === id);
  return c ? c.name : id;
}

/**
 * Modal Handling (Add & Edit)
 */
function openAddProductModal() {
  ADMIN_STATE.currentEditId = null;
  document.getElementById('modalTitle').textContent = 'إضافة صنف جديد إلى المتجر';
  document.getElementById('productForm').reset();
  document.getElementById('productIdInput').value = '';
  document.getElementById('productInStockInput').checked = true;
  document.getElementById('productFeaturedInput').checked = false;
  openModal('productModal');
}

function openEditProductModal(id) {
  const product = ADMIN_STATE.products.find(p => p.id === id);
  if (!product) return;

  ADMIN_STATE.currentEditId = id;
  document.getElementById('modalTitle').textContent = `تعديل: ${product.name}`;
  document.getElementById('productIdInput').value = product.id;
  document.getElementById('productNameInput').value = product.name || '';
  document.getElementById('productCategoryInput').value = product.category || 'blends';
  document.getElementById('productPriceInput').value = product.price || '';
  document.getElementById('productWeightInput').value = product.weight || '250 جم';
  document.getElementById('productImageInput').value = product.image || '';
  document.getElementById('productBadgeInput').value = product.badge || '';
  document.getElementById('productDescInput').value = product.description || '';
  document.getElementById('productInStockInput').checked = product.in_stock !== false;
  document.getElementById('productFeaturedInput').checked = !!product.featured;

  openModal('productModal');
}

async function handleProductFormSubmit(e) {
  e.preventDefault();

  const id = ADMIN_STATE.currentEditId;
  const isEditing = id !== null;

  const payload = {
    id: id,
    name: document.getElementById('productNameInput').value.trim(),
    category: document.getElementById('productCategoryInput').value,
    price: parseFloat(document.getElementById('productPriceInput').value) || 0,
    weight: document.getElementById('productWeightInput').value.trim(),
    image: document.getElementById('productImageInput').value.trim(),
    badge: document.getElementById('productBadgeInput').value.trim(),
    description: document.getElementById('productDescInput').value.trim(),
    in_stock: document.getElementById('productInStockInput').checked,
    featured: document.getElementById('productFeaturedInput').checked
  };

  if (!payload.name || payload.price <= 0) {
    showAdminToast('يرجى كتابة اسم المنتج وتحديد سعر صحيح', 'error');
    return;
  }

  const action = isEditing ? 'update_product' : 'add_product';

  try {
    const res = await fetch(`api.php?action=${action}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.success) {
      if (isEditing) {
        const idx = ADMIN_STATE.products.findIndex(p => p.id === id);
        if (idx !== -1) ADMIN_STATE.products[idx] = data.product;
        showAdminToast('تم تحديث المنتج بنجاح', 'success');
      } else {
        ADMIN_STATE.products.unshift(data.product);
        showAdminToast('تمت إضافة المنتج الجديد بنجاح', 'success');
      }

      closeModal('productModal');
      updateMetrics();
      applyAdminFilter();
    } else {
      showAdminToast(data.message || 'حدث خطأ في الحفظ', 'error');
    }
  } catch (err) {
    console.error('Error saving product:', err);
    showAdminToast('تعذر الاتصال بالخادم لحفظ التعديل', 'error');
  }
}

/**
 * Delete Handling
 */
let productToDeleteId = null;

function confirmDeleteProduct(id, name) {
  productToDeleteId = id;
  const el = document.getElementById('deleteProductName');
  if (el) el.textContent = name;
  openModal('deleteModal');
}

async function executeDeleteProduct() {
  if (!productToDeleteId) return;

  try {
    const res = await fetch('api.php?action=delete_product', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: productToDeleteId })
    });

    const data = await res.json();
    if (data.success) {
      ADMIN_STATE.products = ADMIN_STATE.products.filter(p => p.id !== productToDeleteId);
      closeModal('deleteModal');
      showAdminToast('تم حذف المنتج بنجاح', 'success');
      updateMetrics();
      applyAdminFilter();
    } else {
      showAdminToast(data.message || 'تعذر حذف المنتج', 'error');
    }
  } catch (err) {
    console.error('Error deleting product:', err);
    showAdminToast('خطأ في الاتصال بالخادم', 'error');
  }
}

/**
 * Export Catalog to JSON file
 */
function exportCatalogJson() {
  const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(ADMIN_STATE.products, null, 2));
  const downloadAnchor = document.createElement('a');
  downloadAnchor.setAttribute("href", dataStr);
  downloadAnchor.setAttribute("download", `om_yaman_products_backup_${Date.now()}.json`);
  document.body.appendChild(downloadAnchor);
  downloadAnchor.click();
  downloadAnchor.remove();
  showAdminToast('تم تصدير نسخة احتياطية من البيانات بنجاح', 'success');
}

/**
 * Modal helpers
 */
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.add('active');
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.remove('active');
}

/**
 * Bind Admin DOM Events
 */
function bindAdminEvents() {
  // Category filter
  const catFilter = document.getElementById('adminCategoryFilter');
  if (catFilter) {
    catFilter.addEventListener('change', (e) => {
      ADMIN_STATE.activeCategory = e.target.value;
      applyAdminFilter();
    });
  }

  // Search input
  const searchInput = document.getElementById('adminSearchInput');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      ADMIN_STATE.searchQuery = e.target.value;
      applyAdminFilter();
    });
  }

  // Add Product button
  const btnAdd = document.getElementById('btnOpenAddProduct');
  if (btnAdd) {
    btnAdd.addEventListener('click', openAddProductModal);
  }

  // Product form
  const form = document.getElementById('productForm');
  if (form) {
    form.addEventListener('submit', handleProductFormSubmit);
  }

  // Delete confirm button
  const btnDeleteConfirm = document.getElementById('btnConfirmDelete');
  if (btnDeleteConfirm) {
    btnDeleteConfirm.addEventListener('click', executeDeleteProduct);
  }

  // Export JSON
  const btnExport = document.getElementById('btnExportJson');
  if (btnExport) {
    btnExport.addEventListener('click', exportCatalogJson);
  }

  // Close modals on overlay click or esc
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        overlay.classList.remove('active');
      }
    });
  });

  document.querySelectorAll('.btn-close-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      const modal = btn.closest('.modal-overlay');
      if (modal) modal.classList.remove('active');
    });
  });
}

function showAdminToast(msg, type = 'info') {
  let container = document.getElementById('adminToastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'adminToastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<span>${escapeHtml(msg)}</span>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

function escapeHtml(text) {
  if (!text) return '';
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}
