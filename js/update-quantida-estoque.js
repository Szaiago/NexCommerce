function updateQuantidade(id, quantidade) {
    const xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../functions/update_quantidade.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(`id_produto=${id}&quantidade_produto=${quantidade}`);
}