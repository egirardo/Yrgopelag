// 1. Get the select element by its ID
const roomPicker = document.getElementById("room_picker");

// 2. Define the function to handle the change event
function handleSelectChange(event) {
  // The new value can be accessed via event.target.value
  const currentValue = event.target.value;
  
  // Example: Log the value to the console or update the DOM
  console.log("the current room is: " + currentValue);


  // Optional: Update a results paragraph
//   document.getElementById("result").textContent = currentValue;
}

// 3. Add the 'change' event listener to the select element
roomPicker.addEventListener("change", handleSelectChange);