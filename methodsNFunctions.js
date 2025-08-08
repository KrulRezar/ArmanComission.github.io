
  const items = document.querySelectorAll('.FAQ-container-item');

  items.forEach(item => {
    item.addEventListener('click', () => {
      // Toggle active state
      item.classList.toggle('active');
    });
  });

//Saves Message and Displays the message in a TextArea in that Page
document.getElementById("save-form").addEventListener("submit", function (e) {
  e.preventDefault(); // Stop the form from submitting
  //Saves the Inputted data in the message-box
  const message = document.getElementById("message-box").value;
  localStorage.setItem("userMessage", message);

  // Redirect to another page
  window.location.href = "contact.html";
});

//Function displayMessage 
const savedMessage = localStorage.getItem("userMessage");
  document.getElementById("message").value = savedMessage || "No message found.";