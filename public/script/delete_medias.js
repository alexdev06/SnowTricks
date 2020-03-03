function onClickBtnDeleteImage(event) 
{
    event.preventDefault();
    if (confirm("confirmation de la suppression de l'image ?")) {
        const url = this.href;
        const id = this.id + 'img';
        axios.get(url).then(function(response) {
            document.getElementById("list").removeChild(document.getElementById(id));
        })
    }
}

document.querySelectorAll('a.js-img-delete').forEach(function(link) {
    link.addEventListener('click', onClickBtnDeleteImage);
})

function onClickBtnDeleteVideo(event) {
    event.preventDefault();
    if (confirm("confirmation de la suppression de la video ?")) {
        const url = this.href;
        const id = this.id + 'video';
        axios.get(url).then(function (response) {
            document.getElementById("list").removeChild(document.getElementById(id));
        })
    }
}

document.querySelectorAll('a.js-video-delete').forEach(function (link) {
    link.addEventListener('click', onClickBtnDeleteVideo);
})
