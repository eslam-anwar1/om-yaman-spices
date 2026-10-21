/**
 * Om Yaman Spices - AI Assistant ("Chat Om Yaman")
 * Intelligent Cooking, Recipe & Spice Recommendation Widget
 */

document.addEventListener('DOMContentLoaded', () => {
  initChatWidget();
});

function initChatWidget() {
  const chatToggle = document.getElementById('chatToggleBtn');
  const chatPanel = document.getElementById('chatPanel');
  const btnCloseChat = document.getElementById('btnCloseChat');
  const chatForm = document.getElementById('chatForm');
  const chatInput = document.getElementById('chatInput');
  const chatMessages = document.getElementById('chatMessages');
  const chatSuggestions = document.getElementById('chatSuggestions');

  if (!chatToggle || !chatPanel) return;

  // Toggle Chat
  chatToggle.addEventListener('click', () => {
    chatPanel.classList.toggle('active');
    if (chatPanel.classList.contains('active')) {
      if (chatInput) chatInput.focus();
      scrollChatToBottom();
    }
  });

  if (btnCloseChat) {
    btnCloseChat.addEventListener('click', () => {
      chatPanel.classList.remove('active');
    });
  }

  // Handle Form Submit
  if (chatForm && chatInput) {
    chatForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const text = chatInput.value.trim();
      if (!text) return;

      appendChatMessage(text, 'user');
      chatInput.value = '';
      sendToOmYaman(text);
    });
  }

  // Bind Suggestion Chips
  if (chatSuggestions) {
    chatSuggestions.addEventListener('click', (e) => {
      const chip = e.target.closest('.chat-chip');
      if (chip) {
        const text = chip.textContent.trim();
        appendChatMessage(text, 'user');
        sendToOmYaman(text);
      }
    });
  }
}

/**
 * Send Message to AI Backend (with client-side fallback)
 */
async function sendToOmYaman(message) {
  const chatMessages = document.getElementById('chatMessages');
  
  // Show typing indicator
  const typingEl = document.createElement('div');
  typingEl.className = 'chat-msg bot typing';
  typingEl.id = 'chatTyping';
  typingEl.innerHTML = '<i class="fa-solid fa-ellipsis fa-fade"></i> أم يمان تكتب لك الآن...';
  chatMessages.appendChild(typingEl);
  scrollChatToBottom();

  try {
    const res = await fetch('api.php?action=ai_chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message })
    });

    const data = await res.json();
    removeTyping();

    if (data.success) {
      appendChatMessage(data.reply, 'bot', data.suggested_products);
      updateSuggestions(data.quick_suggestions);
    } else {
      fallbackChatResponse(message);
    }
  } catch (err) {
    removeTyping();
    fallbackChatResponse(message);
  }
}

function removeTyping() {
  const el = document.getElementById('chatTyping');
  if (el) el.remove();
}

/**
 * Append Message Bubble to Chat
 */
function appendChatMessage(text, sender = 'bot', products = []) {
  const container = document.getElementById('chatMessages');
  if (!container) return;

  const msgDiv = document.createElement('div');
  msgDiv.className = `chat-msg ${sender}`;

  let html = `<p>${escapeHtml(text)}</p>`;

  // If there are suggested products, render mini cards
  if (products && products.length > 0) {
    html += `<div style="margin-top: 0.65rem; border-top: 1px dashed var(--spice-border); padding-top: 0.5rem;">
      <small style="font-weight: 700; color: var(--spice-wood); display: block; margin-bottom: 0.35rem;">
        منتجات موصى بها من أم يمان:
      </small>
    `;

    products.forEach(p => {
      html += `
        <div class="chat-product-chip">
          <img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" onerror="this.src='https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=800&q=80'" />
          <div class="chat-product-chip-info">
            <div class="chat-product-chip-name">${escapeHtml(p.name)}</div>
            <div class="chat-product-chip-price">${p.price} ₪</div>
          </div>
          <button class="chat-product-chip-btn" onclick="addToCart(${p.id})">
            + للسلة
          </button>
        </div>
      `;
    });

    html += `</div>`;
  }

  msgDiv.innerHTML = html;
  container.appendChild(msgDiv);
  scrollChatToBottom();
}

