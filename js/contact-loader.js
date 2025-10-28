/**
 * Script pour charger dynamiquement les informations de contact
 */

document.addEventListener('DOMContentLoaded', function() {
    loadContactInfo();
});

function loadContactInfo() {
    fetch('php/get_contact_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayContactInfo(data.data);
            } else {
                console.error('Erreur lors du chargement des contacts:', data.error);
                showFallbackContacts();
            }
        })
        .catch(error => {
            console.error('Erreur réseau:', error);
            showFallbackContacts();
        });
}

function displayContactInfo(contacts) {
    // Mettre à jour la section contact principale
    updateMainContactSection(contacts);
    
    // Mettre à jour le footer
    updateFooterContacts(contacts);
}

function updateMainContactSection(contacts) {
    const contactSection = document.querySelector('#contact .row');
    if (!contactSection) return;
    
    // Vider la section existante
    contactSection.innerHTML = '';
    
    // Afficher les contacts directs
    if (contacts.contact_direct && contacts.contact_direct.length > 0) {
        contacts.contact_direct.forEach(contact => {
            const contactCard = createContactCard(contact);
            contactSection.appendChild(contactCard);
        });
    }
    
    // Afficher la messagerie
    if (contacts.messagerie && contacts.messagerie.length > 0) {
        contacts.messagerie.forEach(contact => {
            const contactCard = createContactCard(contact);
            contactSection.appendChild(contactCard);
        });
    }
    
    // Afficher les réseaux sociaux
    if (contacts.reseaux_sociaux && contacts.reseaux_sociaux.length > 0) {
        contacts.reseaux_sociaux.forEach(contact => {
            const contactCard = createContactCard(contact);
            contactSection.appendChild(contactCard);
        });
    }
}

function createContactCard(contact) {
    const col = document.createElement('div');
    col.className = 'col-lg-3 col-md-6 mb-4';
    
    const card = document.createElement('div');
    card.className = 'card h-100 text-center';
    
    const iconClass = getSafeIconClass(contact.type_contact, contact.icone);
    const linkUrl = getContactLink(contact.type_contact, contact.valeur);
    
    // Gérer l'affichage selon le type de contact
    let displayValue = contact.valeur;
    let displayElement;
    
    if (contact.type_contact === 'adresse') {
        // Pour l'adresse, créer un lien Google Maps
        const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(contact.valeur)}`;
        displayElement = `<a href="${mapsUrl}" class="btn btn-outline-primary btn-sm" target="_blank" title="Voir sur Google Maps">${contact.valeur}</a>`;
    } else if (linkUrl) {
        // Pour les autres liens
        displayValue = contact.valeur.length > 25 ? 
            contact.valeur.substring(0, 22) + '...' : 
            contact.valeur;
        displayElement = `<a href="${linkUrl}" class="btn btn-outline-primary btn-sm" target="_blank" title="${contact.valeur}">${displayValue}</a>`;
    } else {
        // Pour les textes simples
        displayValue = contact.valeur.length > 30 ? 
            contact.valeur.substring(0, 27) + '...' : 
            contact.valeur;
        displayElement = `<span class="text-muted small" style="word-wrap: break-word; line-height: 1.4;">${displayValue}</span>`;
    }
    
    card.innerHTML = `
        <div class="card-body d-flex flex-column">
            <div class="contact-icon-container mb-3">
                <i class="${iconClass}" style="color: ${contact.couleur || '#1e3a8a'}; font-size: 2.5rem;"></i>
            </div>
            <h5 class="card-title">${contact.libelle}</h5>
            <p class="card-text flex-grow-1" style="min-height: 40px; overflow: visible;">${contact.description || ''}</p>
            <div class="mt-auto">
                ${displayElement}
            </div>
        </div>
    `;
    
    col.appendChild(card);
    return col;
}

function getIconClass(type, customIcon) {
    if (customIcon) {
        // Déterminer si c'est une icône de marque ou standard
        const brandIcons = ['whatsapp', 'telegram', 'facebook', 'instagram', 'twitter', 'youtube', 'tiktok', 'linkedin'];
        const iconPrefix = brandIcons.includes(customIcon) ? 'fab' : 'fas';
        return `${iconPrefix} fa-${customIcon}`;
    }
    
    const iconMap = {
        'telephone': 'fas fa-phone',
        'email': 'fas fa-envelope',
        'adresse': 'fas fa-map-marker-alt',
        'site_web': 'fas fa-globe',
        'whatsapp': 'fab fa-whatsapp',
        'telegram': 'fab fa-telegram-plane',
        'facebook': 'fab fa-facebook-f',
        'instagram': 'fab fa-instagram',
        'twitter': 'fab fa-twitter',
        'youtube': 'fab fa-youtube',
        'tiktok': 'fab fa-tiktok',
        'linkedin': 'fab fa-linkedin-in'
    };
    
    return iconMap[type] || 'fas fa-info-circle';
}

// Fonction pour vérifier si une icône existe et utiliser un fallback
function getSafeIconClass(type, customIcon) {
    const iconClass = getIconClass(type, customIcon);
    
    // Fallbacks pour les icônes qui pourraient ne pas exister
    const fallbacks = {
        'fab fa-tiktok': 'fas fa-music',
        'fab fa-telegram-plane': 'fas fa-paper-plane',
        'fab fa-facebook-f': 'fas fa-facebook',
        'fab fa-linkedin-in': 'fas fa-linkedin'
    };
    
    return fallbacks[iconClass] || iconClass;
}

function getContactLink(type, value) {
    switch (type) {
        case 'email':
            return `mailto:${value}`;
        case 'telephone':
            return `tel:${value}`;
        case 'whatsapp':
            return `https://wa.me/${value.replace(/[^0-9]/g, '')}`;
        case 'telegram':
            return `https://t.me/${value.replace('@', '')}`;
        case 'facebook':
        case 'instagram':
        case 'twitter':
        case 'youtube':
        case 'linkedin':
        case 'site_web':
            return value.startsWith('http') ? value : `https://${value}`;
        case 'tiktok':
            return `https://tiktok.com/@${value.replace('@', '')}`;
        default:
            return null;
    }
}

function updateFooterContacts(contacts) {
    const footerContact = document.querySelector('footer .col-lg-4:last-child');
    if (!footerContact) return;
    
    // Trouver les contacts principaux pour le footer
    const phone = contacts.contact_direct?.find(c => c.type_contact === 'telephone');
    const email = contacts.contact_direct?.find(c => c.type_contact === 'email');
    const address = contacts.contact_direct?.find(c => c.type_contact === 'adresse');
    
    if (phone || email || address) {
        const contactHtml = `
            <h6 class="text-white mb-3">Contact</h6>
            ${phone ? `<p><i class="fas fa-phone me-2"></i> ${phone.valeur}</p>` : ''}
            ${email ? `<p><i class="fas fa-envelope me-2"></i> ${email.valeur}</p>` : ''}
            ${address ? `<p><i class="fas fa-map-marker-alt me-2"></i> ${address.valeur}</p>` : ''}
        `;
        
        const existingContact = footerContact.querySelector('h6');
        if (existingContact) {
            existingContact.parentElement.innerHTML = contactHtml;
        }
    }
}

function showFallbackContacts() {
    // Contacts de secours si la base de données n'est pas accessible
    console.log('Affichage des contacts de secours');
}
