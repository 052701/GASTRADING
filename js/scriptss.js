document.addEventListener("DOMContentLoaded", function () {
  const orderItems = document.getElementById("orderItems");
  const grandTotalDisplay = document.getElementById("grandTotal");

  function calculateTotal(item) {
    const gasType = item.querySelector(".gasType");
    const capacity = item.querySelector(".capacity");
    const quantity = item.querySelector(".quantity");
    const priceDisplay = item.querySelector(".price");
    const amountDisplay = item.querySelector(".amount");

    const gasPrice =
      parseFloat(
        gasType.options[gasType.selectedIndex]?.getAttribute("data-price")
      ) || 0;
    const capacityPrice =
      parseFloat(
        capacity.options[capacity.selectedIndex]?.getAttribute("data-price")
      ) || 0;
    const qty = parseInt(quantity.value) || 0;

    const totalPrice = gasPrice + capacityPrice;
    const totalAmount = totalPrice * qty;

    priceDisplay.value = totalPrice.toFixed(2);
    amountDisplay.value = totalAmount.toFixed(2);

    updateGrandTotal();
  }

  function updateGrandTotal() {
    let grandTotal = 0;
    const amounts = document.querySelectorAll(".amount");
    amounts.forEach((amount) => {
      grandTotal += parseFloat(amount.value) || 0;
    });
    grandTotalDisplay.textContent = grandTotal.toFixed(2);
  }

  function addOrderItem() {
    const newItem = document.querySelector(".order-item").cloneNode(true);
    newItem.querySelectorAll("input").forEach((input) => (input.value = "")); // Clear input values
    newItem.querySelector(".remove-item").style.display = "block";

    newItem
      .querySelector(".gasType")
      .addEventListener("change", () => calculateTotal(newItem));
    newItem
      .querySelector(".capacity")
      .addEventListener("change", () => calculateTotal(newItem));
    newItem
      .querySelector(".quantity")
      .addEventListener("input", () => calculateTotal(newItem));
    newItem.querySelector(".remove-item").addEventListener("click", () => {
      newItem.remove();
      updateGrandTotal();
    });

    orderItems.appendChild(newItem);
  }

  // Initial event listeners for the first item
  const firstItem = document.querySelector(".order-item");
  firstItem
    .querySelector(".gasType")
    .addEventListener("change", () => calculateTotal(firstItem));
  firstItem
    .querySelector(".capacity")
    .addEventListener("change", () => calculateTotal(firstItem));
  firstItem
    .querySelector(".quantity")
    .addEventListener("input", () => calculateTotal(firstItem));

  document.getElementById("addItem").addEventListener("click", addOrderItem);
});
