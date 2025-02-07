function filterTable() {
    const input = document.getElementById("pesquisaInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("tabelaProdutos");
    const trs = table.getElementsByTagName("tr");
    
    for (let i = 1; i < trs.length; i++) {
        const tds = trs[i].getElementsByTagName("td");
        let show = false;
        
        for (let j = 0; j < tds.length; j++) {
            if (tds[j].innerText.toLowerCase().indexOf(filter) > -1) {
                show = true;
                break;
            }
        }
        trs[i].style.display = show ? "" : "none";
    }
}