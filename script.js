// Function to open and close modals
function openModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "block";
    // Close the modal if user clicks anywhere outside of it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
    // Close the modal if user presses the "Esc" key
    window.onkeydown = function (event) {
        if (event.key === "Escape") {
            modal.style.display = "none";
        }
    };
}

// Function to close modals
function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "none";
}

// Function to submit form data
function submitForm() {
    var formData = new FormData(document.getElementById("submitForm"));
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "process.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert(xhr.responseText);
            closeModal('submitModal');
        }
    };
    xhr.send(formData);
}

// Function to fetch data
function fetchData() {
    var empid = document.getElementById("fetchEmpid").value;
    var selectedData = Array.from(document.querySelectorAll('input[name="fetchData[]"]:checked')).map(e => e.value);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "process.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.error) {
                    document.getElementById("fetchResult").innerHTML = response.error;
                } else {
                    var output = "";
                    for (var key in response) {
                        output += key.charAt(0).toUpperCase() + key.slice(1) + ": " + response[key] + "<br>";
                    }
                    document.getElementById("fetchResult").innerHTML = output;
                }
            } catch (error) {
                document.getElementById("fetchResult").innerHTML = "Error parsing JSON response";
            }
        }
    };
    xhr.send("empid=" + empid + "&fetchData=" + JSON.stringify(selectedData));
}
