/* Lucian UX/UI Prompt #001 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#001 */
/**
 * Controller cho Lucian UX/UI #001 Liquid Glass Button
 * Tích hợp cho Form Đăng Ký, Form Đăng Nhập và Form Đổi Mật Khẩu
 */
(function () {
  'use strict';

  function setupLiquidButton(rootId, formId, formType) {
    var root = document.getElementById(rootId);
    if (!root) return;

    var btn = root.querySelector('.u001-btn');
    var mag = root.querySelector('.u001-mag');
    var live = root.querySelector('.u001-live');
    var form = document.getElementById(formId);
    if (!btn || !form) return;

    var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Hiệu ứng hút nam châm (Magnetic Pull) khi di chuyển con trỏ gần nút
    root.addEventListener('pointermove', function (e) {
      var r = btn.getBoundingClientRect();
      var dx = e.clientX - (r.left + r.width / 2);
      var dy = e.clientY - (r.top + r.height / 2);
      var pull = rm ? 0 : Math.max(0, 1 - Math.hypot(dx, dy) / 180);
      if (mag) {
        mag.style.transform = 'translate(' + (dx * 0.16 * pull).toFixed(1) + 'px, ' + (dy * 0.16 * pull).toFixed(1) + 'px)';
      }
      btn.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100).toFixed(1) + '%');
      btn.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100).toFixed(1) + '%');
    });

    root.addEventListener('pointerleave', function () {
      if (mag) mag.style.transform = 'translate(0px, 0px)';
    });

    btn.addEventListener('click', function (e) {
      if (root.dataset.state === 'load' || root.dataset.state === 'ok') {
        e.preventDefault();
        return;
      }

      // 1. Kiểm tra HTML5 validation
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      // 2. Kiểm tra mật khẩu đối với form đăng ký & đổi mật khẩu
      if (formType === 'register' || formType === 'change_password') {
        var pw = form.querySelector('#mat_khau') || form.querySelector('input[type="password"]');
        var pwConfirm = form.querySelector('#xac_nhan_mat_khau');
        if (pw && pwConfirm && pw.value !== pwConfirm.value) {
          e.preventDefault();
          if (typeof hienThiToast === 'function') {
            hienThiToast('error', 'Lỗi mật khẩu', 'Mật khẩu và xác nhận mật khẩu không khớp nhau.');
          }
          pwConfirm.focus();
          return;
        }
        if (pw && pw.value.length < 8) {
          e.preventDefault();
          if (typeof hienThiToast === 'function') {
            hienThiToast('warning', 'Mật khẩu yếu', 'Mật khẩu phải từ 8 ký tự trở lên.');
          }
          pw.focus();
          return;
        }
      }

      // 3. Kích hoạt hiệu ứng loading (State: load)
      e.preventDefault();
      root.dataset.state = 'load';
      btn.setAttribute('aria-busy', 'true');
      var loadingText = 'Đang xử lý';
      if (formType === 'register') loadingText = 'Đang xử lý đăng ký';
      else if (formType === 'login') loadingText = 'Đang đăng nhập';
      else if (formType === 'change_password') loadingText = 'Đang lưu mật khẩu';
      if (live) live.textContent = loadingText;

      // 4. Gửi AJAX POST
      var formData = new FormData(form);

      fetch(form.action || window.location.href, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(function (res) {
        return res.text().then(function (html) {
          return { ok: res.ok, redirected: res.redirected, url: res.url, html: html };
        });
      })
      .then(function (result) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(result.html, 'text/html');
        var errAlert = doc.querySelector('.alert.alert-danger');

        // Điều kiện kiểm tra lỗi
        var hasError = false;
        if (formType === 'register') {
          hasError = errAlert && !result.redirected && !result.url.includes('dang_nhap');
        } else if (formType === 'login') {
          hasError = errAlert && !result.redirected && result.url.includes('dang_nhap');
        } else if (formType === 'change_password') {
          hasError = errAlert && !result.redirected && result.url.includes('doi_mk_tam');
        }

        if (hasError && errAlert) {
          setTimeout(function () {
            root.dataset.state = 'idle';
            btn.removeAttribute('aria-busy');
            if (live) live.textContent = '';
            var errMsg = errAlert.textContent.trim();
            if (typeof hienThiToast === 'function') {
              hienThiToast('error', 'Thao tác thất bại', errMsg);
            }
            var localErr = form.parentElement.querySelector('.alert.alert-danger');
            if (localErr) {
              var span = localErr.querySelector('span');
              if (span) span.textContent = errMsg;
              else localErr.textContent = errMsg;
            } else {
              var alertDiv = document.createElement('div');
              alertDiv.className = 'alert alert-danger py-2 px-3 rounded-3 fs-6 mb-3 text-center border-0 d-flex align-items-center justify-content-center gap-1.5';
              alertDiv.style.cssText = 'background: #fef2f2; color: #dc2626;';
              alertDiv.innerHTML = '<i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span class="small fw-semibold">' + errMsg + '</span>';
              form.parentNode.insertBefore(alertDiv, form);
            }
          }, 600);
        } else {
          // Thành công! Kích hoạt hiệu ứng mở rộng checkmark (State: ok)
          setTimeout(function () {
            root.dataset.state = 'ok';
            btn.removeAttribute('aria-busy');
            var successText = 'Thành công';
            if (formType === 'register') successText = 'Đăng ký thành công';
            else if (formType === 'login') successText = 'Đăng nhập thành công';
            else if (formType === 'change_password') successText = 'Đổi mật khẩu thành công';
            if (live) live.textContent = successText;

            // Sau 1.1s, chuyển hướng sang trang đích
            setTimeout(function () {
              if (formType === 'register') {
                window.location.href = 'index.php?trang=dang_nhap';
              } else {
                window.location.href = result.url || 'index.php';
              }
            }, 1100);
          }, 700);
        }
      })
      .catch(function () {
        // Fallback: nếu lỗi mạng, submit form bình thường
        form.submit();
      });
    });
  }

  function init() {
    setupLiquidButton('lucianRegisterBtnSlot', 'formDangKy', 'register');
    setupLiquidButton('lucianLoginBtnSlot', 'formDangNhap', 'login');
    setupLiquidButton('lucianChangePwBtnSlot', 'formDoiMkTam', 'change_password');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
