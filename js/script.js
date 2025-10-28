// Navigation mobile
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Fermer le menu mobile quand on clique sur un lien
document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
    hamburger.classList.remove('active');
    navMenu.classList.remove('active');
}));

// Smooth scrolling pour les liens d'ancrage
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Changement de couleur de la navbar au scroll
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.15)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
    }
});

// Animation des éléments au scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observer les éléments à animer
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.team-card, .news-card, .stat, .contact-item');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});

// Gestion du formulaire d'inscription
const inscriptionForm = document.getElementById('inscriptionForm');
if (inscriptionForm) {
    inscriptionForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Afficher l'état de chargement
        submitBtn.textContent = 'Inscription en cours...';
        submitBtn.disabled = true;
        this.classList.add('loading');
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch('php/inscription.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('Inscription réussie ! Nous vous contacterons bientôt.', 'success');
                this.reset();
            } else {
                showMessage(result.message || 'Erreur lors de l\'inscription. Veuillez réessayer.', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showMessage('Erreur de connexion. Veuillez réessayer plus tard.', 'error');
        } finally {
            // Restaurer l'état du bouton
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            this.classList.remove('loading');
        }
    });
}

// Gestion du formulaire de contact
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Afficher l'état de chargement
        submitBtn.textContent = 'Envoi en cours...';
        submitBtn.disabled = true;
        this.classList.add('loading');
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch('php/contact.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('Message envoyé avec succès ! Nous vous répondrons bientôt.', 'success');
                this.reset();
            } else {
                showMessage(result.message || 'Erreur lors de l\'envoi du message. Veuillez réessayer.', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showMessage('Erreur de connexion. Veuillez réessayer plus tard.', 'error');
        } finally {
            // Restaurer l'état du bouton
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            this.classList.remove('loading');
        }
    });
}

// Fonction pour afficher les messages
function showMessage(message, type) {
    // Supprimer les messages existants
    const existingMessages = document.querySelectorAll('.message');
    existingMessages.forEach(msg => msg.remove());
    
    // Créer le nouveau message
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    // Insérer le message au début du formulaire
    const form = type === 'success' ? inscriptionForm : contactForm;
    if (form) {
        form.insertBefore(messageDiv, form.firstChild);
        
        // Supprimer le message après 5 secondes
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

// Validation en temps réel des formulaires
function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearFieldError);
        });
    });
}

function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    
    // Supprimer les erreurs existantes
    clearFieldError(e);
    
    // Validation selon le type de champ
    let isValid = true;
    let errorMessage = '';
    
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Ce champ est obligatoire.';
    } else if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Veuillez entrer une adresse email valide.';
        }
    } else if (field.type === 'tel' && value) {
        const phoneRegex = /^[0-9+\-\s()]+$/;
        if (!phoneRegex.test(value) || value.length < 10) {
            isValid = false;
            errorMessage = 'Veuillez entrer un numéro de téléphone valide.';
        }
    } else if (field.type === 'date' && value) {
        const birthDate = new Date(value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        
        if (age < 4 || age > 80) {
            isValid = false;
            errorMessage = 'L\'âge doit être entre 4 et 80 ans.';
        }
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    const formGroup = field.closest('.form-group');
    const existingError = formGroup.querySelector('.field-error');
    
    if (existingError) {
        existingError.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.textContent = message;
    
    formGroup.appendChild(errorDiv);
    field.style.borderColor = '#dc3545';
}

function clearFieldError(e) {
    const field = e.target;
    const formGroup = field.closest('.form-group');
    const existingError = formGroup.querySelector('.field-error');
    
    if (existingError) {
        existingError.remove();
    }
    
    field.style.borderColor = '#e9ecef';
}

// Calcul automatique de l'équipe selon l'âge
const dateNaissanceInput = document.getElementById('date_naissance');
const equipeSelect = document.getElementById('equipe');

if (dateNaissanceInput && equipeSelect) {
    dateNaissanceInput.addEventListener('change', function() {
        const birthDate = new Date(this.value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        
        // Calculer l'équipe recommandée
        let recommendedTeam = '';
        if (age >= 6 && age <= 9) {
            recommendedTeam = 'U8-U10';
        } else if (age >= 10 && age <= 13) {
            recommendedTeam = 'U12-U14';
        } else if (age >= 14 && age <= 17) {
            recommendedTeam = 'U16-U18';
        } else if (age >= 18) {
            recommendedTeam = 'Seniors';
        }
        
        // Mettre à jour la sélection si une équipe est recommandée
        if (recommendedTeam && !equipeSelect.value) {
            equipeSelect.value = recommendedTeam;
        }
    });
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    setupFormValidation();
    
    // Ajouter des effets de parallaxe subtils
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.hero');
        
        parallaxElements.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
    
    // Animation des statistiques
    const stats = document.querySelectorAll('.stat h3');
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    stats.forEach(stat => {
        statsObserver.observe(stat);
    });
});

// Animation des compteurs
function animateCounter(element) {
    const target = parseInt(element.textContent.replace(/\D/g, ''));
    const suffix = element.textContent.replace(/\d/g, '');
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current) + suffix;
    }, 30);
}

// Gestion des erreurs globales
window.addEventListener('error', function(e) {
    console.error('Erreur JavaScript:', e.error);
});

// Service Worker pour le cache (optionnel)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker enregistré avec succès');
            })
            .catch(function(error) {
                console.log('Échec de l\'enregistrement du ServiceWorker');
            });
    });
}



