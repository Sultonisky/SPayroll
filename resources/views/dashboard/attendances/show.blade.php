@extends('layouts.app')
@section('title', 'Attendance Detail')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div
                    class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-id-card me-2"></i>Attendance Information
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('attendances.index') }}"
                            class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-auto text-center mb-3 mb-md-0">
                            <div class="d-inline-block position-relative">
                                <div class="text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-4 border-primary"
                                    style="width: 140px; height: 140px;">
                                    <i class="fas fa-user-tie fa-5x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md ms-md-4 text-center text-md-start">
                            <h2 class="fw-bold text-body mb-1">
                                {{ $attendance->employee ? $attendance->employee->name : 'Unknown' }}
                            </h2>
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                <span class="badge bg-body text-body border rounded-pill px-4 py-2 fs-6">
                                    <i class="fas fa-id-badge me-2 text-info"></i>
                                    {{ $attendance->employee ? $attendance->employee->nik : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Period filter --}}
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Select Period</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary border-end-0 text-black"><i
                                        class="fas fa-calendar-alt"></i></span>
                                <select id="periodFilter" class="form-select border-start-0">
                                    @foreach ($periods as $period)
                                        <option value="{{ $period->id }}"
                                            {{ $period->id == $attendance->id ? 'selected' : '' }}>
                                            {{ $period->month }}/{{ $period->year }}
                                            {{ $period->deleted_at ? '(deleted)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Period actions (symmetric, equal-width buttons) --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                                    <a href="{{ route('attendances.edit', $attendance->id) }}"
                                        id="editPeriodBtn"
                                        class="btn btn-warning btn-sm rounded-pill px-3 flex-fill shadow-sm">
                                        <i class="fas fa-edit me-2"></i>Edit Period
                                    </a>
                                    <span id="trashBtnGroup" class="d-flex flex-fill gap-2">
                                        <button type="button"
                                            class="btn btn-outline-danger btn-sm rounded-pill px-3 flex-fill shadow-sm"
                                            data-coreui-toggle="modal" data-coreui-target="#trashModal">
                                            <i class="fas fa-trash me-2"></i>Move to Trash
                                        </button>
                                    </span>
                                    <span id="restoreBtnGroup" class="d-flex flex-fill gap-2">
                                        <button type="button"
                                            class="btn btn-success btn-sm rounded-pill px-3 flex-fill shadow-sm"
                                            data-coreui-toggle="modal" data-coreui-target="#restoreModal">
                                            <i class="fas fa-undo me-2"></i>Restore
                                        </button>
                                        <button type="button"
                                            class="btn btn-danger btn-sm rounded-pill px-3 flex-fill shadow-sm"
                                            data-coreui-toggle="modal" data-coreui-target="#forceDeleteModal">
                                            <i class="fas fa-times-circle me-2"></i>Delete Permanent
                                        </button>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Action modals (targets rebound per selected period via JS) --}}
                    @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                        <x-modal id="trashModal" title="Move to Trash" type="danger"
                            :actionUrl="route('attendances.destroy', $attendance->id)" method="DELETE"
                            confirmText="Move to Trash">
                            <div class="text-center py-3">
                                <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                                <h5 class="fw-bold">Move to Trash?</h5>
                                <p class="text-muted mb-0" id="trashModalText">Are you sure you want to move this
                                    period to trash?</p>
                            </div>
                        </x-modal>

                        <x-modal id="restoreModal" title="Restore Period" type="success"
                            :actionUrl="route('attendances.restore', $attendance->id)" method="POST"
                            confirmText="Restore">
                            <div class="text-center py-3">
                                <i class="fas fa-undo text-success fa-3x mb-3"></i>
                                <h5 class="fw-bold">Restore Period?</h5>
                                <p class="text-muted mb-0" id="restoreModalText">Are you sure you want to restore this
                                    period?</p>
                            </div>
                        </x-modal>

                        <x-modal id="forceDeleteModal" title="Permanently Delete" type="danger"
                            :actionUrl="route('attendances.force-delete', $attendance->id)" method="DELETE"
                            confirmText="Permanently Delete">
                            <div class="text-center py-3">
                                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                                <h5 class="fw-bold">Permanent Action!</h5>
                                <p class="text-muted mb-0" id="forceDeleteModalText">Are you sure you want to
                                    permanently delete this period?</p>
                                <p class="text-danger small mt-2 mb-0">This data cannot be recovered.</p>
                            </div>
                        </x-modal>
                    @endif

                    {{-- Selected period details --}}
                    <div id="periodDetail" data-current="{{ $attendance->id }}">
                        <div class="row g-4">
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Attendance ID</div>
                                        <div class="fs-5 fw-bold text-body">
                                            #{{ str_pad($attendance->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Period</div>
                                        <div class="fs-5 fw-bold text-body">{{ $attendance->month }}/{{ $attendance->year }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Work Days</div>
                                        <div class="fs-5 fw-bold text-body">{{ $attendance->work_days }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Present</div>
                                        <div class="fs-5 fw-bold text-body">{{ $attendance->present }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Sick</div>
                                        <div class="fs-5 fw-bold text-body">{{ $attendance->sick }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Leave</div>
                                        <div class="fs-5 fw-bold text-body">{{ $attendance->leave }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Alpha</div>
                                        <div class="fs-5 fw-bold text-body">{{ $attendance->alpha }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Overtime Hours</div>
                                        <div class="fs-5 fw-bold text-body">{{ $attendance->overtime_hours }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Created At</div>
                                        <div class="fs-6 fw-bold text-body">
                                            {{ $attendance->created_at->translatedFormat('d F Y, H:i') }} WIB
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Last Updated</div>
                                        <div class="fs-6 fw-bold text-body">
                                            {{ $attendance->updated_at->translatedFormat('d F Y, H:i') }} WIB
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Notes</div>
                                        <div class="fs-6 fw-normal text-body">{{ $attendance->notes ?: '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top text-center text-muted small italic">
                        <i class="fas fa-info-circle me-1"></i> Information retrieved from system database on
                        {{ now()->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var periods = @json($periods->keyBy('id'));

            function renderDetail(id) {
                var a = periods[id];
                if (!a) {
                    return;
                }
                var html = '' +
                    '<div class="row g-4">' +
                    card('Attendance ID', '#' + String(a.id).padStart(5, '0')) +
                    card('Period', a.month + '/' + a.year) +
                    card('Work Days', a.work_days) +
                    card('Present', a.present) +
                    card('Sick', a.sick) +
                    card('Leave', a.leave) +
                    card('Alpha', a.alpha) +
                    card('Overtime Hours', a.overtime_hours) +
                    card('Created At', formatDate(a.created_at)) +
                    card('Last Updated', formatDate(a.updated_at)) +
                    cardFull('Notes', a.notes ? a.notes : '-') +
                    '</div>';
                $('#periodDetail').html(html).data('current', id);
            }

            function card(label, value) {
                return '<div class="col-sm-6 col-xl-4"><div class="card h-100 border-0 bg-body-tertiary shadow-sm">' +
                    '<div class="card-body p-3"><div class="text-uppercase small fw-bold text-primary mb-2">' +
                    label + '</div><div class="fs-5 fw-bold text-body">' + value + '</div></div></div></div>';
            }

            function cardFull(label, value) {
                return '<div class="col-12"><div class="card h-100 border-0 bg-body-tertiary shadow-sm">' +
                    '<div class="card-body p-3"><div class="text-uppercase small fw-bold text-primary mb-2">' +
                    label + '</div><div class="fs-6 fw-normal text-body">' + value + '</div></div></div></div>';
            }

            function formatDate(value) {
                if (!value) return '-';
                var d = new Date(value.replace(' ', 'T'));
                if (isNaN(d)) return value;
                return d.toLocaleString('en-GB', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }) + ' WIB';
            }

            var editBase = '{{ route('attendances.show', $attendance->id) }}';
            editBase = editBase.replace(/\/[^\/]+$/, '');

            function updateActions(id) {
                var a = periods[id];
                if (!a) {
                    return;
                }
                var label = a.month + '/' + a.year + (a.deleted_at ? ' (deleted)' : '');

                $('#editPeriodBtn').attr('href', editBase + '/' + id + '/edit');

                $('#trashModal form').attr('action', editBase + '/' + id);
                $('#restoreModal form').attr('action', editBase + '/' + id + '/restore');
                $('#forceDeleteModal form').attr('action', editBase + '/' + id + '/force-delete');

                $('#trashModalText').html('Are you sure you want to move period <strong>' + label + '</strong> to trash?');
                $('#restoreModalText').html('Are you sure you want to restore period <strong>' + label + '</strong>?');
                $('#forceDeleteModalText').html('Are you sure you want to permanently delete period <strong>' + label + '</strong>?');

                $('#trashBtnGroup, #restoreBtnGroup').addClass('d-none');
                if (a.deleted_at) {
                    $('#restoreBtnGroup').removeClass('d-none');
                } else {
                    $('#trashBtnGroup').removeClass('d-none');
                }
            }

            $('#periodFilter').on('change', function() {
                var id = $(this).val();
                renderDetail(id);
                updateActions(id);
            });

            updateActions($('#periodFilter').val());
        });
    </script>
@endpush
