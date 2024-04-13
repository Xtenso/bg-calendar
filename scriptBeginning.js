//! This file is for functions which should be placed at the beginning of the page, before the HTML content is loaded.

/*Changing year from dropdown menu*/
function changeYear(selectElement) {
  const selectedYear = selectElement.value;
  // Redirecting to the current page with the year as a parameter
  window.location.href = "?year=" + selectedYear;
}

/*Accordion animation*/
function toggleAccordion(id) {
  let content = document.getElementById(id);
  if (content.style.maxHeight) {
    content.style.maxHeight = null;
  } else {
    content.style.maxHeight = content.scrollHeight + "px";
  }
}

/*Date conversion to Bulgarian*/
const months = [
  "Януари",
  "Февруари",
  "Март",
  "Април",
  "Май",
  "Юни",
  "Юли",
  "Август",
  "Септември",
  "Октомври",
  "Ноември",
  "Декември",
];
/*from string type date*/
function toBulgarianDateFormat(dateStr) {
  let date = new Date(dateStr); /*converting date string into date type var*/
  return `${date.getDate()} ${months[date.getMonth()]}`;
}
/*from date type var*/
function formatDate(date) {
  return `${date.getDate()} ${months[date.getMonth()]}`;
}
