// let languages = [];

// function addDynamicfield(selectedValue, setAsDefault, selectedText) {
//     // Create a new row
//     var newRow = document.createElement('div');
//     newRow.className = 'row mb-3';
//     // Create the column for the new input field
//     var inputColumn = document.createElement('div');
//     inputColumn.className = 'col-md-5';
//     var newInput = document.createElement('div');
//     newInput.className = 'form-control selected_input';
//     newInput.textContent = selectedText; // Set the text content of the div
//     newInput.setAttribute('data-value', selectedValue); // Set the data-value attribute
//     newInput.contentEditable = 'false'; // Make the div non-editable
//     inputColumn.appendChild(newInput);
//     newRow.appendChild(inputColumn);
//     // Create the column for the form controls (radio button and delete button)
//     var formControlsColumn = document.createElement('div');
//     formControlsColumn.className = 'col-md-7';
//     var formControlsWrapper = document.createElement('div');
//     formControlsWrapper.className = 'd-flex align-items-center';
//     // Radio button
//     var radioButtonDiv = document.createElement('div');
//     radioButtonDiv.className = 'form-check';
//     var radioButtonInput = document.createElement('input');
//     radioButtonInput.className = 'form-check-input';
//     radioButtonInput.type = 'radio';
//     radioButtonInput.name = 'setDefault'; // Ensure the radio buttons are grouped
//     radioButtonInput.value = setAsDefault;
//     radioButtonInput.id = 'flexRadioDefault';
//     if (setAsDefault) {
//         radioButtonInput.checked = true;
//     }
//     var radioButtonLabel = document.createElement('label');
//     radioButtonLabel.className = 'form-check-label mt-1 dynamic_checkbox';
//     radioButtonLabel.htmlFor = 'flexRadioDefault';
//     radioButtonLabel.textContent = 'Set as default';
//     radioButtonDiv.appendChild(radioButtonInput);
//     radioButtonDiv.appendChild(radioButtonLabel);
//     formControlsWrapper.appendChild(radioButtonDiv);
//     // Delete button
//     var deleteButton = document.createElement('button');
//     deleteButton.className = 'QueueDeleteBtn ms-4 mt-2';
//     deleteButton.type = 'button';
//     var deleteIcon = document.createElement('span');
//     deleteIcon.className = 'material-symbols-outlined';
//     deleteIcon.textContent = 'delete';
//     deleteButton.appendChild(deleteIcon);
//     // Add event listener to delete button
//     deleteButton.addEventListener('click', function () {
//         let selectedValue = newInput.getAttribute('data-value');
//         console.log(selectedValue);
//         let index = languages.indexOf(selectedValue);
//         if (index !== -1) {
//             languages.splice(index, 1); // Remove one element at the found index
//             document.getElementById('jsonlang').value = JSON.stringify(languages);
//         }
//         newRow.remove(); // Remove the entire row when delete button is clicked
//     });
//     formControlsWrapper.appendChild(deleteButton);
//     formControlsColumn.appendChild(formControlsWrapper);
//     newRow.appendChild(formControlsColumn);
//     // Append the new row to the dynamicSelects container
//     var dynamicSelects = document.getElementById('TempdynamicSelects');
//     dynamicSelects.appendChild(newRow);
//     // Reset the mainSelect to its default option
//     var mainSelect = document.getElementById('TempmainSelect');
//     mainSelect.selectedIndex = 0; // Select the first option
// }

// document.getElementById('TempmainSelect').addEventListener('change', function () {
//     // Get the index of the selected option
//     var selectedIndex = this.selectedIndex;
//     // Get the selected option element
//     var selectedOption = this.options[selectedIndex];
//     // Get the text content of the selected option
//     var selectedText = selectedOption.textContent;

//     var selectedValue = selectedOption.value;
//     if (!languages.includes(selectedValue)) {
//         addDynamicfield(selectedValue, selectedValue, selectedText);
//         languages.push(selectedValue);
//         document.getElementById('jsonlang').value = JSON.stringify(languages); // Update the hidden field value
//     }
// });

// // Set the initial value of jsonlang hidden input
// document.getElementById('jsonlang').value = JSON.stringify(languages);

let languages = <?php echo json_encode($matchedLanguages); ?> || [];

function addDynamicfield(selectedValue, setAsDefault, selectedText) {
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
    inputColumn.appendChild(newInput);
    newRow.appendChild(inputColumn);

    // Create a hidden input field to store the language code
    var hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'queue_language[]'; // Ensure the name is an array
    hiddenInput.value = selectedValue;
    newRow.appendChild(hiddenInput);

    // Create the form controls column
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
    radioButtonInput.name = 'setDefault'; // Ensure the radio buttons are grouped
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
        let index = languages.indexOf(selectedValue);
        if (index !== -1) {
            languages.splice(index, 1); // Remove one element at the found index
            document.getElementById('jsonlang').value = JSON.stringify(languages);
        }
        newRow.remove(); // Remove the entire row when delete button is clicked
    });
    formControlsWrapper.appendChild(deleteButton);
    formControlsColumn.appendChild(formControlsWrapper);
    newRow.appendChild(formControlsColumn);

    // Append the new row to the dynamicSelects container
    var dynamicSelects = document.getElementById('TempdynamicSelects');
    dynamicSelects.appendChild(newRow);

    // Reset the mainSelect to its default option
    var mainSelect = document.getElementById('TempmainSelect');
    mainSelect.selectedIndex = 0; // Select the first option
}

document.getElementById('TempmainSelect').addEventListener('change', function () {
    // Get the index of the selected option
    var selectedIndex = this.selectedIndex;
    // Get the selected option element
    var selectedOption = this.options[selectedIndex];
    // Get the text content of the selected option
    var selectedText = selectedOption.textContent;

    var selectedValue = selectedOption.value;
    if (!languages.includes(selectedValue)) {
        addDynamicfield(selectedValue, selectedValue, selectedText);
        languages.push(selectedValue);
        document.getElementById('jsonlang').value = JSON.stringify(languages); // Update the hidden field value
    }
});

// Set the initial value of jsonlang hidden input
document.getElementById('jsonlang').value = JSON.stringify(languages);
