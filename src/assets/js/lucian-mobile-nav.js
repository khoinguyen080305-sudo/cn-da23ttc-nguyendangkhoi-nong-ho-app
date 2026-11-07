/* Lucian UX/UI Prompt #041 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#041 */
/**
 * Controller cho Lucian UX/UI #041 Notched Bottom Tab Bar
 * Hiệu ứng lò xo chuyển động mượt mà (Spring Physics) theo tab được chọn
 */
(function () {
  'use strict';

  function initMobileDock() {
    var root = document.getElementById('nhMobileDock');
    if (!root) return;

    var tabs = Array.prototype.slice.call(root.querySelectorAll('.u041-tab'));
    if (!tabs.length) return;

    var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var x = 0, v = 0, target = 0, raf = 0;

    // Tính toán tọa độ tâm chính xác của tab dựa trên kích thước thực tế
    function center(t) {
      var rootRect = root.getBoundingClientRect();
      var tabRect = t.getBoundingClientRect();
      if (rootRect.width === 0 || tabRect.width === 0) {
        // Fallback nếu layout chưa tính toán xong
        var li = t.closest('li') || t;
        return li.offsetLeft + li.offsetWidth / 2;
      }
      return (tabRect.left + tabRect.width / 2) - rootRect.left;
    }

    // Vòng lặp vật lý lò xo (Spring Physics Step)
    function step() {
      var a = (target - x) * 0.18 - v * 0.42;
      v += a;
      x += v;
      root.style.setProperty('--x', x.toFixed(2) + 'px');

      if (Math.abs(target - x) > 0.25 || Math.abs(v) > 0.25) {
        raf = requestAnimationFrame(step);
      } else {
        x = target;
        v = 0;
        root.style.setProperty('--x', x.toFixed(2) + 'px');
        raf = 0;
      }
    }

    function select(i, shouldAnimate) {
      tabs.forEach(function (t, k) {
        var isSelected = (k === i);
        t.setAttribute('aria-selected', String(isSelected));
        t.tabIndex = isSelected ? 0 : -1;
      });

      target = center(tabs[i]);

      if (rm || !shouldAnimate) {
        x = target;
        v = 0;
        if (raf) { cancelAnimationFrame(raf); raf = 0; }
        root.style.setProperty('--x', x.toFixed(2) + 'px');
        return;
      }

      if (!raf) raf = requestAnimationFrame(step);
    }

    // Tìm tab active ban đầu
    var activeIndex = 0;
    tabs.forEach(function (t, i) {
      if (t.getAttribute('aria-selected') === 'true') {
        activeIndex = i;
      }
    });

    // Định vị tab active ngay khi trang vừa tải xong
    function syncInitialPosition() {
      target = x = center(tabs[activeIndex]);
      root.style.setProperty('--x', x.toFixed(2) + 'px');
    }

    syncInitialPosition();
    requestAnimationFrame(syncInitialPosition);
    setTimeout(syncInitialPosition, 100);

    // Xử lý sự kiện click & phím mũi tên
    tabs.forEach(function (t, i) {
      t.addEventListener('click', function () {
        activeIndex = i;
        select(i, true);
      });

      t.addEventListener('keydown', function (e) {
        var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
        if (d) {
          e.preventDefault();
          var nextIndex = (i + d + tabs.length) % tabs.length;
          activeIndex = nextIndex;
          select(nextIndex, true);
          tabs[nextIndex].focus();
        }
      });
    });

    // Cập nhật vị trí khi xoay màn hình hoặc đổi kích thước
    window.addEventListener('resize', function () {
      syncInitialPosition();
    });

    window.addEventListener('orientationchange', function () {
      setTimeout(syncInitialPosition, 150);
    });

    // ==========================================================================
    // TỰ ĐỘNG ẨN/HIỆN KHI CUỘN TRANG (CHÍNH XÁC: CUỘN LÊN THÌ MẤT, CUỘN XUỐNG THÌ HIỆN)
    // - Khi ở đầu trang hoặc cuộn lên trên (scroll up): Thanh dock ẨN ĐI (MẤT) để không che banner
    // - Khi cuộn xuống dưới (scroll down vào nội dung): Thanh dock HIỆN LÊN để thao tác
    // - Khi ở cuối trang: Luôn HIỆN LÊN
    // ==========================================================================
    var lastScrollY = window.pageYOffset || document.documentElement.scrollTop;
    var scrollThreshold = 8;
    var isTicking = false;

    // Kiểm tra trang có đủ dài để cuộn không
    function canPageScroll() {
      return (document.documentElement.scrollHeight - window.innerHeight) > 80;
    }

    function handleScroll() {
      var currentScrollY = window.pageYOffset || document.documentElement.scrollTop;

      // Nếu trang ngắn không đủ cuộn (như form đăng nhập/đăng ký), luôn hiện dock để điều hướng
      if (!canPageScroll()) {
        root.classList.remove('is-hidden');
        lastScrollY = currentScrollY;
        isTicking = false;
        return;
      }

      // 1. Khi ở đầu trang (scrollY <= 70px) -> Luôn ẩn dock (để thoáng banner và nút trang đầu)
      if (currentScrollY <= 70) {
        root.classList.add('is-hidden');
        lastScrollY = currentScrollY;
        isTicking = false;
        return;
      }

      // 2. Khi chạm đáy trang -> Luôn hiện dock để người dùng chuyển tab
      var isAtBottom = (window.innerHeight + currentScrollY) >= (document.documentElement.scrollHeight - 30);
      if (isAtBottom) {
        root.classList.remove('is-hidden');
        lastScrollY = currentScrollY;
        isTicking = false;
        return;
      }

      // 3. Xử lý theo chiều cuộn khi lướt trong trang
      var diff = currentScrollY - lastScrollY;

      if (Math.abs(diff) >= scrollThreshold) {
        if (diff > 0) {
          // Cuộn xuống dưới -> HIỆN LÊN
          root.classList.remove('is-hidden');
        } else {
          // Cuộn lên trên -> NÓ MẤT
          root.classList.add('is-hidden');
        }
        lastScrollY = currentScrollY;
      }

      isTicking = false;
    }

    // Thiết lập trạng thái ban đầu khi vừa tải trang
    function initScrollVisibility() {
      if (canPageScroll() && (window.pageYOffset || document.documentElement.scrollTop) <= 70) {
        root.classList.add('is-hidden');
      } else {
        root.classList.remove('is-hidden');
      }
    }

    initScrollVisibility();
    setTimeout(initScrollVisibility, 150);

    window.addEventListener('scroll', function () {
      if (!isTicking) {
        window.requestAnimationFrame(handleScroll);
        isTicking = true;
      }
    }, { passive: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileDock);
  } else {
    initMobileDock();
  }
})();

