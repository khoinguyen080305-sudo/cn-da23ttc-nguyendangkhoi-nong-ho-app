/**
 * HỆ THỐNG JAVASCRIPT NÔNG HỘ APP (app.js)
 * Xử lý thông báo Toast, Modal xác nhận, Phân chia tiền tệ trực quan, Tự động viết thường tài khoản
 * Tuyệt đối KHÔNG sử dụng window.alert, window.confirm, window.prompt
 */

// 1. THÔNG BÁO TOAST NỔI BẬT, RÕ RÀNG VÀ TỰ ĐỘNG BIẾN MẤT
function hienThiToast(loai, tieuDe, noiDung) {
    if (!noiDung && !tieuDe) return;
    
    let container = document.getElementById('nhToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'nhToastContainer';
        container.className = 'nh-toast-container';
        container.setAttribute('aria-live', 'polite');
        document.body.appendChild(container);
    }

    // Thiết lập icon SVG và class màu sắc chuẩn
    let iconSvg = '';
    let typeClass = 'toast-info';
    let tieuDeMacDinh = 'Thông báo';

    switch (loai) {
        case 'success':
            iconSvg = '<svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
            typeClass = 'toast-success';
            tieuDeMacDinh = 'Thành công';
            break;
        case 'error':
            iconSvg = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            typeClass = 'toast-error';
            tieuDeMacDinh = 'Lỗi';
            break;
        case 'warning':
            iconSvg = '<svg viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
            typeClass = 'toast-warning';
            tieuDeMacDinh = 'Cảnh báo';
            break;
        case 'info':
        default:
            iconSvg = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
            typeClass = 'toast-info';
            tieuDeMacDinh = 'Thông báo';
            break;
    }

    const toast = document.createElement('div');
    toast.className = `nh-toast ${typeClass}`;
    toast.setAttribute('role', loai === 'error' ? 'alert' : 'status');
    toast.innerHTML = `
        <div class="nh-toast-icon" aria-hidden="true">${iconSvg}</div>
        <div class="nh-toast-body">
            <div class="nh-toast-title">${tieuDe || tieuDeMacDinh}</div>
            <div class="nh-toast-desc">${noiDung}</div>
        </div>
        <button type="button" class="nh-toast-close" aria-label="Đóng thông báo" onclick="event.stopPropagation(); dongToast(this.closest('.nh-toast'))">
            <svg viewBox="0 0 14 14" width="14" height="14"><path d="M3.5 3.5l7 7M10.5 3.5l-7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
        </button>
        <div class="nh-toast-progress" aria-hidden="true">
            <div class="nh-toast-bar"></div>
        </div>
    `;

    // 1-Chạm vào toast trên mobile để tắt nhanh
    toast.addEventListener('click', (e) => {
        if (!e.target.closest('a') && !e.target.closest('button')) {
            dongToast(toast);
        }
    });

    container.appendChild(toast);

    // Thời gian hiển thị 2s chuẩn UX
    let timeLeft = 2000;
    let startTime = Date.now();
    let autoCloseTimer = null;

    const startTimer = () => {
        autoCloseTimer = setTimeout(() => {
            dongToast(toast);
        }, timeLeft);
    };

    const pauseTimer = () => {
        if (autoCloseTimer) {
            clearTimeout(autoCloseTimer);
            autoCloseTimer = null;
            timeLeft -= (Date.now() - startTime);
            const bar = toast.querySelector('.nh-toast-bar');
            if (bar) bar.style.animationPlayState = 'paused';
        }
    };

    const resumeTimer = () => {
        if (!autoCloseTimer && timeLeft > 0) {
            startTime = Date.now();
            startTimer();
            const bar = toast.querySelector('.nh-toast-bar');
            if (bar) bar.style.animationPlayState = 'running';
        }
    };

    // Tạm dừng khi rê chuột trên desktop
    toast.addEventListener('mouseenter', pauseTimer);
    toast.addEventListener('mouseleave', resumeTimer);

    startTimer();
    toast._autoCloseTimer = autoCloseTimer;
}

