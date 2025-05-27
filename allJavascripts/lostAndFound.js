function showThis(formId){
    document.querySelectorAll(".box").forEach(form=> form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}

function loggedIn()
{
    window.alert("You are now logged in.");
}

const menuButt = document.getElementById('menuButt');
const sideBar = document.getElementById('sideBar');

if(menuButt && sideBar)
{
    menuButt.addEventListener('mouseenter', ()=> {
        sideBar.classList.add('sideBar-hovered');
    });

    menuButt.addEventListener('mouseleave', ()=>{
        sideBar.classList.remove('sideBar-hovered');
    });

    sideBar.addEventListener('mouseenter', ()=>{
        sideBar.classList.add('sideBar-hovered');
    });

    sideBar.addEventListener('mouseleave', ()=>{
        sideBar.classList.remove('sideBar-hovered');
    });
}

function showElem(elemId){
    document.getElementById(elemId).classList.add("active");
}

function removeElem(elemId){
    document.getElementById(elemId).classList.remove("active");
}

function showRequest(showId){
    document.getElementById(showId).classList.add('inlineActive');
}