/**
 * Om Yaman Spices (توابل وبهارات أم يمان)
 * Main Storefront Application Logic
 */

// Global Configuration
const CONFIG = {
  WHATSAPP_NUMBER: '972595896451',
  DEFAULT_PAGE_SIZE: 24,
  API_BASE: 'api.php',
  DATA_FALLBACK: 'data/products.json',
  CATEGORIES_FALLBACK: 'data/categories.json'
};

// Application State
const state = {
  allProducts: [],
  filteredProducts: [],
  categories: [],
  activeCategory: 'all',
  searchQuery: '',
  sortBy: 'featured',
  displayedCount: CONFIG.DEFAULT_PAGE_SIZE,
  cart: []
};

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', () => {
  initCart();
  loadCatalogData();
  bindEvents();
});

/**
 * Load Catalog Data from API with local fallback
 */
async function loadCatalogData() {
  try {
    // Try API first
    const [productsRes, categoriesRes] = await Promise.all([
      fetch(`${CONFIG.API_BASE}?action=get_products`).catch(() => null),
      fetch(`${CONFIG.API_BASE}?action=get_categories`).catch(() => null)
    ]);

    let products = null;
    let categories = null;

    if (productsRes && productsRes.ok) {
      const data = await productsRes.json();
      if (data.success && Array.isArray(data.products)) {
        products = data.products;
      }
    }

    if (categoriesRes && categoriesRes.ok) {
      const data = await categoriesRes.json();
      if (data.success && Array.isArray(data.categories)) {
        categories = data.categories;
      }
    }

    // Fallback to static JSON if API fails or when opened directly
    if (!products) {
      const fallbackProducts = await fetch(CONFIG.DATA_FALLBACK);
      products = await fallbackProducts.json();
    }

    if (!categories) {
      const fallbackCategories = await fetch(CONFIG.CATEGORIES_FALLBACK);
      categories = await fallbackCategories.json();
    }

    state.allProducts = products;
    state.categories = categories;

    renderCategoriesNav();
    applyFilters();

  } catch (error) {
    console.error('Error loading catalog data:', error);
    showToast('حدث خطأ في تحميل المنتجات، يرجى المحاولة لاحقاً', 'error');
  }
}

/**
 * Render Categories Nav Pills
 */
function renderCategoriesNav() {
  const container = document.getElementById('categoriesNav');
  if (!container) return;

  const totalCount = state.allProducts.length;

  let html = `
    <button class="category-pill ${state.activeCategory === 'all' ? 'active' : ''}" data-category="all">
      <i class="fa-solid fa-border-all"></i>
      <span>جميع الأصناف</span>
      <span class="pill-count">${totalCount}</span>
    </button>
  `;

  state.categories.forEach(cat => {
    // Calculate live count
    const count = state.allProducts.filter(p => p.category === cat.id).length;
    html += `
      <button class="category-pill ${state.activeCategory === cat.id ? 'active' : ''}" data-category="${cat.id}">
        <i class="fa-solid ${cat.icon || 'fa-mortar-pestle'}"></i>
        <span>${cat.name}</span>
        <span class="pill-count">${count}</span>
      </button>
    `;
  });

  container.innerHTML = html;

  // Bind clicks
  container.querySelectorAll('.category-pill').forEach(btn => {
    btn.addEventListener('click', () => {
      container.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      state.activeCategory = btn.dataset.category;
      state.displayedCount = CONFIG.DEFAULT_PAGE_SIZE;
      applyFilters();
    });
  });
}

/**
 * Filter & Sort Products
 */
function applyFilters() {
  let list = [...state.allProducts];

  // 1. Category filter
  if (state.activeCategory && state.activeCategory !== 'all') {
    list = list.filter(p => p.category === state.activeCategory);
  }

  // 2. Search query filter
  if (state.searchQuery.trim() !== '') {
    const q = state.searchQuery.trim().toLowerCase();
    list = list.filter(p => {
      const name = (p.name || '').toLowerCase();
      const desc = (p.description || '').toLowerCase();
      const sku = (p.sku || '').toLowerCase();
      return name.includes(q) || desc.includes(q) || sku.includes(q);
    });
  }

  // 3. Sorting
  if (state.sortBy === 'price_asc') {
    list.sort((a, b) => a.price - b.price);
  } else if (state.sortBy === 'price_desc') {
    list.sort((a, b) => b.price - a.price);
  } else if (state.sortBy === 'name') {
    list.sort((a, b) => a.name.localeCompare(b.name, 'ar'));
  } else if (state.sortBy === 'featured') {
    list.sort((a, b) => (b.featured ? 1 : 0) - (a.featured ? 1 : 0));
  }

  state.filteredProducts = list;
  renderProductsGrid();
  renderPaginationInfo();
}

