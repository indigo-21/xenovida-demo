<x-app-layout>
    <x-slot name="header">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Patients</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Patients</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-injured mr-1"></i> List of Patients
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('patient.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Add Patient
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="patientTbl" class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Postcode</th>
                                <th>Mobile</th>
                                <th style="width:12%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $patient)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $patient->firstname }} {{ $patient->lastname }}</td>
                                    <td>{{ $patient->email }}</td>
                                    <td>{{ optional($patient->dob)->format('Y-m-d') ?? '—' }}</td>
                                    <td>{{ $patient->gender ?? '—' }}</td>
                                    <td>{{ $patient->postcode ?? '—' }}</td>
                                    <td>{{ $patient->mobile ?? '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('patient.show', $patient->id) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('patient.edit', $patient->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('patient.destroy', $patient->id) }}"
                                           landto="{{ route('patient.index') }}"
                                           class="btn btn-sm btn-danger delete-data-btn">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script>
            $(function () {
                $('#patientTbl').DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                }).buttons().container().appendTo('#patientTbl_wrapper .col-md-6:eq(0)');
            });
        </script>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
    </x-slot>
</x-app-layout>
