// Custome URL JS

// Upload image
document.getElementById('iconQueue').addEventListener('change', function (event) {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      document.querySelector('.iconQueueImg').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
});

const datePickerIds = ['datepicker', 'Enddatepicker']; // IDs of date picker input fields
const timePickerIds = ['timePicker', 'EndtimePicker']; // IDs of time picker input fields

let language = [];

// Tabs Js
var MultistepForm = {
  currentStep: 0,
  totalSteps: 4,

  prevStep: function () {
    if (this.currentStep > 0) {
      this.currentStep--;
      this.updateSteps();
    }
  },

  nextStep: function () {
    if (this.currentStep < this.totalSteps - 1) {
      this.currentStep++;
      this.updateSteps();
    } else {
    }
  },

  updateSteps: function () {
    const tabs = document.querySelectorAll('.nav-links');
    tabs.forEach(tab => tab.classList.remove('active'));
    tabs[this.currentStep].classList.add('active');

    const tabPanes = document.querySelectorAll('.tab-pane');
    tabPanes.forEach(pane => pane.classList.remove('show', 'active'));
    tabPanes[this.currentStep].classList.add('show', 'active');
  }
};



// Advance Setting
$(document).ready(function () {
  // Check if the checkbox is checked on page load
  if ($('#AdvanceSettingCheckBox').is(':checked')) {
    $('.AdvanceSettingBox').slideDown(); // Slide down if checked
  } else {
    $('.AdvanceSettingBox').slideUp(); // Otherwise, slide up
  }

  // Change event handler for the checkbox
  $('#AdvanceSettingCheckBox').change(function () {
    if ($(this).is(':checked')) {
      $('.AdvanceSettingBox').slideDown(); // Slide down if checked
    } else {
      $('.AdvanceSettingBox').slideUp(); // Otherwise, slide up
    }
  });
});



// const addButton = document.getElementById('addButton');
// const table = document.querySelector('#AdvanceSettingTable2 tbody');
// let rowCount = 0; // Counter for rows

// function updateInputData() {
//   const rowsData = Array.from(table.querySelectorAll('tr')).map(row => {
//     const cells = row.querySelectorAll('td select, td input');
//     return Array.from(cells).map(cell => cell.value);
//   });
//   document.getElementById('advancedata').value = JSON.stringify(rowsData);
// }

// function insertNewRow() {
//   rowCount++; // Increment row counter
//   const newRow = document.createElement('tr');

//   // Generate unique ID for the new row
//   const rowId = `row_${rowCount}`;
//   newRow.id = rowId;

//   // Create columns for the new row
//   const newCell1 = document.createElement('td');
//   const newCell2 = document.createElement('td');
//   const newCell3 = document.createElement('td');
//   const newCell4 = document.createElement('td');
//   const newCell5 = document.createElement('td'); // Cell for delete button
//   const newCell6 = document.createElement('td'); // Cell for plus button

//   // Select Fields
//   const select1 = document.createElement('select');
//   select1.classList.add('form-select', 'form-control', 'FormInputBox');
//   select1.innerHTML = '<option value="AND">AND</option><option value="OR">OR</option>';
//   select1.setAttribute('name', 'advancedata[operator][]'); 
//   newCell1.appendChild(select1);

//   const select2 = document.createElement('select');
//   select2.classList.add('form-select', 'form-control', 'FormInputBox');
//   select2.innerHTML = '<option value="HOST_NAME">HOST NAME</option><option value="PAGE_PATH">PAGE PATH</option><option value="PAGE_URL">PAGE URL</option>';
//   select2.setAttribute('name', 'advancedata[condition_place][]'); 
//   newCell2.appendChild(select2);

//   const select3 = document.createElement('select');
//   select3.classList.add('form-select', 'form-control', 'FormInputBox');
//   select3.innerHTML = '<option value="CONTAINS">CONTAINS</option><option value="DOES_NOT_CONTAIN">DOES NOT CONTAIN</option><option value="EQUALS">EQUALS</option><option value="DOES_NOT_EQUAL">DOES NOT EQUAL</option>';
//   select3.setAttribute('name', 'advancedata[condition][]'); 
//   newCell3.appendChild(select3);

//   // Delete button
//   const deleteButton = document.createElement('button');
//   deleteButton.innerHTML = '<span class="material-symbols-outlined">delete</span>';
//   deleteButton.classList.add('DeleteTableRow');
//   deleteButton.addEventListener('click', () => {
//     table.removeChild(newRow); // Remove the row when delete button is clicked
//     updateInputData(); // Update advancedata input field after deletion
//     showPlusButton();
//   });
//   newCell5.appendChild(deleteButton);

//   // Input Field
//   const newInput4 = document.createElement('input');
//   newInput4.type = 'text';
//   newInput4.classList.add('form-control', 'FormInputBox');
//   newInput4.setAttribute('name', 'advancedata[value][]'); 
//   newCell4.appendChild(newInput4);

