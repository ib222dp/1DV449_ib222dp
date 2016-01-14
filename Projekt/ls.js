if (typeof jQuery == "undefined") {
    var latestBooks = JSON.parse(window.localStorage.getItem("booklist"));
    var ul = window.document.getElementById("latestresults");
    ul.style.listStyle = "none";
    if (latestBooks !== null) {
        var BHLli = document.createElement("li");
        BHLli.innerHTML = "<strong>Results from BHL</strong>";
        ul.appendChild(BHLli);
        for (var j = 0; j < latestBooks.length; j++) {
            if (latestBooks[j] === "endBHL") {
                var li = document.createElement("li");
                var strong = document.createElement("strong");
                li.innerHTML = "<strong>Results from Gallica</strong>";
                ul.appendChild(li);
            } else {
                var li = document.createElement("li");
                if (latestBooks[j] === "View book") {
                    li.style.marginBottom = "20px";
                } else {
                    li.innerText = latestBooks[j];
                }
                ul.appendChild(li);
            }
        }
    } else {
        var li = document.createElement("li");
        li.innerText = "No results saved";
        ul.appendChild(li);
    }
} else {
    var bhlbookdiv = window.parent.document.getElementById("bhlbooks");
    var gabooksdiv = window.parent.document.getElementById("gabooks");
    var bhlbooks = bhlbookdiv.getElementsByTagName("li");
    var gabooks = gabooksdiv.getElementsByTagName("li");
    var booksArray = [];
    for (var i = 0; i < bhlbooks.length; i++) {
        booksArray.push(bhlbooks[i].innerText);
    }
    booksArray.push("endBHL");
    for (var k = 0; k < gabooks.length; k++) {
        booksArray.push(gabooks[k].innerText);
    }
    window.localStorage.setItem("booklist", JSON.stringify(booksArray));
}
