// 1. Get the select element by its ID
const roomPickerTotal = document.getElementById("room_picker");

// 2. Define the function to handle the change event
function handleSelectChange(event) {
  // The new value can be accessed via event.target.value
  const currentValue = event.target.value;
  
  // Example: Log the value to the console or update the DOM
  console.log("Dropdown value changed to: " + currentValue);

  // Optional: Update a results paragraph
  const total = document.createElement('p');
  total.classList.add("prelim-total-amount");

  total.textContent = "$" + currentValue;

  const prelimTotal = document.getElementById("prelim-total");

  prelimTotal.appendChild((total));
}

// 3. Add the 'change' event listener to the select element
roomPickerTotal.addEventListener("change", handleSelectChange);