
// Gestion des boutons de sélection de créneaux
function showSlots(index) { // Cacher tous les conteneurs de créneaux
  document
    .querySelectorAll('.slots-list')
    .forEach(function (slotList) {
      slotList
        .style
        .display = 'none';
    });
  // Afficher le conteneur correspondant au bouton sélectionné
  document
    .getElementById('slots_' + index)
    .style
    .display = 'block';
}
