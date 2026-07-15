function previewFoto(input, previewSelector = '#imagePreview', placeholderSelector = '#previewPlaceholder') {
    const preview = document.querySelector(previewSelector);
    const placeholder = document.querySelector(placeholderSelector);

    if (preview && input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function resetPreview(previewSelector = '#imagePreview', placeholderSelector = '#previewPlaceholder', defaultSrc = '#') {
    const preview = document.querySelector(previewSelector);
    const placeholder = document.querySelector(placeholderSelector);

    if (preview) {
        preview.src = defaultSrc;
        preview.style.display = defaultSrc === '#' ? 'none' : 'block';
    }
    if (placeholder) {
        placeholder.style.display = defaultSrc === '#' ? 'block' : 'none';
    }
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

// Global DataTable defaults
if (typeof $.fn.dataTable !== 'undefined') {
    $.extend(true, $.fn.dataTable.defaults, {
        "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
        "pageLength": 10,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],
        "language": {
            "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
            "sProcessing": "Sedang memproses...",
            "sLengthMenu": "Tampilkan _MENU_ entri",
            "sZeroRecords": "Tidak ditemukan data yang sesuai",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "sInfoPostFix": "",
            "sSearch": "Cari:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            },
            "search": "",
            "searchPlaceholder": "Cari...",
            "lengthMenu": "Tampilkan _MENU_ entri",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "paginate": {
                "next": "<i class='fas fa-chevron-right'></i>",
                "previous": "<i class='fas fa-chevron-left'></i>"
            }
        },
        "buttons": [
            {
                extend: 'print',
                text: '<i class="fas fa-print text-white mr-1"></i> Print',
                className: 'btn btn-primary text-white btn-sm shadow-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                messageTop: 'Yayasan At-Tarbiyah - Data Export'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv text-white mr-1"></i> CSV',
                className: 'btn btn-warning text-white btn-sm shadow-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                title: 'Yayasan At-Tarbiyah - Data Export'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel text-white mr-1"></i> Excel',
                className: 'btn btn-success text-white btn-sm shadow-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                title: 'Yayasan At-Tarbiyah - Data Export'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf text-white mr-1"></i> PDF',
                className: 'btn btn-danger text-white btn-sm shadow-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                title: 'Yayasan At-Tarbiyah - Data Export'
            }
        ]
    });
}

// Gallery Module Search
$(document).ready(function() {
    if ($("#gallerySearch").length > 0) {
        $("#gallerySearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".gallery-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
            
            // Show empty state if no results
            if($(".gallery-item:visible").length === 0) {
                if($("#noResults").length === 0) {
                    $("#galleryGrid").append('<div id="noResults" class="col-12 text-center py-5"><i class="fas fa-search fa-3x text-light mb-3"></i><h5 class="text-muted">No matching galleries found</h5></div>');
                }
            } else {
                $("#noResults").remove();
            }
        });
    }
});

/**
 * Common visibility toggler for forms
 * @param {string} selectId - The ID of the select element
 * @param {string} targetId - The ID of the element to show/hide
 * @param {string|Array} valuesToShow - The value(s) that should trigger visibility
 */
function toggleVisibility(selectId, targetId, valuesToShow) {
    const select = document.getElementById(selectId);
    const target = document.getElementById(targetId);
    if (!select || !target) return;

    const values = Array.isArray(valuesToShow) ? valuesToShow : [valuesToShow];
    target.style.display = values.includes(select.value) ? 'block' : 'none';
}

/**
 * Requirements Manager for Programs
 */
function setupRequirementsManager(containerId, addButtonId) {
    const container = document.getElementById(containerId);
    const addButton = document.getElementById(addButtonId);
    if (!container || !addButton) return;

    addButton.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'input-group mb-2 requirement-item';
        newItem.innerHTML = `
            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
            <input type="text" name="requirements[]" class="form-control" placeholder="Tambah persyaratan...">
            <button type="button" class="btn btn-outline-danger remove-requirement">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(newItem);
        updateRemoveButtons(containerId);
    });

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-requirement') || e.target.parentElement.classList.contains('remove-requirement')) {
            const item = e.target.closest('.requirement-item');
            item.remove();
            updateRemoveButtons(containerId);
        }
    });

    function updateRemoveButtons(id) {
        const items = document.querySelectorAll(`#${id} .requirement-item`);
        items.forEach((item) => {
            const removeBtn = item.querySelector('.remove-requirement');
            if (items.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    // Initial check
    updateRemoveButtons(containerId);
}

/**
 * Transaction Form Logic
 */
function setupTransactionForm() {
    const donationSelect = document.getElementById('donation_id');
    const orderIdInput = document.getElementById('order_id');
    const noRefInput = document.getElementById('no_ref');
    const grossAmountInput = document.getElementById('gross_amount');
    const paymentTypeSelect = document.getElementById('payment_type');
    const bankSelect = document.getElementById('bank');
    const form = document.querySelector('form');

    if (!donationSelect || !paymentTypeSelect || !bankSelect) return;

    function handlePaymentTypeChange() {
        const paymentType = paymentTypeSelect.value;
        if (paymentType === 'bank_transfer') {
            bankSelect.disabled = false;
        } else if (paymentType === 'echannel') {
            bankSelect.value = 'mandiri';
            bankSelect.disabled = true;
        } else {
            bankSelect.value = '';
            bankSelect.disabled = true;
        }
    }

    donationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            if (orderIdInput) orderIdInput.value = selectedOption.getAttribute('data-order-id');
            if (noRefInput) noRefInput.value = selectedOption.getAttribute('data-no-ref');
            if (grossAmountInput) grossAmountInput.value = selectedOption.getAttribute('data-amount');
            
            const paymentType = selectedOption.getAttribute('data-payment-type');
            const bank = selectedOption.getAttribute('data-bank');
            
            if (paymentType) {
                paymentTypeSelect.value = paymentType;
                handlePaymentTypeChange();
                if (bank) bankSelect.value = bank;
            }
        } else {
            if (orderIdInput) orderIdInput.value = '';
            if (grossAmountInput) grossAmountInput.value = '';
            paymentTypeSelect.value = '';
            bankSelect.value = '';
            handlePaymentTypeChange();
        }
    });

    paymentTypeSelect.addEventListener('change', handlePaymentTypeChange);

    if (form) {
        form.addEventListener('submit', function() {
            bankSelect.disabled = false;
        });
    }

    // Initial state
    handlePaymentTypeChange();
}

/**
 * Chart.js Helpers
 */
function initChartDefaults() {
    if (typeof Chart === 'undefined') return;
    
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';
}

function createLineChart(elementId, labels, data, labelName = 'Total', color = '#1cc88a') {
    const ctx = document.getElementById(elementId);
    if (!ctx) return;

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: labelName,
                lineTension: 0.3,
                backgroundColor: color.startsWith('rgba') ? color : hexToRgba(color, 0.05),
                borderColor: color,
                pointRadius: 3,
                pointBackgroundColor: color,
                pointBorderColor: color,
                pointHoverRadius: 3,
                pointHoverBackgroundColor: color,
                pointHoverBorderColor: color,
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: data,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: { left: 10, right: 25, top: 25, bottom: 0 }
            },
            scales: {
                xAxes: [{
                    time: { unit: 'date' },
                    gridLines: { display: false, drawBorder: false },
                    ticks: { maxTicksLimit: 7 }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return 'Rp ' + number_format(value);
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: { display: false },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': Rp ' + number_format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });
}

function createDoughnutChart(elementId, labels, data, colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']) {
    const ctx = document.getElementById(elementId);
    if (!ctx) return;

    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                hoverBackgroundColor: colors.map(c => hexToRgba(c, 0.8)),
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var index = tooltipItem.index;
                        return data.labels[index] + ': Rp ' + number_format(dataset.data[index]);
                    }
                }
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: { boxWidth: 12 }
            },
            cutoutPercentage: 70,
        },
    });
}

