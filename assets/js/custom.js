const slotsLists = document.querySelectorAll('.slots-list');
slotsLists.forEach(function (slotsList) {
  slotsList.style.display = 'none';
}
);

const slotsButtons = document.querySelectorAll('.slot-button');

slotsLists[0].style.display = 'block'; // show the first slots list

slotsButtons.forEach(
  (slotButton, index) => {
    slotButton.addEventListener('click', function () {
      slotsLists.forEach(function (slotsList) {
        slotsList.style.display = 'none';
      });
      slotsLists[index].style.display = 'block';
    });
  }
);

