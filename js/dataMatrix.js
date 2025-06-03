divs = document.querySelectorAll(".datamatrix")

console.log(divs)

divs.forEach(div => {
    let object_number = div.getAttribute("data-matrix")
    let svg = DATAMatrix(object_number)
    svg.style.height = "128px"
    svg.style.width = "128px"
    div.appendChild(svg)
});