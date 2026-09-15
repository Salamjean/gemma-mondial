    <!--? Hero Start -->
    <div class="slider-area2">
        <div class="slider-height2 d-flex align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="hero-cap hero-cap2 text-center">
                            <h2>Contactez-nous</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- ================ contact section start ================= -->
    <section class="contact-section section-padding">
        <div class="container">

            <div class="row">
                <div class="col-12">
                    <h2 class="contact-title fw-bold mb-4" style="color: #000a2d;">Restons en contact</h2>
                </div>

                <!-- Message de succès d'envoi -->
                <div class="col-12" id="contactSuccessBanner" style="display: {{ request()->has('sent') ? 'block' : 'none' }};">
                    <div class="alert alert-success d-flex align-items-center p-3 mb-4 rounded-3 shadow-sm border border-success" role="alert">
                        <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                        <div>
                            <h5 class="alert-heading fw-bold mb-1" style="color: #0f5132;">Message envoyé avec succès !</h5>
                            <p class="mb-0 fs-14">
                                Merci d'avoir contacté l'équipe <strong>GEMMA</strong> (KKS-TECHNOLOGIES). Votre message a été bien transmis et notre support prendra contact avec vous dans les plus brefs délais.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-5 mb-lg-0">
                    <form class="form-contact contact_form" action="{{ route('contact') }}" method="get" id="contactForm" onsubmit="handleContactSubmit(event)">
                        <input type="hidden" name="sent" value="1">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label for="message" class="form-label fw-bold">Votre message</label>
                                    <textarea class="form-control w-100 rounded" name="message" id="message" cols="30" rows="6" placeholder="Saisissez votre message ici..." required></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="name" class="form-label fw-bold">Nom & Prénom</label>
                                    <input class="form-control rounded" name="name" id="name" type="text" placeholder="Entrez votre nom complet" value="" required>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-group">
                                    <label for="email" class="form-label fw-bold">Adresse Email</label>
                                    <input class="form-control rounded" name="email" id="email" type="email" placeholder="Ex: exemple@domaine.com" value="" required>
                                </div>
                            </div>
                            <div class="col-12 mb-4">
                                <div class="form-group">
                                    <label for="subject" class="form-label fw-bold">Sujet / Objet</label>
                                    <input class="form-control rounded" name="subject" id="subject" type="text" placeholder="Entrez le sujet de votre message" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <button type="submit" class="button button-contactForm boxed-btn">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-3 offset-lg-1">
                    <div class="media contact-info mb-4 d-flex align-items-start">
                        <span class="contact-info__icon me-3"><i class="ti-home fs-20 text-primary"></i></span>
                        <div class="media-body">
                            <h3 class="fw-bold fs-16 mb-1">KKS-TECHNOLOGIES</h3>
                            <p class="text-muted mb-0">Abidjan, Côte d'Ivoire</p>
                        </div>
                    </div>
                    <div class="media contact-info mb-4 d-flex align-items-start">
                        <span class="contact-info__icon me-3"><i class="ti-tablet fs-20 text-primary"></i></span>
                        <div class="media-body">
                            <h3 class="fw-bold fs-16 mb-1">+225 07 00 00 00 00</h3>
                            <p class="text-muted mb-0">Du Lundi au Vendredi de 08h à 18h</p>
                        </div>
                    </div>
                    <div class="media contact-info mb-4 d-flex align-items-start">
                        <span class="contact-info__icon me-3"><i class="ti-email fs-20 text-primary"></i></span>
                        <div class="media-body">
                            <h3 class="fw-bold fs-16 mb-1">contact@kks-technologies.com</h3>
                            <p class="text-muted mb-0">Envoyez-nous vos requêtes à tout moment !</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- ================ contact section end ================= -->

    <script>
        function handleContactSubmit(e) {
            e.preventDefault();
            var banner = document.getElementById('contactSuccessBanner');
            var form = document.getElementById('contactForm');
            if (banner) {
                banner.style.display = 'block';
                banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            if (form) {
                form.reset();
            }
        }
    </script>
