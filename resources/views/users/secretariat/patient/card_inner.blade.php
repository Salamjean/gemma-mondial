<div class="row justify-content-center">
    <div class="col-md-11 text-center">

        <!-- FLIP CONTAINER -->
        <div class="id-card-wrapper">
            <div class="flip-card" onclick="this.classList.toggle('flipped')">
                <div class="flip-card-inner">

                    <!-- RECTO (FRONT) -->
                    <div class="id-card id-card-front">
                        <!-- HEADER -->
                        <div class="id-card-header">
                            <div class="header-logo-left">
                                <div style="font-size: 10px; font-weight: bold; text-align: center; line-height: 1.1;">
                                    <img src="{{ asset('assets/images/sante.jpg') }}"
                                        style="height: 90px; display: block; margin: 0 auto 5px;" alt="Logo">
                                </div>
                            </div>
                            <div class="header-logo-center">
                                <div style="font-size: 8px; text-transform: uppercase; margin-bottom: 2px;">Ma Carte de
                                    Soin Electronique</div>
                                <div
                                    style="background: white; border: 1px solid #ddd; border-radius: 20px; padding: 5px 15px; box-shadow: 2px 2px 5px rgba(0,0,0,0.1); display: inline-flex; align-items: center;">
                                    <span
                                        style="font-weight: 900; font-size: 20px; color: #0056b3; margin-right: 10px; font-family: sans-serif;">MA-CSE</span>
                                    <i class="fa fa-heartbeat" style="font-size: 24px; color: #dc3545;"></i>
                                </div>
                                <div
                                    style="font-size: 16px; font-weight: bold; margin-top: 8px; color: #333; letter-spacing: 1px;">
                                    {{ $patient->code_patient }}
                                </div>
                            </div>
                            <div class="header-logo-right">
                                <img src="{{ asset('assets/images/embleme.png') }}" style="height: 90px;"
                                    alt="Emblème CI">
                            </div>
                        </div>

                        <!-- QR CODE (Front) -->
                        <div class="qrcode-container" id="qrcode-front"></div>

                        <!-- INFO BOX -->
                        <div class="patient-info-box" style="text-align: start;">
                            <div class="info-row">
                                <span class="info-label">Nom:</span> <span
                                    class="info-value">{{ strtoupper($patient->user->name) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Prénom:</span> <span
                                    class="info-value">{{ ucfirst($patient->user->prenom) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Né(e) le:</span> <span class="info-value">
                                    {{ \Carbon\Carbon::createFromFormat('d/m/Y', $patient->birth_date)->locale('fr')->isoFormat('D MMMM YYYY') }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Validité:</span> <span class="info-value">jusqu'au
                                    {{ \Carbon\Carbon::parse($patient->created_at)->addYears(5)->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
                            </div>
                        </div>

                        <!-- PHOTO -->
                        @php
                            $avatar = 'assets/images/avatar.png';

                            if ($patient->gender === 'masculin') {
                                $avatar = 'assets/images/avatar.png';
                            } elseif ($patient->gender === 'feminin') {
                                $avatar = 'assets/images/avatar3.png';
                            }
                        @endphp

                        <div class="patient-photo-container">
                            @if ($patient->img_url)
                                <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}" class="patient-photo"
                                    alt="Photo" onerror="this.onerror=null; this.src='{{ asset($avatar) }}';">
                            @else
                                <img src="{{ asset($avatar) }}" class="patient-photo" alt="Photo">
                            @endif
                        </div>


                        <!-- MAIN TITLE -->
                        <div class="main-title">
                            CARTE DE SANTE ELECTRONIQUE
                        </div>

                        @if ($patient->isDeceased() || $patient->declarationDeces)
                            <div style="position: absolute; top: 48%; left: 50%; transform: translate(-50%, -50%) rotate(-20deg); border: 4px solid #dc3545; color: #dc3545; font-size: 30px; font-weight: 900; padding: 6px 22px; border-radius: 10px; text-transform: uppercase; letter-spacing: 3px; background: rgba(255, 255, 255, 0.92); box-shadow: 0 6px 20px rgba(220, 53, 69, 0.35); z-index: 99; pointer-events: none;">
                                <i class="fa-solid fa-skull-crossbones me-2"></i> DÉCÉDÉ
                            </div>
                        @endif

                        <!-- FOOTER -->
                        <div class="card-footer" style="background-color: #3596f7;">
                            <div class="footer-logo"><img src="{{ asset('home/assets/img/logo/GEMMA.jpeg') }}"
                                    style="height: 100%; width: 100%; object-fit: contain;" alt="Logo"></div>
                            <div class="footer-text">
                                <strong>GEMMA votre partenaire en santé</strong><br>
                                Tel: +225 07 11 11 79 79<br>
                                Email: gemma@gmail.com
                            </div>
                        </div>
                    </div>

                    <!-- VERSO (BACK) -->
                    <div class="id-card id-card-back">
                        <div class="magnetic-strip"></div>

                        <div class="back-content">
                            <div class="legal-text">
                                <p>Cette carte est la propriété de GEMMA.
                                </p>
                                <p>Elle est personnelle et incessible. En cas de perte, veuillez contacter
                                    GEMMA.</p>
                                <p class="mt-2"><strong>En cas d'urgence:</strong>
                                    {{ $patient->telephone_personne_cas_urgence ?? '+225 07 11 117 979' }}</p>
                            </div>

                            <div class="codes-section">
                                <div class="qr-box">
                                    <div id="qrcode-back"></div>
                                    <span class="code-label">Scan ME</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="footer-text w-100 text-center" style="font-size: 10px; opacity: 0.8;">
                                Powered by GEMMA Technology
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var qrCodeScript = "https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js";

        function runQRCode() {
            var qf = document.getElementById("qrcode-front");
            var qb = document.getElementById("qrcode-back");

            if (qf) qf.innerHTML = "";
            if (qb) qb.innerHTML = "";

            if (typeof QRCode !== 'undefined') {
                var qrText =
                    "DM:{{ $patient->code_patient }}\n" +
                    "Nom:{{ optional($patient->user)->name ?? '' }}\n" +
                    "Prenom:{{ optional($patient->user)->prenom ?? '' }}\n" +
                    "Tel:{{ $patient->telephone ?? '' }}";

                // var qrText = "{{ $patient->code_patient }}|{{ optional($patient->user)->name ?? '' }}|{{ optional($patient->user)->prenom ?? '' }}|{{ $patient->telephone ?? '' }}";

                console.log("Texte raccourci:", qrText);
                console.log("Longueur du texte:", qrText.length);

                new QRCode(qf, {
                    text: qrText,
                    width: 70,
                    height: 70,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.L
                });

                new QRCode(qb, {
                    text: qrText,
                    width: 90,
                    height: 90,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.L
                });

                setTimeout(() => {
                    document.querySelectorAll(".qrcode-container img, .qr-box img").forEach(img => {
                        img.style.margin = "auto";
                        img.style.display = "block";
                    });
                }, 100);
            }
        }

        if (typeof QRCode === 'undefined') {
            var script = document.createElement('script');
            script.src = qrCodeScript;
            script.onload = runQRCode;
            document.head.appendChild(script);
        } else {
            runQRCode();
        }
    })();
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Libre+Barcode+39+Text&display=swap');

    .id-card-wrapper {
        perspective: 1000px;
        width: 600px;
        height: 380px;
        margin: 0 auto;
        zoom: 1.3;
        transform-origin: top center;
    }

    /* Modal specific: scale down if screen is small */
    @media (max-width: 768px) {
        .id-card-wrapper {
            zoom: 0.8;
            max-width: 100%;
        }
    }
    @media (max-width: 520px) {
        .id-card-wrapper {
            zoom: 0.55;
            max-width: 100%;
        }
    }
    @media (max-width: 380px) {
        .id-card-wrapper {
            zoom: 0.45;
            max-width: 100%;
        }
    }

    .flip-card {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.8s;
        transform-style: preserve-3d;
        cursor: pointer;
    }

    .flip-card.flipped {
        transform: rotateY(180deg);
    }

    .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transform-style: preserve-3d;
    }

    /* Common Face Styles */
    .id-card {
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        border-radius: 25px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        border: 1px solid #eee;
        background-color: #fff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Front Face */
    .id-card-front {
        z-index: 2;
    }

    /* Back Face */
    .id-card-back {
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        background: #f9f9f9;
        justify-content: space-between;
    }

    /* --- Inner Component Styles --- */
    .id-card-header {
        display: flex;
        justify-content: space-between;
        padding: 15px 30px 0 30px;
        align-items: flex-start;
    }

    .header-logo-left {
        width: 100px;
    }

    .header-logo-right {
        width: 100px;
        text-align: right;
    }

    .header-logo-center {
        flex-grow: 1;
        text-align: center;
    }

    .qrcode-container {
        position: absolute;
        top: 155px;
        left: 40px;
        padding: 5px;
        background: white;
        border: 1px solid #eee;
        border-radius: 4px;
    }

    .patient-info-box {
        position: absolute;
        top: 130px;
        align-items: start;
        left: 130px;
        padding: 10px 15px;
        background: white;
        border-radius: 12px;
        box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.3), -1px -1px 2px rgba(255, 255, 255, 0.5);
        width: 320px;
        z-index: 2;
    }

    .info-row {
        margin-bottom: 6px;
        font-size: 15px;
        line-height: 1.4;
    }

    .info-label {
        font-weight: normal;
        color: #555;
        width: 70px;
        display: inline-block;
    }

    .info-value {
        font-weight: bold;
        color: #000;
        font-size: 16px;
    }

    .patient-photo-container {
        position: absolute;
        top: 130px;
        right: 20px;
        width: 115px;
        height: 130px;
        background: #eee;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .patient-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .main-title {
        position: absolute;
        bottom: 75px;
        width: 100%;
        text-align: center;
        font-size: 22px;
        font-weight: bold;
        color: #000;
        letter-spacing: 0.5px;
    }

    .card-footer {
        height: 60px;
        background-color: #3596f7;
        display: flex;
        align-items: center;
        padding: 0 20px;
        color: white;
        margin-top: auto;
    }

    .id-card-front .card-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
    }

    .footer-logo {
        width: 60px;
        height: 40px;
        background: #fff;
        margin-right: 15px;
        overflow: hidden;
    }

    .footer-text {
        font-size: 11px;
        line-height: 1.3;
    }

    .magnetic-strip {
        width: 100%;
        height: 50px;
        background: #333;
        margin-top: 30px;
    }

    .back-content {
        padding: 10px 40px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .legal-text {
        font-size: 12px;
        color: #666;
        text-align: center;
        margin-bottom: 15px;
    }

    .codes-section {
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .qr-box {
        text-align: center;
    }

    .code-label {
        display: block;
        font-size: 10px;
        color: #999;
        margin-top: 5px;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .id-card-wrapper,
        .id-card-wrapper * {
            visibility: visible;
        }

        .id-card-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
            transform: none;
            perspective: none;
            zoom: 1;
        }

        .flip-card,
        .flip-card-inner,
        .id-card,
        .id-card-back {
            transform: none !important;
            display: block;
            height: auto;
            backface-visibility: visible !important;
            margin-bottom: 20px;
            position: relative;
            top: auto;
            left: auto;
        }

        /* Buttons hidden */
        .btn,
        .action-buttons,
        .modal-header,
        .btn-close {
            display: none !important;
        }
    }
</style>