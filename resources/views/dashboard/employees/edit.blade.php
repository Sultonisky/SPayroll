@extends('layouts.app')
@section('title', 'Edit Employee')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div
                    class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-user-edit me-2"></i>Edit Employee
                    </h5>
                    <a href="{{ route('employees.index') }}"
                        class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee Code</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-hashtag"></i></span>
                                    <input type="text" class="form-control border-start-0 font-monospace"
                                        value="{{ $employee->employee_code ?? '-' }}" disabled>
                                </div>
                                <small class="text-muted">Employee code is auto-generated and cannot be changed.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">User Account</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-user-circle"></i></span>
                                    <select name="user_id"
                                        class="form-select border-start-0 @error('user_id') is-invalid @enderror">
                                        <option value="">Not linked to any user</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id', $employee->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('user_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-id-badge"></i></span>
                                    <input type="text" name="nik"
                                        class="form-control border-start-0 @error('nik') is-invalid @enderror"
                                        placeholder="Enter employee NIK" value="{{ old('nik', $employee->nik) }}"
                                        required>
                                </div>
                                @error('nik')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-user"></i></span>
                                    <input type="text" name="name"
                                        class="form-control border-start-0 @error('name') is-invalid @enderror"
                                        placeholder="Enter full name" value="{{ old('name', $employee->name) }}"
                                        required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-venus-mars"></i></span>
                                    <select name="gender" class="form-select border-start-0 @error('gender') is-invalid @enderror" required>
                                        <option value="">Select gender</option>
                                        <option value="laki-laki" {{ old('gender', $employee->gender) === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="perempuan" {{ old('gender', $employee->gender) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                @error('gender')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-envelope"></i></span>
                                    <input type="email" name="email"
                                        class="form-control border-start-0 @error('email') is-invalid @enderror"
                                        placeholder="Enter email address" value="{{ old('email', $employee->email) }}"
                                        required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-building"></i></span>
                                    <select name="department_id"
                                        class="form-select border-start-0 @error('department_id') is-invalid @enderror"
                                        required>
                                        <option value="">Select department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('department_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-briefcase"></i></span>
                                    <select name="position_id" id="position_id"
                                        class="form-select border-start-0 @error('position_id') is-invalid @enderror"
                                        required>
                                        <option value="">Select position</option>
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->id }}"
                                                data-salary-fulltime="{{ $position->base_salary_fulltime }}"
                                                data-salary-internship="{{ $position->base_salary_internship }}"
                                                {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('position_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee Type <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-user-tag"></i></span>
                                    <select name="employee_type" id="employee_type"
                                        class="form-select border-start-0 @error('employee_type') is-invalid @enderror" required>
                                        <option value="">Select type</option>
                                        <option value="fulltime" {{ old('employee_type', $employee->employee_type) === 'fulltime' ? 'selected' : '' }}>Fulltime</option>
                                        <option value="internship" {{ old('employee_type', $employee->employee_type) === 'internship' ? 'selected' : '' }}>Internship</option>
                                    </select>
                                </div>
                                @error('employee_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Base Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-money-bill-wave"></i></span>
                                    <input type="text" id="base_salary_display"
                                        class="form-control border-start-0"
                                        placeholder="Select position & type to see salary" readonly>
                                </div>
                            </div>
                            
                            <script>
                                function updateSalaryDisplay() {
                                    const positionSelect = document.getElementById('position_id');
                                    const typeSelect = document.getElementById('employee_type');
                                    const display = document.getElementById('base_salary_display');
                                    const selectedOption = positionSelect.options[positionSelect.selectedIndex];
                                    const type = typeSelect.value;
                                    let salary = null;
                                    if (type === 'fulltime') {
                                        salary = selectedOption.getAttribute('data-salary-fulltime');
                                    } else if (type === 'internship') {
                                        salary = selectedOption.getAttribute('data-salary-internship');
                                    }
                                    display.value = salary ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(salary) : '';
                                }
                                document.getElementById('position_id').addEventListener('change', updateSalaryDisplay);
                                document.getElementById('employee_type').addEventListener('change', updateSalaryDisplay);
                                // Trigger on load
                                updateSalaryDisplay();
                            </script>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-phone"></i></span>
                                    <input type="text" name="phone"
                                        class="form-control border-start-0 @error('phone') is-invalid @enderror"
                                        placeholder="Enter phone number" value="{{ old('phone', $employee->phone) }}">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bank Name</label>
                                @php
                                    $commonBanks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga'];
                                    $isOther = $employee->bank_name && !in_array($employee->bank_name, $commonBanks);
                                @endphp
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-university"></i></span>
                                    <select name="bank_name" id="bank_select" class="form-select border-start-0 @error('bank_name') is-invalid @enderror">
                                        <option value="">Select Bank</option>
                                        @foreach($commonBanks as $bank)
                                            <option value="{{ $bank }}" {{ old('bank_name', $employee->bank_name) == $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                        @endforeach
                                        <option value="Other" {{ $isOther ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div id="other_bank_div" class="mt-2" style="{{ $isOther ? 'display: block;' : 'display: none;' }}">
                                    <input type="text" name="bank_name_other" id="other_bank_input" class="form-control" placeholder="Enter other bank name" value="{{ $isOther ? $employee->bank_name : '' }}">
                                </div>
                                @error('bank_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <script>
                                document.getElementById('bank_select').addEventListener('change', function() {
                                    const otherDiv = document.getElementById('other_bank_div');
                                    const otherInput = document.getElementById('other_bank_input');
                                    if (this.value === 'Other') {
                                        otherDiv.style.display = 'block';
                                        otherInput.required = true;
                                    } else {
                                        otherDiv.style.display = 'none';
                                        otherInput.required = false;
                                        otherInput.value = '';
                                    }
                                });
                            </script>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bank Account Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-credit-card"></i></span>
                                    <input type="text" name="bank_account_number"
                                        class="form-control border-start-0 @error('bank_account_number') is-invalid @enderror"
                                        placeholder="Enter bank account number"
                                        value="{{ old('bank_account_number', $employee->bank_account_number) }}">
                                </div>
                                @error('bank_account_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Join Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-calendar-plus"></i></span>
                                    <input type="date" name="join_date"
                                        class="form-control border-start-0 @error('join_date') is-invalid @enderror"
                                        value="{{ old('join_date', optional($employee->join_date)->format('Y-m-d')) }}"
                                        required>
                                </div>
                                @error('join_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Birth Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-calendar-day"></i></span>
                                    <input type="date" name="birth_date"
                                        class="form-control border-start-0 @error('birth_date') is-invalid @enderror"
                                        value="{{ old('birth_date', optional($employee->birth_date)->format('Y-m-d')) }}">
                                </div>
                                @error('birth_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-toggle-on"></i></span>
                                    <select name="employee_status"
                                        class="form-select border-start-0 @error('employee_status') is-invalid @enderror" required>
                                        <option value="">Select status</option>
                                        <option value="active" {{ old('employee_status', $employee->employee_status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('employee_status', $employee->employee_status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="resigned" {{ old('employee_status', $employee->employee_status) === 'resigned' ? 'selected' : '' }}>Resigned</option>
                                    </select>
                                </div>
                                @error('employee_status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-map-marker-alt"></i></span>
                                    <textarea name="address" rows="3" class="form-control border-start-0 @error('address') is-invalid @enderror"
                                        placeholder="Enter address">{{ old('address', $employee->address) }}</textarea>
                                </div>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit"
                                class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-check-circle me-2"></i>Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