/**
 * Update quick suggestions chips
 */
function updateSuggestions(suggestions = []) {
  const container = document.getElementById('chatSuggestions');
  if (!container || !suggestions || suggestions.length === 0) return;

  container.innerHTML = suggestions.map(s => `
    <button type="button" class="chat-chip">${escapeHtml(s)}</button>
  `).join('');
}

/**
 * Client-Side Smart Fallback Engine
 */
function fallbackChatResponse(message) {
  const msg = message.toLowerCase();
  let reply = "يا هلا بيك يا بني/يا بنتي في متجر أم يمان! توابلنا بلدية وطبيعية 100% بدون أي مواد صناعية. شو الوصفة اللي حابب تسويها اليوم لحتى أنصحك بأنسب خلطة؟";
  let suggestedProds = [];
  let suggestions = ['بهار منسف أردني', 'سماق فلاحي للمسخن', 'زعتر بلدي بالسمسم', 'طريقة الطلب'];

  if (msg.includes('منسف')) {
    reply = "المنسف البلدي تاجه بهار المنسف الفاخر تبعنا والهيل الأخضر الغواتيمالي مع السمنة الغنمية البلدية والصنوبر المحمر!";
    suggestedProds = (window.state && window.state.allProducts ? window.state.allProducts.filter(p => p.name.includes('منسف') || p.name.includes('صنوبر')) : []).slice(0, 2);
    suggestions = ['أضف بهار المنسف', 'سمنة بلدية', 'صنوبر بلدي'];
  } else if (msg.includes('مسخن') || msg.includes('سماق')) {
    reply = "المسخن الفلسطيني ما بيحلى إلا بالسماق البلدي الصافي وزيت الزيتون البكر! سماقنا قطفة جديدة وحامض طبيعي 100%.";
    suggestedProds = (window.state && window.state.allProducts ? window.state.allProducts.filter(p => p.name.includes('سماق') || p.name.includes('مسخن')) : []).slice(0, 2);
    suggestions = ['سماق بلدي صافي', 'زيت زيتون بكر', 'بهار دجاج'];
  } else if (msg.includes('شاورما')) {
    reply = "توابل الشاورما البيتية تبعتنا بتعطيك نكهة أفخم المطاعم! انقع اللحم أو الدجاج مع شوية لبن وثوم وخل وبهارات الشاورما لساعتين.";
    suggestedProds = (window.state && window.state.allProducts ? window.state.allProducts.filter(p => p.name.includes('شاورما')) : []).slice(0, 2);
  } else if (msg.includes('زعتر') || msg.includes('دقة')) {
    reply = "فطورك الصباحي أطيب مع زعتر أم يمان البلدي إكسترا بالسمسم المحمص وزيت الزيتون الفلاحي، ودقة غزة الحمراء الشهية!";
    suggestedProds = (window.state && window.state.allProducts ? window.state.allProducts.filter(p => p.name.includes('زعتر') || p.name.includes('دقة')) : []).slice(0, 2);
  } else if (msg.includes('طلب') || msg.includes('واتساب') || msg.includes('توصيل')) {
    reply = "التوصيل سريع لكافة المناطق! يمكنك اختيار بهاراتك والضغط على 'طلب عبر واتساب' أو مراسلتنا مباشرة على الرقم 0595896451.";
  }

  appendChatMessage(reply, 'bot', suggestedProds);
  updateSuggestions(suggestions);
}

function scrollChatToBottom() {
  const container = document.getElementById('chatMessages');
  if (container) {
    container.scrollTop = container.scrollHeight;
  }
}
