@extends('admin.layout')

@section('title', 'System management')

@section('content')
    <div class="mb-3">
        <h1 class="page-title">System management</h1>
        <p class="page-subtitle">Analytics, reporting, and admin activity visibility.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-lg-5">
            <div class="dashboard-card p-4 h-100">
                <h2 class="h6 text-uppercase text-muted mb-2" style="letter-spacing: 0.04em;">History</h2>
                <h3 class="h5 mb-2">Audit Trail (History Log)</h3>
                <p class="text-muted small mb-3">
                    Review who changed what approval actions, account updates, and other recorded admin events.
                </p>
                <a href="{{ route('admin.system.audit') }}" class="btn btn-primary btn-sm">Open Audit Trail</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-7">
            <div class="dashboard-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div>
                        <div class="h6 text-uppercase text-muted mb-2" style="letter-spacing: 0.04em;">Analytics & Charts</div>
                        <h3 class="h5 mb-1">Requests per month</h3>
                        <p class="text-muted small mb-0">Breakdown by Pending / Approved / Rejected for year <b>{{ $year }}</b>.</p>
                    </div>

                    <form method="GET" class="d-flex align-items-end gap-2">
                        <div>
                            <label class="form-label small mb-1">Year</label>
                            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach($yearOptions as $y)
                                    <option value="{{ $y }}" {{ (int)$y === (int)$year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <div class="mt-3">
                    <canvas id="requestsPerMonthChart" height="110"></canvas>
                </div>

                <div class="row g-2 mt-3">
                    <div class="col-4">
                        <div class="dashboard-card p-3 h-100">
                            <div class="small text-muted">Pending</div>
                            <div class="metric-value" style="font-size:1.25rem;">{{ array_sum($pendingCounts) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="dashboard-card p-3 h-100">
                            <div class="small text-muted">Approved</div>
                            <div class="metric-value" style="font-size:1.25rem;">{{ array_sum($approvedCounts) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="dashboard-card p-3 h-100">
                            <div class="small text-muted">Rejected</div>
                            <div class="metric-value" style="font-size:1.25rem;">{{ array_sum($rejectedCounts) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card p-4 mt-3 mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="h5 mb-1">Reports</h2>
                <div class="text-muted small">Generate a summary report for a day, a full month, or a full year then filter by system and status.</div>
            </div>
            <div class="text-end">
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#reportsModal"
                >
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Reports Modal -->
    <div class="modal fade" id="reportsModal" tabindex="-1" aria-labelledby="reportsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportsModalLabel">Generate Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Report period</label>
                            <div class="d-flex flex-wrap gap-3">
                                <label class="form-check-label">
                                    <input type="radio" name="report-period" class="form-check-input" value="daily">
                                    Daily
                                </label>
                                <label class="form-check-label">
                                    <input type="radio" name="report-period" class="form-check-input" value="monthly" checked>
                                    Monthly
                                </label>
                                <label class="form-check-label">
                                    <input type="radio" name="report-period" class="form-check-input" value="yearly">
                                    Yearly
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 report-period-panel d-none" data-report-period="daily">
                            <label class="form-label" for="report-period-day">Day</label>
                            <input
                                type="date"
                                class="form-control"
                                id="report-period-day"
                                value="{{ $defaultReportDay }}"
                            >
                        </div>
                        <div class="col-md-6 report-period-panel" data-report-period="monthly">
                            <label class="form-label" for="report-period-month">Month</label>
                            <input
                                type="month"
                                class="form-control"
                                id="report-period-month"
                                value="{{ $defaultReportMonth }}"
                            >
                        </div>
                        <div class="col-md-6 report-period-panel d-none" data-report-period="yearly">
                            <label class="form-label" for="report-period-year">Year</label>
                            <select id="report-period-year" class="form-select">
                                @foreach($yearOptions as $y)
                                    <option value="{{ $y }}" {{ (int) $y === (int) now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Select System</label>
                            <select id="report-system" class="form-select">
                                <option value="all" selected>All Systems</option>
                                @foreach($systemModules as $sys)
                                    <option value="{{ $sys }}">{{ $sys }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Select Status</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="report_statuses[]" value="approved" checked>
                                    Approved
                                </label>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="report_statuses[]" value="rejected" checked>
                                    Rejected
                                </label>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="report_statuses[]" value="pending" checked>
                                    Pending
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Format</label>
                            <div class="d-flex gap-3 flex-wrap">
                                <label class="form-check-label">
                                    <input type="radio" name="report-format" class="form-check-input" value="pdf" checked>
                                    PDF (for Printing)
                                </label>
                                <label class="form-check-label">
                                    <input type="radio" name="report-format" class="form-check-input" value="excel">
                                    Excel (for Data Analysis)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="me-auto">
                        <div id="reportModalError" class="text-danger small d-none"></div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div id="reportModalLoading" class="text-muted small d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Generating Report...
                        </div>
                        <button type="button" class="btn btn-primary" id="report-modal-generate-btn">
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden canvas for pie chart export -->
    <canvas
        id="reportPieCanvas"
        width="280"
        height="280"
        style="position:absolute; left:-9999px; top:-9999px;"
    ></canvas>

    <div class="dashboard-card p-4 mt-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <div class="h6 text-uppercase text-muted mb-2" style="letter-spacing: 0.04em;">Monthly Report</div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="h5 mb-0">Monthly totals for {{ $year }}</h3>
                    <form method="GET" class="d-inline">
                        <label class="form-label small mb-0" style="display:block;">Year</label>
                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($yearOptions as $y)
                                <option value="{{ $y }}" {{ (int)$y === (int)$year ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <p class="text-muted small mb-0">Chart above matches this report table.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 7rem;">Month</th>
                        <th>Total</th>
                        <th>Pending</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 12; $i++)
                        <tr>
                            <td class="text-nowrap">{{ $labels[$i] }}</td>
                            <td>{{ $totalCounts[$i] }}</td>
                            <td>{{ $pendingCounts[$i] }}</td>
                            <td>{{ $approvedCounts[$i] }}</td>
                            <td>{{ $rejectedCounts[$i] }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.4/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('requestsPerMonthChart');
            if (!ctx) return;

            var labels = {!! json_encode($labels) !!};
            var pending = {!! json_encode($pendingCounts) !!};
            var approved = {!! json_encode($approvedCounts) !!};
            var rejected = {!! json_encode($rejectedCounts) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pending',
                            data: pending,
                            backgroundColor: 'rgba(245, 158, 11, 0.65)',
                            borderColor: 'rgba(245, 158, 11, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Approved',
                            data: approved,
                            backgroundColor: 'rgba(34, 197, 94, 0.55)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Rejected',
                            data: rejected,
                            backgroundColor: 'rgba(239, 68, 68, 0.55)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        });
    </script>

    <script>
        (function () {
            var generateBtn = document.getElementById('report-modal-generate-btn');
            var loadingEl = document.getElementById('reportModalLoading');
            var errorEl = document.getElementById('reportModalError');
            var pieCanvas = document.getElementById('reportPieCanvas');

            if (!generateBtn || !loadingEl || !errorEl || !pieCanvas) return;

            var csrfToken = '{{ csrf_token() }}';
            var reportDataUrl = '{{ route('admin.system.reports.data') }}';

            var currentPieChart = null;

            function pad2(n) {
                return String(n).padStart(2, '0');
            }

            function toggleReportPeriodPanels() {
                var checked = document.querySelector('input[name="report-period"]:checked');
                var mode = checked ? checked.value : 'monthly';
                document.querySelectorAll('.report-period-panel').forEach(function (el) {
                    if (el.getAttribute('data-report-period') === mode) {
                        el.classList.remove('d-none');
                    } else {
                        el.classList.add('d-none');
                    }
                });
            }

            document.querySelectorAll('input[name="report-period"]').forEach(function (radio) {
                radio.addEventListener('change', toggleReportPeriodPanels);
            });
            toggleReportPeriodPanels();

            function computeReportRange() {
                var modeEl = document.querySelector('input[name="report-period"]:checked');
                var mode = modeEl ? modeEl.value : 'monthly';
                if (mode === 'daily') {
                    var d = document.getElementById('report-period-day').value;
                    return { start: d, end: d };
                }
                if (mode === 'monthly') {
                    var mv = document.getElementById('report-period-month').value;
                    if (!mv || mv.indexOf('-') < 0) {
                        return { start: '', end: '' };
                    }
                    var parts = mv.split('-');
                    var y = parseInt(parts[0], 10);
                    var m = parseInt(parts[1], 10);
                    if (!y || !m) {
                        return { start: '', end: '' };
                    }
                    var dim = new Date(y, m, 0).getDate();
                    var start = y + '-' + pad2(m) + '-01';
                    var end = y + '-' + pad2(m) + '-' + pad2(dim);
                    return { start: start, end: end };
                }
                if (mode === 'yearly') {
                    var ySel = document.getElementById('report-period-year');
                    var y = ySel ? parseInt(ySel.value, 10) : NaN;
                    if (!y) {
                        return { start: '', end: '' };
                    }
                    return { start: y + '-01-01', end: y + '-12-31' };
                }
                return { start: '', end: '' };
            }

            function setError(msg) {
                if (!msg) {
                    errorEl.textContent = '';
                    errorEl.classList.add('d-none');
                    return;
                }
                errorEl.textContent = msg;
                errorEl.classList.remove('d-none');
            }

            function truncateText(str, maxLen) {
                str = String(str ?? '');
                if (str.length <= maxLen) return str;
                return str.substring(0, maxLen - 1) + '...';
            }

            function ensureAutoTable(doc) {
                if (doc && typeof doc.autoTable === 'function') return true;
                try {
                    if (window.jspdf && window.jspdf.jsPDF && window.jspdf.jsPDF.API && typeof window.jspdf.jsPDF.API.autoTable === 'function') {
                        doc.autoTable = window.jspdf.jsPDF.API.autoTable;
                        return true;
                    }
                } catch (e) {}
                return false;
            }

            function renderPieDataUrl(approvedCount, rejectedCount) {
                if (currentPieChart) {
                    currentPieChart.destroy();
                    currentPieChart = null;
                }

                // Clear canvas so previous frames don't get blended.
                var ctx2d = pieCanvas.getContext('2d');
                if (ctx2d) {
                    ctx2d.clearRect(0, 0, pieCanvas.width, pieCanvas.height);
                }

                currentPieChart = new Chart(pieCanvas, {
                    type: 'pie',
                    data: {
                        labels: ['Approved', 'Rejected'],
                        datasets: [{
                            data: [approvedCount, rejectedCount],
                            backgroundColor: ['rgba(79, 70, 229, 0.85)', 'rgba(239, 68, 68, 0.85)'],
                            borderColor: ['rgba(79, 70, 229, 1)', 'rgba(239, 68, 68, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        animation: false,
                        layout: { padding: 0 },
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    }
                });

                // Force immediate draw before exporting.
                if (currentPieChart) {
                    currentPieChart.update();
                }

                // toDataURL requires the chart to be drawn at least once.
                var dataUrl = pieCanvas.toDataURL('image/png');
                currentPieChart.destroy();
                currentPieChart = null;
                return dataUrl;
            }

            async function fetchReportData(payload) {
                var res = await fetch(reportDataUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) {
                    var text = await res.text();
                    throw new Error(text || 'Failed to generate report data.');
                }

                return res.json();
            }

            function generatePDF(report) {
                var jsPDFCtor = window.jspdf && window.jspdf.jsPDF ? window.jspdf.jsPDF : null;
                if (!jsPDFCtor) throw new Error('jsPDF failed to load.');

                var doc = new jsPDFCtor({ orientation: 'p', unit: 'pt', format: 'a4' });
                if (!ensureAutoTable(doc)) throw new Error('jsPDF-autotable is not available.');

                var pageWidth = 595.28;
                var pageHeight = 841.89;
                var primaryRGB = [79, 70, 229];

                var approvedCount = (report.summary && report.summary.approvedCount) || 0;
                var rejectedCount = (report.summary && report.summary.rejectedCount) || 0;

                var pieImgData = renderPieDataUrl(approvedCount, rejectedCount);
                var pieSize = 155; // square to keep circle look in PDF
                var pieX = 40;
                var pieY = 125;

                // Header bar
                doc.setFillColor(primaryRGB[0], primaryRGB[1], primaryRGB[2]);
                doc.rect(0, 0, pageWidth, 72, 'F');
                doc.setTextColor(255, 255, 255);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(16);
                // Keep title on the first header line.
                var rp = String(report.reportPeriod || 'custom_range');
                var titleByPeriod = {
                    daily: 'Access Request Daily Summary',
                    monthly: 'Access Request Monthly Summary',
                    yearly: 'Access Request Yearly Summary',
                    custom_range: 'Access Request Summary'
                };
                var docTitle = titleByPeriod[rp] || titleByPeriod.custom_range;
                doc.text(docTitle, pageWidth / 2, 28, { align: 'center' });
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10);
                doc.text('LOGO', 40, 28);
                // Draw period label safely (wrap if needed) so it never overlaps the header title.
                var periodText = String(report.periodLabel || '');
                var maxWidth = pageWidth - 80;
                var periodLines = doc.splitTextToSize(periodText, maxWidth);
                doc.setFontSize(9);
                var startYPeriod = 52;
                var lineHeight = 10;
                for (var pi = 0; pi < periodLines.length; pi++) {
                    doc.text(String(periodLines[pi] || ''), pageWidth - 40, startYPeriod + (pi * lineHeight), { align: 'right' });
                }

                // Section 1
                doc.setTextColor(31, 41, 55);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(12);
                doc.text('Section 1: Visual Analytics', 40, 95);
                doc.setFontSize(11);
                doc.text('Approval vs. Rejection', 40, 112);

                doc.addImage(pieImgData, 'PNG', pieX, pieY, pieSize, pieSize);

                var breakdown = (report.summary && report.summary.statusBreakdown) || {};
                var yBreak = 125;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10);
                doc.text('Pending: ' + (breakdown.pending?.count || 0) + ' (' + (breakdown.pending?.percent || 0) + '%)', 215, yBreak + 10);
                doc.text('Approved: ' + (breakdown.approved?.count || 0) + ' (' + (breakdown.approved?.percent || 0) + '%)', 215, yBreak + 26);
                doc.text('Rejected: ' + (breakdown.rejected?.count || 0) + ' (' + (breakdown.rejected?.percent || 0) + '%)', 215, yBreak + 42);

                // Section 2: KPIs
                var totalRequests = (report.summary && report.summary.totalRequests) || 0;
                var approvalRate = Number((report.summary && report.summary.approvalRate) || 0);
                var mostSystem = truncateText(report.summary?.mostRequestedSystem || 'N/A', 24);

                var startY = 315;
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(12);
                doc.text('Section 2: Executive Summary', 40, startY);

                var cardY = startY + 25;
                var cardH = 78;
                var gap = 15;
                var cardW = (pageWidth - 2 * 40 - 2 * gap) / 3;

                function drawKpiCard(x, label, value) {
                    doc.setFillColor(primaryRGB[0], primaryRGB[1], primaryRGB[2]);
                    doc.rect(x, cardY, cardW, cardH, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(10);
                    doc.text(label, x + 12, cardY + 22);
                    doc.setFontSize(18);
                    doc.text(String(value), x + 12, cardY + 48);
                    doc.setFont('helvetica', 'normal');
                    doc.setTextColor(31, 41, 55);
                }

                drawKpiCard(40 + 0 * (cardW + gap), 'Total Requests', totalRequests);
                drawKpiCard(40 + 1 * (cardW + gap), 'Approval Rate (%)', approvalRate.toFixed(2));
                drawKpiCard(40 + 2 * (cardW + gap), 'Most Requested System', mostSystem);

                // Section 3: Systems table
                var yTable = cardY + cardH + 25;
                var systemRows = (report.systemRows || []).map(function (r) {
                    return [String(r.system || ''), String(r.count || 0)];
                });

                doc.autoTable({
                    head: [['System', 'Requests']],
                    body: systemRows,
                    startY: yTable,
                    theme: 'grid',
                    headStyles: { fillColor: primaryRGB, textColor: 255, fontStyle: 'bold' },
                    styles: { fontSize: 10, cellPadding: 4, overflow: 'linebreak' },
                    alternateRowStyles: { fillColor: [245, 247, 255] },
                    margin: { left: 40, right: 40 }
                });

                // Footer
                var timestamp = new Date().toLocaleString();
                var pageCount = doc.internal.getNumberOfPages();
                for (var i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(8);
                    doc.setTextColor(100);
                    doc.text('Generated by Access Request Admin System', pageWidth / 2, pageHeight - 20, { align: 'center' });
                    doc.text('Page ' + i + ' of ' + pageCount, pageWidth - 70, pageHeight - 20);
                    doc.text(timestamp, pageWidth / 2, pageHeight - 8, { align: 'center' });
                }

                var pdfNameByPeriod = {
                    daily: 'AccessRequestDailySummary_Report.pdf',
                    monthly: 'AccessRequestMonthlySummary_Report.pdf',
                    yearly: 'AccessRequestYearlySummary_Report.pdf',
                    custom_range: 'AccessRequestSummary_Report.pdf'
                };
                doc.save(pdfNameByPeriod[rp] || pdfNameByPeriod.custom_range);
            }

            function generateExcel(report) {
                var summary = report.summary || {};
                var sysRows = report.systemRows || [];

                var period = report.periodLabel || '';
                var breakdown = summary.statusBreakdown || {};

                // Export as HTML (saved with .xls) so it opens nicely in Excel.
                var safeStart = String(report.filters?.start_date || '').replaceAll('-', '');
                var safeEnd = String(report.filters?.end_date || '').replaceAll('-', '');
                var filename = 'AccessRequestReport_' + safeStart + '_to_' + safeEnd + '.xls';

                function esc(v) {
                    return String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                }

                var html = '';
                html += '<html><head><meta charset="utf-8" /></head>';
                html += '<body>';
                var rp = String(report.reportPeriod || 'custom_range');
                var typeLabel = { daily: 'Daily', monthly: 'Monthly', yearly: 'Yearly', custom_range: 'Custom range' }[rp] || 'Summary';
                html += '<h2 style="font-family:Arial;color:#4F46E5;">Access Request Report (' + esc(typeLabel) + ')</h2>';
                html += '<div style="font-family:Arial;margin-bottom:12px;">';
                html += '<b>Period:</b> ' + esc(period) + '<br />';
                html += '<b>Total Requests:</b> ' + esc(summary.totalRequests || 0) + '<br />';
                html += '<b>Approval Rate (%):</b> ' + esc(Number(summary.approvalRate || 0).toFixed ? Number(summary.approvalRate || 0).toFixed(2) : summary.approvalRate || 0) + '<br />';
                html += '<b>Most Requested System:</b> ' + esc(summary.mostRequestedSystem || 'N/A') + '<br />';
                html += '<b>Status Breakdown:</b> Pending ' + esc(breakdown.pending?.count || 0)
                    + ', Approved ' + esc(breakdown.approved?.count || 0)
                    + ', Rejected ' + esc(breakdown.rejected?.count || 0);
                html += '</div>';

                html += '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-family:Arial;font-size:12px;width:100%;">';
                html += '<thead>';
                html += '<tr style="background-color:#4F46E5;color:#fff;">';
                html += '<th style="text-align:left;">System</th>';
                html += '<th style="text-align:right;">Requests</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                if (!sysRows.length) {
                    html += '<tr><td colspan="2" style="text-align:center;">No data found</td></tr>';
                } else {
                    sysRows.forEach(function (r) {
                        html += '<tr>';
                        html += '<td>' + esc(r.system) + '</td>';
                        html += '<td style="text-align:right;">' + esc(r.count) + '</td>';
                        html += '</tr>';
                    });
                }

                html += '</tbody></table>';
                html += '</body></html>';

                var blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }

            generateBtn.addEventListener('click', async function () {
                setError('');

                var range = computeReportRange();
                var startDate = range.start;
                var endDate = range.end;
                var system = document.getElementById('report-system').value;

                var statuses = Array.prototype.slice.call(
                    document.querySelectorAll('input[name="report_statuses[]"]:checked')
                ).map(function (el) { return el.value; });

                var format = document.querySelector('input[name="report-format"]:checked').value;

                if (!startDate || !endDate) {
                    setError('Please choose a valid report period (day, month, or year).');
                    return;
                }

                if (!statuses.length) {
                    setError('Please select at least one status.');
                    return;
                }

                try {
                    // Close the modal immediately after clicking "Generate Report".
                    try {
                        if (window.bootstrap && window.bootstrap.Modal) {
                            var reportsModalEl = document.getElementById('reportsModal');
                            if (reportsModalEl) {
                                window.bootstrap.Modal.getOrCreateInstance(reportsModalEl).hide();
                            }
                        }
                    } catch (e) {}

                    generateBtn.disabled = true;
                    loadingEl.classList.remove('d-none');
                    setError('');

                    var report = await fetchReportData({
                        start_date: startDate,
                        end_date: endDate,
                        system: system,
                        statuses: statuses,
                        format: format
                    });

                    if (format === 'pdf') {
                        generatePDF(report);
                    } else {
                        generateExcel(report);
                    }
                } catch (e) {
                    setError('Failed to generate report: ' + (e && e.message ? e.message : 'Unknown error'));
                    console.error(e);
                } finally {
                    loadingEl.classList.add('d-none');
                    generateBtn.disabled = false;
                }
            });
        })();
    </script>
@endpush
