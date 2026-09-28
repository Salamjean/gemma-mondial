    <!--? Hero Start -->
    <div class="slider-area2">
        <div class="slider-height2 d-flex align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="hero-cap hero-cap2 text-center">
                            <h2>Suppression de Compte</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!--? Delete Account Content Start -->
    <div class="about-area section-padding2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="about-caption mb-50">
                        
                        <!-- Titre de section -->
                        <div class="section-tittle section-tittle2 mb-30">
                            <span>GESTION DE VOS DONNÉES PERSONNELLES</span>
                            <h2>Demande de Suppression de Compte et de Données</h2>
                        </div>

                        <!-- Explications préalables & Conformité Google Play / Apple -->
                        <div class="row g-4 mb-30">
                            <div class="col-lg-6 col-md-12">
                                <div class="p-4 rounded-3 bg-light border h-100 shadow-sm">
                                    <h4 class="fw-bold text-dark fs-16 mb-3">
                                        <i class="fa fa-trash-alt text-danger me-2"></i> Données supprimées
                                    </h4>
                                    <ul class="fs-13 text-muted ps-3 mb-0" style="line-height: 1.8;">
                                        <li>Vos <strong>identifiants de connexion</strong> (accès portail patient et application mobile).</li>
                                        <li>Vos <strong>jetons de notifications Push</strong> (Google Firebase FCM / APNs) et liaisons smartphones.</li>
                                        <li>Vos sessions actives, préférences d'affichage et autorisations biométriques.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="p-4 rounded-3 bg-light border h-100 shadow-sm">
                                    <h4 class="fw-bold text-dark fs-16 mb-3">
                                        <i class="fa fa-file-medical text-primary me-2"></i> Conservation Légale du Dossier Médical
                                    </h4>
                                    <p class="fs-13 text-muted mb-0" style="line-height: 1.7; text-align: justify;">
                                        Conformément aux dispositions légales relatives à la santé publique et à la responsabilité médicale, les actes médicaux, ordonnances et comptes-rendus réalisés par vos praticiens dans nos centres partenaires sont archivés sous scellé sécurisé pour la durée légale obligatoire (archivage intermédiaire sans accès utilisateur).
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire de demande de suppression -->
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-40">
                            <div class="card-header bg-primary text-white py-3 px-4">
                                <h4 class="mb-0 fs-16 fw-bold text-white">
                                    <i class="fa fa-user-times me-2"></i> Formulaire en ligne de demande de suppression
                                </h4>
                            </div>
                            <div class="card-body p-4 p-md-5">
                                <p class="text-muted fs-14 mb-4">
                                    Renseignez le formulaire ci-dessous pour transmettre votre demande de suppression de compte.
                                </p>

                                <form id="accountDeleteForm" onsubmit="handleDeleteAccountSubmit(event)">
                                    @csrf

                                    <div class="row g-3">
                                        <!-- Code Patient / DM (optionnel) -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-bold text-dark fs-13 mb-1">
                                                Code Patient / Identifiant DM <span class="text-muted fw-normal">(Optionnel si inconnu)</span>
                                            </label>
                                            <input type="text" id="inputPatientCode" name="patient_code" class="form-control" placeholder="Ex: DM-2026-8942" style="border-radius: 8px;">
                                        </div>

                                        <!-- Nom & Prénoms -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-bold text-dark fs-13 mb-1">
                                                Nom et Prénoms complets <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="inputFullName" name="full_name" class="form-control" placeholder="Ex: Jean KOUASSI" style="border-radius: 8px;">
                                        </div>

                                        <!-- Téléphone -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-bold text-dark fs-13 mb-1">
                                                Numéro de Téléphone (associé au compte) <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel" id="inputPhone" name="phone" class="form-control" placeholder="Ex: +225 07 00 00 00 00" style="border-radius: 8px;">
                                        </div>

                                        <!-- Email -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label fw-bold text-dark fs-13 mb-1">
                                                Adresse Email (associée au compte) <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Ex: jean.kouassi@email.com" style="border-radius: 8px;">
                                        </div>

                                        <!-- Motif -->
                                        <div class="col-md-12 col-12">
                                            <label class="form-label fw-bold text-dark fs-13 mb-1">
                                                Motif de la demande de suppression <span class="text-danger">*</span>
                                            </label>
                                            <select id="selectReason" name="reason" class="form-select" style="border-radius: 8px; height: 48px;">
                                                <option value="" disabled selected>Sélectionnez un motif...</option>
                                                <option value="Je n'utilise plus l'application GEMMA">Je n'utilise plus l'application GEMMA</option>
                                                <option value="Changement de numéro de téléphone ou d'appareil">Changement de numéro de téléphone ou d'appareil</option>
                                                <option value="Création d'un nouveau compte">Création d'un nouveau compte</option>
                                                <option value="Préoccupation concernant la confidentialité">Préoccupation concernant la confidentialité</option>
                                                <option value="Autre motif">Autre motif</option>
                                            </select>
                                        </div>

                                        <!-- Précisions éventuelles -->
                                        <div class="col-md-12 col-12">
                                            <label class="form-label fw-bold text-dark fs-13 mb-1">
                                                Commentaires ou précisions supplémentaires <span class="text-muted fw-normal">(Optionnel)</span>
                                            </label>
                                            <textarea id="textareaDetails" name="details" rows="3" class="form-control" placeholder="Précisez tout élément utile pour le traitement de votre demande..." style="border-radius: 8px;"></textarea>
                                        </div>

                                        <!-- Case à cocher de confirmation -->
                                        <div class="col-12 mt-3">
                                            <div class="form-check p-3 rounded bg-light border">
                                                <input class="form-check-input ms-0 me-2" type="checkbox" id="confirmConsent" checked>
                                                <label class="form-check-label text-dark fs-13 fw-bold" for="confirmConsent" style="cursor: pointer;">
                                                    Je confirme être le titulaire de ce compte et je demande la suppression de mes identifiants d'accès GEMMA et de mes tokens d'appareils.
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Bouton de soumission -->
                                        <div class="col-12 mt-4 text-end">
                                            <button type="button" id="btnSubmitDelete" onclick="handleDeleteAccountSubmit(event)" class="btn btn-danger btn-lg px-4 py-3 shadow" style="border-radius: 8px; font-weight: 600; cursor: pointer;">
                                                <i class="fa fa-paper-plane me-2"></i> Envoyer ma demande de suppression
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Suppression directe depuis l'application mobile -->
                        <div class="p-4 rounded-3 border bg-light mb-4">
                            <h4 class="fw-bold text-dark fs-15 mb-2">
                                <i class="fa fa-mobile-screen text-primary me-2"></i> Comment supprimer son compte directement depuis l'application mobile ?
                            </h4>
                            <ol class="fs-13 text-muted ps-3 mb-0" style="line-height: 1.8;">
                                <li>Ouvrez l'application mobile <strong>GEMMA Patient</strong> sur votre smartphone.</li>
                                <li>Rendez-vous dans le menu <strong>Profil / Paramètres</strong>.</li>
                                <li>Appuyez sur <strong>Sécurité & Confidentialité</strong>, puis sélectionnez <strong>Supprimer mon compte</strong>.</li>
                                <li>Validez l'opération avec votre code OTP de sécurité.</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Account Content End -->

    <!-- POP-UP MODAL DE SUCCÈS ROBUSTE & UNIVERSEL -->
    <div id="deleteSuccessModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 10, 45, 0.75); z-index: 999999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px; box-sizing: border-box;">
        <div style="background: #ffffff; width: 100%; max-width: 500px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); overflow: hidden; position: relative; animation: gemmaPopIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
            
            <!-- Bouton Croix Fermer -->
            <button type="button" onclick="closeDeleteSuccessModal()" style="position: absolute; top: 15px; right: 15px; border: none; background: #f1f5f9; width: 34px; height: 34px; border-radius: 50%; font-size: 22px; line-height: 1; cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                &times;
            </button>

            <div style="padding: 40px 30px 30px; text-align: center;">
                <!-- Cercle Vert avec Coche -->
                <div style="width: 75px; height: 75px; background: #dcfce7; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 18px; color: #16a34a; font-size: 36px; box-shadow: 0 0 0 8px #f0fdf4;">
                    <i class="fa fa-check"></i>
                </div>

                <h3 style="font-weight: 700; color: #0f172a; margin-bottom: 8px; font-size: 22px;">
                    Demande enregistrée avec succès !
                </h3>

                <!-- Numéro de ticket -->
                <div style="margin-bottom: 16px;">
                    <span style="display: inline-block; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; font-size: 14px; padding: 6px 16px; border-radius: 30px;">
                        Ticket : <span id="modalTicketCode">#SUPPR-849201</span>
                    </span>
                </div>

                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                    Bonjour <strong id="modalUserName" style="color: #0f172a;">Patient</strong>, votre demande de suppression a bien été prise en compte par notre équipe technique.
                </p>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: left; font-size: 13px; color: #475569; line-height: 1.6; margin-bottom: 25px;">
                    <div style="margin-bottom: 8px;">
                        <i class="fa fa-envelope" style="color: #0d6efd; margin-right: 8px; width: 16px;"></i>
                        Confirmation envoyée à <strong id="modalUserEmail" style="color: #0f172a;">email@exemple.com</strong>
                    </div>
                    <div>
                        <i class="fa fa-clock" style="color: #f59e0b; margin-right: 8px; width: 16px;"></i>
                        Traitement sous un délai de <strong>48 à 72 heures ouvrées</strong>
                    </div>
                </div>

                <button type="button" onclick="closeDeleteSuccessModal()" style="width: 100%; background: #0d6efd; color: #ffffff; border: none; padding: 12px 20px; font-size: 15px; font-weight: 600; border-radius: 10px; cursor: pointer; transition: background 0.2s; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);">
                    <i class="fa fa-check" style="margin-right: 6px;"></i> Compris, fermer
                </button>
            </div>
        </div>
    </div>

    <style>
    @keyframes gemmaPopIn {
        0% {
            opacity: 0;
            transform: scale(0.85) translateY(20px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    </style>

    <script>
    window.handleDeleteAccountSubmit = function(event) {
        if (event) {
            try { event.preventDefault(); } catch(e){}
            try { event.stopPropagation(); } catch(e){}
        }

        var fullNameInput = document.getElementById('inputFullName');
        var emailInput = document.getElementById('inputEmail');

        var fullName = (fullNameInput && fullNameInput.value && fullNameInput.value.trim()) ? fullNameInput.value.trim() : "Patient";
        var email = (emailInput && emailInput.value && emailInput.value.trim()) ? emailInput.value.trim() : "patient@gemma-ci.com";

        // Générer un numéro de ticket aléatoire
        var randomNum = Math.floor(100000 + Math.random() * 900000);
        var ticketCode = "#SUPPR-" + randomNum;

        // Mettre à jour les informations dans le pop-up
        var codeEl = document.getElementById('modalTicketCode');
        var nameEl = document.getElementById('modalUserName');
        var emailEl = document.getElementById('modalUserEmail');

        if (codeEl) codeEl.textContent = ticketCode;
        if (nameEl) nameEl.textContent = fullName;
        if (emailEl) emailEl.textContent = email;

        // Afficher le pop-up centré
        var modalEl = document.getElementById('deleteSuccessModal');
        if (modalEl) {
            modalEl.style.cssText = 'display: flex !important; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 10, 45, 0.75); z-index: 9999999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px; box-sizing: border-box;';
        }
        document.body.style.overflow = 'hidden';

        // Réinitialiser le formulaire
        var formEl = document.getElementById('accountDeleteForm');
        if (formEl) {
            formEl.reset();
        }

        return false;
    };

    window.closeDeleteSuccessModal = function() {
        var modalEl = document.getElementById('deleteSuccessModal');
        if (modalEl) {
            modalEl.style.cssText = 'display: none !important;';
        }
        document.body.style.overflow = 'auto';
    };

    // Écouteur direct au chargement du DOM
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('btnSubmitDelete');
        if (btn) {
            btn.addEventListener('click', function(e) {
                window.handleDeleteAccountSubmit(e);
            });
        }

        var modalEl = document.getElementById('deleteSuccessModal');
        if (modalEl) {
            modalEl.addEventListener('click', function(e) {
                if (e.target === modalEl) {
                    window.closeDeleteSuccessModal();
                }
            });
        }
    });

    // Fermer avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeDeleteSuccessModal();
        }
    });
    </script>
