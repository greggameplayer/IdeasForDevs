window.addEventListener("load", (event) => {
    document.querySelector("#skillsbtn").addEventListener("click", (event) => {
        event.preventDefault();
        if(document.querySelector("#skills").value !== "") {
            const cardnumber = document.querySelectorAll(".skillscard").length;

            switch (cardnumber) {
                case 0:
                    document.querySelector("#skills_display").innerHTML += (`<div class="card skillscard"><div class="card-body">${document.querySelector("#skills").value}</div></div>`);
                    break;
                case 1:
                    document.querySelectorAll(".skillscard")[0].style.borderRadius = "0.5rem 0.5rem 0 0";
                    document.querySelector("#skills_display").innerHTML += (`<div class="card skillscard" style="border-radius: 0 0 0.5rem 0.5rem"><div class="card-body">${document.querySelector("#skills").value}</div></div>`);
                    break;
                default:
                    document.querySelector("#skills_display").innerHTML += (`<div class="card skillscard" style="border-radius: 0 0 0.5rem 0.5rem"><div class="card-body">${document.querySelector("#skills").value}</div></div>`);
                    document.querySelectorAll(".skillscard")[cardnumber - 1].style.borderRadius = "0 0 0 0";
            }
        }
    });
})
