window.addEventListener("load", (event) => {
    let skills = [];
    let jobs = [];
    $("#registration_form_avatar_avatar").attr("accept", "image/png, image/jpeg, image/jpg, image/gif");

    document.querySelector("#registration_form_avatar_avatar").addEventListener("change", (event) => {
        const target = event.target;
        if (target.files[0].size > 2000000) {
            target.value = "";
        }
        if (!["image/png", "image/jpg", "image/jpeg", "image/gif"].includes(target.files[0].type)) {
            target.value = "";
        }
    })


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
            }).css("visibility", "visible");
            $(".multiselect").addClass("btn btn-primary");
        });

    document.querySelector("form[name='registration_form']").addEventListener("submit", (event) => {
        event.preventDefault();

        if(document.querySelector("#registration_form_password_first").value !== document.querySelector("#registration_form_password_second").value) {
            document.querySelector("#errorAlert").classList.remove("d-none");
            document.querySelector("#errorAlert").innerHTML = "Les mot de passe doivent ??tre identique !";
        } else {
            let data = new FormData();
            data.append('avatar', document.querySelector("#registration_form_avatar_avatar").files[0])
            data.append('firstname', document.querySelector("#registration_form_firstname").value)
            data.append('lastname', document.querySelector("#registration_form_lastname").value)
            data.append('birthDate', document.querySelector("#registration_form_birthDate").value)
            data.append('email', document.querySelector("#registration_form_email").value)
            data.append('password[first]', document.querySelector("#registration_form_password_first").value)
            data.append('password[second]', document.querySelector("#registration_form_password_second").value)
            data.append('jobs', JSON.stringify(jobs))
            data.append('skills', JSON.stringify(skills))

            fetch('/register', {
                method: 'POST',
                body: data
            })
                .then((response) => response.json())
                .then((json) => {
                    if(typeof json.error != "undefined") {
                        document.querySelector("#errorAlert").classList.remove("d-none");
                        document.querySelector("#errorAlert").innerHTML = json.error;
                    } else {
                        if (!document.querySelector("#errorAlert").classList.contains("d-none")){
                            document.querySelector("#errorAlert").classList.add("d-none");
                        }
                        $("#successmodal").modal();
                        setTimeout(() => {
                            window.location.href = "/";
                        }, 5000);
                    }
                });
        }
    })
})
