const addTransferCode = (code) => {
  const offcanvas = document.querySelector('.offcanvas-body');
  const transferCode = document.createElement('p');
  transferCode.textContent = "Your transfer code is: " + code;
  offcanvas.appendChild(transferCode);
};

// First we register an event listener when the form is submitted.
const transferCodeForm = document.getElementById('tc-offcanvas');
transferCodeForm.addEventListener('submit', (event) => {

  event.preventDefault();

  const formData = new FormData(transferCodeForm);

  const data = Object.fromEntries(formData.entries());

  const user = data.user;
  const api_key = data.api_key;
  const amount = data.amount;

  console.log(Object.fromEntries(formData.entries()));

  fetch('https://www.yrgopelag.se/centralbank/withdraw', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ user, api_key, amount })},
    )

    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.error(data.error);
      } else {
        addTransferCode(data.transferCode);
        console.log(data.transferCode);
      }
    });
});

// When the page loads we fetch all vampires from the database and list them in
// our unordered list.
//  fetch('https://www.yrgopelag.se/centralbank/withdraw', {
//     method: 'POST',
//     headers: { 'Content-Type': 'application/json' },
//     body: JSON.stringify({ user, api_key, amount })
//   })
//   .then((response) => response.json())
//   .then((transferCodes) => {
//     transferCodes.forEach((transferCode) => addTransferCode(data.transferCode));
//   });