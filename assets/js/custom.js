document.addEventListener('DOMContentLoaded', function () {
  console.log('custom.js loaded');

  const slotsLists = document.querySelectorAll('.slots-list');
  const slotsButtons = document.querySelectorAll('.slot-button');

  // Vérifiez si nous avons bien des listes et des boutons
  if (slotsLists.length === 0 || slotsButtons.length === 0) {
    console.warn('Aucun élément ".slots-list" ou ".slot-button" trouvé.');
    return;
  }

  // Masquez toutes les listes au départ
  slotsLists.forEach(function (slotsList) {
    slotsList.style.display = 'none';
  });

  // Affichez la première liste
  slotsLists[0].style.display = 'block';

  // Ajoutez les écouteurs aux boutons
  slotsButtons.forEach((slotButton, index) => {
    slotButton.addEventListener('click', function () {
      // Masquez toutes les listes
      slotsLists.forEach(function (slotsList) {
        slotsList.style.display = 'none';
      });

      // Affichez la liste correspondant au bouton cliqué
      if (slotsLists[index]) {
        slotsLists[index].style.display = 'block';
      } else {
        console.warn(`Aucune liste de créneaux trouvée pour l'index ${index}`);
      }
    });
  });
});
