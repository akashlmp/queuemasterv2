// Tabs Js
var MultistepForm = {
    currentStep: 0,
    totalSteps: 3,
  
    prevStep: function() {
      if (this.currentStep > 0) {
        this.currentStep--;
        this.updateSteps();
      }
    },
  
    nextStep: function() {
      if (this.currentStep < this.totalSteps - 1) {
        this.currentStep++;
        this.updateSteps();
      } else {
      }
    },
  
    updateSteps: function() {
      const tabs = document.querySelectorAll('.nav-links');
      tabs.forEach(tab => tab.classList.remove('active'));
      tabs[this.currentStep].classList.add('active');
  
      const tabPanes = document.querySelectorAll('.tab-pane');
      tabPanes.forEach(pane => pane.classList.remove('show', 'active'));
      tabPanes[this.currentStep].classList.add('show', 'active');
    }
  };
  
  
  
  // Advance Setting
  $(document).ready(function(){
    $('#InOutAdvanceSettingCheckBox').change(function(){
        if($(this).is(':checked')){
            $('.AdvanceSettingBox').slideDown(); // Show the AdvanceSettingBox with slide down effect
        } else {
            $('.AdvanceSettingBox').slideUp(); // Hide the AdvanceSettingBox with slide up effect
        }
    });
  });
  
  // Add Row
  const addButton = document.getElementById('InOutaddButton');
  const table = document.getElementById('InOutAdvanceSettingTable');
  let rowCount = 0; // Counter for rows
  
  function insertNewRow() {
    rowCount++; // Increment row counter
    const newRow = document.createElement('tr');
  
    // Generate unique ID for the new row
    const rowId = `row_${rowCount}`;
    newRow.id = rowId;
  
    // Create columns for the new row
    const newCell1 = document.createElement('td');
    const newCell2 = document.createElement('td');
    const newCell3 = document.createElement('td');
    const newCell4 = document.createElement('td');
    const newCell5 = document.createElement('td'); // Cell for delete button
    const newCell6 = document.createElement('td'); // Cell for plus button
  
    // Select Fields
    const select1 = document.createElement('select');
    select1.classList.add('form-select', 'form-control', 'FormInputBox'); 
    select1.innerHTML = '<option>AND</option><option>OR</option>';
    newCell1.appendChild(select1);
  
    const select2 = document.createElement('select');
    select2.classList.add('form-select', 'form-control', 'FormInputBox'); 
    select2.innerHTML = '<option>Page Path</option>';
    newCell2.appendChild(select2);
  
    const select3 = document.createElement('select');
    select3.classList.add('form-select', 'form-control', 'FormInputBox'); 
    select3.innerHTML = '<option>Contains</option>';
    newCell3.appendChild(select3);
  
    // Delete button
    const deleteButton = document.createElement('button');
    deleteButton.innerHTML  = '<span class="material-symbols-outlined">delete</span>';
    deleteButton.classList.add('DeleteTableRow');
    deleteButton.addEventListener('click', () => {
      table.removeChild(newRow); // Remove the row when delete button is clicked
      showPlusButton(); // Show plus button in the last row after deletion
    });
    newCell5.appendChild(deleteButton);
    
    // Input Field
    const newInput4 = document.createElement('input');
    newInput4.type = 'text';
    newInput4.classList.add('form-control', 'FormInputBox'); 
    newInput4.placeholder = 'registration';
    newCell4.appendChild(newInput4);
  
    
  
    // Plus button
    const plusButton = document.createElement('button');
    plusButton.innerHTML  = '<span class="material-symbols-outlined">add</span>';
    plusButton.classList.add('AddTableRow');
    plusButton.type = 'button'; // Specify type as button
    plusButton.addEventListener('click', insertNewRow); // Attach event listener to create a new row on click
    newCell6.appendChild(plusButton);
  
    // Hide all plus buttons except in the last row
    const plusButtons = table.querySelectorAll('td:last-child button');
    plusButtons.forEach(button => {
      button.style.display = 'none';
    });
  
    // Append columns to the row
    newRow.appendChild(newCell1);
    newRow.appendChild(newCell2);
    newRow.appendChild(newCell3);
    newRow.appendChild(newCell4);
    newRow.appendChild(newCell5); // Append delete button column
    newRow.appendChild(newCell6); // Append plus button column
  
    // Append row to the table
    table.appendChild(newRow);
  
    // Show plus button only in the last row
    showPlusButton();
  }
  
  // Function to show plus button in the last row
  function showPlusButton() {
    // Hide all plus buttons except in the last row
    const plusButtons = table.querySelectorAll('td:last-child button');
    plusButtons.forEach(button => {
      button.style.display = 'none';
    });
  
    // Show plus button only in the last row
    const lastRowPlusButtons = table.lastElementChild.querySelectorAll('td:last-child button');
    lastRowPlusButtons.forEach(button => {
      button.style.display = 'inline-block';
    });
  }
  
  addButton.addEventListener('click', insertNewRow);
  
  
  // Time Zone js
  
  // Function to populate the select dropdown with all world time zones
  function populateTimezones() {
    const select = document.getElementById("timezone");
    
    // Fetch all time zones
    const timeZones = moment.tz.names();
    
    // Iterate through time zones and populate options
    timeZones.forEach(timeZone => {
      const offset = moment.tz(timeZone).format('Z');
      const optionText = `(UTC${offset}) GMT`;
      
      const option = document.createElement("option");
      option.value = timeZone;
      option.innerHTML = `${optionText}`;
      select.appendChild(option);
    });
  }
  
  // Call the function to populate the dropdown when the page loads
  window.onload = populateTimezones;
  
  
  // date Piker like: 01 jan 2024 js
  function showDatePicker() {
    // Set the date format
    const dateFormat = {day: 'numeric', month: 'long', year: 'numeric'};
    // Get the input field and create a new date picker
    const inputField = document.getElementById('datepicker');
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
        }
    });
    // Set the default date to today
    picker.setDate(new Date());
    // Event listener to update the input field when the date is changed
    picker.on('change', function(date) {
        inputField.value = date.toLocaleDateString('en-GB', dateFormat);
    });
  }
  // Call the function when the page loads
  document.addEventListener('DOMContentLoaded', showDatePicker);
  
  
  
  
  // start redio timeZone
  
  document.addEventListener("DOMContentLoaded", function() {
    // Add event listener to the radio button
    document.getElementById("Startnow").addEventListener("click", function() {
      // Fetch system date and time
      var currentDateTime = new Date();
      // Extract the date portion
      var dateString = currentDateTime.toISOString().split('T')[0];
      // Extract the time portion
      var timeString = currentDateTime.toTimeString().split(' ')[0];
      // Set the date and time as the value of the respective hidden input fields
      document.getElementById("dateValue").value = dateString;
      document.getElementById("timeValue").value = timeString;
    });
  });
  
  // Custom date and time 
  
  document.querySelectorAll('input[name="favTime"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      if (this.value === 'option2') {
        document.getElementById('CustomDateTimeId').style.display = 'block';
      } else {
        document.getElementById('CustomDateTimeId').style.display = 'none';
      }
    });
  });
  