function dongToast(toastEl) {
    if (!toastEl || toastEl.classList.contains('closing')) return;
    if (toastEl._autoCloseTimer) clearTimeout(toastEl._autoCloseTimer);
    toastEl.classList.add('closing');
    setTimeout(() => {
        if (toastEl.parentElement) {
            toastEl.parentElement.removeChild(toastEl);
        }
    }, 320);
}

// 2. MODAL XÁC NHẬN HÀNH ĐỘNG THAY THẾ window.confirm
let callbackXacNhan = null;

function moModalXacNhan(tieuDe, thongDiep, onXacNhan, nutNhan = 'Đồng ý', mauNut = 'btn-success') {
    callbackXacNhan = onXacNhan;
    
    let modal = document.getElementById('nhCustomConfirmModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'nhCustomConfirmModal';
        modal.className = 'nh-modal-backdrop';
        modal.innerHTML = `
            <div class="nh-modal-box">
                <div class="mb-2">
                    <i class="bi bi-question-circle-fill text-warning" style="font-size: 2.4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2" id="nhConfirmTitle" style="color: #064e3b;">Xác nhận</h5>
                <p class="text-muted small mb-4" id="nhConfirmMessage"></p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="dongModalXacNhan()">Hủy bỏ</button>
                    <button type="button" class="btn btn-sm px-3 fw-bold" id="nhConfirmBtnOk" onclick="thucHienXacNhan()">Đồng ý</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    document.getElementById('nhConfirmTitle').textContent = tieuDe;
    document.getElementById('nhConfirmMessage').innerHTML = thongDiep;
    const btnOk = document.getElementById('nhConfirmBtnOk');
    btnOk.className = `btn btn-sm px-3 fw-bold ${mauNut}`;
    btnOk.textContent = nutNhan;

    modal.style.display = 'flex';
}

function dongModalXacNhan() {
    const modal = document.getElementById('nhCustomConfirmModal');
    if (modal) modal.style.display = 'none';
    callbackXacNhan = null;
}

function thucHienXacNhan() {
    if (typeof callbackXacNhan === 'function') {
        callbackXacNhan();
    }
    dongModalXacNhan();
}

// 3. HÀM PHÂN CHIA TIỀN TỆ TRỰC QUAN (LIVE CURRENCY PREVIEW)
// Nông hộ nhập số tiền (Vd: 45000), hiển thị ngay "45.000 đ" bên dưới để không bị nhầm số 0
function capNhatTienPreview(input, previewId) {
    const previewEl = document.getElementById(previewId);
    if (!previewEl) return;

    let giaTri = input.value.replace(/[^0-9]/g, '');
    if (!giaTri || parseInt(giaTri) <= 0) {
        previewEl.innerHTML = '<span class="text-muted">Liên hệ báo giá</span>';
        return;
    }

    let formatted = new Intl.NumberFormat('vi-VN').format(parseInt(giaTri)) + ' đ';
    previewEl.innerHTML = `<span class="currency-preview-badge"><i class="bi bi-cash-stack me-1"></i>${formatted}</span>`;
}

// 4. HÀM THÊM VÀO GIỎ HÀNG QUA AJAX (CHỈ DÀNH CHO NGƯỜI MUA)
function themVaoGio(maSanPham, soLuong = 1, giaDeNghi = null) {
    const formData = new FormData();
    formData.append('ma_san_pham', maSanPham);
    formData.append('so_luong', soLuong);
    formData.append('ajax', '1');
    if (giaDeNghi !== null) formData.append('gia_de_nghi', giaDeNghi);

    fetch('index.php?trang=them_gio_hang&ajax=1', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('HTTP ' + res.status);
        }
        return res.json();
    })
    .then(data => {
        if (data.thanh_cong) {
            hienThiToast('success', 'Giỏ hàng', data.thong_bao || 'Đã thêm nông sản vào giỏ hàng thành công.');
            // Cập nhật số lượng trên badge giỏ hàng Header
            const badge = document.getElementById('headerCartCount');
            if (badge && data.so_luong_gio !== undefined) {
                badge.textContent = data.so_luong_gio;
                badge.style.display = data.so_luong_gio > 0 ? 'inline-block' : 'none';
            }
        } else {
            hienThiToast('error', 'Giỏ hàng', data.thong_bao || 'Không thể thêm vào giỏ hàng.');
        }
    })
    .catch(err => {
        console.error('Lỗi thêm giỏ hàng:', err);
        hienThiToast('error', 'Lỗi kết nối', 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra lại mạng hoặc đăng nhập lại.');
    });
}

// 6. CHỨC NĂNG ẨN / HIỆN MẬT KHẨU TOÀN HỆ THỐNG
function toggleHienMatKhau(idInput, nutBam) {
    const oNhap = document.getElementById(idInput);
    if (!oNhap) return;
    const icon = nutBam ? nutBam.querySelector('i') : null;
    if (oNhap.type === 'password') {
        oNhap.type = 'text';
        if (icon) {
            icon.className = 'bi bi-eye-slash fs-5';
        }
    } else {
        oNhap.type = 'password';
        if (icon) {
            icon.className = 'bi bi-eye fs-5';
        }
    }
}

// 7. HÀM ĐO ĐỘ MẠNH MẬT KHẨU (0 - 100%) CHUẨN NGUYÊN TẮC BẢO MẬT
function kiemTraDoManhMatKhau(matKhau) {
    const bar = document.getElementById('pwStrengthBar');
    const label = document.getElementById('pwStrengthLabel');
    const badgeLen = document.getElementById('ruleLen');
    const badgeCase = document.getElementById('ruleCase');
    const badgeNum = document.getElementById('ruleNum');
    const badgeSpecial = document.getElementById('ruleSpecial');

    if (!bar || !label) return;

    if (!matKhau || matKhau.length === 0) {
        bar.style.width = '0%';
        bar.style.backgroundColor = '#e2e8f0';
        label.innerHTML = 'Độ mạnh: <span class="text-muted fw-bold">0%</span>';
        if (badgeLen) badgeLen.classList.remove('passed');
        if (badgeCase) badgeCase.classList.remove('passed');
        if (badgeNum) badgeNum.classList.remove('passed');
        if (badgeSpecial) badgeSpecial.classList.remove('passed');
        return;
    }

    let score = 0;

    // 1. Độ dài tối thiểu 8 ký tự
    const isLen = matKhau.length >= 8;
    if (isLen) score += 25;
    if (badgeLen) badgeLen.classList.toggle('passed', isLen);

    // 2. Chữ thường và chữ hoa
    const isCase = /[a-z]/.test(matKhau) && /[A-Z]/.test(matKhau);
    if (isCase) score += 25;
    if (badgeCase) badgeCase.classList.toggle('passed', isCase);

    // 3. Có số (0-9)
    const isNum = /[0-9]/.test(matKhau);
    if (isNum) score += 25;
    if (badgeNum) badgeNum.classList.toggle('passed', isNum);

    // 4. Có ký tự đặc biệt (@, #, $, %, ...)
    const isSpecial = /[\W_]/.test(matKhau);
    if (isSpecial) score += 25;
    if (badgeSpecial) badgeSpecial.classList.toggle('passed', isSpecial);

    // Cập nhật giao diện thanh tiến trình
    bar.style.width = score + '%';

    let mau = '#ef4444'; // Đỏ (Rất yếu)
    let text = 'Rất yếu';
    if (score === 50) {
        mau = '#f59e0b'; // Vàng cam (Trung bình)
        text = 'Trung bình';
    } else if (score === 75) {
        mau = '#3b82f6'; // Xanh lam (Khá tốt)
        text = 'Khá tốt';
    } else if (score === 100) {
        mau = '#10b981'; // Xanh lá (Rất mạnh - Đạt chuẩn 100%)
        text = 'Rất mạnh (Đạt chuẩn 100%)';
    }

    bar.style.backgroundColor = mau;
    label.innerHTML = `Độ mạnh: <strong style="color: ${mau};">${score}% — ${text}</strong>`;
}

// 8. KIỂM TRA TRÙNG KHỚP XÁC NHẬN MẬT KHẨU
function kiemTraKhopMatKhau() {
    const pw = document.getElementById('mat_khau');
    const confirm = document.getElementById('xac_nhan_mat_khau');
    const msg = document.getElementById('confirmPasswordMatchMsg');

    if (!pw || !confirm || !msg) return;

    if (!confirm.value) {
        msg.innerHTML = '';
        return;
    }

    if (pw.value === confirm.value) {
        msg.innerHTML = '<span class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Mật khẩu trùng khớp chính xác</span>';
    } else {
        msg.innerHTML = '<span class="text-danger small fw-bold"><i class="bi bi-x-circle-fill me-1"></i> Mật khẩu xác nhận chưa trùng khớp</span>';
    }
}

// 9. KÉO THẢ & CUỘN MƯỢT MÀ DÃY DANH MỤC (DRAG-TO-SCROLL & NAVIGATION)
document.addEventListener('DOMContentLoaded', function () {
    const catRow = document.getElementById('nhCategoriesRow');
    if (!catRow) return;

    const btnLeft = document.getElementById('btnCatScrollLeft');
    const btnRight = document.getElementById('btnCatScrollRight');

    // Cập nhật trạng thái hiển thị 2 nút mũi tên trượt trái / phải
    function capNhatNutCuon() {
        if (!btnLeft || !btnRight) return;
        const maxScroll = catRow.scrollWidth - catRow.clientWidth;
        if (maxScroll <= 5) {
            btnLeft.classList.add('d-none-nav');
            btnRight.classList.add('d-none-nav');
            return;
        }
        if (catRow.scrollLeft <= 5) {
            btnLeft.classList.add('d-none-nav');
        } else {
            btnLeft.classList.remove('d-none-nav');
        }

        if (catRow.scrollLeft >= maxScroll - 5) {
            btnRight.classList.add('d-none-nav');
        } else {
            btnRight.classList.remove('d-none-nav');
        }
    }

    // Bấm nút mũi tên trượt trái / phải
    if (btnLeft) {
        btnLeft.addEventListener('click', function (e) {
            e.preventDefault();
            catRow.scrollBy({ left: -220, behavior: 'smooth' });
        });
    }

    if (btnRight) {
        btnRight.addEventListener('click', function (e) {
            e.preventDefault();
            catRow.scrollBy({ left: 220, behavior: 'smooth' });
        });
    }

    catRow.addEventListener('scroll', capNhatNutCuon, { passive: true });
    window.addEventListener('resize', capNhatNutCuon, { passive: true });
    setTimeout(capNhatNutCuon, 150);

    // Kéo trượt bằng chuột (Mouse Drag-to-Scroll)
    let isDown = false;
    let startX = 0;
    let scrollLeft = 0;
    let isDragging = false;

    catRow.addEventListener('mousedown', function (e) {
        isDown = true;
        isDragging = false;
        catRow.classList.add('is-dragging');
        startX = e.pageX - catRow.offsetLeft;
        scrollLeft = catRow.scrollLeft;
    });

    catRow.addEventListener('mouseleave', function () {
        if (!isDown) return;
        isDown = false;
        catRow.classList.remove('is-dragging');
    });

    catRow.addEventListener('mouseup', function () {
        if (!isDown) return;
        isDown = false;
        catRow.classList.remove('is-dragging');
    });

    catRow.addEventListener('mousemove', function (e) {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - catRow.offsetLeft;
        const walk = (x - startX) * 1.5; // Hệ số kéo trượt theo tay chuột
        if (Math.abs(walk) > 5) {
            isDragging = true;
        }
        catRow.scrollLeft = scrollLeft - walk;
    });

    // Ngăn chặn chuyển trang nếu người dùng đang thao tác kéo (drag)
    catRow.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (isDragging) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        });
    });

    // Hỗ trợ cuộn ngang bằng con lăn chuột (Mouse wheel to horizontal scroll)
    catRow.addEventListener('wheel', function (e) {
        if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
            e.preventDefault();
            catRow.scrollLeft += e.deltaY;
        }
    }, { passive: false });
});