/**
 * Render Products Grid
 */
function renderProductsGrid() {
  const container = document.getElementById('productsGrid');
  if (!container) return;

  const totalFiltered = state.filteredProducts.length;

  if (totalFiltered === 0) {
    container.innerHTML = `
      <div class="empty-state">
        <i class="fa-solid fa-seedling"></i>
        <h3>لم نعثر على بهارات مطابقة لبحثك</h3>
        <p>جرب البحث بكلمات أخرى أو تصفح باقي التصنيفات المميزة لمتجر أم يمان.</p>
        <button class="btn-hero-primary" onclick="resetFilters()">عرض جميع الأصناف</button>
      </div>
    `;
    return;
  }

  const itemsToRender = state.filteredProducts.slice(0, state.displayedCount);

  container.innerHTML = itemsToRender.map(product => `
    <div class="product-card" data-id="${product.id}">
      <div class="product-image-wrapper">
        <img 
          src="${escapeHtml(product.image)}" 
          alt="${escapeHtml(product.name)}" 
          class="product-image"
          loading="lazy"
          onerror="this.src='https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=800&q=80'"
        />
        ${product.badge ? `<span class="product-badge">${escapeHtml(product.badge)}</span>` : ''}
        <span class="product-weight">${escapeHtml(product.weight || '250 جم')}</span>
      </div>

      <div class="product-content">
        <div class="product-category-name">${getCategoryName(product.category)}</div>
        <h3 class="product-name" title="${escapeHtml(product.name)}">${escapeHtml(product.name)}</h3>
        <p class="product-desc">${escapeHtml(product.description || '')}</p>

        <div class="product-footer">
          <div class="product-price-row">
            <div class="product-price">
              ${product.price.toFixed(0)} <span class="currency">₪</span>
            </div>
            <span class="product-stock">
              <i class="fa-solid fa-circle-check"></i> متوفر طازج
            </span>
          </div>

          <div class="product-actions">
            <button class="btn-add-cart" onclick="addToCart(${product.id})">
              <i class="fa-solid fa-cart-plus"></i> أضف للسلة
            </button>
            <button class="btn-whatsapp-order" title="طلب مباشر عبر واتساب" onclick="orderSingleViaWhatsApp(${product.id})">
              <i class="fa-brands fa-whatsapp"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  `).join('');
}

/**
 * Render Pagination & Load More Info
 */
function renderPaginationInfo() {
  const wrapper = document.getElementById('loadMoreWrapper');
  const infoText = document.getElementById('loadMoreInfo');
  const btnLoadMore = document.getElementById('btnLoadMore');

  if (!wrapper || !infoText || !btnLoadMore) return;

  const total = state.filteredProducts.length;
  const current = Math.min(state.displayedCount, total);

  infoText.textContent = `عرض ${current} من أصل ${total} صنف مميز`;

  if (current >= total) {
    btnLoadMore.style.display = 'none';
  } else {
    btnLoadMore.style.display = 'inline-flex';
  }
}

/**
 * Load More Handler
 */
function loadMoreProducts() {
  state.displayedCount += CONFIG.DEFAULT_PAGE_SIZE;
  renderProductsGrid();
  renderPaginationInfo();
}

/**
 * Helper: Category Name by ID
 */
function getCategoryName(catId) {
  const cat = state.categories.find(c => c.id === catId);
  return cat ? cat.name : 'بهارات وتوابل';
}

/**
 * Reset all filters
 */
function resetFilters() {
  state.activeCategory = 'all';
  state.searchQuery = '';
  state.sortBy = 'featured';
  state.displayedCount = CONFIG.DEFAULT_PAGE_SIZE;

  const searchInput = document.getElementById('searchInput');
  if (searchInput) searchInput.value = '';

  const sortSelect = document.getElementById('sortSelect');
  if (sortSelect) sortSelect.value = 'featured';

  renderCategoriesNav();
  applyFilters();
}

/* ==========================================================================
   Shopping Cart Logic
   ========================================================================== */

function initCart() {
  try {
    const saved = localStorage.getItem('om_yaman_cart');
    state.cart = saved ? JSON.parse(saved) : [];
  } catch (e) {
    state.cart = [];
  }
  updateCartUI();
}

