/* ===================================================
   JayVisionX - Admin Panel JavaScript
   =================================================== */

document.addEventListener('DOMContentLoaded', function () {

  /* ===== SIDEBAR TOGGLE ===== */
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarClose  = document.getElementById('sidebarClose');
  const sidebar       = document.getElementById('adminSidebar');
  const mainContent   = document.getElementById('adminMain');

  function openSidebar() {
    sidebar && sidebar.classList.add('open');
    // Add overlay
    let overlay = document.getElementById('sidebarOverlay');
    if (!overlay) {
      overlay           = document.createElement('div');
      overlay.id        = 'sidebarOverlay';
      overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;';
      overlay.addEventListener('click', closeSidebar);
      document.body.appendChild(overlay);
    }
    overlay.style.display = 'block';
  }

  function closeSidebar() {
    sidebar && sidebar.classList.remove('open');
    const overlay = document.getElementById('sidebarOverlay');
    if (overlay) overlay.style.display = 'none';
  }

  sidebarToggle && sidebarToggle.addEventListener('click', openSidebar);
  sidebarClose  && sidebarClose.addEventListener('click', closeSidebar);

  /* ===== ALERT AUTO DISMISS ===== */
  document.querySelectorAll('.alert').forEach(function (alert) {
    setTimeout(function () {
      alert.style.transition = 'all 0.5s ease';
      alert.style.opacity    = '0';
      alert.style.transform  = 'translateY(-10px)';
      setTimeout(function () { alert.remove(); }, 500);
    }, 4000);
  });

  /* ===== TOGGLE STATUS (AJAX) ===== */
  window.toggleStatus = function (url, btn) {
    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?
          document.querySelector('meta[name="csrf-token"]').content :
          getCsrfFromForm(),
      },
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        const isActive = btn.classList.contains('active');
        btn.classList.toggle('active',   !isActive);
        btn.classList.toggle('inactive', isActive);
        btn.textContent = isActive ? 'Inactive' : 'Active';
      }
    })
    .catch(function (err) {
      console.error('Toggle failed:', err);
    });
  };

  function getCsrfFromForm() {
    const input = document.querySelector('input[name="_token"]');
    return input ? input.value : '';
  }

  /* ===== SETTINGS TABS ===== */
  document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.classList.remove('active');
      });
      document.querySelectorAll('.tab-content').forEach(function (t) {
        t.classList.remove('active');
      });
      this.classList.add('active');
      const tabId = 'tab-' + this.dataset.tab;
      const tab   = document.getElementById(tabId);
      if (tab) tab.classList.add('active');
    });
  });

  /* ===== IMAGE PREVIEW ===== */
  document.querySelectorAll('input[type="file"][accept*="image"]').forEach(function (input) {
    input.addEventListener('change', function () {
      const file   = this.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        // Find existing preview or create one
        let preview = input.parentElement.querySelector('.preview-img');
        if (!preview) {
          preview           = document.createElement('img');
          preview.className = 'preview-img';
          input.parentElement.appendChild(preview);
        }
        preview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  });

  /* ===== CONFIRM DELETES ===== */
  document.querySelectorAll('form[onsubmit*="confirm"]').forEach(function (form) {
    // Already handled inline, but add extra safety
    form.addEventListener('submit', function (e) {
      // Handled by onsubmit attribute
    });
  });

  /* ===== CHARACTER COUNTER FOR META FIELDS ===== */
  function addCharCounter(input, max) {
    const wrapper       = document.createElement('div');
    wrapper.style.cssText = 'display:flex;justify-content:flex-end;margin-top:3px;';
    const counter       = document.createElement('span');
    counter.style.cssText = 'font-size:0.72rem;color:#6B7FA3;';

    function update() {
      const len       = input.value.length;
      counter.textContent = len + ' / ' + max;
      counter.style.color = len > max ? '#dc2626' : (len > max * 0.9 ? '#d97706' : '#6B7FA3');
    }

    update();
    input.addEventListener('input', update);
    wrapper.appendChild(counter);
    input.parentElement.appendChild(wrapper);
  }

  // Apply counters to SEO fields
  document.querySelectorAll('input[name="meta_title"]').forEach(function (el) {
    addCharCounter(el, 60);
  });
  document.querySelectorAll('textarea[name="meta_description"]').forEach(function (el) {
    addCharCounter(el, 160);
  });

  /* ===== SEARCH FILTER FOR TABLES ===== */
  const searchInput = document.getElementById('tableSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const query = this.value.toLowerCase();
      document.querySelectorAll('.admin-table tbody tr').forEach(function (row) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  }

  /* ===== SLUG PREVIEW ===== */
  const titleInput = document.querySelector('input[name="title"]');
  const slugPreview = document.getElementById('slugPreview');
  if (titleInput && slugPreview) {
    titleInput.addEventListener('input', function () {
      const slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
      slugPreview.textContent = slug || '---';
    });
  }

  /* ===== MARK ENQUIRY AS READ ===== */
  window.markAsRead = function (id, url) {
    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': getCsrfFromForm(),
        'Content-Type': 'application/json',
      },
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        const row = document.querySelector('[data-contact-id="' + id + '"]');
        if (row) {
          row.classList.remove('row-unread');
          const badge = row.querySelector('.badge-new');
          if (badge) { badge.className = 'badge badge-read'; badge.textContent = 'Read'; }
        }
      }
    });
  };

  /* ===== BULK SELECT ===== */
  const selectAll = document.getElementById('selectAll');
  if (selectAll) {
    selectAll.addEventListener('change', function () {
      document.querySelectorAll('.row-checkbox').forEach(function (cb) {
        cb.checked = selectAll.checked;
      });
    });
  }

  /* ===== SORTABLE ORDER INPUTS ===== */
  document.querySelectorAll('input[name="sort_order"]').forEach(function (input) {
    input.setAttribute('min', '0');
    input.setAttribute('max', '999');
  });

  /* ===== FORM DIRTY STATE WARNING ===== */
  let formDirty = false;
  document.querySelectorAll('.admin-form-card form').forEach(function (form) {
    form.querySelectorAll('input, textarea, select').forEach(function (field) {
      field.addEventListener('change', function () { formDirty = true; });
      field.addEventListener('input',  function () { formDirty = true; });
    });
    form.addEventListener('submit', function () { formDirty = false; });
  });

  window.addEventListener('beforeunload', function (e) {
    if (formDirty) {
      e.preventDefault();
      e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
    }
  });

});
