@extends('layouts.app')

@section('title', 'Reports - Savidya Education')
@section('page-title', 'Reports')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Report Management</li>
@endsection

@section('content')

{{-- ================= Daily Payment Report ================= --}}
<div class="card mb-4">
    <div class="card-body">

        <h5>Daily Payment Report</h5>

        <a href="{{ route('reports.daily.pdf', date('Y-m-d')) }}"
           class="btn btn-danger">
            📄 Download Daily Report (PDF)
        </a>

    </div>
</div>

{{-- ================= Teacher Payment Report ================= --}}
<div class="card">
    <div class="card-body">

        <h5>Teacher Payment Report</h5>

        <div class="row g-3 align-items-end">

            {{-- Teacher Dropdown --}}
            <div class="col-md-5">
                <label class="form-label">Teacher</label>
                <select id="teacher_id" class="form-select">
                    <option value="">-- Select Teacher --</option>
                </select>
            </div>

            {{-- Year Month --}}
            <div class="col-md-4">
                <label class="form-label">Month</label>
                <input type="month" id="year_month" class="form-control">
            </div>

            {{-- Download Button --}}
            <div class="col-md-3">
                <button id="downloadReport" class="btn btn-danger w-100">
                    📄 Download Payment Report
                </button>
            </div>

        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ---------- Load Teachers Dropdown ---------- */
    fetch('/api/teachers/dropdown')
        .then(response => response.json())
        .then(res => {
            if (res.data) {
                const select = document.getElementById('teacher_id');

                res.data.forEach(teacher => {
                    const option = document.createElement('option');
                    option.value = teacher.id;
                    option.textContent = `${teacher.custom_id} - ${teacher.fname}`;
                    select.appendChild(option);
                });
            }
        })
        .catch(() => {
            alert('Failed to load teachers');
        });

    /* ---------- Download Teacher Payment Report ---------- */
    document.getElementById('downloadReport').addEventListener('click', function () {

        const teacherId = document.getElementById('teacher_id').value;
        const yearMonth = document.getElementById('year_month').value;

        if (!teacherId || !yearMonth) {
            alert('Please select Teacher and Month');
            return;
        }

        const url = `/send-mail/pdf/${teacherId}/${yearMonth}`;
        window.open(url, '_blank');
    });

});
</script>
@endpush
