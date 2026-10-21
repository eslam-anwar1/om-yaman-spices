<?php
/**
 * Om Yaman Spices - Main Storefront
 * متجر توابل وبهارات أم يمان
 */
$pageTitle = "توابل وبهارات أم يمان | أصالة النكهة الفلسطينية والشرقية";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle; ?></title>
  
  <!-- Primary Meta Tags -->
  <meta name="description" content="متجر توابل وبهارات أم يمان - تشكيلة فاخرة لأكثر من 180 صنف من البهارات والأعشاب والمتبلات البلدية الفلسطينية والشرقية. طحن طازج، جودة مضمونة، وطلب فوري عبر واتساب.">
  <meta name="keywords" content="توابل أم يمان, بهارات فلسطينية, زعتر بلدي, سماق فلاحي, بهار منسف, فريكة جنين, ميرمية جبلية, بهارات مشكلة">
  
  <!-- Open Graph / WhatsApp Sharing -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="توابل وبهارات أم يمان | نكهة الأصالة من خيرات الطبيعة">
  <meta property="og:description" content="أكثر من 180 صنف من التوابل الفاخرة، الزعتر البلدي، السماق الفلاحي والأعشاب الجبلية بأسعار منافسة (شيكل) وتوصيل سريع.">
  <meta property="og:image" content="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=1200&q=80">

  <!-- Google Fonts: Cairo & Amiri -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Stylesheets -->
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

  <!-- Top Announcement Bar -->
  <div class="top-bar">
    <div class="container">
      <div>
        <i class="fa-solid fa-leaf text-warning"></i>
        <span>توابل طبيعية 100% مطحونة طازجة بدون أي إضافات صناعية أو مواد حافظة</span>
      </div>
      <div class="top-bar-contact">
        <a href="https://wa.me/972595896451" target="_blank">
          <i class="fa-brands fa-whatsapp"></i> طلب سريع: +972 59-589-6451
        </a>
        <a href="admin.php" style="opacity: 0.85;">
          <i class="fa-solid fa-lock"></i> لوحة الإدارة
        </a>
      </div>
    </div>
  </div>

  <!-- Main Header -->
  <header class="site-header">
    <div class="container header-main">
      <div class="header-inner">
        
        <!-- Brand Logo -->
        <a href="index.php" class="logo">
          <div class="logo-icon-wrapper">
            <i class="fa-solid fa-mortar-pestle"></i>
          </div>
          <div class="logo-text">
            <h1>توابل أم يمان</h1>
            <p>أصالة البهارات الفلسطينية والشرقية</p>
          </div>
        </a>

        <!-- Live Real-Time Search Bar -->
        <div class="search-wrapper">
          <input 
            type="text" 
            id="searchInput" 
            class="search-input" 
            placeholder="ابحث عن بهارك المفضل (مثل: منسف، سماق، هيل، زعتر...)"
            autocomplete="off"
          />
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <!-- Header Actions -->
        <div class="header-actions">
          <!-- WhatsApp Contact Button -->
          <a href="https://wa.me/972595896451?text=<?php echo urlencode('مرحباً أم يمان، أود الاستفسار عن تشكيلة التوابل والبهارات المتوفرة لديكم.'); ?>" target="_blank" class="btn-whatsapp-header">
            <i class="fa-brands fa-whatsapp"></i>
            <span>تواصل عبر واتساب</span>
          </a>

          <!-- Cart Drawer Button -->
          <button class="btn-icon" id="btnCartToggle" title="سلة المشتريات">
            <i class="fa-solid fa-basket-shopping"></i>
            <span class="badge-count" id="cartCountBadge">0</span>
          </button>
        </div>

      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <div class="hero-content">
        <div class="hero-tag">
          <i class="fa-solid fa-award"></i> نكهة البيت الفلسطيني على أصولها
        </div>
        <h2 class="hero-title">
          سر طَبخاتك الشهية يبدأ من <span>توابل أم يمان</span>
        </h2>
        <p class="hero-desc">
          أكثر من 180 صنفاً من أندر وأجود التوابل الحصوية والمطحونة، الخلطات التراثية للمنسف والمسخن والمقلوبة، الزعتر البلدي وزيت الزيتون البكر. طحن طازج، نكهة فواحة، وجودة تشهد لها الأجيال.
        </p>

        <div class="hero-buttons">
          <a href="#catalogSection" class="btn-hero-primary">
            <i class="fa-solid fa-store"></i> استكشف التشكيلة (180+ صنف)
          </a>
          <button class="btn-hero-secondary" onclick="document.getElementById('chatToggleBtn').click()">
            <i class="fa-solid fa-wand-magic-sparkles"></i> اسأل أم يمان عن بهار طبختك
          </button>
        </div>
      </div>

      <!-- Trust Badges Strip -->
      <div class="features-strip">
        <div class="feature-box">
          <i class="fa-solid fa-mortar-pestle"></i>
          <div>
            <h4>طحن طازج ومستمر</h4>
            <p>تُطحن التوابل أسبوعياً لحفظ الزيوت العطرية</p>
          </div>
        </div>

        <div class="feature-box">
          <i class="fa-solid fa-certificate"></i>
          <div>
            <h4>بلدي ونقي 100%</h4>
            <p>خالية من أي ملونات أو نشا أو مواد كيميائية</p>
          </div>
        </div>

        <div class="feature-box">
          <i class="fa-solid fa-truck-fast"></i>
          <div>
            <h4>توصيل سريع ومضمون</h4>
            <p>خدمة توصيل تغطي كافة المدن والقرى</p>
          </div>
        </div>

        <div class="feature-box">
          <i class="fa-brands fa-whatsapp"></i>
          <div>
            <h4>طلب مباشر عبر واتساب</h4>
            <p>تأكيد فوري للطلبية بضغطة زر واحدة</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Products Catalog Section -->
  <section class="catalog-section" id="catalogSection">
    <div class="container">

      <!-- Section Head & Sort -->
      <div class="section-head">
        <div class="section-title">
          <h2>
            <i class="fa-solid fa-jar"></i> تشكيلة بهارات وتوابل أم يمان
          </h2>
          <p>تصفح خياراتنا المختارة بعناية لألذ الطبخات الشرقية والغربية</p>
        </div>

        <div class="catalog-sort">
          <label for="sortSelect" style="font-weight: 700; color: var(--spice-wood); font-size: 0.9rem;">ترتيب حسب:</label>
          <select id="sortSelect">
            <option value="featured">الأكثر تميزاً وشهرة</option>
            <option value="price_asc">السعر: من الأقل للأعلى</option>
            <option value="price_desc">السعر: من الأعلى للأقل</option>
            <option value="name">الاسم أبجدياً</option>
          </select>
        </div>
      </div>

      <!-- Category Filter Tabs -->
      <div class="categories-nav" id="categoriesNav">
        <!-- Rendered dynamically by app.js -->
        <button class="category-pill active" data-category="all">
          <i class="fa-solid fa-border-all"></i>
          <span>جميع الأصناف</span>
          <span class="pill-count">...</span>
        </button>
      </div>

      <!-- Products Grid -->
      <div class="products-grid" id="productsGrid">
        <!-- Rendered dynamically by app.js -->
        <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
          <i class="fa-solid fa-spinner fa-spin" style="font-size: 2.5rem; color: var(--spice-gold);"></i>
          <p style="margin-top: 1rem; font-weight: 700; color: var(--spice-wood);">جاري تحضير أجود التوابل...</p>
        </div>
      </div>

      <!-- Load More Pagination -->
      <div class="load-more-wrapper" id="loadMoreWrapper">
        <div class="load-more-info" id="loadMoreInfo">جاري احتساب الأصناف...</div>
        <button class="btn-load-more" id="btnLoadMore">
          <i class="fa-solid fa-rotate-right"></i> عرض المزيد من الأصناف
        </button>
      </div>

    </div>
  </section>

  <!-- Secrets of Spices Section -->
  <section class="recipes-section">
    <div class="container">
      <div class="section-title" style="text-align: center;">
        <h2><i class="fa-solid fa-fire-burner"></i> أسرار بهارات وطبخات أم يمان</h2>
        <p>نصائح ذهبية من تراث الأجداد لكل سيدة وشيف يبحث عن التميز</p>
      </div>

      <div class="recipes-grid">
        <div class="recipe-card">
          <div class="recipe-icon">
            <i class="fa-solid fa-crown"></i>
          </div>
          <h3>سر المنسف الأردني والفلسطيني</h3>
          <p>
            حبة هيل أخضر تُفتح باليد، وعود قرفة سيلانية مع ملعقة من بهار منسف أم يمان والكركم الصافي، ورشة سمنة غنمية بلدية في مرقة الجميد لتعطي الدسامة والرائحة الفواحة.
          </p>
        </div>

        <div class="recipe-card">
          <div class="recipe-icon">
            <i class="fa-solid fa-seedling"></i>
          </div>
          <h3>سر المسخن الفلاحي الأصيل</h3>
          <p>
            تغميسة الخبز الطابوني بالبصل المكرمل لا تكتمل إلا بالسماق البلدي الصافي خالي الملح، وزيت الزيتون الفلسطيني قطفة أولى، ليمنحك الحموضة القرمزية الطبيعية دون مرارة.
          </p>
        </div>

        <div class="recipe-card">
          <div class="recipe-icon">
            <i class="fa-solid fa-bowl-rice"></i>
          </div>
          <h3>سر الفريكة والمقلوبة الذهبية</h3>
          <p>
            تحميص الفريكة الخضراء بملعقة سمن بلدي قبل سلقها، وإضافة خلطة المقلوبة المتوازنة مع رشة بهار حلو وجوزة الطيب الطازجة يضمن حبة رز مفلفلة وشهية لكل العائلة.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Slide-out Shopping Cart Drawer -->
  <div class="cart-drawer-overlay" id="cartDrawerOverlay"></div>
  <aside class="cart-drawer" id="cartDrawer">
    <div class="cart-header">
      <h3>
        <i class="fa-solid fa-basket-shopping" style="color: var(--spice-primary);"></i>
        <span>سلة طلباتك</span>
      </h3>
      <button class="btn-close-cart" id="btnCloseCart" title="إغلاق">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="cart-body">
      <div class="cart-items-list" id="cartItemsList">
        <!-- Rendered by app.js -->
      </div>
    </div>

    <div class="cart-footer">
      <div class="cart-total-row">
        <span>المجموع الإجمالي:</span>
        <span class="cart-total-amount" id="cartTotalAmount">0 ₪</span>
      </div>

      <!-- Quick Checkout Form -->
      <div class="cart-checkout-inputs">
        <input type="text" id="checkoutCustomerName" placeholder="اسمك الكريم *" required />
        <input type="tel" id="checkoutCustomerPhone" placeholder="رقم الهاتف للتواصل" />
        <input type="text" id="checkoutCustomerCity" placeholder="المدينة / الحي لتحديد التوصيل *" required />
        <input type="text" id="checkoutCustomerNotes" placeholder="أي ملاحظات خاصة بالطلب (اختياري)" />
      </div>

      <button class="btn-checkout-whatsapp" id="btnCheckoutWhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
        <span>إتمام الطلب عبر واتساب</span>
      </button>
    </div>
  </aside>

  <!-- AI Assistant ("Chat Om Yaman") Floating Widget -->
  <div class="chat-widget">
    <div class="chat-panel" id="chatPanel">
      <div class="chat-panel-header">
        <div class="chat-panel-title">
          <div class="chat-avatar-icon">
            <i class="fa-solid fa-spoon"></i>
          </div>
          <div>
            <h4>أم يمان</h4>
            <span>مستشارك الذكي للبهارات والطبخات</span>
          </div>
        </div>
        <button class="btn-close-chat" id="btnCloseChat">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="chat-messages" id="chatMessages">
        <div class="chat-msg bot">
          <p>يا مية أهلاً وسهلاً بيك في متجر أم يمان! نورتنا يا غالي. اسألني عن بهارات أي طبخة محتار فيها، أو نصيحة لتتبيل الشاورما والمنسف والمسخن، وأنا جاهزة أساعدك وأرشحلك أفضل المنتجات! 🌿✨</p>
        </div>
      </div>

      <div class="chat-suggestions" id="chatSuggestions">
        <button type="button" class="chat-chip">بهار المنسف البلدي</button>
        <button type="button" class="chat-chip">سر المسخن والسماق</button>
        <button type="button" class="chat-chip">توابل الشاورما البيتية</button>
        <button type="button" class="chat-chip">أعشاب للمناعة والراحة</button>
      </div>

      <form class="chat-input-bar" id="chatForm">
        <input type="text" id="chatInput" class="chat-input" placeholder="اكتب سؤالك لأم يمان هنا..." autocomplete="off" />
        <button type="submit" class="btn-send-chat" title="إرسال">
          <i class="fa-solid fa-paper-plane"></i>
        </button>
      </form>
    </div>

    <!-- Floating Launcher Button -->
    <button class="chat-toggle-btn" id="chatToggleBtn">
      <div class="chat-avatar-icon">
        <i class="fa-solid fa-comments"></i>
      </div>
      <span>شات أم يمان</span>
    </button>
  </div>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        
        <div class="footer-about">
          <h3>توابل وبهارات أم يمان</h3>
          <p>
            متجر فلسطيني متخصص في تقديم أجود أنواع التوابل والبهارات الحصوية والناعمة، الزعتر البلدي الجنيني، السماق الفلاحي، حبوب الفريكة والبرغل، والأعشاب الطبيعية. نحرص على النقاء والطحن الأسبوعي الطازج لنصلك بأفضل نكهة.
          </p>
        </div>

        <div class="footer-col">
          <h4>التصنيفات الرئيسية</h4>
          <ul class="footer-links">
            <li><a href="#catalogSection" onclick="resetFilters()">خلطات وتوابل مشكلة</a></li>
            <li><a href="#catalogSection" onclick="resetFilters()">بهارات حب وحصوية</a></li>
            <li><a href="#catalogSection" onclick="resetFilters()">بهارات ناعمة ومطحونة</a></li>
            <li><a href="#catalogSection" onclick="resetFilters()">زعتر ودقة ومتبلات</a></li>
            <li><a href="#catalogSection" onclick="resetFilters()">حبوب وبقوليات بلدية</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>روابط سريعة</h4>
          <ul class="footer-links">
            <li><a href="index.php">الرئيسية</a></li>
            <li><a href="#catalogSection">قائمة المنتجات (180+)</a></li>
            <li><a href="admin.php">لوحة الإدارة (Admin)</a></li>
            <li><a href="https://wa.me/972595896451" target="_blank">خدمة العملاء واتساب</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4>تواصل وطلب سريع</h4>
          <div class="footer-contact-item">
            <i class="fa-brands fa-whatsapp"></i>
            <span>واتساب: <a href="https://wa.me/972595896451" target="_blank" style="color: #FCD34D;">+972 59-589-6451</a></span>
          </div>
          <div class="footer-contact-item">
            <i class="fa-solid fa-phone"></i>
            <span>هاتف: 0595896451</span>
          </div>
          <div class="footer-contact-item">
            <i class="fa-solid fa-location-dot"></i>
            <span>فلسطين - خدمة التوصيل متوفرة لجميع المناطق</span>
          </div>
          <div class="footer-contact-item">
            <i class="fa-solid fa-clock"></i>
            <span>أوقات العمل: يومياً من 8:00 صباحاً حتى 10:00 مساءً</span>
          </div>
        </div>

      </div>

      <div class="footer-bottom">
        <div>
          جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?> متجر توابل وبهارات أم يمان.
        </div>
        <div style="color: #CBBFB5;">
          صُمم بعناية لدعم الأصالة والتراث الفلسطيني 🌿
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="assets/js/app.js"></script>
  <script src="assets/js/chat.js"></script>
</body>
</html>