/**
 * Utility: Hex to RGBA
 */
function hexToRgba(hex, alpha = 1) {
    let r = 0, g = 0, b = 0;
    // 3 digits
    if (hex.length == 4) {
        r = "0x" + hex[1] + hex[1];
        g = "0x" + hex[2] + hex[2];
        b = "0x" + hex[3] + hex[3];
    } else if (hex.length == 7) {
        r = "0x" + hex[1] + hex[2];
        g = "0x" + hex[3] + hex[4];
        b = "0x" + hex[5] + hex[6];
    }
    return `rgba(${+r}, ${+g}, ${+b}, ${alpha})`;
}

/**
 * Gallery Module Helpers
 */
function toggleLpyaFields() {
    const select = document.getElementById('categorySelect');
    if (!select) return;

    const selectedOption = select.options[select.selectedIndex];
    const slug = selectedOption ? selectedOption.getAttribute('data-slug') : null;
    
    const dateWrap = document.getElementById('dateWrap');
    const titleWrap = document.getElementById('titleWrap');
    const sliderWrap = document.getElementById('imagesSliderWrap');
    const sliderPreviewWrap = document.getElementById('sliderPreviewWrap');
    const programWrap = document.getElementById('programWrap');
    const programSelect = document.getElementById('programSelect');
    
    if (slug === 'lpya') {
        if (dateWrap) dateWrap.style.display = 'block';
        if (titleWrap) titleWrap.classList.replace('col-md-9', 'col-md-6');
        if (sliderWrap) sliderWrap.style.display = 'block';
        if (sliderPreviewWrap) sliderPreviewWrap.style.display = 'block';
        if (programWrap) programWrap.style.display = 'none';
        if (programSelect) programSelect.value = '';
    } else {
        if (dateWrap) dateWrap.style.display = 'none';
        if (titleWrap) titleWrap.classList.replace('col-md-6', 'col-md-9');
        if (sliderWrap) sliderWrap.style.display = 'none';
        if (sliderPreviewWrap) sliderPreviewWrap.style.display = 'none';
        if (programWrap) programWrap.style.display = 'block';
    }

    filterProgramsByCategory();
}