function saveCart() {
  try {
    localStorage.setItem('om_yaman_cart', JSON.stringify(state.cart));
  } catch (e) {
    console.error('Error saving cart:', e);
  }
  updateCartUI();
}

function addToCart(productId, qty = 1) {
  const product = state.allProducts.find(p => p.id === productId);
  if (!product) return;

  const existing = state.cart.find(item => item.id === productId);
  if (existing) {
    existing.quantity += qty;
  } else {
    state.cart.push({
      id: product.id,
      name: product.name,
      price: product.price,
      weight: product.weight,
      image: product.image,
      quantity: qty
    });
  }

  saveCart();
  showToast(`تمت إضافة "${product.name}" إلى السلة`, 'success');
}

function updateCartItemQty(productId, delta) {
  const item = state.cart.find(i => i.id === productId);
  if (!item) return;

  item.quantity += delta;
  if (item.quantity <= 0) {
    removeFromCart(productId);
  } else {
    saveCart();
  }
}

function removeFromCart(productId) {
  state.cart = state.cart.filter(i => i.id !== productId);
  saveCart();
  showToast('تم حذف المنتج من السلة');
}

function updateCartUI() {
  const countBadge = document.getElementById('cartCountBadge');
  const cartItemsList = document.getElementById('cartItemsList');
  const cartTotalAmount = document.getElementById('cartTotalAmount');

  const totalCount = state.cart.reduce((sum, item) => sum + item.quantity, 0);
  const totalPrice = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

  if (countBadge) countBadge.textContent = totalCount;
  if (cartTotalAmount) cartTotalAmount.textContent = `${totalPrice.toFixed(0)} ₪`;

  if (!cartItemsList) return;

  if (state.cart.length === 0) {
    cartItemsList.innerHTML = `
      <div style="text-align: center; padding: 2.5rem 1rem; color: var(--spice-muted);">
        <i class="fa-solid fa-basket-shopping" style="font-size: 2.5rem; margin-bottom: 0.75rem; color: var(--spice-border);"></i>
        <p style="font-weight: 700;">سلتك فارغة حالياً</p>
        <span style="font-size: 0.85rem;">اختر من تشكيلتنا الفاخرة وأضف لمطبخك نكهة أصيلة</span>
      </div>
    `;
    return;
  }

  cartItemsList.innerHTML = state.cart.map(item => `
    <div class="cart-item">
      <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" class="cart-item-img" />
      <div class="cart-item-info">
        <h4 class="cart-item-title">${escapeHtml(item.name)}</h4>
        <div class="cart-item-price">${item.price} ₪ <small style="color: var(--spice-muted); font-size: 0.75rem;">(${escapeHtml(item.weight)})</small></div>
        <div class="cart-item-controls">
          <button class="btn-qty" onclick="updateCartItemQty(${item.id}, -1)">-</button>
          <span class="cart-item-qty">${item.quantity}</span>
          <button class="btn-qty" onclick="updateCartItemQty(${item.id}, 1)">+</button>
        </div>
      </div>
      <button class="btn-delete-item" onclick="removeFromCart(${item.id})" title="حذف">
        <i class="fa-solid fa-trash-can"></i>
      </button>
    </div>
  `).join('');
}

function toggleCartDrawer(open = null) {
  const drawer = document.getElementById('cartDrawer');
  const overlay = document.getElementById('cartDrawerOverlay');
  if (!drawer || !overlay) return;

  const isActive = drawer.classList.contains('active');
  const shouldOpen = open !== null ? open : !isActive;

  if (shouldOpen) {
    drawer.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  } else {
    drawer.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }
}

/* ==========================================================================
   WhatsApp Direct Ordering Logic
   ========================================================================== */

/**
 * 1. Single Product Direct WhatsApp Order
 */
function orderSingleViaWhatsApp(productId) {
  const product = state.allProducts.find(p => p.id === productId);
  if (!product) return;

  const msg = 
`السلام عليكم متجر أم يمان للبهارات والتوابل 🌿
أود الاستفسار وطلب هذا المنتج مباشرة:

📦 *${product.name}*
⚖️ الوزن/العبوة: ${product.weight || '250 جم'}
💰 السعر: ${product.price} ₪
🔢 الكود: ${product.sku || 'OY'}

يرجى تزويدي بتفاصيل التوصيل وتأكيد الطلب. شكراً لكم!`;

  const waUrl = `https://wa.me/${CONFIG.WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`;
  window.open(waUrl, '_blank');
}

