// Lucian UX/UI Prompt #021 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#021
/**
 * ==============================================================================
 * BỘ ĐIỀU KHIỂN GIAO DIỆN SÁNG / TỐI (THEME TOGGLE CONTROLLER)
 * Dựa trên phần tử Lucian UX/UI Prompt #021 (Day-night sky switch)
 * Dự án: THÔNG VỤ - Nông sản sạch
 * ==============================================================================
 */

(function () {
  'use strict';

  function layCheDoHienTai() {
    const daLuu = localStorage.getItem('theme');
    if (daLuu === 'dark' || daLuu === 'light') {
      return daLuu;
    }
    // Mặc định theo cài đặt hệ thống của thiết bị
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      return 'dark';
    }
    return 'light';
  }

  function apDungGiaoDien(cheDo, luuStorage = false) {
    const laToi = (cheDo === 'dark');

    if (laToi) {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else {
      document.documentElement.removeAttribute('data-theme');
    }

    if (luuStorage) {
      localStorage.setItem('theme', cheDo);
    }

    // Cập nhật trạng thái hiển thị của công tắc Lucian #021
    const cacCongTac = document.querySelectorAll('.u021');
    cacCongTac.forEach(function (root) {
      root.classList.toggle('is-night', laToi);
      const sw = root.querySelector('.u021-sw');
      if (sw) {
        sw.setAttribute('aria-checked', String(laToi));
        sw.setAttribute('title', laToi ? 'Chuyển sang chế độ Sáng' : 'Chuyển sang chế độ Tối');
      }
      const nhanText = root.querySelector('.u021-text-badge');
      if (nhanText) {
        nhanText.innerHTML = laToi 
          ? '<i class="bi bi-moon-stars-fill text-warning"></i> Chế độ Đêm'
          : '<i class="bi bi-sun-fill text-warning"></i> Chế độ Ngày';
      }
    });
  }

  function khoiTaoThemeToggle() {
    const cheDoBanDau = layCheDoHienTai();
    apDungGiaoDien(cheDoBanDau, false);

    // Lắng nghe sự kiện click trên tất cả công tắc Lucian #021
    document.addEventListener('click', function (e) {
      const btnSw = e.target.closest('.u021-sw');
      if (!btnSw) return;

      e.preventDefault();
      const root = btnSw.closest('.u021');
      const dangLaToi = root ? root.classList.contains('is-night') : (document.documentElement.getAttribute('data-theme') === 'dark');
      const cheDoMoi = dangLaToi ? 'light' : 'dark';

      apDungGiaoDien(cheDoMoi, true);
    });

    // Lắng nghe thay đổi theme từ hệ thống khi người dùng chưa tự chọn
    if (window.matchMedia) {
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        if (!localStorage.getItem('theme')) {
          apDungGiaoDien(e.matches ? 'dark' : 'light', false);
        }
      });
    }
  }

  // Khởi chạy ngay khi DOM đã sẵn sàng
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', khoiTaoThemeToggle);
  } else {
    khoiTaoThemeToggle();
  }
})();