//   // Plus button
//   const plusButton = document.createElement('button');
//   plusButton.innerHTML = '<span class="material-symbols-outlined">add</span>';
//   plusButton.classList.add('AddTableRow');
//   plusButton.type = 'button'; // Specify type as button
//   plusButton.addEventListener('click', insertNewRow); // Attach event listener to create a new row on click
//   newCell6.appendChild(plusButton);

//   // Hide all plus buttons except in the last row
//   const plusButtons = table.querySelectorAll('td:last-child button');
//   plusButtons.forEach(button => {
//     button.style.display = 'none';
//   });

//   // Append columns to the row
//   newRow.appendChild(newCell1);
//   newRow.appendChild(newCell2);
//   newRow.appendChild(newCell3);
//   newRow.appendChild(newCell4);
//   newRow.appendChild(newCell5); // Append delete button column
//   newRow.appendChild(newCell6); // Append plus button column

//   // Append row to the table
//   table.appendChild(newRow);

//   // Update advancedata input field after insertion
//   updateInputData();
//   showPlusButton();
// }
// function showPlusButton() {
//   const allPlusButtons = table.querySelectorAll('.AddTableRow');
//   allPlusButtons.forEach(button => {
//     button.style.display = 'none';
//   });
//   // Show plus button in the last row
//   const lastRowPlusButtons = table.lastElementChild.querySelector('.AddTableRow');
//   if (lastRowPlusButtons) {
//     lastRowPlusButtons.style.display = 'inline-block';
//   }
// }


// addButton.addEventListener('click', insertNewRow);


// date Piker like: 01 jan 2024 js
function showDatePicker() {
  // Set the date format
  const dateFormat = { day: 'numeric', month: 'long', year: 'numeric' };

  const currentDate = new Date();

  // Loop through each date picker ID
  datePickerIds.forEach(datePickerId => {
    const inputField = document.getElementById(datePickerId);

    // Create a new Pikaday date picker
    const picker = new Pikaday({
      field: inputField,
      format: 'DD MMMM YYYY',
      yearRange: [1900, new Date().getFullYear()],
      showYearDropdown: true,
      i18n: {
        previousMonth: 'Previous Month',
        nextMonth: 'Next Month',
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        weekdays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
      },
      minDate: currentDate
    });

    // Set the default date to today
    picker.setDate(new Date());
  });

  // Loop through each time picker ID

  timePickerIds.forEach(timePickerId => {
    const timePicker = document.getElementById(timePickerId);

    // Get the current time
    const currentTime = new Date();

    // Format the current time as HH:MM (24-hour format)
    const formattedHours = ('0' + currentTime.getHours()).slice(-2);
    const formattedMinutes = ('0' + currentTime.getMinutes()).slice(-2);
    const formattedTime = formattedHours + ':' + formattedMinutes;

    // Set the formatted time as the value of the time picker input field
    timePicker.value = formattedTime;
  });
}
// Call the function when the page loads
document.addEventListener('DOMContentLoaded', showDatePicker);




// start redio timeZone