/**
 * 2. Entire Cart WhatsApp Checkout
 */
function checkoutCartViaWhatsApp() {
  if (state.cart.length === 0) {
    showToast('السلة فارغة، أضف بعض البهارات أولاً!', 'error');
    return;
  }

  const nameInput = document.getElementById('checkoutCustomerName');
  const phoneInput = document.getElementById('checkoutCustomerPhone');
  const cityInput = document.getElementById('checkoutCustomerCity');
  const notesInput = document.getElementById('checkoutCustomerNotes');

  const customerName = nameInput ? nameInput.value.trim() : '';
  const customerPhone = phoneInput ? phoneInput.value.trim() : '';
  const customerCity = cityInput ? cityInput.value.trim() : '';
  const notes = notesInput ? notesInput.value.trim() : '';

  if (!customerName || !customerCity) {
    showToast('يرجى كتابة الاسم والمدينة لتسهيل التوصيل', 'error');
    if (nameInput && !customerName) nameInput.focus();
    else if (cityInput && !customerCity) cityInput.focus();
    return;
  }

  const totalPrice = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

  let itemsListText = '';
  state.cart.forEach((item, index) => {
    const lineTotal = item.price * item.quantity;
    itemsListText += `${index + 1}. *${item.name}* (${item.weight})\n   الكمية: ${item.quantity} × ${item.price} ₪ = ${lineTotal} ₪\n`;
  });

  const message = 
`*طلب جديد من متجر توابل وبهارات أم يمان* 🌿✨
--------------------------------------
👤 *بيانات العميل:*
• الاسم: ${customerName}
• الهاتف: ${customerPhone || 'نفس رقم الواتساب'}
• العنوان/المدينة: ${customerCity}
${notes ? `• ملاحظات إضافية: ${notes}\n` : ''}
--------------------------------------
🛍️ *تفاصيل الطلبية:*
${itemsListText}
--------------------------------------
💵 *إجمالي الحساب:* *${totalPrice.toFixed(0)} ₪ (شيكل)*
--------------------------------------
يرجى تأكيد استلام الطلب وموعد التوصيل. بارك الله فيكم!`;

  const waUrl = `https://wa.me/${CONFIG.WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;
  window.open(waUrl, '_blank');

  // Optionally clear cart after confirming order
  showToast('تم تحويلك إلى واتساب لإتمام الطلب!', 'success');
}

/* ==========================================================================
   Events & Utilities
   ========================================================================== */

function bindEvents() {
  // Search input debounced
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    let timeout = null;
    searchInput.addEventListener('input', (e) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        state.searchQuery = e.target.value;
        state.displayedCount = CONFIG.DEFAULT_PAGE_SIZE;
        applyFilters();
      }, 250);
    });
  }

  // Sort select
  const sortSelect = document.getElementById('sortSelect');
  if (sortSelect) {
    sortSelect.addEventListener('change', (e) => {
      state.sortBy = e.target.value;
      applyFilters();
    });
  }

  // Load more button
  const btnLoadMore = document.getElementById('btnLoadMore');
  if (btnLoadMore) {
    btnLoadMore.addEventListener('click', loadMoreProducts);
  }

  // Cart Drawer open/close
  const btnCartToggle = document.getElementById('btnCartToggle');
  if (btnCartToggle) {
    btnCartToggle.addEventListener('click', () => toggleCartDrawer(true));
  }

  const btnCloseCart = document.getElementById('btnCloseCart');
  if (btnCloseCart) {
    btnCloseCart.addEventListener('click', () => toggleCartDrawer(false));
  }

  const cartOverlay = document.getElementById('cartDrawerOverlay');
  if (cartOverlay) {
    cartOverlay.addEventListener('click', () => toggleCartDrawer(false));
  }

  // WhatsApp Checkout button
  const btnCheckout = document.getElementById('btnCheckoutWhatsApp');
  if (btnCheckout) {
    btnCheckout.addEventListener('click', checkoutCartViaWhatsApp);
  }
}

/**
 * Toast Notifications
 */
function showToast(message, type = 'info') {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  
  let icon = 'fa-circle-info';
  if (type === 'success') icon = 'fa-circle-check';
  if (type === 'error') icon = 'fa-circle-exclamation';

  toast.innerHTML = `
    <i class="fa-solid ${icon}"></i>
    <span>${escapeHtml(message)}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-100%)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3200);
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
