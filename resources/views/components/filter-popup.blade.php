{{--
    Komponen Filter Popup - Konsisten di seluruh sistem
    
    Props:
      - formId    : ID form filter (default: 'filterForm')
      - modalId   : ID unik modal (default: 'filterModal')
      - title     : Judul modal (default: 'Filter')
      - resetUrl  : URL untuk reset filter
      - badge     : Jumlah filter aktif (opsional, angka)
      
    Slot:
      - default   : Isi field-field filter
--}}
@props([
    'formId'   => 'filterForm',
    'modalId'  => 'filterModal',
    'title'    => 'Filter',
    'resetUrl' => '#',
    'badge'    => 0,
])

<style>
/* ===== Filter Popup Component Styles ===== */
.fp-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9990;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(2px);
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.fp-overlay.fp-open {
    display: flex;
}
.fp-box {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    width: 100%;
    max-width: 400px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: fpSlideIn 0.22s ease;
    overflow: hidden;
}
@keyframes fpSlideIn {
    from { opacity:0; transform: translateY(-16px) scale(0.97); }
    to   { opacity:1; transform: translateY(0) scale(1); }
}
.fp-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg,#0B5EA8 0%,#073864 100%);
    flex-shrink: 0;
}
.fp-header-left {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.fp-header-left svg {
    color: rgba(255,255,255,0.85);
    width: 1.1rem;
    height: 1.1rem;
    flex-shrink: 0;
}
.fp-title {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #fff;
    margin: 0;
}
.fp-header-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.fp-reset-link {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255,255,255,0.75);
    text-decoration: none;
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    border: 1px solid rgba(255,255,255,0.3);
    transition: all 0.2s;
}
.fp-reset-link:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
}
.fp-close-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 0.375rem;
    cursor: pointer;
    padding: 0.3rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.fp-close-btn:hover { background: rgba(255,255,255,0.28); }
.fp-close-btn svg { width: 1rem; height: 1rem; }
.fp-body {
    padding: 1.25rem;
    overflow-y: auto;
    flex: 1;
}
.fp-body .fp-field + .fp-field { margin-top: 1rem; }
.fp-body label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.375rem;
}
.fp-body select,
.fp-body input[type="date"],
.fp-body input[type="text"] {
    display: block;
    width: 100%;
    padding: 0.55rem 0.75rem;
    border: 1.5px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #111827;
    background: #fff;
    box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.fp-body select:focus,
.fp-body input[type="date"]:focus,
.fp-body input[type="text"]:focus {
    outline: none;
    border-color: #0B5EA8;
    box-shadow: 0 0 0 3px rgba(11,94,168,0.12);
}
.fp-date-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}
.fp-date-row .fp-date-col label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}
.fp-footer {
    padding: 0.875rem 1.25rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    flex-shrink: 0;
}
.fp-apply-btn {
    width: 100%;
    padding: 0.625rem 1rem;
    background: linear-gradient(135deg,#0B5EA8 0%,#073864 100%);
    border: none;
    border-radius: 0.5rem;
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.1s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}
.fp-apply-btn:hover { opacity: 0.9; }
.fp-apply-btn:active { transform: scale(0.98); }
.fp-apply-btn svg { width: 1rem; height: 1rem; }

/* Trigger Button */
.fp-trigger-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.575rem 1rem;
    border: 1.5px solid #d1d5db;
    border-radius: 0.5rem;
    background: #fff;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    position: relative;
}
.fp-trigger-btn:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}
.fp-trigger-btn svg { width: 1rem; height: 1rem; color: #6b7280; }
.fp-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.125rem;
    height: 1.125rem;
    padding: 0 0.3rem;
    background: #0B5EA8;
    color: #fff;
    border-radius: 99px;
    font-size: 0.65rem;
    font-weight: 700;
    line-height: 1;
}
</style>

{{-- ===== Trigger Button ===== --}}
<button type="button"
        class="fp-trigger-btn"
        onclick="fpOpen('{{ $modalId }}')"
        id="fp-trigger-{{ $modalId }}">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
    </svg>
    <span>Filter</span>
    @if((int)$badge > 0)
        <span class="fp-badge">{{ $badge }}</span>
    @endif
</button>

{{-- ===== Modal Overlay ===== --}}
<div id="{{ $modalId }}" class="fp-overlay" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title">
    <div class="fp-box">
        {{-- Header --}}
        <div class="fp-header">
            <div class="fp-header-left">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h3 class="fp-title" id="{{ $modalId }}-title">{{ $title }}</h3>
            </div>
            <div class="fp-header-right">
                <a href="{{ $resetUrl }}" class="fp-reset-link">Reset</a>
                <button type="button" class="fp-close-btn" onclick="fpClose('{{ $modalId }}')" title="Tutup">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body (slot) --}}
        <div class="fp-body">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <div class="fp-footer">
            <button type="button" class="fp-apply-btn" onclick="document.getElementById('{{ $formId }}').submit()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Terapkan Filter
            </button>
        </div>
    </div>
</div>

{{-- ===== Script (load once) ===== --}}
@once
<script>
    function fpOpen(id) {
        document.getElementById(id).classList.add('fp-open');
        document.body.style.overflow = 'hidden';
    }
    function fpClose(id) {
        document.getElementById(id).classList.remove('fp-open');
        document.body.style.overflow = '';
    }
    // Close on overlay click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('fp-overlay') && e.target.classList.contains('fp-open')) {
            e.target.classList.remove('fp-open');
            document.body.style.overflow = '';
        }
    });
    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.fp-overlay.fp-open').forEach(function(el) {
                el.classList.remove('fp-open');
                document.body.style.overflow = '';
            });
        }
    });
</script>
@endonce
