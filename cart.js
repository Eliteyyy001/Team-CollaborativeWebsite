//validate if quantity is greater than 0
function validateQuantity(input) {
  
//number the user typed in
  var value = input.value;
  
  if (value <= 0) {
    alert("Quantity must be greater than 0");
    input.value = 1;
  }
}

//validate if cart is not empty before checkout
function validateCheckout() {
  var cartBody = document.getElementById("cartBody");
  var message = document.getElementById("message");

  var rows = cartBody.getElementsByTagName("tr");
  var rowCount = rows.length;

  if (rowCount === 0) {
    message.textContent = "Your cart is empty. Add items before checking out.";
    return false; 
  }
  if (rowCount === 1) {
    var firstRow = rows[0];
    var rowText = firstRow.textContent;
    
    if (rowText.indexOf("empty") !== -1 || rowText.indexOf("Empty") !== -1) {
      message.textContent = "Your cart is empty. Add items before checking out.";
      return false;
    }
  }
  
  message.textContent = ""; 
  return true; // submit form
}
