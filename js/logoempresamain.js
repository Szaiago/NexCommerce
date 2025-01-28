const logos = [
    "../css/images/next1.png",
    "../css/images/next2.png",
    "../css/images/next3.png"
];

let logoIndex = 0;

function changeLogo() {
    logoIndex = (logoIndex + 1) % logos.length;
    document.getElementById("logo").src = logos[logoIndex];
}

setInterval(changeLogo, 10000);