function filterProgramsByCategory() {
    const categorySelect = document.getElementById('categorySelect');
    const programSelect = document.getElementById('programSelect');
    if (!categorySelect || !programSelect) return;

    const selectedCategoryId = categorySelect.value;
    let hasVisibleProgram = false;
    const currentProgramValue = programSelect.value;

    Array.from(programSelect.options).forEach(option => {
        const optionCategoryId = option.getAttribute('data-category-id');
        
        if (optionCategoryId === "" || optionCategoryId === selectedCategoryId) {
            option.style.display = 'block';
            if (option.value === currentProgramValue && option.value !== "") {
                hasVisibleProgram = true;
            }
        } else {
            option.style.display = 'none';
        }
    });

    if (currentProgramValue !== "" && !hasVisibleProgram) {
        programSelect.value = "";
    }
}

function previewSlider(input) {
    const container = document.getElementById('sliderPreviewContainer');
    const placeholder = document.getElementById('sliderPlaceholder');
    if (!container) return;
    
    // In edit mode, we don't clear (append new ones), in create mode we clear
    const isEditMode = document.querySelector('input[name="_method"][value="PUT"]');
    
    if (!isEditMode) {
        container.innerHTML = '';
    }
    
    if (input.files && input.files.length > 0) {
        if (placeholder) placeholder.style.display = 'none';
        
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'position-relative';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                
                div.appendChild(img);
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else if (!isEditMode) {
        placeholder.style.display = 'block';
    }
}

function resetGalleryPreview() {
    resetPreview('#imagePreview', '#previewPlaceholder');
    const sliderContainer = document.getElementById('sliderPreviewContainer');
    if (sliderContainer) {
        sliderContainer.innerHTML = '';
        const placeholder = document.createElement('div');
        placeholder.id = 'sliderPlaceholder';
        placeholder.className = 'w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted';
        placeholder.innerHTML = '<i class="fas fa-images fa-3x mb-2"></i><p class="mb-0">Tidak ada gambar slider</p>';
        sliderContainer.appendChild(placeholder);
    }
}
