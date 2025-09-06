// Ouvre une modal d'inscription par son ID
export function openSignupModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.showModal(); // Changement de show() à showModal()
    console.log('Modal ouverte:', modalId); // Debug
  } else {
    console.error('Modal non trouvée:', modalId); // Debug
  }
}

// Ouvre un onglet par son index
export function openTab(index) {
  const contents = document.querySelectorAll('[id^="content-"]');
  const tabs = document.querySelectorAll('[role="tab"]');

  // Masquer tous les contenus
  contents.forEach((content) => {
    if (!content.classList.contains('hidden')) {
      content.classList.add('opacity-0');
      setTimeout(() => content.classList.add('hidden'), 300);
    }
  });

  // Désactiver tous les onglets
  tabs.forEach((tab) => tab.classList.remove('tab-active'));

  // Activer l'onglet sélectionné
  if (tabs[index - 1]) {
    tabs[index - 1].classList.add('tab-active');
  }

  // Afficher le contenu sélectionné
  setTimeout(() => {
    const selectedContent = document.getElementById(`content-${index}`);
    if (selectedContent) {
      selectedContent.classList.remove('hidden');
      requestAnimationFrame(() => {
        selectedContent.classList.remove('opacity-0');
        selectedContent.classList.add('opacity-100');
      });
    }
  }, 300);
}

// Initialisation des modals au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('dialog.modal').forEach((modal) => {
    // Fermeture avec Escape
    modal.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') modal.close();
    });
    // Fermeture au clic en dehors
    modal.addEventListener('click', (event) => {
      if (event.target === modal) modal.close();
    });
  });
});
