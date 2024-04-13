//! This is file is for JS functions which interact with the HTML content and should be placed at the end of the page, after the HTML content is loaded.

//! Home Page
/*Month changing*/
document.addEventListener("DOMContentLoaded", function () {
  const prevMonthBtn = document.getElementById("prev-month");
  const nextMonthBtn = document.getElementById("next-month");
  prevMonthBtn.addEventListener("click", function () {
    navigate(-1);
  });
  nextMonthBtn.addEventListener("click", function () {
    navigate(1);
  });
  function navigate(direction) {
    // Get the current month value from the hidden input element
    const currentMonth = parseInt(
      document.getElementById("currentMonth").value,
      10
    );
    // Calculate the new month
    let newMonth = currentMonth + direction;
    // Adjust newMonth if necessary
    if (newMonth < 1) {
      newMonth = 12;
    } else if (newMonth > 12) {
      newMonth = 1;
    }
    // Construct the new URL with the updated month
    const newUrl = `index.php?month=${newMonth}`;
    // Redirect to the new URL
    window.location.href = newUrl;
  }
});

//! Holidays page
/*Checkbox filters*/
function filterHolidays() {
  console.log("Function executed"); /*to check if function is being executed*/
  let showOfficial = document.getElementById("officialCheckbox").checked;
  let showSchool = document.getElementById("schoolCheckbox").checked;

  let officialHolidays = document.querySelectorAll(".date.official");
  let schoolHolidays = document.querySelectorAll(".date.school");

  officialHolidays.forEach((holiday) => {
    if (showOfficial) {
      holiday.style.backgroundColor = "#B30000";
    } else {
      holiday.style.backgroundColor = "transparent";
    }
  });

  schoolHolidays.forEach((holiday) => {
    if (showSchool) {
      holiday.style.backgroundColor = "#E6AD00";
    } else {
      holiday.style.backgroundColor = "transparent";
    }
  });
}

//! Name days page
if (
  window.location.pathname === "/nameDays.php" ||
  window.location.pathname.endsWith("/nameDays.php")
) {
  //Executes on window startup
  window.onload = function () {
    const dateElements = document.querySelectorAll(".date-to-format");
    dateElements.forEach((element) => {
      let dateStr = element.getAttribute("data-date");
      element.textContent = toBulgarianDateFormat(dateStr);
    });
    // Start page with a clean search bar
    document.getElementById("nameSearch").value = "";
  };

  // Function to search names
  function searchNames(searchTerm) {
    const results = allNames.filter((name) =>
      name.toLowerCase().startsWith(searchTerm.toLowerCase())
    );
    displayResults(results);
  }

  // Event listener for input changes in the search input
  document.getElementById("nameSearch").addEventListener("input", function () {
    const searchTerm = this.value.trim();
    if (searchTerm === "") {
      document.getElementById("searchResults").innerHTML = "";
      document.getElementById("searchResults").style.display = "none";
      return;
    }
    searchNames(searchTerm);
  });

  // Function to display search results
  function displayResults(results) {
    const resultsContainer = document.getElementById("searchResults");
    resultsContainer.innerHTML = "";

    if (results.length > 0) {
      // Show the results container
      resultsContainer.style.display = "block";

      results.forEach((result) => {
        const listItem = document.createElement("p");
        listItem.textContent = result;

        // Adding the click event to the list item
        listItem.addEventListener("click", () => {
          // Construct the URL with query parameter for the name
          const url = `nameDayDetails.php?name=${result}`;
          // Redirect to the details page
          window.location.href = url;
        });

        resultsContainer.appendChild(listItem);
      });
    } else {
      // Hide the results container if there are no search results
      resultsContainer.style.display = "none";
    }
  }

  // Event listener to hide search results when clicking outside the results container
  document.body.addEventListener("click", function (event) {
    const searchResultsContainer = document.getElementById("searchResults");
    // Check if the click target is outside the search results container
    if (!searchResultsContainer.contains(event.target)) {
      document.getElementById("searchResults").style.display = "none";
    }
  });
}

// JavaScript function to load data when a date on the calendar is clicked
function loadData(element, date) {
  // Scroll to the element with id "nameDayClicked"
  document.getElementById("scrollToHere").scrollIntoView({
    behavior: "smooth", // Optional: for smooth scrolling
  });
  // Get the name day information array from allNameDayData
  var nameDays = allNameDayData[date];
  // Populate the hidden div with the fetched data
  var hiddenDiv = document.getElementById("nameDayClickedContainer");
  hiddenDiv.innerHTML = "";
  // Loop through name days and populate the hidden div
  for (var i = 0; i < nameDays.length; i++) {
    var nameDay = nameDays[i];
    var nameDayDate = toBulgarianDateFormat(nameDay.date);
    var nameDayContent = `
      <div class="nameDayClicked">
        <h3>${nameDay.name} - ${nameDayDate}</h3>
        <p>${nameDay.list_names}</p>
      </div>
      `;
    hiddenDiv.innerHTML += nameDayContent;
  }
  // Display the hidden div
  hiddenDiv.style.display = "flex";
}

//! Footer
//Report form popup
// Function to open the modal
function openModal() {
  document.getElementById("modalOverlay").style.display = "block";
}
// Function to close the modal
function closeModal() {
  document.getElementById("modalOverlay").style.display = "none";
}

// Event listeners for buttons
document.getElementById("reportButton").addEventListener("click", openModal);
document.getElementById("closeButton").addEventListener("click", closeModal);

// Get the URL of the current page using JavaScript
var currentUrl = window.location.href;
// Set the value of the hidden input field
document.getElementById("currentUrlInput").value = currentUrl;

//! Header
// Start confirmation message animation after 9 seconds
setTimeout(function () {
  var confirmationMessage = document.getElementById("confirmationMessage");
  var mainBody = document.getElementById("mainBody");
  if (
    confirmationMessage &&
    window.getComputedStyle(confirmationMessage).display !== "none"
  ) {
    confirmationMessage.classList.add("slide-up");
    mainBody.classList.add("slide-up-body");
  }
}, 9000);

//! Responsive design
//Hamburger menu for devices with smaller screens
const hamburger = document.querySelector(".hamburger-menu");
const menu = document.querySelector("nav ul");
hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("active");
  menu.classList.toggle("active");
});
