<section class="bg-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-20">
        <div class="flex flex-col justify-center">
            <p class="text-sm font-semibold text-teal-700">Sistem pakar kesehatan mental remaja</p>
            <h1 class="mt-4 max-w-3xl text-4xl font-semibold leading-tight tracking-normal text-slate-950 lg:text-5xl">
                Deteksi awal yang tenang, jelas, dan mudah dipahami.
            </h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600">
                Pilih gejala yang dialami, lalu sistem menghitung kemungkinan awal dengan metode Dempster-Shafer.
            </p>
            <div id="konsultasi" class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ \Illuminate\Support\Facades\Route::has('consultation.create') ? route('consultation.create') : '#' }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800">
                    Mulai Konsultasi
                </a>
                <a href="{{ \Illuminate\Support\Facades\Route::has('info') ? route('info') : '#info' }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Info Gangguan
                </a>
            </div>
            <p class="mt-4 max-w-xl text-sm leading-6 text-slate-500">
                Hasil sistem hanya untuk deteksi awal dan perlu ditinjau tenaga profesional bila gejala berlanjut.
            </p>
        </div>

        <div class="self-center rounded-lg border border-slate-200 bg-slate-50 p-6">
            <p class="text-sm font-semibold text-slate-950">Alur singkat</p>
            <div class="mt-5 space-y-4">
                <div class="flex gap-4 rounded-md bg-white p-4">
                    <span class="grid size-8 shrink-0 place-items-center rounded-full bg-teal-100 text-sm font-semibold text-teal-800">1</span>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-950">Isi data singkat</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Nama, usia, dan konteks keluarga seperlunya.</p>
                    </div>
                </div>
                <div class="flex gap-4 rounded-md bg-white p-4">
                    <span class="grid size-8 shrink-0 place-items-center rounded-full bg-teal-100 text-sm font-semibold text-teal-800">2</span>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-950">Pilih gejala</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Sistem membaca gejala dari basis pengetahuan.</p>
                    </div>
                </div>
                <div class="flex gap-4 rounded-md bg-white p-4">
                    <span class="grid size-8 shrink-0 place-items-center rounded-full bg-teal-100 text-sm font-semibold text-teal-800">3</span>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-950">Lihat hasil</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Hasil dapat dicetak untuk arsip konsultasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="info" class="border-t border-slate-200 bg-slate-50">
    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
        <article class="rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="text-base font-semibold text-slate-950">Konsultasi</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Form sederhana untuk memilih gejala dan menyimpan hasil.</p>
        </article>
        <article class="rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="text-base font-semibold text-slate-950">Basis pengetahuan</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Admin mengelola gejala, gangguan, dan nilai belief.</p>
        </article>
        <article class="rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="text-base font-semibold text-slate-950">Riwayat</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Hasil konsultasi tersimpan dan dapat dicetak.</p>
        </article>
    </div>
</section>
