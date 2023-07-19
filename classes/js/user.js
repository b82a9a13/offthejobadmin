const div = document.getElementById('modal_div');
function sign_btn_clicked(type, action){
    const errorTxt = document.getElementById(`${type}_error`);
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/sign_render.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = text['error'];
                errorTxt.style.display = 'block';
            } else if(text['return']){
                div.innerHTML = text['return'];
                div.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send(`type=`+type+`&action=${action}`);
}
function close_modal_div(){
    document.getElementById('modal_div').style.display = 'none';
}
function reset_sign(type){
    const errorTxt = document.getElementById('modal_error');
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/sign_reset.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = text['error'];
                errorTxt.style.display = 'block';
            } else if(text['return']){
                div.style.display = 'none';
                window.location.reload();
            } else {
                errorTxt.innerText = 'Reset error.';
                errorTxt.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send(`type=${type}`);
}
function setup_reset_btn_clicked(){
    const errorTxt = document.getElementById('initial_error');
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/intial_reset_render.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = text['error'];
                errorTxt.style.display = 'block';
            } else if(text['return']){
                div.innerHTML = text['return'];
                div.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send();
}
function setup_reset_clicked(){
    const errorTxt = document.getElementById('reset_error');
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/intial_reset.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = text['error'];
                errorTxt.style.display = 'block';
            } else if(text['return']){
                window.location.href = './admin.php';
            } else {
                errorTxt.innerText = 'Reset error.';
                errorTxt.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send();
}
function plan_reset_btn_clicked(){
    const errorTxt = document.getElementById('plan_error');
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/trainingplan_reset_render.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = text['error'];
                errorTxt.style.display = 'block';
            } else if(text['return']){
                div.innerHTML = text['return'];
                div.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send();
}
function plan_reset_clicked(){
    const errorTxt = document.getElementById('reset_error');
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/trainingplan_reset.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = text['error'];
                errorTxt.style.display = 'block';
            } else if(text['return']){
                div.style.display = 'none';
                window.location.reload();
            } else {
                errorTxt.innerText = 'Reset error.';
                errorTxt.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send();
}