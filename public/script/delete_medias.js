
function onClickBtnDeleteImage(event) 
{
    event.preventDefault();
    const url = this.href;
    const id = this.id + 'img';
    console.log(id);
    axios.get(url).then(function(response) {
        document.getElementById("list").removeChild(document.getElementById(id));
    })
}

document.querySelectorAll('a.js-img-delete').forEach(function(link) {
    link.addEventListener('click', onClickBtnDeleteImage);
})


function onClickBtnDeleteVideo(event) {
    event.preventDefault();
    const url = this.href;
    const id = this.id + 'video';
    console.log(id);
    axios.get(url).then(function (response) {
        document.getElementById("list").removeChild(document.getElementById(id));
    })
}

document.querySelectorAll('a.js-video-delete').forEach(function (link) {
    link.addEventListener('click', onClickBtnDeleteVideo);
})
