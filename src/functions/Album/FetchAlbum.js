document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("row-input");
  const incrementButton = document.getElementById("increment-button");
  const decrementButton = document.getElementById("decrement-button");
  const searchInput = document.getElementById("default-search");

  input.value = "10";

  incrementButton.addEventListener("click", function () {
    let value = parseInt(input.value, 10);
    value += 10;
    input.value = value;
    sendData();
  });

  decrementButton.addEventListener("click", function () {
    let value = parseInt(input.value, 10);
    value = Math.max(0, value - 10);
    input.value = value;
    sendData();
  });

  input.addEventListener("change", function () {
    let value = parseInt(this.value, 10);
    if (isNaN(value)) {
      value = 0;
    }
    this.value = Math.max(0, value);
    sendData();
  });


  searchInput.addEventListener("input", function () {
    sendData();
  });
});

function sendData() {
  const searchInput = document.getElementById("default-search").value;
  const rowInput = document.getElementById("row-input").value;

  $.ajax({
    url: "/fetchAlbum",
    type: "GET",
    data: {
      s: searchInput,
      query: rowInput,
    },
    success: function (response) {
      $("#albums-container").html(response);
    },
  });
}
