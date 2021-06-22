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
            fetch('/api/user/jobs')
                .then(function(response) {
                    return response.json();
                })
                .then(function(json) {
                    json.forEach((el) => {
                        jobs.push(el);
                        $("#jobsSelect").multiselect('select', el);
                    });
                    $("#personalinfossubmitbtn").attr("disabled", false)
                });
        });

        document.querySelector("#infosform").addEventListener("submit", (event) => {
            event.preventDefault();
            fetch('/user/personalInfos', {
                method: 'POST',
                body: JSON.stringify({firstname: document.querySelector("#firstname").value, lastname: document.querySelector("#lastname").value, birthDate: document.querySelector("#birthDate").value, jobs: jobs})
            })
                .then((response) => response.json())
                .then((json) => {
                    if(typeof json.message != "undefined" && json.message === "ok") {
                        document.querySelector("#toastheader").innerHTML = "Informations personnelles";
                        document.querySelector("#toastalert .toast-body").innerHTML = "Sauvegardé avec succès !";

                        $("#toastalert").toast({delay: 4000}).toast('show');
                    }
                });
        });

        document.querySelector("#mdpform").addEventListener("submit", (event) => {
            event.preventDefault();
            document.querySelector("#oldpassword").classList.remove("is-invalid");
            document.querySelector("#newpassword").classList.remove("is-invalid");
            document.querySelector("#confirmpassword").classList.remove("is-invalid");
            document.querySelectorAll(".invalid-feedback").forEach((el) => {
                document.querySelector("#mdpform").removeChild(el);
            })
            let feedback = document.createElement("div");
            if(document.querySelector("#newpassword").value !== document.querySelector("#confirmpassword").value) {
                feedback.classList.add("invalid-feedback");
                feedback.innerHTML = "Les mot de passe doivent être identique !";
                document.querySelector("#newpassword").classList.add("is-invalid");
                document.querySelector("#confirmpassword").classList.add("is-invalid");
                document.querySelector("#newpassword").after(feedback);
            } else if (document.querySelector("#newpassword").value.length < 8) {
                feedback.classList.add("invalid-feedback");
                feedback.innerHTML = "Le nouveau mot de passe doit faire au moins 8 caractères !";
                document.querySelector("#newpassword").classList.add("is-invalid");
                document.querySelector("#confirmpassword").classList.add("is-invalid");
                document.querySelector("#newpassword").after(feedback);
            }

            fetch('/user/password', {
                method: "POST",
                body: JSON.stringify({oldpassword: document.querySelector("#oldpassword").value, password: document.querySelector("#newpassword").value, confirmpassword: document.querySelector("#confirmpassword").value})
            })
                .then((response) => response.json())
                .then((json) => {
                   if (typeof json.error != "undefined") {
                       let feedback = document.createElement("div");
                       if (json.error.type === "oldpassword") {
                           feedback.classList.add("invalid-feedback");
                           feedback.innerHTML = json.error.message;
                           document.querySelector("#oldpassword").classList.add("is-invalid");
                           document.querySelector("#oldpassword").after(feedback);
                       } else if (json.error.type === "newpassword") {
                           feedback.classList.add("invalid-feedback");
                           feedback.innerHTML = json.error.message;
                           document.querySelector("#newpassword").classList.add("is-invalid");
                           document.querySelector("#confirmpassword").classList.add("is-invalid");
                           document.querySelector("#newpassword").after(feedback);
                       }
                   } else {
                       document.querySelector("#toastheader").innerHTML = "Mot de passe";
                       document.querySelector("#toastalert .toast-body").innerHTML = "Sauvegardé avec succès !";

                       $("#toastalert").toast({delay: 4000}).toast('show');
                       document.querySelector("#oldpassword").value = "";
                       document.querySelector("#newpassword").value = "";
                       document.querySelector("#confirmpassword").value = "";
                   }
                });
        });

    document.querySelector("#avatarfile").addEventListener("change", (event) => {
        const target = event.target;
        if (target.files[0].size > 2000000) {
            target.value = "";
        }
        if (!["image/png", "image/jpg", "image/jpeg", "image/gif"].includes(target.files[0].type)) {
            target.value = "";
        }
    })

        document.querySelector("#avatarform").addEventListener("submit", (event) => {
            event.preventDefault();
            let data = new FormData();
            data.append("avatar", document.querySelector("#avatarfile").files[0]);

            document.querySelector("#avatarfile").classList.remove("is-invalid");
            document.querySelectorAll(".invalid-feedback").forEach((el) => {
                document.querySelector("#avatarform").removeChild(el);
            })

            document.querySelector("#avatarsubmit").disabled = true;

            fetch('/user/avatar' , {
                method: 'POST',
                body: data
            })
                .then((response) => response.json())
                .then((json) => {
                    console.log(json);
                    if (typeof json.error != "undefined") {
                        let feedback = document.createElement("div");
                        feedback.classList.add("invalid-feedback");
                        feedback.innerHTML = json.error.message;
                        document.querySelector("#avatarfile").classList.add("is-invalid");
                        document.querySelector("#avatarfile").after(feedback);
                        document.querySelector("#avatarsubmit").disabled = false;
                    } else {
                        document.querySelector("#toastheader").innerHTML = "Avatar";
                        document.querySelector("#toastalert .toast-body").innerHTML = "Sauvegardé avec succès ! Rechargement en cours";

                        $("#toastalert").toast({delay: 4000}).toast('show');
                        setTimeout(() => {
                            window.location.href = "/profile";
                        }, 4000);
                    }
                })
        })
});
