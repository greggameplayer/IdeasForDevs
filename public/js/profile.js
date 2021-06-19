window.addEventListener("load", () => {
    let jobs = [];
    let skills = [];
    document.querySelector("#skillsbtn").addEventListener("click", (event) => {
        event.preventDefault();
        if (document.querySelector("#skills").value !== "") {
            document.querySelector("#skills_display ul").innerHTML += (`<li class="list-group-item d-flex flex-row justify-content-between">${document.querySelector("#skills").value}<i class="fas fa-times deleteskills my-auto"></i></li>`);
            skills.push(document.querySelector("#skills").value);
            document.querySelector("#skills").value = "";
            document.querySelectorAll(".deleteskills").forEach((el, idx) => {
                el.addEventListener("click", () => {
                    document.querySelector("#skills_display ul").removeChild(el.parentElement);
                    skills.splice(skills.indexOf(el.parentElement.innerText), 1);
                });
            })
        }
    });
    fetch('/api/jobs')
        .then(function(response) {
            return response.json();
        })
        .then(function(json) {
            json.forEach((el) => {
                document.querySelector("#jobsSelect").innerHTML += (`<option value=${el.id} name=${el.name}>${el.name}</option>`)
            });
            $("#jobsSelect").multiselect({
                onChange: function(element, checked) {
                    if (checked === true) {
                        jobs.push(element[0].value);
                    } else {
                        jobs.splice(jobs.indexOf(element[0].value), 1);
                    }
                }
            });
            $(".multiselect").addClass("btn btn-primary");
        });
});
