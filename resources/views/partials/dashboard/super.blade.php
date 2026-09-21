@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.min.css"  crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        #map {
            height: 600px;
        }
    </style>
@endpush


<div class="row">
    <div class="col-xxl-2 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-success-light">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/icons/hospital.png')}}" class="avatar avatar-md" alt="icon">
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 fw-700 text-success">{{ \App\Models\Hospital::count() }}</h2>
                        <p class="text-muted mt-5 mb-0 fs-13">Hôpitaux</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-primary-light">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/icons/team.png')}}" class="avatar avatar-md" alt="patients">
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 fw-700 text-primary">{{ \App\Models\Patient::count() }}</h2>
                        <p class="text-muted mt-5 mb-0 fs-13">Patients Inscrits</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-info-light">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/icons/consult.png')}}" class="avatar avatar-md" alt="doctor">
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 fw-700 text-info">{{ \App\Models\Consultation::count() }}</h2>
                        <p class="text-muted mt-5 mb-0 fs-13">Consultations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-2 col-xl-6 col-md-6 col-sm-6 col-12">
        <div class="box bg-warning-light">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/uploads/baby.png')}}" class="avatar avatar-md" alt="secretariat">
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 fw-700 text-warning">{{ \App\Models\Declaration::where('type', 'birth')->count() }}</h2>
                        <p class="text-muted mt-5 mb-0 fs-13">Naissances</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-2 col-xl-6 col-md-6 col-sm-6 col-12">
        <div class="box bg-danger-light">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/uploads/deces.png')}}" class="avatar avatar-md" alt="consultation">
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 fw-700 text-danger">{{ \App\Models\Declaration::where('type', 'death')->count() }}</h2>
                        <p class="text-muted mt-5 mb-0 fs-13">Décès</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row pt-35">
    <div class="" style="width: 50%;">
        <canvas id="declaration"></canvas>
    </div>

    <div class="" style="width: 50%;">
        <canvas id="consultation"></canvas>
    </div>

</div>


@push('js')
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script >

        (async function() {

            const dataC = [
                    @foreach($dataConsultation as $key => $item)
                { year: "{{ chartMonthFrensh($key . "-01") }}", count: {{ $item }} },
                @endforeach
            ];

            new Chart(
                document.getElementById('consultation'),
                {
                    type: 'bar',
                    data: {
                        labels: dataC.map(row => row.year),
                        datasets: [
                            {
                                label: 'Nombre de consultation.',
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(255, 159, 64, 0.2)',
                                    'rgba(255, 205, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(201, 203, 207, 0.2)',
                                    'rgba(97,255,19,0.2)',
                                    'rgba(0,0,0,0.2)',
                                    'rgba(4,28,62,0.2)',
                                ],
                                borderColor: [
                                    'rgb(255, 99, 132)',
                                    'rgb(255, 159, 64)',
                                    'rgb(255, 205, 86)',
                                    'rgb(75, 192, 192)',
                                    'rgb(54, 162, 235)',
                                    'rgb(153, 102, 255)',
                                    'rgb(201, 203, 207)',
                                    'rgba(97,255,19,1)',
                                    'rgba(0,0,0,1)',
                                    'rgba(4,28,62,1)',
                                ],
                                borderWidth: 1,
                                data: dataC.map(row => row.count)
                            },

                        ]
                    }
                }
            );

            const dataN = [
                    @foreach($dataBirth as $key => $item)
                { year: "{{ chartMonthFrensh($key . "-01") }}", count: {{ $item }} },
                @endforeach
            ];

            const dataD = [
                    @foreach($dataDeath as $key => $item)
                { year: "{{ chartMonthFrensh($key . "-01") }}", count: {{ $item }} },
                @endforeach
            ];

            new Chart(
                document.getElementById('declaration'),
                {
                    type: 'line',
                    data: {
                        labels: dataN.map(row => row.year),
                        datasets: [

                            {
                                label: 'Nombre de naissance.',
                                data: dataN.map(row => row.count)
                            },
                            {
                                label: 'Nombre de décès.',
                                data: dataD.map(row => row.count)
                            },


                        ]
                    }
                }
            );
        })();

    </script>
@endpush