document.addEventListener("DOMContentLoaded", function () {
  // Add event listener to the radio button
  document.getElementById("Startnow").addEventListener("click", function () {
    // Fetch system date and time
    var currentDateTime = new Date();
    // Extract the date portion
    var dateString = currentDateTime.toISOString().split('T')[0];
    // Extract the time portion
    var timeString = currentDateTime.toTimeString().split(' ')[0];
    // Set the date and time as the value of the respective hidden input fields
    document.getElementById("dateValue").value = dateString;
    //showDatePicker();
    //console.log(timeString) ;
    document.getElementById("timeValue").value = timeString;
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // Add event listener to the radio button
  document.getElementById("QueuingEnds").addEventListener("click", function () {
    // Fetch current system date and time
    var currentDateTime = new Date();
    // Extract the date portion
    var dateString = currentDateTime.toISOString().split('T')[0];
    // Extract the time portion
    var timeString = currentDateTime.toTimeString().split(' ')[0];
    // Set the date and time as the value of the respective hidden input fields
    document.getElementById("endDate").value = dateString;
    document.getElementById("endTime").value = timeString;
  });
});


// Custom date and time



document.querySelectorAll('input[name="endTime"]').forEach(function (radio) {
  radio.addEventListener('change', function () {
    if (this.value === '0') {
      document.getElementById('EndCustomDateTimeId').style.display = 'block';
    } else {
      document.getElementById('EndCustomDateTimeId').style.display = 'none';
    }
  });
});

let languageDesignTemp = []; // Array for language in design-temp form

function addDynamicSelect(selectedValue, setAsDefault, selectedText, formIdentifier) {
  // Create a new row
  var newRow = document.createElement('div');
  newRow.className = 'row mb-3';

  // Create the column for the new input field
  var inputColumn = document.createElement('div');
  inputColumn.className = 'col-md-5';
  var newInput = document.createElement('div');
  newInput.className = 'form-control selected_input';
  newInput.textContent = selectedText; // Set the text content of the div
  newInput.setAttribute('data-value', selectedValue); // Set the data-value attribute
  newInput.contentEditable = 'false'; // Make the div non-editable

  // Append the new input field to the column
  inputColumn.appendChild(newInput);
  newRow.appendChild(inputColumn);

  // Create the column for the form controls (radio button and delete button)
  var formControlsColumn = document.createElement('div');
  formControlsColumn.className = 'col-md-7';
  var formControlsWrapper = document.createElement('div');
  formControlsWrapper.className = 'd-flex align-items-center';
  // Radio button
  var radioButtonDiv = document.createElement('div');
  radioButtonDiv.className = 'form-check';
  var radioButtonInput = document.createElement('input');
  radioButtonInput.className = 'form-check-input';
  radioButtonInput.type = 'radio';
  radioButtonInput.name = formIdentifier + '-setDefault'; // Ensure the radio buttons are grouped
  radioButtonInput.value = setAsDefault;
  radioButtonInput.id = 'flexRadioDefault';
  if (setAsDefault) {
    radioButtonInput.checked = true;
  }
  var radioButtonLabel = document.createElement('label');
  radioButtonLabel.className = 'form-check-label mt-1 dynamic_checkbox';
  radioButtonLabel.htmlFor = 'flexRadioDefault';
  radioButtonLabel.textContent = 'Set as default';
  radioButtonDiv.appendChild(radioButtonInput);
  radioButtonDiv.appendChild(radioButtonLabel);
  formControlsWrapper.appendChild(radioButtonDiv);
  // Delete button
  var deleteButton = document.createElement('button');
  deleteButton.className = 'QueueDeleteBtn ms-4 mt-2';
  deleteButton.type = 'button';
  var deleteIcon = document.createElement('span');
  deleteIcon.className = 'material-symbols-outlined';
  deleteIcon.textContent = 'delete';
  deleteButton.appendChild(deleteIcon);
  // Add event listener to delete button
  deleteButton.addEventListener('click', function () {
    let selectedValue = newInput.getAttribute('data-value');
    console.log(selectedValue);
    let index = languageDesignTemp.indexOf(selectedValue);
    if (index !== -1) {
      languageDesignTemp.splice(index, 1); // Remove one element at the found index
      document.getElementById('jsonlangDesignTemp').value = JSON.stringify(languageDesignTemp);
    }

    newRow.remove(); // Remove the entire row when delete button is clicked
  });
  formControlsWrapper.appendChild(deleteButton);
  formControlsColumn.appendChild(formControlsWrapper);
  newRow.appendChild(formControlsColumn);

  // Append the new row to the dynamicSelects container
  var dynamicSelects = document.getElementById('dynamicSelectsDesignTemp');
  dynamicSelects.appendChild(newRow);

  // Reset the mainSelect to its default option
  var mainSelect = document.getElementById('mainSelectDesignTemp');
  mainSelect.selectedIndex = 0; // Select the first option

  document.getElementById('jsonlangDesignTemp').value = JSON.stringify(languageDesignTemp);
}

document.getElementById('mainSelectDesignTemp').addEventListener('change', function () {
  // Get the index of the selected option
  var selectedIndex = this.selectedIndex;

  // Get the selected option element
  var selectedOption = this.options[selectedIndex];

  // Get the text content of the selected option
  var selectedText = selectedOption.textContent;

  // Check if the selected value is not already in the language array
  var selectedValue = selectedOption.value;
  if (!languageDesignTemp.includes(selectedValue)) {
    languageDesignTemp.push(selectedValue); // Add the selected value to the array first

    addDynamicSelect(selectedValue, selectedValue, selectedText, 'design-temp'); // Then add it to the DOM
  }

  // Update the hidden input field value after updating the language array
  document.getElementById('jsonlangDesignTemp').value = JSON.stringify(languageDesignTemp);
});



function convertToJson() {
  var tableData = [];

  // Iterate over each row
  $('#AdvanceSettingTable tbody tr').each(function (row, tr) {
    // Get the cells (td) in the current row
    var tds = $(tr).find('td');

    // Initialize an array for the current row
    var rowData = [];

    // Iterate over the first four cells in the row
    tds.slice(0, 4).each(function (column, td) {
      // Push the value of the cell to the array for the current row
      rowData.push($(td).find('select, input').val());
    });

    // Push the data of the current row to the tableData array
    tableData.push(rowData);
  });

  // Transpose the array
  var transposedData = [];
  for (var i = 0; i < tableData[0].length; i++) {
    transposedData[i] = [];
    for (var j = 0; j < tableData.length; j++) {
      transposedData[i][j] = tableData[j][i];
    }
  }
  var transposedJson = JSON.stringify(transposedData);

  // Log the transposed JSON string
  // console.log(transposedJson);
  // Log the transposed data
  document.getElementById('advancedata').value = transposedJson;
}

$('#AdvanceSettingTable').on('click keyup', 'select, input ,td', function () {
  //console.log("cc");
  convertToJson();
});
