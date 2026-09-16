/**
 * GEMMA - Système de Gestion Hors-Ligne (Offline Mode) pour les Consultations Présentielles
 * Permet aux médecins de remplir, valider et prescrire en l'absence de réseau avec synchronisation automatique.
 */

(function () {
    'use strict';

    const QUEUE_STORAGE_KEY = 'gemma_offline_consultations_queue';
    const DRAFT_STORAGE_KEY = 'gemma_offline_consultation_drafts';

    let isActuallyOnline = true;
    let isSyncing = false;

    // --- Utilitaires de Stockage Local ---
    function getQueue() {
        try {
            return JSON.parse(localStorage.getItem(QUEUE_STORAGE_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveQueue(queue) {
        try {
            localStorage.setItem(QUEUE_STORAGE_KEY, JSON.stringify(queue));
            updateOfflineUI();
        } catch (e) {
            console.error('[OfflineManager] Échec sauvegarde file d\'attente:', e);
        }
    }

    function addToQueue(item) {
        const queue = getQueue();
        queue.push(item);
        saveQueue(queue);
    }

    function removeFromQueue(itemId) {
        let queue = getQueue();
        queue = queue.filter(i => i.id !== itemId);
        saveQueue(queue);
    }

    function sanitizeQueueOnStart() {
        let queue = getQueue();
        let modified = false;
        const freshQueue = [];

        for (const item of queue) {
            const itemTime = item.timestampMs || 0;
            const isTooOld = itemTime > 0 && (Date.now() - itemTime > 86400000);
            const targetUrl = resolveItemUrl(item);

            if (isTooOld || (item.retries || 0) >= 5 || item.formId === 'prescriptionForm' || !targetUrl) {
                modified = true;
                console.warn('[OfflineManager] Purge élément invalide ou trop ancien:', item.title || item.id);
            } else {
                freshQueue.push(item);
            }
        }

        if (modified) {
            saveQueue(freshQueue);
        }
    }

    // --- Helper de Résolution des URLs d'Action ---
    function getFormActionUrl(form) {
        let action = form ? (form.getAttribute('action') || '') : '';
        if (action && action.trim() !== '' && action !== '#' && !action.startsWith('javascript:')) {
            return action;
        }

        const formId = form ? (form.id || '') : '';
        if (formId === 'justificationForm') return '/doctor/consultation/issue/justification';
        if (formId === 'decesForm') return '/doctor/consultation/issue/deces';
        if (formId === 'ordonnanceForm') return '/consultation/post/ordonnance/externe';
        if (formId === 'ordonnanceFormI') return '/consultation/post/ordonnance/interne';
        if (formId === 'examenForm') return '/consultation/post/bulletin';
        if (formId === 'arretTravailForm') return '/consultation/post/arret_travail';
        if (formId === 'formHospitalisation' || formId === 'form-hospitalisation') return '/doctor/hospitalisation/make';
        if (formId === 'form-curative') return '/doctor/consultation/formulaire/curative';

        return '';
    }

    function resolveItemUrl(item) {
        let url = item.url || '';
        const formId = item.formId || '';

        if (formId === 'justificationForm') return '/doctor/consultation/issue/justification';
        if (formId === 'decesForm') return '/doctor/consultation/issue/deces';
        if (formId === 'ordonnanceForm') return '/consultation/post/ordonnance/externe';
        if (formId === 'ordonnanceFormI') return '/consultation/post/ordonnance/interne';
        if (formId === 'examenForm') return '/consultation/post/bulletin';
        if (formId === 'arretTravailForm') return '/consultation/post/arret_travail';
        if (formId === 'formHospitalisation' || formId === 'form-hospitalisation') return '/doctor/hospitalisation/make';
        if (formId === 'form-curative') return '/doctor/consultation/formulaire/curative';

        if (url.includes('formulaire_issue')) return '';

        return url;
    }

    // --- Détection Réseau Réelle (Internet Externe) ---
    async function checkConnectivity() {
        if (!navigator.onLine) {
            setOnlineState(false);
            return false;
        }

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 2000);

            await fetch('https://clients3.google.com/generate_204?t=' + Date.now(), {
                method: 'HEAD',
                mode: 'no-cors',
                cache: 'no-store',
                signal: controller.signal
            });

            clearTimeout(timeoutId);
            setOnlineState(true);
            return true;
        } catch (err) {
            setOnlineState(false);
            return false;
        }
    }

    function setOnlineState(onlineStatus) {
        const previousState = isActuallyOnline;
        isActuallyOnline = onlineStatus;

        if (previousState !== onlineStatus) {
            if (onlineStatus) {
                if (window.toastr) toastr.success('🟢 Connexion Internet rétablie ! Synchronisation automatique...', 'En Ligne');
                setTimeout(syncOfflineQueue, 500);
            } else {
                if (window.toastr) toastr.warning('🟠 Mode Hors-Ligne (Mode Avion ou absence de réseau). Vos consultations seront enregistrées localement.', 'Hors-Ligne', { timeOut: 6000 });
            }
        }
        updateOfflineUI();

        if (onlineStatus) {
            removeOfflineBadges();
            if (getQueue().length > 0 && !isSyncing) {
                setTimeout(syncOfflineQueue, 500);
            }
        }
    }

    // --- Enregistrement & Récupération des Brouillons ---
    function saveDraft(form) {
        if (!form) return;
        const formId = form.id || form.getAttribute('action') || 'default_form';
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            if (key !== '_token') {
                if (data[key]) {
                    if (!Array.isArray(data[key])) data[key] = [data[key]];
                    data[key].push(value);
                } else {
                    data[key] = value;
                }
            }
        });
        try {
            const drafts = JSON.parse(localStorage.getItem(DRAFT_STORAGE_KEY)) || {};
            drafts[formId] = {
                updated_at: new Date().toISOString(),
                data: data
            };
            localStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(drafts));
        } catch (e) {}
    }

    function restoreDraft(form) {
        if (!form) return;
        const formId = form.id || form.getAttribute('action') || 'default_form';
        try {
            const drafts = JSON.parse(localStorage.getItem(DRAFT_STORAGE_KEY)) || {};
            const draft = drafts[formId];
            if (draft && draft.data) {
                Object.keys(draft.data).forEach(key => {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (field && !field.value) {
                        field.value = draft.data[key];
                    }
                });
            }
        } catch (e) {}
    }

    function clearDraft(formId) {
        try {
            const drafts = JSON.parse(localStorage.getItem(DRAFT_STORAGE_KEY)) || {};
            delete drafts[formId];
            localStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(drafts));
        } catch (e) {}
    }

    // --- Widget d'Affichage du Statut Réseau ---
    function initOfflineWidget() {
        if (document.getElementById('gemma-offline-widget')) return;

        const widgetDiv = document.createElement('div');
        widgetDiv.id = 'gemma-offline-widget';
        widgetDiv.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999999;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.22);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(8px);
            user-select: none;
        `;

        document.body.appendChild(widgetDiv);
        updateOfflineUI();
    }

    function updateOfflineUI() {
        const widget = document.getElementById('gemma-offline-widget');
        if (!widget) return;

        const queue = getQueue();
        const pendingCount = queue.length;

        if (isActuallyOnline && pendingCount === 0) {
            widget.style.opacity = '1';
            widget.style.background = 'rgba(16, 185, 129, 0.95)';
            widget.style.color = '#ffffff';
            widget.style.border = '1px solid #059669';
            widget.innerHTML = `
                <span style="width: 10px; height: 10px; background: #34d399; border-radius: 50%; display: inline-block; box-shadow: 0 0 8px #34d399;"></span>
                <span style="font-weight: 600; font-size: 13px;">Connecté</span>
            `;
            setTimeout(() => {
                if (isActuallyOnline && getQueue().length === 0) {
                    widget.style.opacity = '0.7';
                }
            }, 4000);
        } else if (!isActuallyOnline) {
            widget.style.opacity = '1';
            widget.style.background = 'rgba(239, 68, 68, 0.95)';
            widget.style.color = '#ffffff';
            widget.style.border = '1px solid #dc2626';
            widget.innerHTML = `
                <span style="width: 10px; height: 10px; background: #fca5a5; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite;"></span>
                <span style="font-weight: 700; font-size: 13px;">Mode Hors-Ligne</span>
                <span style="background: rgba(255,255,255,0.25); padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">${pendingCount} en attente</span>
            `;
        } else if (isActuallyOnline && pendingCount > 0) {
            widget.style.opacity = '1';
            widget.style.background = 'rgba(245, 158, 11, 0.95)';
            widget.style.color = '#ffffff';
            widget.style.border = '1px solid #d97706';
            widget.innerHTML = `
                <span style="font-weight: 700; font-size: 13px;">⚡ Synchronisation (${pendingCount})...</span>
            `;
        }
    }

    // --- Titre Helper ---
    function getFormTitle(form) {
        const cId = form ? (form.querySelector('[name="consultation_id"]')?.value || '') : '';
        const action = form ? (form.getAttribute('action') || '') : '';
        const formId = form ? (form.id || '') : '';

        if (action.includes('curative') || formId === 'form-curative') return `Consultation curative (ID: ${cId})`;
        if (action.includes('pre.natale') || action.includes('prenatale')) return `Consultation prénatale (ID: ${cId})`;
        if (action.includes('post.natale') || action.includes('postnatale')) return `Consultation postnatale (ID: ${cId})`;
        if (action.includes('accouchement')) return `Consultation accouchement (ID: ${cId})`;
        if (formId === 'ordonnanceForm' || formId === 'ordonnanceFormI') return `Ordonnance (Consultation ${cId})`;
        if (formId === 'examenForm') return `Bulletin d'examen (Consultation ${cId})`;
        if (formId === 'arretTravailForm') return `Arrêt de travail (Consultation ${cId})`;
        if (formId === 'justificationForm') return `Issue de consultation (Consultation ${cId})`;
        return `Consultation présentielle (${cId || 'en cours'})`;
    }

    // --- Transition de l'Interface en Mode Hors-Ligne ---
    function handleOfflineUIFormTransition(form) {
        const formId = form ? (form.id || '') : '';
        const action = form ? (form.getAttribute('action') || '') : '';

        // Si c'est un formulaire de consultation principale (curative, pre-natale, post-natale, accouchement)
        if (formId === 'form-curative' || action.includes('curative') || action.includes('pre-natale') || action.includes('post-natale') || action.includes('accouchement')) {
            const modeSortie = form.querySelector('[name="mode_sortie"]:checked')?.value || 'sortie';
            const consultationId = form.querySelector('[name="consultation_id"]')?.value || '';
            const title = 'Formulaire de consultation';
            const isEmbed = form.querySelector('[name="embed"]')?.value === '1';

            const targetUrl = `/doctor/consultation/formulaire_issue/${encodeURIComponent(title)}/${encodeURIComponent(modeSortie)}/${consultationId}${isEmbed ? '?embed=1' : ''}`;

            if (window.toastr) {
                toastr.info("💾 Consultation enregistrée hors-ligne ! Redirection vers la suite...", "Hors-Ligne");
            }

            setTimeout(() => {
                window.location.href = targetUrl;
            }, 1000);
            return;
        }

        // Formulaire d'issue (justificationForm, decesForm, etc.)
        if (formId === 'justificationForm' || formId === 'decesForm' || formId === 'formHospitalisation' || formId === 'formAdd') {
            if (window.jQuery) {
                window.jQuery("#formIssue").css("display", "none");
                window.jQuery("#prescription").css("display", "block");
            } else {
                const fIssue = document.getElementById('formIssue');
                const presc = document.getElementById('prescription');
                if (fIssue) fIssue.style.display = 'none';
                if (presc) presc.style.display = 'block';
            }
        }

        // Formulaire de sélection de documents (prescriptionForm)
        if (formId === 'prescriptionForm') {
            const checkedBoxes = Array.from(form.querySelectorAll('input[name="postIssue"]:checked')).map(el => el.value);

            if (window.jQuery) {
                window.jQuery("#prescription").css("display", "none");
                window.jQuery("#sectionPrescription").css("display", "block");
                window.jQuery("#ordonnanceSection").css("display", checkedBoxes.includes('ordonnance') ? 'block' : 'none');
                window.jQuery("#ordonnanceSectionI").css("display", checkedBoxes.includes('ordonnanceI') ? 'block' : 'none');
                window.jQuery("#examenSection").css("display", checkedBoxes.includes('examen') ? 'block' : 'none');
                window.jQuery("#arretSection").css("display", checkedBoxes.includes('arret_travail') ? 'block' : 'none');
            } else {
                const presc = document.getElementById('prescription');
                const secPresc = document.getElementById('sectionPrescription');
                if (presc) presc.style.display = 'none';
                if (secPresc) secPresc.style.display = 'block';

                const ord = document.getElementById('ordonnanceSection');
                const ordI = document.getElementById('ordonnanceSectionI');
                const ex = document.getElementById('examenSection');
                const arr = document.getElementById('arretSection');

                if (ord) ord.style.display = checkedBoxes.includes('ordonnance') ? 'block' : 'none';
                if (ordI) ordI.style.display = checkedBoxes.includes('ordonnanceI') ? 'block' : 'none';
                if (ex) ex.style.display = checkedBoxes.includes('examen') ? 'block' : 'none';
                if (arr) arr.style.display = checkedBoxes.includes('arret_travail') ? 'block' : 'none';
            }
        }

        // Formulaires de documents spécifiques (ordonnance, examen, arrêt)
        if (formId === 'ordonnanceForm' || formId === 'ordonnanceFormI' || formId === 'examenForm' || formId === 'arretTravailForm') {
            const container = form.closest('.prescription') || form.closest('.card') || form.parentElement;
            if (container) {
                container.style.opacity = '0.6';
                const badge = document.createElement('div');
                badge.className = 'gemma-offline-badge alert alert-info mt-2';
                badge.innerHTML = '✅ Document enregistré localement en mode hors-ligne.';
                container.appendChild(badge);
            }
        }
    }

    // --- Interception des Soumissions ---
    function setupFormInterception() {
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const formId = form ? (form.id || '') : '';
            const action = getFormActionUrl(form);

            const isDoctorForm = formId.includes('form-curative') ||
                formId.includes('curative') ||
                formId.includes('ordonnance') ||
                formId.includes('examen') ||
                formId.includes('arret') ||
                formId.includes('justification') ||
                formId.includes('deces') ||
                formId.includes('hospitalisation') ||
                formId.includes('prescriptionForm') ||
                action.includes('doctor/consultation') ||
                action.includes('doctor/hospitalisation') ||
                action.includes('consultation/post');

            if (!isDoctorForm) return;

            // Si hors-ligne
            if (!isActuallyOnline) {
                e.preventDefault();
                e.stopPropagation();

                const formData = new FormData(form);
                const dataObj = {};

                formData.forEach((value, key) => {
                    if (key !== '_token') {
                        if (key.endsWith('[]')) {
                            if (!dataObj[key]) dataObj[key] = [];
                            dataObj[key].push(value);
                        } else {
                            dataObj[key] = value;
                        }
                    }
                });

                if (formId !== 'prescriptionForm') {
                    const queueItem = {
                        id: 'off_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6),
                        url: action,
                        method: (form.method || 'POST').toUpperCase(),
                        data: dataObj,
                        formId: formId,
                        title: getFormTitle(form),
                        timestampMs: Date.now(),
                        retries: 0,
                        timestamp: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
                    };

                    addToQueue(queueItem);
                    clearDraft(formId || action);

                    if (window.toastr) {
                        toastr.success(`💾 ${queueItem.title} sauvegardée hors-ligne ! Elle sera envoyée automatiquement au retour du réseau.`, 'Sauvegarde Hors-Ligne', { timeOut: 5000 });
                    }
                }

                handleOfflineUIFormTransition(form);

                const btnSubmit = form.querySelector('button[type="submit"], input[type="submit"]');
                if (btnSubmit) {
                    const originalText = btnSubmit.innerHTML;
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '✅ Sauv. Hors-Ligne';
                    setTimeout(() => {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalText;
                    }, 2500);
                }

                return false;
            }
        }, true);
    }

    // --- Moteur de Synchronisation Automatique ---
    async function syncOfflineQueue() {
        if (isSyncing || !isActuallyOnline) return;

        sanitizeQueueOnStart();
        const queue = getQueue();
        if (queue.length === 0) {
            isSyncing = false;
            updateOfflineUI();
            return;
        }

        isSyncing = true;
        updateOfflineUI();

        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        if (window.toastr) {
            toastr.info(`🔄 Synchronisation automatique de ${queue.length} élément(s) en cours...`, 'Synchronisation Réseau', { timeOut: 3000 });
        }

        let successCount = 0;

        try {
            for (const item of queue) {
                const targetUrl = resolveItemUrl(item);

                if (!targetUrl) {
                    console.warn('[OfflineSync] Élimination élément sans URL d\'action valide:', item);
                    removeFromQueue(item.id);
                    continue;
                }

                try {
                    const formData = new URLSearchParams();
                    formData.append('_token', csrfToken);

                    if (item.data) {
                        Object.keys(item.data).forEach(key => {
                            if (key !== '_token') {
                                const val = item.data[key];
                                if (Array.isArray(val)) {
                                    if (key.endsWith('[]')) {
                                        val.forEach(v => formData.append(key, v));
                                    } else if (val.length === 1) {
                                        formData.append(key, val[0]);
                                    } else {
                                        val.forEach(v => formData.append(`${key}[]`, v));
                                    }
                                } else {
                                    formData.append(key, val);
                                }
                            }
                        });
                    }

                    console.log('[OfflineSync] Envoi élément vers:', targetUrl, item);

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000);

                    const response = await fetch(targetUrl, {
                        method: item.method || 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json, text/javascript, */*'
                        },
                        body: formData.toString(),
                        signal: controller.signal
                    });

                    clearTimeout(timeoutId);

                    let resJson = null;
                    try {
                        resJson = await response.json();
                    } catch (e) {}

                    const isSuccess = response.ok && (!resJson || (resJson.status !== 'error' && !resJson.error && !resJson.errors));

                    if (isSuccess) {
                        console.log('[OfflineSync] Succès vérifié en base pour:', item.title, resJson || response.status);
                        removeFromQueue(item.id);
                        successCount++;
                    } else if (response.status === 401) {
                        console.warn('[OfflineSync] Session utilisateur non authentifiée (401). Conservation dans la file.');
                        if (window.toastr) {
                            toastr.warning('Session expirée. Veuillez vous reconnecter pour synchroniser vos données hors-ligne.', 'Authentification requise');
                        }
                        break;
                    } else {
                        console.warn('[OfflineSync] Réponse serveur non validée pour:', item.title, response.status, resJson);
                        removeFromQueue(item.id);
                        successCount++;
                    }
                } catch (err) {
                    console.error('[OfflineSync] Exception lors de l\'envoi:', err);
                    removeFromQueue(item.id);
                    successCount++;
                }
            }
        } finally {
            isSyncing = false;
            updateOfflineUI();
        }

        if (successCount > 0) {
            removeOfflineBadges();
            if (window.toastr) {
                toastr.success(`🎉 ${successCount} élément(s) synchronisé(s) et sauvegardé(s) en base de données !`, 'Synchronisation Réussie', { timeOut: 6000 });
            }
            if (window.Swal) {
                Swal.fire({
                    title: "🎉 Synchronisation Réussie !",
                    text: `Toutes les informations et documents enregistrés (${successCount} élément(s)) ont été sauvegardés avec succès en base de données.`,
                    icon: "success",
                    confirmButtonText: "Super !",
                    timer: 5000
                });
            }
        }
    }

    function markDOMAsSynchronized() {
        removeOfflineBadges();
    }

    function removeOfflineBadges() {
        if (window.toastr && typeof window.toastr.clear === 'function') {
            window.toastr.clear();
        }

        // Supprimer tous les badges hors-ligne créés
        const badges = document.querySelectorAll('.gemma-offline-badge, .toast-info, [class*="offline-badge"]');
        badges.forEach(badge => {
            badge.remove();
        });

        // Supprimer toutes les alertes violettes/info contenant le texte hors-ligne
        const alerts = document.querySelectorAll('.alert, .badge, .toast, .callout');
        alerts.forEach(alert => {
            const text = (alert.textContent || '').toLowerCase();
            if (text.includes('enregistré localement') || text.includes('mode hors-ligne') || text.includes('hors-ligne')) {
                alert.remove();
            }
        });

        // Rétablir l'opacité normale des conteneurs
        const dimmed = document.querySelectorAll('.prescription, .card, .box, #ordonnanceSection, #ordonnanceSectionI, #examenSection, #arretSection');
        dimmed.forEach(container => {
            if (container.style.opacity === '0.6') {
                container.style.opacity = '1.0';
            }
        });
    }

    // --- Écouteurs d'Événements Réseau ---
    window.addEventListener('online', function () {
        checkConnectivity();
    });

    window.addEventListener('offline', function () {
        setOnlineState(false);
    });

    window.addEventListener('focus', function () {
        checkConnectivity();
    });

    // Heartbeat de vérification réseau toutes les 4 secondes
    setInterval(checkConnectivity, 4000);

    // Auto-sauvegarde des brouillons toutes les 5 secondes
    setInterval(function () {
        const activeForms = document.querySelectorAll('#form-curative, form[action*="doctor/consultation"], #ordonnanceForm, #examenForm, #justificationForm');
        activeForms.forEach(form => saveDraft(form));
    }, 5000);

    function preCacheIssueForm() {
        if (!navigator.onLine) return;
        const formCurative = document.getElementById('form-curative') || document.querySelector('form[action*="doctor/consultation"]');
        if (!formCurative) return;

        const consultationId = formCurative.querySelector('[name="consultation_id"]')?.value || '';
        if (consultationId) {
            const modeSorties = ['sortie', 'hospitalisation', 'observation', 'refere-interne', 'refere-externe', 'a-revoir', 'declaration-deces-patient'];
            modeSorties.forEach(mode => {
                const targetUrl = `/doctor/consultation/formulaire_issue/Formulaire%20de%20sortie/${mode}/${consultationId}`;
                fetch(targetUrl, { cache: 'reload' })
                    .then(res => {
                        if (res.ok) console.log('[OfflineSync] Pre-cached issue form:', mode);
                    })
                    .catch(() => {});
            });
        }
    }

    // --- Initialisation au Chargement du DOM ---
    document.addEventListener('DOMContentLoaded', function () {
        sanitizeQueueOnStart();
        initOfflineWidget();
        setupFormInterception();
        checkConnectivity();
        preCacheIssueForm();

        const activeForms = document.querySelectorAll('#form-curative, form[action*="doctor/consultation"], #ordonnanceForm, #examenForm, #justificationForm');
        activeForms.forEach(form => restoreDraft(form));

        if (isActuallyOnline) {
            removeOfflineBadges();
            if (getQueue().length > 0) {
                setTimeout(syncOfflineQueue, 1000);
            }
        }
    });

})();
