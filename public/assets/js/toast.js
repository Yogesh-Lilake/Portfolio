// Toast function (global)
function showToast(type, message) {
    const box = document.getElementById("toastBox");
    const toast = document.createElement("div");

    toast.className = "toast " + type;
    toast.innerText = message;

    box.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 20);
    setTimeout(() => {
        toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
