// $(document).ready(function() {
//     var step1IsValid = false;
//     var step2IsValid = false;

//     // Disable next button by default
//     $('.nextBtn').prop('disabled', true);

//     // Listen for changes in step 1 inputs
//     $('#roomname, .startqueueCheckBox, .endqueueCheckBox').on('keyup change', function() {
//         validateStep1();
//     });

//     // Listen for changes in step 2 input
//     $('#template_name').on('keyup change', function() {
//         validateStep2();
//     });

//     // Function to validate step 1 inputs
//     function validateStep1() {
//         // Perform validation for roomname input
//         var roomname = $('#roomname').val().trim();
//         var roomnameIsValid = roomname.length > 0;
//         if (roomnameIsValid) {
//             $('#roomnameErrors').text('');
//         } else {
//             $('#roomnameErrors').text('The roomname field is required.');
//         }

//         // Perform validation for start queue radio button inputs
//         var startRadioChecked = $('.startqueueCheckBox:checked').length > 0;
//         var startRadioIsValid = startRadioChecked;
//         if (startRadioIsValid) {
//             $('#QueuingStartsErrors').text('');
//         } else {
//             $('#QueuingStartsErrors').text('The Start time field is required.');
//         }

//         // Perform validation for end queue radio button inputs
//         var endRadioChecked = $('.endqueueCheckBox:checked').length > 0;
//         var endRadioIsValid = endRadioChecked;
//         if (endRadioIsValid) {
//             $('#QueuingEndsErrors').text('');
//         } else {
//             $('#QueuingEndsErrors').text('The End time field is required.');
//         }

//         // Update step 1 validity
//         step1IsValid = roomnameIsValid && startRadioIsValid && endRadioIsValid;

//         updateNextButton();
//     }

//     // Function to validate step 2 inputs
//     function validateStep2() {
//         // Perform validation for template_name input
//         var templateName = $('#template_name').val().trim();
//         var templateNameIsValid = templateName.length > 0;
//         if (templateNameIsValid) {
//             $('#templateNameErrors').text('');
//         } else {
//             $('#templateNameErrors').text('The template name field is required.');
//         }

//         // Update step 2 validity
//         step2IsValid = templateNameIsValid;

//         updateNextButton();
//     }

//     // Function to enable/disable the next button based on step 1 and step 2 validation
//     function updateNextButton() {
//         if (step1IsValid && step2IsValid) {
//             $('.nextBtn').prop('disabled', false); // Enable next button if both step 1 and step 2 are valid
//         } else {
//             $('.nextBtn').prop('disabled', true); // Disable next button if either step 1 or step 2 is not valid
//         }
//     }

//     // Listen for next button click
//     $('.nextBtn').on('click', function() {
//         if (!step1IsValid || !step2IsValid) {
//             return false; // Prevent form from proceeding if either step 1 or step 2 is not valid
//         }
//     });
// });
