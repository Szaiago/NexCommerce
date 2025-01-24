const logos = [
    "css/images/logoempresa1.png",
    "css/images/logoempresa2.png",
    "css/images/logoempresa3.png"
];

let logoIndex = 0;

function changeLogo() {
    logoIndex = (logoIndex + 1) % logos.length;
    document.getElementById("logo").src = logos[logoIndex];
}

setInterval(changeLogo, 5000);